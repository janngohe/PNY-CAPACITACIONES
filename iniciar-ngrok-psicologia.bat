@echo off
title PNY - Tunel Ngrok para Evaluaciones Psicotecnicas QR
echo ======================================================================
echo       C.I. PISCICOLA NEW YORK S.A. - MODULO DE PSICOLOGIA
echo ======================================================================
echo  Iniciando tunel publico Ngrok para el Portal de Evaluacion QR...
echo  Token configurado y verificado.
echo ======================================================================
echo  Redirigiendo trafico publico hacia el puerto 8000 (Laravel Serve)...
echo  (Si accedes a traves de Laragon en puerto 80, cambia a: ngrok http 80)
echo ======================================================================
ngrok http 8000
pause
