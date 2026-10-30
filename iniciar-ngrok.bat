@echo off
title PNY - Tunel Ngrok para Registro Movil QR
echo ========================================================
echo  Iniciando tunel ngrok para pruebas QR en celular
echo  Puerto objetivo: 8000 (Laravel)
echo ========================================================
ngrok http 8000
pause
