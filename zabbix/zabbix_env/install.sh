#!/bin/bash

cp -rf ./bin/* /tmp

#apt-get purge -y mysql*

apt-get update

cd /tmp/

dpkg -i zabbix-release_2.4-1+trusty_all.deb

apt-get install -y zabbix-server-mysql  php5-mysql zabbix-frontend-php 

service zabbix-server start

service  mysql start

mysql -uroot -e "create database zabbix character set utf8 collate utf8_bin; grant all privileges on zabbix.* to zabbix@localhost identified by 'zabbix'; flush privileges;"

cd /usr/share/zabbix-server-mysql/

gunzip *.gz

mysql -u zabbix -pzabbix zabbix < schema.sql

mysql -u zabbix -pzabbix zabbix < images.sql

mysql -u zabbix -pzabbix zabbix < data.sql

mv /tmp/apache2.conf /etc/apache2/apache2.conf

service apache2 restart

chown  www-data.www-data  /etc/zabbix -R

mv /tmp/zabbix.conf.php /etc/zabbix/zabbix.conf.php

apt-get update

apt-get install -y zabbix-agent

service zabbix-agent restart

cd /tmp

mv index.html /var/www/html/index.html