#!/bin/bash
apt-get update

cp -rf ./bin/* /tmp

apt-get install -y sendmail unzip

cd /tmp

cp index.php /var/www/html

unzip -d /var/www/html /tmp/phpmailer-5.2.17.zip

chown -R www-data:www-data /var/www/html

cd /tmp

mv /tmp/apache2.conf /etc/apache2

mv /tmp/flag.txt /var/www/html

chmod 755 start.sh

nohup bash start.sh > myLog.log 2>&1 &





