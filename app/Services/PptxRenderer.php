<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Symfony\Component\Process\Process;
use Throwable;
use ZipArchive;

/**
 * Renderizador de presentaciones PowerPoint (.pptx) en el servidor.
 *
 * Convierte cada diapositiva en HTML/SVG posicionado en un lienzo del tamaño original
 * de la diapositiva (ej. 1920x1080). No depende de servicios externos (Office Online,
 * Google Docs) ni de LibreOffice, por lo que funciona igual en local y en producción.
 * El resultado se guarda en caché (storage/app/public/inducciones/render/{hash}).
 */
class PptxRenderer
{
    private const EMU_PX = 9525;
    private const NS_R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    private const CACHE_VERSION = 'v3';

    private ZipArchive $zip;
    private DOMXPath $xp;
    private ?DOMXPath $layoutXp = null;
    private ?DOMXPath $masterXp = null;
    private array $theme = [];
    private array $rels = [];
    private string $outDir = '';
    private int $gradId = 0;

    /**
     * Renderiza el archivo .pptx (ruta relativa al disco "public") y retorna
     * ['width' => int, 'height' => int, 'slides' => string[]] o null si no es posible.
     */
    public static function render(?string $rutaRelativa): ?array
    {
        if (empty($rutaRelativa) || !class_exists(ZipArchive::class)) {
            return null;
        }

        $ext = strtolower(pathinfo($rutaRelativa, PATHINFO_EXTENSION));
        if (!in_array($ext, ['pptx', 'ppsx'], true)) {
            return null; // .ppt (binario antiguo) no es un ZIP OOXML
        }

        $path = storage_path('app/public/' . ltrim($rutaRelativa, '/'));
        if (!is_file($path)) {
            return null;
        }

        $hash = md5($rutaRelativa . '|' . filesize($path) . '|' . filemtime($path) . '|' . self::CACHE_VERSION);
        $outDir = storage_path('app/public/inducciones/render/' . $hash);
        $cache = $outDir . '/slides.json';
        // URL relativa al host (solo el path) para no depender de APP_URL ni del dominio de despliegue
        $base = rtrim((string) parse_url(asset('storage/inducciones/render/' . $hash), PHP_URL_PATH), '/');

        try {
            if (is_file($cache)) {
                $data = json_decode((string) file_get_contents($cache), true);
            } else {
                $data = (new self())->build($path, $outDir, $cache);
            }
        } catch (Throwable $e) {
            report($e);
            return null;
        }

        if (empty($data['slides'])) {
            return null;
        }

        $data['slides'] = array_map(fn ($html) => str_replace('__BASE__', $base, $html), $data['slides']);

        return $data;
    }

    private function build(string $path, string $outDir, string $cache): ?array
    {
        // 1. Prioridad: Render nativo vía PowerPoint (fidelidad 100% en curvas, colores, fondos y tipografías)
        try {
            $native = $this->exportWithPowerPoint($path, $outDir);
            if ($native && !empty($native['slides'])) {
                @file_put_contents($cache, json_encode($native, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE));
                return $native;
            }
        } catch (Throwable $e) {
            report($e);
        }

        // 2. Fallback: Parseo manual de OpenXML si PowerPoint COM no está disponible
        $this->zip = new ZipArchive();
        if ($this->zip->open($path) !== true) {
            return null;
        }

        if (!is_dir($outDir)) {
            @mkdir($outDir, 0775, true);
        }
        $this->outDir = $outDir;

        $presDoc = $this->loadXml('ppt/presentation.xml');
        if (!$presDoc) {
            $this->zip->close();
            return null;
        }

        $presXp = $this->makeXpath($presDoc);
        $sz = $presXp->query('//p:sldSz')->item(0);
        $cx = $sz ? (int) $sz->getAttribute('cx') : 9144000;
        $cy = $sz ? (int) $sz->getAttribute('cy') : 6858000;
        $width = max(1, (int) round($cx / self::EMU_PX));
        $height = max(1, (int) round($cy / self::EMU_PX));

        $this->loadTheme();

        $slides = [];
        foreach ($this->slidePaths($presDoc, $presXp) as $slidePath) {
            try {
                $slides[] = $this->renderSlide($slidePath, $width, $height);
            } catch (Throwable $e) {
                report($e);
                $slides[] = $this->canvas($width, $height, '#ffffff', '');
            }
        }

        $this->zip->close();

        $data = ['width' => $width, 'height' => $height, 'slides' => $slides];
        @file_put_contents($cache, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE));

        return $data;
    }

    // ------------------------------------------------------------------
    // Utilidades de ZIP / XML
    // ------------------------------------------------------------------

    private function loadXml(string $name): ?DOMDocument
    {
        $content = $this->zip->getFromName($name);
        if ($content === false || $content === '') {
            return null;
        }

        $doc = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $ok = $doc->loadXML($content, LIBXML_NONET | LIBXML_PARSEHUGE);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        return $ok ? $doc : null;
    }

    private function makeXpath(DOMDocument $doc): DOMXPath
    {
        $xp = new DOMXPath($doc);
        $xp->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xp->registerNamespace('p', 'http://schemas.openxmlformats.org/presentationml/2006/main');
        $xp->registerNamespace('r', self::NS_R);

        return $xp;
    }

    /** Normaliza una ruta relativa dentro del ZIP ("ppt/slides" + "../media/a.png"). */
    private function resolvePath(string $baseDir, string $target): string
    {
        if (str_starts_with($target, '/')) {
            return ltrim($target, '/');
        }

        $parts = [];
        foreach (explode('/', $baseDir . '/' . $target) as $seg) {
            if ($seg === '' || $seg === '.') {
                continue;
            }
            if ($seg === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $seg;
        }

        return implode('/', $parts);
    }

    /** Lee un archivo .rels y retorna [rId => ['type' => string, 'path' => string]]. */
    private function parseRels(string $relsPath, string $baseDir): array
    {
        $doc = $this->loadXml($relsPath);
        if (!$doc) {
            return [];
        }

        $out = [];
        foreach ($doc->getElementsByTagName('Relationship') as $rel) {
            $type = basename((string) $rel->getAttribute('Type'));
            $out[$rel->getAttribute('Id')] = [
                'type' => $type,
                'path' => $this->resolvePath($baseDir, (string) $rel->getAttribute('Target')),
            ];
        }

        return $out;
    }

    private function slidePaths(DOMDocument $presDoc, DOMXPath $presXp): array
    {
        $rels = $this->parseRels('ppt/_rels/presentation.xml.rels', 'ppt');
        $paths = [];

        foreach ($presXp->query('//p:sldIdLst/p:sldId') as $sldId) {
            $rid = $sldId->getAttributeNS(self::NS_R, 'id');
            if (isset($rels[$rid]) && $this->zip->locateName($rels[$rid]['path']) !== false) {
                $paths[] = $rels[$rid]['path'];
            }
        }

        if (empty($paths)) {
            for ($i = 0; $i < $this->zip->numFiles; $i++) {
                $name = $this->zip->getNameIndex($i);
                if (preg_match('#^ppt/slides/slide\d+\.xml$#', $name)) {
                    $paths[] = $name;
                }
            }
            natsort($paths);
            $paths = array_values($paths);
        }

        return $paths;
    }

    private function loadTheme(): void
    {
        $doc = $this->loadXml('ppt/theme/theme1.xml');
        if (!$doc) {
            return;
        }

        $xp = $this->makeXpath($doc);
        foreach ($xp->query('//a:clrScheme/*') as $node) {
            foreach ($node->childNodes as $c) {
                if (!$c instanceof DOMElement) {
                    continue;
                }
                $hex = $c->localName === 'sysClr' ? $c->getAttribute('lastClr') : $c->getAttribute('val');
                if (preg_match('/^[0-9A-Fa-f]{6}$/', $hex)) {
                    $this->theme[$node->localName] = strtoupper($hex);
                }
                break;
            }
        }
    }

    // ------------------------------------------------------------------
    // Diapositiva
    // ------------------------------------------------------------------

    private function canvas(int $w, int $h, string $bg, string $inner): string
    {
        return '<div class="pptx-canvas" style="position:relative;width:' . $w . 'px;height:' . $h . 'px;overflow:hidden;background:' . $bg . ';">'
            . $inner . '</div>';
    }

    private function renderSlide(string $path, int $w, int $h): string
    {
        $doc = $this->loadXml($path);
        if (!$doc) {
            return $this->canvas($w, $h, '#ffffff', '');
        }

        $this->xp = $this->makeXpath($doc);
        $this->rels = $this->parseRels(dirname($path) . '/_rels/' . basename($path) . '.rels', dirname($path));
        $this->layoutXp = null;
        $this->masterXp = null;

        // Layout y master (para heredar posiciones de placeholders y fondo)
        foreach ($this->rels as $rel) {
            if ($rel['type'] === 'slideLayout') {
                $layoutDoc = $this->loadXml($rel['path']);
                if ($layoutDoc) {
                    $this->layoutXp = $this->makeXpath($layoutDoc);
                    $layoutRels = $this->parseRels(dirname($rel['path']) . '/_rels/' . basename($rel['path']) . '.rels', dirname($rel['path']));
                    foreach ($layoutRels as $lr) {
                        if ($lr['type'] === 'slideMaster') {
                            $masterDoc = $this->loadXml($lr['path']);
                            if ($masterDoc) {
                                $this->masterXp = $this->makeXpath($masterDoc);
                            }
                        }
                    }
                }
                break;
            }
        }

        $bg = $this->backgroundCss($this->xp) ?? $this->backgroundCss($this->layoutXp) ?? $this->backgroundCss($this->masterXp) ?? '#ffffff';

        $tree = $this->xp->query('//p:cSld/p:spTree')->item(0);
        $html = '';
        if ($tree) {
            $m = ['sx' => 1.0, 'sy' => 1.0, 'tx' => 0.0, 'ty' => 0.0];
            foreach ($tree->childNodes as $child) {
                if ($child instanceof DOMElement) {
                    $html .= $this->renderNode($child, $m);
                }
            }
        }

        return $this->canvas($w, $h, $bg, $html);
    }

    private function backgroundCss(?DOMXPath $xp): ?string
    {
        if (!$xp) {
            return null;
        }

        $bgPr = $xp->query('//p:cSld/p:bg/p:bgPr')->item(0);
        if (!$bgPr) {
            return null;
        }

        $prev = $this->xp ?? null;
        $this->xp = $xp;
        $spec = $this->fillSpec($bgPr);
        $css = $spec ? $this->specToCss($spec) : null;
        if ($prev) {
            $this->xp = $prev;
        }

        return $css;
    }

    // ------------------------------------------------------------------
    // Nodos
    // ------------------------------------------------------------------

    private function renderNode(DOMElement $n, array $m): string
    {
        $cNvPr = $this->xp->query('.//p:cNvPr', $n)->item(0);
        if ($cNvPr && $cNvPr->getAttribute('hidden') === '1') {
            return '';
        }

        try {
            return match ($n->localName) {
                'grpSp' => $this->renderGroup($n, $m),
                'sp' => $this->renderSp($n, $m),
                'pic' => $this->renderPic($n, $m),
                'cxnSp' => $this->renderCxn($n, $m),
                default => '',
            };
        } catch (Throwable $e) {
            return '';
        }
    }

    private function renderGroup(DOMElement $n, array $m): string
    {
        $x = $this->xp->query('p:grpSpPr/a:xfrm', $n)->item(0);
        $nm = $m;

        if ($x) {
            $off = $this->xp->query('a:off', $x)->item(0);
            $ext = $this->xp->query('a:ext', $x)->item(0);
            $choff = $this->xp->query('a:chOff', $x)->item(0);
            $chext = $this->xp->query('a:chExt', $x)->item(0);

            if ($off && $ext && $choff && $chext) {
                $cx = (float) $ext->getAttribute('cx');
                $cy = (float) $ext->getAttribute('cy');
                $chcx = (float) $chext->getAttribute('cx');
                $chcy = (float) $chext->getAttribute('cy');

                if ($chcx > 0 && $chcy > 0 && $cx > 0 && $cy > 0) {
                    $kx = $cx / $chcx;
                    $ky = $cy / $chcy;
                    $offx = (float) $off->getAttribute('x');
                    $offy = (float) $off->getAttribute('y');
                    $choffx = (float) $choff->getAttribute('x');
                    $choffy = (float) $choff->getAttribute('y');

                    $nm = [
                        'sx' => $m['sx'] * $kx,
                        'sy' => $m['sy'] * $ky,
                        'tx' => $m['tx'] + $m['sx'] * ($offx - $choffx * $kx),
                        'ty' => $m['ty'] + $m['sy'] * ($offy - $choffy * $ky),
                    ];
                }
            }
        }

        $html = '';
        foreach ($n->childNodes as $c) {
            if ($c instanceof DOMElement && !in_array($c->localName, ['nvGrpSpPr', 'grpSpPr'], true)) {
                $html .= $this->renderNode($c, $nm);
            }
        }

        return $html;
    }

    private function xfrmData(DOMElement $x, array $m): ?array
    {
        // Se lee con DOM directo porque el nodo puede pertenecer al layout/master (otro documento)
        $off = null;
        $ext = null;
        foreach ($x->childNodes as $c) {
            if ($c instanceof DOMElement && $c->localName === 'off') {
                $off = $c;
            }
            if ($c instanceof DOMElement && $c->localName === 'ext') {
                $ext = $c;
            }
        }
        if (!$off || !$ext) {
            return null;
        }

        $e = self::EMU_PX;

        return [
            'x' => ($m['tx'] + $m['sx'] * (float) $off->getAttribute('x')) / $e,
            'y' => ($m['ty'] + $m['sy'] * (float) $off->getAttribute('y')) / $e,
            'w' => ($m['sx'] * (float) $ext->getAttribute('cx')) / $e,
            'h' => ($m['sy'] * (float) $ext->getAttribute('cy')) / $e,
            'rot' => ((int) $x->getAttribute('rot')) / 60000,
            'flipH' => in_array($x->getAttribute('flipH'), ['1', 'true'], true),
            'flipV' => in_array($x->getAttribute('flipV'), ['1', 'true'], true),
        ];
    }

    private function nodeGeometry(DOMElement $n, array $m): ?array
    {
        $x = $this->xp->query('p:spPr/a:xfrm | p:xfrm', $n)->item(0);
        if ($x) {
            return $this->xfrmData($x, $m);
        }

        // Placeholder sin posición propia: heredar del layout / master
        $ph = $this->xp->query('p:nvSpPr/p:nvPr/p:ph | p:nvPicPr/p:nvPr/p:ph', $n)->item(0);
        if (!$ph) {
            return null;
        }

        $type = preg_replace('/[^A-Za-z0-9_]/', '', $ph->getAttribute('type'));
        $idx = preg_replace('/[^A-Za-z0-9_]/', '', $ph->getAttribute('idx'));

        foreach ([$this->layoutXp, $this->masterXp] as $lxp) {
            if (!$lxp) {
                continue;
            }
            $queries = [];
            if ($idx !== '') {
                $queries[] = "//p:sp[p:nvSpPr/p:nvPr/p:ph[@idx='$idx']]/p:spPr/a:xfrm";
            }
            if ($type !== '') {
                $queries[] = "//p:sp[p:nvSpPr/p:nvPr/p:ph[@type='$type']]/p:spPr/a:xfrm";
            }
            foreach ($queries as $q) {
                $f = $lxp->query($q)->item(0);
                if ($f) {
                    return $this->xfrmData($f, $m);
                }
            }
        }

        return null;
    }

    private function boxStyle(array $g): string
    {
        $style = sprintf(
            'position:absolute;left:%.2fpx;top:%.2fpx;width:%.2fpx;height:%.2fpx;',
            $g['x'], $g['y'], max($g['w'], 0), max($g['h'], 0)
        );
        if (abs($g['rot']) > 0.01) {
            $style .= 'transform:rotate(' . round($g['rot'], 3) . 'deg);';
        }

        return $style;
    }

    private function flipStyle(array $g): string
    {
        if (!$g['flipH'] && !$g['flipV']) {
            return '';
        }

        return 'transform:scale(' . ($g['flipH'] ? -1 : 1) . ',' . ($g['flipV'] ? -1 : 1) . ');';
    }

    // ------------------------------------------------------------------
    // Relleno y líneas
    // ------------------------------------------------------------------

    private function rgba(string $hex, float $alpha): string
    {
        $hex = ltrim($hex, '#');
        if (!preg_match('/^[0-9A-Fa-f]{6}$/', $hex)) {
            $hex = '000000';
        }

        return sprintf('rgba(%d,%d,%d,%s)', hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)), rtrim(rtrim(number_format($alpha, 3, '.', ''), '0'), '.') ?: '0');
    }

    private function applyLum(string $hex, float $mod, float $off): string
    {
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;
        $h = 0;
        $s = 0;

        if ($d > 0) {
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
            if ($max === $r) {
                $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
            } elseif ($max === $g) {
                $h = ($b - $r) / $d + 2;
            } else {
                $h = ($r - $g) / $d + 4;
            }
            $h /= 6;
        }

        $l = min(1, max(0, $l * $mod + $off));

        $hue = function ($p, $q, $t) {
            if ($t < 0) {
                $t += 1;
            }
            if ($t > 1) {
                $t -= 1;
            }
            if ($t < 1 / 6) {
                return $p + ($q - $p) * 6 * $t;
            }
            if ($t < 1 / 2) {
                return $q;
            }
            if ($t < 2 / 3) {
                return $p + ($q - $p) * (2 / 3 - $t) * 6;
            }

            return $p;
        };

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $hue($p, $q, $h + 1 / 3);
            $g = $hue($p, $q, $h);
            $b = $hue($p, $q, $h - 1 / 3);
        }

        return sprintf('%02X%02X%02X', round($r * 255), round($g * 255), round($b * 255));
    }

    /** Busca un hijo de color (srgbClr, schemeClr...) dentro de $holder y lo retorna como rgba(). */
    private function color(?DOMElement $holder): ?string
    {
        if (!$holder) {
            return null;
        }

        foreach ($holder->childNodes as $c) {
            if (!$c instanceof DOMElement) {
                continue;
            }

            $hex = null;
            switch ($c->localName) {
                case 'srgbClr':
                    $hex = $c->getAttribute('val');
                    break;
                case 'sysClr':
                    $hex = $c->getAttribute('lastClr') ?: '000000';
                    break;
                case 'schemeClr':
                    $v = $c->getAttribute('val');
                    $map = ['tx1' => 'dk1', 'bg1' => 'lt1', 'tx2' => 'dk2', 'bg2' => 'lt2'];
                    $hex = $this->theme[$map[$v] ?? $v] ?? (in_array($v, ['bg1', 'lt1'], true) ? 'FFFFFF' : '000000');
                    break;
                case 'prstClr':
                    $hex = ['black' => '000000', 'white' => 'FFFFFF', 'red' => 'FF0000', 'blue' => '0000FF', 'green' => '008000'][$c->getAttribute('val')] ?? '000000';
                    break;
                default:
                    continue 2;
            }

            if (!preg_match('/^[0-9A-Fa-f]{6}$/', (string) $hex)) {
                $hex = '000000';
            }

            $alpha = 1.0;
            $lumMod = null;
            $lumOff = 0.0;
            foreach ($c->childNodes as $mod) {
                if (!$mod instanceof DOMElement) {
                    continue;
                }
                $val = (float) $mod->getAttribute('val') / 100000;
                if ($mod->localName === 'alpha') {
                    $alpha = $val;
                } elseif ($mod->localName === 'lumMod') {
                    $lumMod = $val;
                } elseif ($mod->localName === 'lumOff') {
                    $lumOff = $val;
                }
            }

            if ($lumMod !== null || $lumOff != 0) {
                $hex = $this->applyLum($hex, $lumMod ?? 1.0, $lumOff);
            }

            return $this->rgba($hex, $alpha);
        }

        return null;
    }

    /**
     * Retorna la especificación de relleno encontrada como hijo directo de $parent:
     * ['type' => 'solid'|'grad'|'blip', ...] o null (sin relleno).
     */
    private function fillSpec(?DOMElement $parent): ?array
    {
        if (!$parent) {
            return null;
        }

        foreach ($parent->childNodes as $c) {
            if (!$c instanceof DOMElement) {
                continue;
            }

            switch ($c->localName) {
                case 'noFill':
                    return null;

                case 'solidFill':
                    $col = $this->color($c);
                    return $col ? ['type' => 'solid', 'color' => $col] : null;

                case 'gradFill':
                    $stops = [];
                    foreach ($this->xp->query('a:gsLst/a:gs', $c) as $gs) {
                        $col = $this->color($gs);
                        if ($col) {
                            $stops[] = [(float) $gs->getAttribute('pos') / 1000, $col];
                        }
                    }
                    if (empty($stops)) {
                        return null;
                    }
                    if (count($stops) === 1) {
                        return ['type' => 'solid', 'color' => $stops[0][1]];
                    }
                    $lin = $this->xp->query('a:lin', $c)->item(0);
                    $path = $this->xp->query('a:path', $c)->item(0);

                    return [
                        'type' => 'grad',
                        'stops' => $stops,
                        'radial' => $path !== null,
                        'angle' => $lin ? (float) $lin->getAttribute('ang') / 60000 : 90.0,
                    ];

                case 'blipFill':
                    $blip = $this->xp->query('a:blip', $c)->item(0);
                    if ($blip) {
                        $url = $this->mediaUrl($blip->getAttributeNS(self::NS_R, 'embed'));
                        if ($url) {
                            return ['type' => 'blip', 'url' => $url];
                        }
                    }
                    return null;
            }
        }

        return null;
    }

    private function specToCss(array $spec): string
    {
        switch ($spec['type']) {
            case 'solid':
                return $spec['color'];

            case 'grad':
                $stops = implode(',', array_map(fn ($s) => $s[1] . ' ' . round($s[0], 1) . '%', $spec['stops']));
                if ($spec['radial']) {
                    return 'radial-gradient(circle at center,' . $stops . ')';
                }

                return 'linear-gradient(' . round($spec['angle'] + 90, 2) . 'deg,' . $stops . ')';

            case 'blip':
                return 'url(' . $spec['url'] . ') center/cover no-repeat';
        }

        return 'transparent';
    }

    /** Atributos SVG de relleno; los gradientes se añaden a $defs. */
    private function specToSvgFill(?array $spec, string &$defs): string
    {
        if (!$spec) {
            return 'none';
        }
        if ($spec['type'] === 'solid') {
            return $spec['color'];
        }
        if ($spec['type'] !== 'grad') {
            return 'none';
        }

        $id = 'pg' . (++$this->gradId);
        $stops = '';
        foreach ($spec['stops'] as $s) {
            $stops .= '<stop offset="' . round($s[0], 1) . '%" stop-color="' . $s[1] . '"/>';
        }

        if ($spec['radial']) {
            $defs .= '<radialGradient id="' . $id . '" cx="0.5" cy="0.5" r="0.5">' . $stops . '</radialGradient>';
        } else {
            $rad = deg2rad($spec['angle']);
            $x1 = round(0.5 - cos($rad) / 2, 4);
            $y1 = round(0.5 - sin($rad) / 2, 4);
            $x2 = round(0.5 + cos($rad) / 2, 4);
            $y2 = round(0.5 + sin($rad) / 2, 4);
            $defs .= '<linearGradient id="' . $id . '" x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '">' . $stops . '</linearGradient>';
        }

        return 'url(#' . $id . ')';
    }

    /** @return array{0:?string,1:float} [color, ancho en px] */
    private function lineInfo(?DOMElement $spPr): array
    {
        if (!$spPr) {
            return [null, 0.0];
        }

        $ln = $this->xp->query('a:ln', $spPr)->item(0);
        if (!$ln) {
            return [null, 0.0];
        }

        $w = $ln->hasAttribute('w') ? max(1.0, (float) $ln->getAttribute('w') / self::EMU_PX) : 1.0;
        $spec = $this->fillSpec($ln);

        return [($spec && $spec['type'] === 'solid') ? $spec['color'] : null, $w];
    }

    private function mediaUrl(string $rid): ?string
    {
        if ($rid === '' || !isset($this->rels[$rid])) {
            return null;
        }

        $zipPath = $this->rels[$rid]['path'];
        $ext = strtolower(pathinfo($zipPath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'svg', 'bmp'], true)) {
            return null; // emf / wmf no son visibles en navegadores
        }

        $name = basename($zipPath);
        $dest = $this->outDir . '/' . $name;
        if (!is_file($dest)) {
            $data = $this->zip->getFromName($zipPath);
            if ($data === false) {
                return null;
            }
            @file_put_contents($dest, $data);
        }

        return '__BASE__/' . rawurlencode($name);
    }

    // ------------------------------------------------------------------
    // Formas
    // ------------------------------------------------------------------

    private function renderSp(DOMElement $n, array $m): string
    {
        $g = $this->nodeGeometry($n, $m);
        if (!$g) {
            return '';
        }

        $spPr = $this->xp->query('p:spPr', $n)->item(0);
        $spec = $this->fillSpec($spPr);
        [$lnColor, $lnW] = $this->lineInfo($spPr);
        $cust = $spPr ? $this->xp->query('a:custGeom', $spPr)->item(0) : null;
        $prstNode = $spPr ? $this->xp->query('a:prstGeom', $spPr)->item(0) : null;
        $prst = $prstNode ? $prstNode->getAttribute('prst') : 'rect';

        $shape = '';
        if ($cust && (!$spec || $spec['type'] !== 'blip')) {
            $shape = $this->customGeometrySvg($cust, $spec, $lnColor, $lnW, $g);
        } elseif ($spec || $lnColor) {
            $css = '';
            if ($spec) {
                $css .= 'background:' . $this->specToCss($spec) . ';';
            }
            if ($lnColor) {
                $css .= 'border:' . round($lnW, 2) . 'px solid ' . $lnColor . ';box-sizing:border-box;';
            }
            $css .= $this->radiusCss($prst, $prstNode, $g);
            $shape = '<div style="position:absolute;inset:0;' . $css . '"></div>';
        }

        $text = $this->renderText($n);

        if ($shape === '' && $text === '') {
            return '';
        }

        $flip = $this->flipStyle($g);
        if ($shape !== '' && $flip !== '') {
            $shape = '<div style="position:absolute;inset:0;' . $flip . '">' . $shape . '</div>';
        }

        return '<div style="' . $this->boxStyle($g) . '">' . $shape . $text . '</div>';
    }

    private function radiusCss(string $prst, ?DOMElement $prstNode, array $g): string
    {
        if ($prst === 'ellipse') {
            return 'border-radius:50%;';
        }

        if (in_array($prst, ['roundRect', 'round1Rect', 'round2SameRect', 'round2DiagRect'], true)) {
            $adj = 16667;
            if ($prstNode) {
                $gd = $this->xp->query("a:avLst/a:gd[@name='adj']", $prstNode)->item(0);
                if ($gd && preg_match('/val\s+(-?\d+)/', $gd->getAttribute('fmla'), $mt)) {
                    $adj = (int) $mt[1];
                }
            }
            $r = min($g['w'], $g['h']) * min($adj, 50000) / 100000;

            return 'border-radius:' . round($r, 2) . 'px;';
        }

        return '';
    }

    private function customGeometrySvg(DOMElement $cust, ?array $spec, ?string $lnColor, float $lnW, array $g): string
    {
        $out = '';

        foreach ($this->xp->query('a:pathLst/a:path', $cust) as $path) {
            $pw = (float) $path->getAttribute('w');
            $ph = (float) $path->getAttribute('h');
            if ($pw <= 0 || $ph <= 0) {
                $pw = max($g['w'] * self::EMU_PX, 1);
                $ph = max($g['h'] * self::EMU_PX, 1);
            }

            $d = '';
            foreach ($path->childNodes as $cmd) {
                if (!$cmd instanceof DOMElement) {
                    continue;
                }

                $pts = [];
                foreach ($cmd->childNodes as $pt) {
                    if ($pt instanceof DOMElement && $pt->localName === 'pt') {
                        $pts[] = $pt->getAttribute('x') . ' ' . $pt->getAttribute('y');
                    }
                }

                switch ($cmd->localName) {
                    case 'moveTo':
                        $d .= 'M' . ($pts[0] ?? '0 0') . ' ';
                        break;
                    case 'lnTo':
                        $d .= 'L' . ($pts[0] ?? '0 0') . ' ';
                        break;
                    case 'cubicBezTo':
                        if (count($pts) >= 3) {
                            $d .= 'C' . implode(' ', array_slice($pts, 0, 3)) . ' ';
                        }
                        break;
                    case 'quadBezTo':
                        if (count($pts) >= 2) {
                            $d .= 'Q' . implode(' ', array_slice($pts, 0, 2)) . ' ';
                        }
                        break;
                    case 'close':
                        $d .= 'Z ';
                        break;
                }
            }

            if (trim($d) === '') {
                continue;
            }

            $defs = '';
            $noFill = $path->getAttribute('fill') === 'none';
            $noStroke = in_array($path->getAttribute('stroke'), ['0', 'false'], true);

            $fill = $noFill ? 'none' : $this->specToSvgFill($spec, $defs);
            $stroke = (!$noStroke && $lnColor) ? $lnColor : 'none';

            $out .= '<svg viewBox="0 0 ' . round($pw, 2) . ' ' . round($ph, 2) . '" preserveAspectRatio="none" '
                . 'style="position:absolute;left:0;top:0;width:100%;height:100%;overflow:visible">'
                . ($defs ? '<defs>' . $defs . '</defs>' : '')
                . '<path d="' . trim($d) . '" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="' . round($lnW, 2) . '" vector-effect="non-scaling-stroke"/>'
                . '</svg>';
        }

        return $out;
    }

    private function renderCxn(DOMElement $n, array $m): string
    {
        $g = $this->nodeGeometry($n, $m);
        if (!$g) {
            return '';
        }

        $spPr = $this->xp->query('p:spPr', $n)->item(0);
        [$lnColor, $lnW] = $this->lineInfo($spPr);
        if (!$lnColor) {
            return '';
        }

        $x1 = $g['flipH'] ? '100%' : '0';
        $x2 = $g['flipH'] ? '0' : '100%';
        $y1 = $g['flipV'] ? '100%' : '0';
        $y2 = $g['flipV'] ? '0' : '100%';

        return '<svg style="' . $this->boxStyle($g) . 'overflow:visible">'
            . '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="' . $lnColor . '" stroke-width="' . round($lnW, 2) . '"/>'
            . '</svg>';
    }

    private function renderPic(DOMElement $n, array $m): string
    {
        $g = $this->nodeGeometry($n, $m);
        if (!$g) {
            return '';
        }

        $blip = $this->xp->query('p:blipFill/a:blip', $n)->item(0);
        if (!$blip) {
            return '';
        }

        $url = $this->mediaUrl($blip->getAttributeNS(self::NS_R, 'embed'));
        if (!$url) {
            return '';
        }

        $l = $t = $r = $b = 0.0;
        $src = $this->xp->query('p:blipFill/a:srcRect', $n)->item(0);
        if ($src) {
            $l = (float) $src->getAttribute('l') / 100000;
            $t = (float) $src->getAttribute('t') / 100000;
            $r = (float) $src->getAttribute('r') / 100000;
            $b = (float) $src->getAttribute('b') / 100000;
        }

        $wFrac = max(0.01, 1 - $l - $r);
        $hFrac = max(0.01, 1 - $t - $b);

        $spPr = $this->xp->query('p:spPr', $n)->item(0);
        $prstNode = $spPr ? $this->xp->query('a:prstGeom', $spPr)->item(0) : null;
        $radius = $this->radiusCss($prstNode ? $prstNode->getAttribute('prst') : 'rect', $prstNode, $g);

        $img = sprintf(
            '<img src="%s" alt="" draggable="false" style="position:absolute;max-width:none;width:%.3f%%;height:%.3f%%;left:%.3f%%;top:%.3f%%;">',
            $url, 100 / $wFrac, 100 / $hFrac, -$l / $wFrac * 100, -$t / $hFrac * 100
        );

        return '<div style="' . $this->boxStyle($g) . '">'
            . '<div style="position:absolute;inset:0;overflow:hidden;' . $radius . $this->flipStyle($g) . '">' . $img . '</div>'
            . '</div>';
    }

    // ------------------------------------------------------------------
    // Texto
    // ------------------------------------------------------------------

    private function renderText(DOMElement $sp): string
    {
        $tx = $this->xp->query('p:txBody', $sp)->item(0);
        if (!$tx || trim($tx->textContent) === '') {
            return '';
        }

        $body = $this->xp->query('a:bodyPr', $tx)->item(0);
        $anchor = $body ? ($body->getAttribute('anchor') ?: 't') : 't';
        $justify = ['t' => 'flex-start', 'ctr' => 'center', 'b' => 'flex-end'][$anchor] ?? 'flex-start';
        $e = self::EMU_PX;

        $lIns = $body && $body->hasAttribute('lIns') ? (float) $body->getAttribute('lIns') : 91440;
        $tIns = $body && $body->hasAttribute('tIns') ? (float) $body->getAttribute('tIns') : 45720;
        $rIns = $body && $body->hasAttribute('rIns') ? (float) $body->getAttribute('rIns') : 91440;
        $bIns = $body && $body->hasAttribute('bIns') ? (float) $body->getAttribute('bIns') : 45720;
        $nowrap = $body && $body->getAttribute('wrap') === 'none';

        $scale = 1.0;
        $fit = $body ? $this->xp->query('a:normAutofit', $body)->item(0) : null;
        if ($fit && $fit->hasAttribute('fontScale')) {
            $scale = (float) $fit->getAttribute('fontScale') / 100000;
        }

        // Tamaño por defecto según tipo de placeholder
        $ph = $this->xp->query('p:nvSpPr/p:nvPr/p:ph', $sp)->item(0);
        $phType = $ph ? $ph->getAttribute('type') : '';
        $defaultSz = match ($phType) {
            'title', 'ctrTitle' => 44,
            'subTitle' => 24,
            default => 18,
        };

        $html = '';
        foreach ($this->xp->query('a:p', $tx) as $p) {
            $html .= $this->renderParagraph($p, $scale, $defaultSz, $nowrap);
        }

        return '<div style="position:absolute;inset:0;display:flex;flex-direction:column;justify-content:' . $justify . ';'
            . 'box-sizing:border-box;padding:' . round($tIns / $e, 2) . 'px ' . round($rIns / $e, 2) . 'px ' . round($bIns / $e, 2) . 'px ' . round($lIns / $e, 2) . 'px;'
            . 'overflow:visible;word-wrap:break-word;">' . $html . '</div>';
    }

    private function renderParagraph(DOMElement $p, float $scale, float $defaultSz, bool $nowrap): string
    {
        $pPr = $this->xp->query('a:pPr', $p)->item(0);
        $e = self::EMU_PX;

        $algn = $pPr ? $pPr->getAttribute('algn') : '';
        $align = ['l' => 'left', 'ctr' => 'center', 'r' => 'right', 'just' => 'justify'][$algn] ?? 'left';

        $lineHeight = 1.2;
        $before = 0.0;
        $after = 0.0;
        $marL = 0.0;
        $indent = 0.0;
        $bullet = '';

        if ($pPr) {
            $pct = $this->xp->query('a:lnSpc/a:spcPct', $pPr)->item(0);
            if ($pct) {
                $lineHeight = max(0.5, (float) $pct->getAttribute('val') / 100000 * 1.2);
            }
            $bef = $this->xp->query('a:spcBef/a:spcPts', $pPr)->item(0);
            if ($bef) {
                $before = (float) $bef->getAttribute('val') / 100 * 96 / 72;
            }
            $aft = $this->xp->query('a:spcAft/a:spcPts', $pPr)->item(0);
            if ($aft) {
                $after = (float) $aft->getAttribute('val') / 100 * 96 / 72;
            }
            $marL = (float) $pPr->getAttribute('marL') / $e;
            $indent = (float) $pPr->getAttribute('indent') / $e;

            $bu = $this->xp->query('a:buChar', $pPr)->item(0);
            if ($bu) {
                $bullet = htmlspecialchars($bu->getAttribute('char') ?: '•', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '&nbsp;';
            }
        }

        $runs = '';
        $lastSize = $defaultSz * 96 / 72 * $scale;

        foreach ($p->childNodes as $c) {
            if (!$c instanceof DOMElement) {
                continue;
            }

            if ($c->localName === 'br') {
                $runs .= '<br>';
                continue;
            }

            if (!in_array($c->localName, ['r', 'fld'], true)) {
                continue;
            }

            $t = $this->xp->query('a:t', $c)->item(0);
            if (!$t) {
                continue;
            }

            $rPr = $this->xp->query('a:rPr', $c)->item(0);
            [$style, $size] = $this->runStyle($rPr, $scale, $defaultSz);
            $lastSize = $size;

            $runs .= '<span style="' . $style . '">' . htmlspecialchars($t->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</span>';
        }

        if (trim(strip_tags(str_replace('&nbsp;', '', $runs))) === '' && !str_contains($runs, '<br>')) {
            $end = $this->xp->query('a:endParaRPr', $p)->item(0);
            if ($end && $end->hasAttribute('sz')) {
                $lastSize = (float) $end->getAttribute('sz') / 100 * 96 / 72 * $scale;
            }

            return '<div style="margin:0;height:' . round($lastSize * $lineHeight, 2) . 'px;"></div>';
        }

        $pad = $marL > 0 ? 'padding-left:' . round($marL, 2) . 'px;' : '';
        $textIndent = $indent != 0 ? 'text-indent:' . round($indent, 2) . 'px;' : '';

        return '<div style="margin:' . round($before, 2) . 'px 0 ' . round($after, 2) . 'px 0;text-align:' . $align . ';line-height:' . round($lineHeight, 3) . ';'
            . $pad . $textIndent . 'white-space:' . ($nowrap ? 'nowrap' : 'pre-wrap') . ';">'
            . $bullet . $runs . '</div>';
    }

    /** @return array{0:string,1:float} [estilo CSS, tamaño en px] */
    private function runStyle(?DOMElement $rPr, float $scale, float $defaultSz): array
    {
        $sizePt = $defaultSz;
        $style = '';

        if ($rPr) {
            if ($rPr->hasAttribute('sz')) {
                $sizePt = (float) $rPr->getAttribute('sz') / 100;
            }
            if (in_array($rPr->getAttribute('b'), ['1', 'true'], true)) {
                $style .= 'font-weight:700;';
            }
            if (in_array($rPr->getAttribute('i'), ['1', 'true'], true)) {
                $style .= 'font-style:italic;';
            }
            $deco = [];
            if (in_array($rPr->getAttribute('u'), ['sng', 'dbl', 'heavy'], true)) {
                $deco[] = 'underline';
            }
            if (in_array($rPr->getAttribute('strike'), ['sngStrike', 'dblStrike'], true)) {
                $deco[] = 'line-through';
            }
            if ($deco) {
                $style .= 'text-decoration:' . implode(' ', $deco) . ';';
            }
            if ($rPr->getAttribute('cap') === 'all') {
                $style .= 'text-transform:uppercase;';
            }
            if ($rPr->hasAttribute('spc')) {
                $style .= 'letter-spacing:' . round((float) $rPr->getAttribute('spc') / 100 * 96 / 72, 2) . 'px;';
            }

            $spec = $this->fillSpec($rPr);
            if ($spec && $spec['type'] === 'solid') {
                $style .= 'color:' . $spec['color'] . ';';
            }

            $latin = $this->xp->query('a:latin', $rPr)->item(0);
            if ($latin) {
                $face = preg_replace('/[^A-Za-z0-9 \-_]/', '', $latin->getAttribute('typeface'));
                if ($face !== '' && !str_starts_with($face, '+')) {
                    $style .= "font-family:'" . $face . "','DM Sans','Segoe UI',Arial,sans-serif;";
                }
            }
        }

        if (!str_contains($style, 'color:')) {
            $style .= 'color:#000000;';
        }

        $px = $sizePt * 96 / 72 * $scale;
        $style .= 'font-size:' . round($px, 2) . 'px;';

        return [$style, $px];
    }

    /**
     * Exporta las diapositivas a imágenes PNG de alta definición (1920px) usando
     * Microsoft PowerPoint a través de PowerShell/COM.
     * Garantiza fidelidad visual idéntica: fuentes reales, formas curvas, esquemas de color y fondos maestros.
     */
    private function exportWithPowerPoint(string $path, string $outDir): ?array
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return null;
        }

        $realPath = realpath($path);
        if (!$realPath || !is_file($realPath)) {
            return null;
        }

        $outDir = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $outDir);
        if (!is_dir($outDir)) {
            @mkdir($outDir, 0775, true);
        }

        $cleanPptx = str_replace("'", "''", $realPath);
        $cleanOut = str_replace("'", "''", $outDir);

        $psCode = <<<POWERSHELL
\$ErrorActionPreference = 'Stop'
\$pptxPath = '$cleanPptx'
\$outDir = '$cleanOut'

if (-not (Test-Path \$outDir)) {
    New-Item -ItemType Directory -Path \$outDir -Force | Out-Null
}

\$ppt = New-Object -ComObject PowerPoint.Application
try {
    \$pres = \$ppt.Presentations.Open(\$pptxPath, -1, 0, 0)
    \$w = [int]\$pres.PageSetup.SlideWidth
    \$h = [int]\$pres.PageSetup.SlideHeight
    \$count = \$pres.Slides.Count
    \$targetW = 1920
    \$targetH = [int](\$targetW * (\$h / \$w))

    for (\$i = 1; \$i -le \$count; \$i++) {
        \$slide = \$pres.Slides.Item(\$i)
        \$dest = Join-Path \$outDir ("slide_" + \$i + ".png")
        \$slide.Export(\$dest, "PNG", \$targetW, \$targetH)
    }

    \$pres.Close()
    Write-Output "JSON_START"
    ConvertTo-Json @{ width = \$targetW; height = \$targetH; count = \$count; success = \$true } -Compress
} finally {
    \$ppt.Quit()
    [System.Runtime.Interopservices.Marshal]::ReleaseComObject(\$ppt) | Out-Null
}
POWERSHELL;

        $encoded = base64_encode(mb_convert_encoding($psCode, 'UTF-16LE', 'UTF-8'));
        $process = new Process([
            'powershell.exe',
            '-NoProfile',
            '-NonInteractive',
            '-ExecutionPolicy',
            'Bypass',
            '-EncodedCommand',
            $encoded,
        ]);
        $process->setTimeout(180);
        $process->run();

        if (!$process->isSuccessful()) {
            return null;
        }

        $stdout = $process->getOutput();
        $lines = preg_split('/\r?\n/', $stdout);
        $jsonLine = null;
        $capture = false;
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === 'JSON_START') {
                $capture = true;
                continue;
            }
            if ($capture && !empty($line)) {
                $jsonLine = $line;
                break;
            }
        }

        if (!$jsonLine) {
            return null;
        }

        $meta = json_decode($jsonLine, true);
        if (!$meta || empty($meta['success']) || empty($meta['count'])) {
            return null;
        }

        $w = (int) $meta['width'];
        $h = (int) $meta['height'];
        $count = (int) $meta['count'];

        $slides = [];
        for ($i = 1; $i <= $count; $i++) {
            $imgFile = $outDir . DIRECTORY_SEPARATOR . "slide_{$i}.png";
            if (!is_file($imgFile)) {
                return null;
            }
            $slides[] = '<div class="pptx-canvas" style="position:relative;width:' . $w . 'px;height:' . $h . 'px;overflow:hidden;background:#020617;display:flex;align-items:center;justify-content:center;">'
                . '<img src="__BASE__/slide_' . $i . '.png" alt="Diapositiva ' . $i . '" draggable="false" style="width:100%;height:100%;object-fit:contain;display:block;user-select:none;pointer-events:none;">'
                . '</div>';
        }

        return ['width' => $w, 'height' => $h, 'slides' => $slides];
    }
}
