@echo off
echo ============================================
echo APPOLIOS Database Import Script
echo ============================================
echo.

set MYSQL_PATH=C:\xampp\mysql\bin\mysql.exe
set DB_USER=root
set DB_PASS=
set DB_NAME=appolios_db
set SQL_FILE=%~dp0appolios_db.sql

echo Importing database...
echo.

"%MYSQL_PATH%" -u %DB_USER% %DB_PASS% < "%SQL_FILE%"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ============================================
    echo SUCCESS: Database imported successfully!
    echo ============================================
    echo.
    echo Database: appolios_db
    echo.
    echo Default accounts:
    echo   Admin:   admin@appolios.com / admin123
    echo   Student: student@appolios.com / student123
    echo.
) else (
    echo.
    echo ERROR: Failed to import database.
    echo Make sure MySQL is running in XAMPP.
)

echo.
pause