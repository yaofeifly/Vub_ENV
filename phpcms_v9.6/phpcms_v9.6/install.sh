#!/bin/bash

apt-get update

apt-get install -y apache2 php mysql

cp -rf ./bin/* /var/www/html

chown -R root:root /var/www/html/* 
chmod -R 777 /var/www/html/* 

service apache2 start

service mysql start

mysql -e "source /var/www/html/phpcmsv9.sql;" 



