#!/bin/bash

set -x

export TZ=JST-9

httpd -V
httpd -M
php --version
whereis php
printenv

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

  # ml
  if [ ! -v REMOTE_PATH_1 ]; then
    echo "Error : REMOTE_PATH_1 not defined."
    exit
  fi

  # ttrss
  if [ ! -v REMOTE_PATH_2 ]; then
    echo "Error : REMOTE_PATH_2 not defined."
    exit
  fi

  home_fqdn=$(echo ${REMOTE_PATH_2} | awk -F/ '{print $3}')
  nslookup ${home_fqdn} 8.8.8.8

  export HOME_IP_ADDRESS=$(nslookup ${home_fqdn} 8.8.8.8 \
    | grep ^Address \
    | grep -v 8.8.8.8 \
    | awk '{print $2}')

  url="https://logs-01.loggly.com/inputs/${LOGGLY_TOKEN}/tag/${home_fqdn}/"
  curl -H 'User-Agent : curl 2-33-9-51' -H 'content-type:text/plain' -d "${home_fqdn} ${HOME_IP_ADDRESS}" ${url}

  htpasswd -c -b .htpasswd ${BASIC_USER} ${BASIC_PASSWORD}

  vendor/bin/heroku-php-apache2 -C apache.conf www
else

  if [ ! -v DELEGATE_OPTION ]; then
    export DELEGATE_OPTION="-v"
  fi

  ./delegate/delegated -r ${DELEGATE_OPTION} -P${PORT} +=/app/delegate/delegate.conf
fi
