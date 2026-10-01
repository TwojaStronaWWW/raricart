@echo off
title Raricart Local Server
cd /d "%~dp0"

where php >nul 2>nul
if %ERRORLEVEL% equ 0 (
    set PHP_CMD=php
) else if exist "%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" (
    set PHP_CMD="%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
) else if exist "C:\xampp\php\php.exe" (
    set PHP_CMD=C:\xampp\php\php.exe
) else (
    echo ===================================================
    echo  [BLAD] Nie znaleziono interpretera PHP!
    echo  Upewnij sie, ze PHP jest zainstalowane na komputerze.
    echo ===================================================
    pause
    exit /b 1
)

:: Detekcja adresu IP w sieci lokalnej do testow na telefonie (Wi-Fi)
set LOCAL_IP=
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    for /f "tokens=1" %%b in ("%%a") do (
        if not "%%b"=="127.0.0.1" if not "%%b"=="" (
            echo %%b | findstr /v "169.254" >nul && set LOCAL_IP=%%b
        )
    )
)

echo ===================================================
echo     Lokalny serwer deweloperski Raricart.pl
echo ===================================================
echo  [PC]            http://localhost:8000
if defined LOCAL_IP (
echo  [Telefon / LAN] http://%LOCAL_IP%:8000
)
echo ===================================================
echo  Aby zatrzymac serwer, zamknij to okno lub wcisnij Ctrl+C.
echo.

start http://localhost:8000
%PHP_CMD% -S 0.0.0.0:8000 bin/router.php
