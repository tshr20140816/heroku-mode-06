#!/bin/bash

set -x

if [ ! -v MODE ]; then
  echo "Error : MODE not defined."
  exit
fi

if [ ! -v LOGGLY_TOKEN ]; then
  echo "Error : LOGGLY_TOKEN not defined."
  exit
fi

export TZ=JST-9

httpd -V
httpd -M
php --version
whereis php

if [ ${MODE} = 'APACHE' ]; then

  if [ ! -v LOG_LEVEL ]; then
    # echo "Error : LOG_LEVEL not defined."
    # exit
    export LOG_LEVEL="warn"
  fi

  if [ ! -v BASIC_USER ]; then
    echo "Error : BASIC_USER not defined."
    exit
  fi

  if [ ! -v BASIC_PASSWORD ]; then
    echo "Error : BASIC_PASSWORD not defined."
    exit
  fi

  if [ ! -v REMOTE_PATH_1 ]; then
    echo "Error : REMOTE_PATH_1 not defined."
    exit
  fi

  if [ ! -v REMOTE_PATH_2 ]; then
    echo "Error : REMOTE_PATH_2 not defined."
    exit
  fi
  
  htpasswd -c -b .htpasswd ${BASIC_USER} ${BASIC_PASSWORD}
  
  vendor/bin/heroku-php-apache2 -C apache.conf www
else
  ./delegate/delegated -r -v -P${PORT} +=/app/delegate/delegate.conf
fi
