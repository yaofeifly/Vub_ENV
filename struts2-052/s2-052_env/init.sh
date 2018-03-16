#!/bin/bash

#mv /tmp/flag.txt /usr/local/tomcat7/

sed -i "s/xxxxxx/$1/" /var/lib/tomcat8/webapps/flag.txt

