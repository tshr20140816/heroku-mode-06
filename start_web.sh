#!/bin/bash

set -x

export TZ=JST-9

httpd -V
httpd -M
php --version
whereis php
whereis whois

if [ ! -v MODE ]; then
  echo "Error : MODE not defined."
  exit
fi

if [ ! -v LOGGLY_TOKEN ]; then
  echo "Error : LOGGLY_TOKEN not defined."
  exit
fi

if [ ${MODE} = 'APACHE' ]; then

  if [ ! -v LOG_LEVEL ]; then
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

  nslookup $(echo ${REMOTE_PATH_2} | awk -F/ '{print $3}') 8.8.8.8

  export HOME_IP_ADDRESS=$(nslookup $(echo ${REMOTE_PATH_2} | awk -F/ '{print $3}') 8.8.8.8 \
    | grep ^Address \
    | grep -v 8.8.8.8 \
    | awk '{print $2}')

  htpasswd -c -b .htpasswd ${BASIC_USER} ${BASIC_PASSWORD}

  vendor/bin/heroku-php-apache2 -C apache.conf www
else

  if [ ! -v DELEGATE_OPTION ]; then
    export DELEGATE_OPTION="-v"
  fi

  ./delegate/delegated -r ${DELEGATE_OPTION} -P${PORT} +=/app/delegate/delegate.conf
fi
