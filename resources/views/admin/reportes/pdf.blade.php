<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }} - C.I. Piscícola New York</title>
    <style>
        @page {
            margin: 24px 30px 30px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.35;
        }
        .header {
            border-bottom: 2px solid #0056b3;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }
        .header table {
            width: 100%;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #0b1f3a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #0056b3;
            margin-top: 4px;
        }
        .meta-table {
            font-size: 9px;
            color: #64748b;
            text-align: right;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 7px 6px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }
        table.data td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
        }
        table.data tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%;">
                    <div class="company-name">C.I. Piscícola New York S.A.</div>
                    <div class="company-sub">Sistema Integral de Capacitaciones y Gestión del Talento Humano</div>
                    <div class="report-title">{{ $titulo }}</div>
                </td>
                <td class="meta-table" style="width: 40%;">
                    <div><strong>Generado el:</strong> {{ $fecha->format('d/m/Y H:i:s') }}</div>
                    <div><strong>Emitido por:</strong> {{ $generadoPor }}</div>
                    <div><strong>Total registros:</strong> {{ count($filas) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                @foreach ($columnas as $col)
                    <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($filas as $fila)
                <tr>
                    @foreach ($fila as $val)
                        <td>{{ $val }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columnas) }}" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No se encontraron registros con los filtros seleccionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento confidencial emitido por la Plataforma de Capacitaciones de C.I. Piscícola New York S.A. · Página única o correlativa de auditoría.
    </div>
</body>
</html>
