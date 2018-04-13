#!/bin/bash

sed -i "s/xxxxxx/$1/" /tmp/insert.php

cd /tmp

php insert.php
