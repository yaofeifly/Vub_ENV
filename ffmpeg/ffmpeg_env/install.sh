#!/bin/bash
apt-get update

cp -rf ./bin/index.php /var/www/html/

cp -rf ./bin/apache2.conf /etc/apache2/

cp -rf ./bin/flag /root/

apt-get install -y python-software-properties

apt-get install -y software-properties-common

add-apt-repository ppa:kirillshkrogalev/ffmpeg-next

apt-get update

apt-get install -y ffmpeg

chmod 777 -R /var/www/html/

rm -rf /var/www/html/index.html



