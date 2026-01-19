@echo off
cd /d "c:\Users\melch\Antonio_MidtermsExam\twitter-like-app"
echo Generating App Key...
php artisan key:generate
echo.
echo Running Database Migrations...
php artisan migrate --force
echo.
echo Starting Laravel Server...
php artisan serve
pause
