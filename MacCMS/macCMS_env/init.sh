#!/bin/bash

sed -i "s/xxxxxx/$1/" /var/www/html/flag.txt

rm -rf /var/www/html/maccms.sql
