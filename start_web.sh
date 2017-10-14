#!/bin/bash

set -x

export TZ=JST-9

unset BASIC_USER
unset BASIC_PASSWORD

printenv

hostname

uname -a

cat /proc/version

cat /proc/cpuinfo

export IP_ADDR=$(ip -4 address | grep global | sed 's/\// /' | awk '{print $2}')
echo ${IP_ADDR}

echo apache
vendor/bin/heroku-php-apache2 -C apache.conf www

# sleep 10s

# ./start_worker.sh
