@echo off
cd /d D:\cladue\masala-store\storage\framework\views
del /Q *.php
echo Deleted all cached views

cd /d D:\cladue\masala-store\bootstrap\cache
del /Q *.php 2>nul
echo Deleted bootstrap cache

cd /d D:\cladue\masala-store
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

echo.
echo All caches cleared! Refresh your browser.
pause
