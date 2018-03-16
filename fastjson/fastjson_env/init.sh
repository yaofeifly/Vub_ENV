#!/bin/bash

service tomcat8 restart

sed -i "s/xxxxxx/$1/" /var/lib/tomcat8/webapps/flag.txt

#rm -rf /var/lib/tomcat8/webapps/fastjson-1.0.war
