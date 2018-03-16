#!/bin/bash

cp -rf /root/flag /tmp/

sed -i "s/xxxxxx/$1/" /tmp/flag

