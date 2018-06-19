#!/bin/bash
apt-get update

apt-get install -y tomcat6

cp -rf ./bin/* /var/lib/tomcat6/

mv /var/lib/tomcat6/Strust2.war /var/lib/tomcat6/webapps/

mv /var/lib/tomcat6/index.html /var/lib/tomcat6/webapps/ROOT/

service tomcat6 restart




