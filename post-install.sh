#!/bin/bash

set -x

printenv

mkdir -m 777 www/icons
touch www/icons/index.html

rm -f delegate9.9.13.tar.gz
wget http://delegate.hpcc.jp/anonftp/DeleGate/delegate9.9.13.tar.gz
rm -rf delegate9.9.13
tar xfz delegate9.9.13.tar.gz
rm -f delegate9.9.13.tar.gz
cp ./delegate9.9.13/src/builtin/icons/ysato/*.gif ./www/icons/
rm -rf delegate9.9.13

htpasswd -c -b .htpasswd ${BASIC_USER} ${BASIC_PASSWORD}

unset BASIC_USER
unset BASIC_PASSWORD

printenv
