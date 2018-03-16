#!/bin/bash
apt-get update

cp -rf ./bin/* /var/www/html

chown -R www-data:www-data /var/www/html/* 

chmod 777 /var/www/html

chmod 777 /var/www/html/maccms.sql

apt-get install -y php5-curl

mv /var/www/html/apache2.conf /etc/apache2/apache2.conf

service apache2 restart

mysql -e "source /var/www/html/maccms.sql;" 



