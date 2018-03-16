#!/bin/bash

apt-get install -y python3-pip

pip3 install --upgrade pip

pip install flask Pillow

rm -rf /var/lib/apt/lists/*

cp -rf ./bin/* /root

cd /root

mkdir -p /opt/ghostscript

tar -zxf ghostscript-9.21-linux-x86_64.tgz

mv ghostscript-9.21-linux-x86_64/gs-921-linux-x86_64 /usr/local/bin/gs

mv /root/app.py /usr/src/




