@echo off
echo Running Custom Combo Migration and Seeder...
echo.

cd /d D:\cladue\masala-store

echo Step 1: Running migration...
php artisan migrate --force

echo.
echo Step 2: Running combo seeder...
php artisan db:seed --class=CustomComboSeeder --force

echo.
echo Step 3: Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo ========================================
echo Custom Combo feature installed successfully!
echo ========================================
echo.
echo You can now:
echo 1. Visit /admin/combos to manage combo settings
echo 2. Visit /combo to see the customer combo builder
echo.
pause
