@echo off
title Raricart Local Server
echo ===================================================
echo     Uruchamianie lokalnego serwera Raricart.pl
echo ===================================================
echo Serwer: http://localhost:8000
echo Aby zatrzymac serwer, zamknij to okno lub wcisnij Ctrl+C.
echo.
start http://localhost:8000
C:\xampp\php\php.exe -S 127.0.0.1:8000
