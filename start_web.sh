#!/bin/bash

set -x

export TZ=JST-9

apachectl -v
httpd -V
httpd -M
php --version
whereis php

if [ ${MODE} = 'APACHE' ]; then
  vendor/bin/heroku-php-apache2 -C apache.conf www
else
  ./delegate/delegated -r -v -P${PORT} +=./delegate/delegate.conf
fi
