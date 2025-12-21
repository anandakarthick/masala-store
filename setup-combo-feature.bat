@echo off
echo ==========================================
echo   Custom Combo Feature Setup
echo ==========================================
echo.

cd /d %~dp0

echo Running migration for custom combo tables...
php artisan migrate --force

echo.
echo Running combo seeder...
php artisan db:seed --class=CustomComboSeeder

echo.
echo Clearing cache...
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo.
echo ==========================================
echo   Setup Complete!
echo ==========================================
echo.
echo Custom Combo feature has been installed.
echo.
echo You can now:
echo 1. Visit /admin/combos to manage combo settings
echo 2. Visit /combo to see the customer-facing combo builder
echo.
pause
