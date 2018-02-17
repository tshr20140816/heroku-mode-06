#!/bin/bash

set -x

date

export HOME2=${PWD}

cat /proc/version
cat /proc/cpuinfo

ls -lang /tmp

if [ ! -v DEVELOP_MODE ]; then
  export DEVELOP_MODE='OFF'
fi

bash ./post_install_sub01.sh &

git clone --depth 1 https://github.com/tshr20140816/heroku-mode-03.git /tmp/self_repository &

# ***** delegate *****

mkdir -m 777 -p /tmp/usr/bin
mkdir -m 777 -p /tmp/ccache

export PATH="/tmp/usr/bin:${PATH}"

# mkdir -m 777 ../delegate
mkdir -m 777 -p ../delegate/icons

# apache
chmod 777 ../www
mkdir -m 777 ../www/icons

if [ ${DEVELOP_MODE} = 'OFF' ]; then
  mv ../ccache_cache.zip /tmp/
  pushd /tmp
  unzip -q ccache_cache.zip
  popd
fi
export CCACHE_DIR=/tmp/ccache

export CFLAGS="-O2 -march=native"
export CXXFLAGS="$CFLAGS"

if [ -e ../ccache.zip ]; then
  mv ccache.zip /tmp/usr/bin/
  pushd /tmp/usr/bin
  unzip ccache.zip
  popd
  rm -f ../ccache-3.3.4.tar.gz
else
  pushd /tmp
  wget https://www.samba.org/ftp/ccache/ccache-3.3.4.tar.gz
  tar xf ccache-3.3.4.tar.gz
  pushd ccache-3.3.4
  ./configure --prefix=/tmp/usr
  make -j$(grep -c -e processor /proc/cpuinfo)
  make install
  popd
  popd
fi
pushd /tmp/usr/bin
ln -s ccache gcc
ln -s ccache g++
ln -s ccache cc
ln -s ccache c++
popd

ccache -s
ccache -z

if [ ! -e ../delegate9.9.13.tar.gz ]; then
  pushd /tmp
  time wget http://delegate.hpcc.jp/anonftp/DeleGate/delegate9.9.13.tar.gz
  popd
else
  mv ../delegate9.9.13.tar.gz /tmp/
fi
pushd /tmp
tar xf delegate9.9.13.tar.gz
pushd delegate9.9.13

rm ./src/builtin/mssgs/news/artlistfooter.dhtml
echo "<HR>" > ./src/builtin/mssgs/news/artlistfooter.dhtml

# time make -j$(grep -c -e processor /proc/cpuinfo) ADMIN="admin@localhost"
time make ADMIN="admin@localhost"

cp ./src/delegated ${HOME2}/delegate/
cp ./src/builtin/icons/ysato/*.gif ${HOME2}/delegate/icons/

# apache
cp ./src/builtin/icons/ysato/*.gif ${HOME2}/www/icons/

cp ${HOME2}/delegate.conf ${HOME2}/delegate/

popd
popd

ccache -s

pushd ${HOME2}

if [ ${DEVELOP_MODE} != 'OFF' ]; then
  pushd /tmp
  zip -9r ccache_cache.zip ./ccache
  mv ccache_cache.zip ${HOME2}/www/
  popd
fi

mkdir -m 777 -p ../delegate/cache
mkdir -m 777 -p ../delegate/tmp

wait

# pushd ./www/ttrss/css/
# gzip -9c tt-rss.css > tt-rss.css.gz
# rm tt-rss.css
# popd

# ***** last update *****

pushd /tmp/self_repository

last_update=$(git log | grep Date | grep -o "\w\{3\} .\+$")

echo "${last_update}" > ${HOME2}/www/last_update.txt

popd

chmod 755 ../start_web.sh
chmod 755 ../loggly.php

popd

date
