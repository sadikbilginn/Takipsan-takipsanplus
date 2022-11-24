cd $1
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan optimize
php artisan key:generate
php artisan migrate --seed
php artisan optimize
