@echo off
REM ============================================================
REM  STOP_APMS - Stop Ahmed Pharmacy Management System servers
REM  Developed by Ahmed
REM ============================================================
setlocal EnableExtensions
cd /d "%~dp0"

echo Stopping APMS servers...
taskkill /fi "WINDOWTITLE eq APMS Backend*" /f >nul 2>nul
taskkill /fi "WINDOWTITLE eq APMS Frontend*" /f >nul 2>nul

REM Also kill any lingering artisan serve / vite processes for this project
for /f "tokens=2" %%p in ('tasklist /fi "IMAGENAME eq php.exe" /fo csv 2^>nul ^| findstr /i "artisan"') do taskkill /pid %%p /f >nul 2>nul

echo Done. APMS servers stopped.
pause
