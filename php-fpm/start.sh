#/bin/sh

echo "installing application ..."
cd /var/www/symfony3
composer install

chmod 777 /var/www/symfony3/var/ -R


#php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create

php bin/console doctrine:schema:update --force

echo "Installation finished!"

echo "starting php-fpm ..."
php-fpm7 --nodaemonize 



