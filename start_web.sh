#!/bin/bash

set -x

export TZ=JST-9

if [ ! -v MODE ]; then
  echo "Error : MODE not defined."
  exit
fi

if [ ! -v LOGGLY_TOKEN ]; then
  echo "Error : LOGGLY_TOKEN not defined."
  exit
fi

export USER_AGENT=$(curl https://raw.githubusercontent.com/tshr20140816/heroku-mode-06/master/useragent.txt)

export IP_ADDRESS=$(ip address | grep 'inet ' | grep -v '127.0.0.1' | awk '{print $4}')
echo "${IP_ADDRESS}" > /app/IP_ADDRESS
  
export X_ACCESS_KEY=$(sha256sum www/last_update.txt | awk '{print $1}')

export X_HOST_NAME=$(hostname)
  
if [ ${MODE} = 'APACHE' ]; then

  httpd -V
  httpd -M | sort
  php --version
  whereis php
  php -m | sort
  cat /proc/version
  curl --version
  printenv | sort

  current_version=$(cat composer.lock | grep version | awk '{print $2}' | tr -d ,)
  composer update > /dev/null 2>&1 &
  rm -rf /tmp/heroku-mode-06
  git clone --depth 1 https://github.com/tshr20140816/heroku-mode-06.git /tmp/heroku-mode-06 &

  ss -lnt4

  url="https://logs-01.loggly.com/inputs/${LOGGLY_TOKEN}/tag/START/"

  linux_version=$(cat /proc/version)
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} ${IP_ADDRESS} ${linux_version}" ${url}

  model_name=$(cat /proc/cpuinfo | grep 'model name' | head -n 1)
  cpu_count=$(cat /proc/cpuinfo | grep processor | wc -l)
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} ${IP_ADDRESS} ${model_name:13} x ${cpu_count}" ${url}

  php_lastest_version=$(curl http://us1.php.net/downloads.php | grep -o -E 'v7\.2\.[0-9]+')
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} ${IP_ADDRESS} PHP Lastest Version : ${php_lastest_version}" ${url}

  php_version="$(php -v | head -n 1)"
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} ${IP_ADDRESS} ${php_version}" ${url}

  apache_lastest_version=$(curl https://github.com/apache/httpd/releases | grep tag-name | head -n 1 | sed -e 's/<[^>]*>//g' | awk '{print $1}')
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} ${IP_ADDRESS} Apache Lastest Version : ${apache_lastest_version}" ${url}

  apache_version="$(httpd -v)"
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} ${IP_ADDRESS} ${apache_version}" ${url}

  curl_lastest_version=$(curl https://curl.haxx.se/download.html | grep ", Released on the " | grep -E -o "curl [0-9\.]+")
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} ${IP_ADDRESS} curl Lastest Version : ${curl_lastest_version}" ${url}

  curl_version="$(curl --version | head -n 1)"
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} ${IP_ADDRESS} ${curl_version}" ${url}
  
  wait
  new_version=$(cat composer.lock | grep version | awk '{print $2}' | tr -d ,)
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} ${IP_ADDRESS} heroku/heroku-buildpack-php current ${current_version} new ${new_version}" ${url} &

  echo ${HEROKU_APP_NAME}
  echo ${HEROKU_RELEASE_CREATED_AT}
  echo ${HEROKU_RELEASE_VERSION}

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

  # ml2
  if [ ! -v REMOTE_PATH_0 ]; then
    echo "Error : REMOTE_PATH_0 not defined."
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

  # ttrss2
  if [ ! -v REMOTE_PATH_3 ]; then
    echo "Error : REMOTE_PATH_3 not defined."
    exit
  fi
  
  if [ ! -v RSS_TEMPLATE_URL ]; then
    echo "Error : RSS_TEMPLATE_URL not defined."
    exit  
  fi
  
  if [ ! -v ALLOW_USER_AGENT ]; then
    export  ALLOW_USER_AGENT=${X_ACCESS_KEY}${X_HOST_NAME}
  fi

  mkdir -p /tmp/usr/lib
  cp ./lib/libnghttp2.so.14 /tmp/usr/lib/
  cp ./lib/libbrotlicommon.so.1 /tmp/usr/lib/
  cp ./lib/libbrotlienc.so.1 /tmp/usr/lib/

  export HOME_FQDN=$(echo ${REMOTE_PATH_2} | awk -F: '{print $1}')
  nslookup ${HOME_FQDN} 8.8.8.8

  # export HOME_IP_ADDRESS=$(nslookup ${HOME_FQDN} 8.8.8.8 \
  #   | grep ^Address \
  #   | grep -v 8.8.8.8 \
  #   | awk '{print $2}')

  export HOME_IP_ADDRESS=$(nslookup ${HOME_FQDN} 8.8.8.8 | tail -n2 | grep -o '[0-9]\+.\+')
  if [ -z "${HOME_IP_ADDRESS}" ]; then
    HOME_IP_ADDRESS=127.0.0.1
  fi

  export HOME_FQDN_SPARE=$(echo ${REMOTE_PATH_3} | awk -F: '{print $1}')
  nslookup ${HOME_FQDN_SPARE} 8.8.8.8
  export HOME_IP_ADDRESS_SPARE=$(nslookup ${HOME_FQDN_SPARE} 8.8.8.8 | tail -n2 | grep -o '[0-9]\+.\+')
  if [ -z "${HOME_IP_ADDRESS_SPARE}" ]; then
    HOME_IP_ADDRESS_SPARE=127.0.0.1
  fi  
  
  last_update=$(cat /app/www/last_update.txt)
  
  url="https://logs-01.loggly.com/inputs/${LOGGLY_TOKEN}/tag/START/"
  
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} * ${HOME_FQDN} ${HOME_IP_ADDRESS} * ${HOME_FQDN_SPARE} ${HOME_IP_ADDRESS_SPARE} * ${last_update}"  ${url} &

  htpasswd -c -b .htpasswd ${BASIC_USER} ${BASIC_PASSWORD}

  export LD_LIBRARY_PATH=/tmp/usr/lib
  export X_URL_DELEGATE_CACHE=https://${HEROKU_APP_NAME}.herokuapp.com/delegate_cache.php

  pushd /tmp
  mkdir -m 777 ml
  popd
  pushd www
  ln -s ./ttrss ttrss2
  popd  

  ls -lang www

  # vendor/bin/heroku-php-apache2 -C apache.conf www
  rm apache.conf
  # wget https://raw.githubusercontent.com/tshr20140816/heroku-mode-06/master/apache.conf
  cp /tmp/heroku-mode-06/apache.conf ./
  apachectl configtest apache.conf
  
  apachectl_configtest=$(apachectl configtest apache.conf 2>&1)
  curl -H 'content-type:text/plain' -d "S ${HEROKU_APP_NAME} apachectl configtest ${apachectl_configtest}" ${url} &
  
  vendor/bin/heroku-php-apache2 -C apache.conf www
else
  if [ ! -v DELEGATE_LOG_LEVEL ]; then
    export DELEGATE_LOG_LEVEL="simple"
  fi

  if [ ! -v DELEGATE_OPTION ]; then
    export DELEGATE_OPTION="-v"
  fi

  mkdir -m 777 ./delegate/tmp/
  # chmod 777 ./delegate/tmp/
  mkdir -m 777 /tmp/delegate
  mkdir -m 777 /tmp/delegate/adm
  mkdir -m 777 /tmp/delegate/cache
  mkdir -m 777 /tmp/delegate/tmp
  ./delegate/delegated -r ${DELEGATE_OPTION} -P${PORT} +=/app/delegate/delegate.conf
fi
