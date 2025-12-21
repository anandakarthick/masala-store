@echo off
echo Completing Custom Combo Setup...
echo.

cd /d D:\cladue\masala-store

echo Tables already exist, running seeder only...
php artisan db:seed --class=CustomComboSeeder --force

echo.
echo Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo ========================================
echo Custom Combo feature ready!
echo ========================================
echo.
echo You can now:
echo 1. Visit /admin/combos to manage combo settings
echo 2. Visit /combo to see the customer combo builder
echo.
pause
