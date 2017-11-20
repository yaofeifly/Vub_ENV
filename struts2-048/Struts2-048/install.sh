#!/bin/bash
apt-get update

#echo 1 >> /tmp/1.txt

apt-get install -y tomcat8

cp -rf ./bin/* /var/lib/tomcat8/

mv /var/lib/tomcat8/feifei.war /var/lib/tomcat8/webapps/

mv /var/lib/tomcat8/index.html /var/lib/tomcat8/webapps/ROOT/

mv mv /var/lib/tomcat8/tomcat8 /etc/default/

service tomcat8 restart




