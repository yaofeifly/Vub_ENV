#!/bin/bash

apt-get update


cp -rf ./bin/* /tmp

apt-get install -y make


cd /tmp

mv apache2.conf /etc/apache2/apache2.conf

service apache2 restart

apt-get purge -"Yes, do as I say!" bash

tar -zxvf bash-4.3.tar.gz

cd /tmp/bash-4.3/

./configure

make

make install


rm /bin/bash


ln -s /usr/local/bin/bash /bin/bash


mv /tmp/000-default.conf /etc/apache2/sites-enabled/000-default.conf

mkdir /var/www/html/cgi-bin/

mv /tmp/test.sh /var/www/html/cgi-bin/

chmod 755 /var/www/html/cgi-bin/test.sh

sudo a2enmod cgi

service apache2 restart

mv /tmp/shellshock.png /var/www/html/

mv /tmp/index.html /var/www/html/

mv /tmp/flag.txt /var/www/html/cgi-bin/


