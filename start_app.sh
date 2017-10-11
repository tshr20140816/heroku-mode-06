#!/bin/bash

set -x

export TZ=JST-9

unset BASIC_USER
unset BASIC_PASSWORD

echo apache
vendor/bin/heroku-php-apache2 -C apache.conf www
