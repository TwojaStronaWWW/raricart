@echo off
title Raricart Local Server
cd /d "%~dp0"
echo ===================================================
echo     Uruchamianie lokalnego serwera Raricart.pl
echo ===================================================
echo Serwer: http://localhost:8000
echo Aby zatrzymac serwer, zamknij to okno lub wcisnij Ctrl+C.
echo.

where php >nul 2>nul
if %ERRORLEVEL% equ 0 (
    set PHP_CMD=php
) else if exist "%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" (
    set PHP_CMD="%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
) else if exist "C:\xampp\php\php.exe" (
    set PHP_CMD=C:\xampp\php\php.exe
) else (
    echo [BLAD] Nie znaleziono PHP! Upewnij sie, ze PHP jest zainstalowane.
    pause
    exit /b 1
)

start http://localhost:8000
%PHP_CMD% -S 127.0.0.1:8000 bin/router.php
