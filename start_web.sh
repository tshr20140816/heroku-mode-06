#!/bin/bash

set -x

export TZ=JST-9

httpd -V
httpd -M
php --version
whereis php
cat /proc/version
curl --version
printenv

if [ ! -v MODE ]; then
  echo "Error : MODE not defined."
  exit
fi

if [ ! -v LOGGLY_TOKEN ]; then
  echo "Error : LOGGLY_TOKEN not defined."
  exit
fi

url="https://logs-01.loggly.com/inputs/${LOGGLY_TOKEN}/tag/START/"

export IP_ADDRESS=$(ip address | grep 'inet ' | grep -v '127.0.0.1' | awk '{print $4}')

linux_version="$(cat /proc/version)"
curl -i -H 'content-type:text/plain' -d "S ${IP_ADDRESS} ${linux_version}" ${url}

model_name="$(cat /proc/cpuinfo | grep 'model name' | head -n 1)"
curl -i -H 'content-type:text/plain' -d "S ${IP_ADDRESS} ${model_name:13}" ${url}

curl_version="$(curl --version | head -n 1)"
curl -i -H 'content-type:text/plain' -d "S ${IP_ADDRESS} ${curl_version}" ${url}

echo "${IP_ADDRESS}" > /app/IP_ADDRESS

export X_ACCESS_KEY=$(md5sum www/last_update.txt | awk '{print $1}')

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
  
  if [ ! -v RSS_TEMPLATE_URL ]; then
    echo "Error : RSS_TEMPLATE_URL not defined."
    exit  
  fi

  export HOME_FQDN=$(echo ${REMOTE_PATH_2} | awk -F/ '{print $3}' | awk -F: '{print $1}')
  nslookup ${HOME_FQDN} 8.8.8.8

  export HOME_IP_ADDRESS=$(nslookup ${HOME_FQDN} 8.8.8.8 \
    | grep ^Address \
    | grep -v 8.8.8.8 \
    | awk '{print $2}')

  echo "${HOME_FQDN} ${HOME_IP_ADDRESS}" > /app/HOME_IP_ADDRESS

  htpasswd -c -b .htpasswd ${BASIC_USER} ${BASIC_PASSWORD}

  vendor/bin/heroku-php-apache2 -C apache.conf www
else

  if [ ! -v DELEGATE_OPTION ]; then
    export DELEGATE_OPTION="-v"
  fi

  chmod 777 ./delegate/tmp/

  ./delegate/delegated -r ${DELEGATE_OPTION} -P${PORT} +=/app/delegate/delegate.conf
fi
