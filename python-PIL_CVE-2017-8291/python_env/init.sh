#!/bin/bash

cd /usr/src/

nohup python3 app.py > myLog.log 2>&1 &

mv /root/flag.txt /tmp

sed -i "s/xxxxxx/$1/" /tmp/flag.txt



