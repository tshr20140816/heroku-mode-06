#!/bin/bash

set -x

export TZ=JST-9

apachectl -v
php --version

if [ ${MODE} = 'FRONT' ];
  vendor/bin/heroku-php-apache2 -C apache.conf www
else
  ./delegate/delegated -r -v -P${PORT} +=./delegate/delegate.conf
fi
