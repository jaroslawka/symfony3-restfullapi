#/bin/sh

echo "installing application ..."
cd /var/www/symfony3
composer install

chmod 777 /var/www/symfony3/var/ -R

echo "Installation finished!"

echo "starting php-fpm ..."
php-fpm7 --nodaemonize 



