#!/bin/bash

cp -rf ./bin/* /tmp

apt-get update

mkdir /opt/node

cd /tmp

tar zxf /tmp/node-v4.3.2-linux-x64.tar.gz -C /opt/node --strip-components=1

ln -s /opt/node/bin/node /usr/bin/node

ln -s /opt/node/bin/npm /usr/bin/npm

mkdir -p /htdocs

mv node.tar.gz /htdocs

mv flag.txt /htdocs

cd /htdocs

tar -zxvf /htdocs/node.tar.gz

nohup node hello.js > myLog.log 2>&1 &




