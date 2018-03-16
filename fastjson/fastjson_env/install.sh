#!/bin/bash
apt-get update

apt-get install -y tomcat8

cp -rf ./bin/* /var/lib/tomcat8/webapps

mv /var/lib/tomcat8/webapps/tomcat8 /etc/default/

mv /var/lib/tomcat8/webapps/index.html /var/lib/tomcat8/webapps/ROOT/

service tomcat8 restart




