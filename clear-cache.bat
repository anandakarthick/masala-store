@echo off
cd /d D:\cladue\masala-store
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo All caches cleared!
pause
