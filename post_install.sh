#!/bin/bash

set -x

date

cat /proc/version
cat /proc/cpuinfo

if [ ! -v DEVELOP_MODE ]; then
  export DEVELOP_MODE='OFF'
fi

wget $(curl https://java.com/en/download/manual.jsp \
 | grep 'Download Java software for Linux x64"' \
 | head -n 1 \
 | grep -oP 'http:.+?BundleId=[0-9a-z_]+') -O java.tar.gz \
 && tar xvfz java.tar.gz &

wget https://github.com/yui/yuicompressor/releases/download/v2.4.8/yuicompressor-2.4.8.jar &

git clone --depth 1 -b 17.4 https://tt-rss.org/git/tt-rss.git ttrss &

git clone --depth 1 https://github.com/tshr20140816/heroku-mode-03.git self_repository &

# ***** delegate *****

export HOME2=${PWD}
export PATH="${HOME2}/usr/local/bin:${PATH}"

mkdir -m 777 delegate
mkdir -m 777 -p delegate/icons

# apache
chmod 777 www
mkdir -m 777 -p www/icons

mkdir -m 777 -p usr/local
mkdir -m 777 ccache

if [ ${DEVELOP_MODE} = 'OFF' ]; then
  time unzip -q ccache_cache.zip
fi
rm -f ccache_cache.zip

export CFLAGS="-O2 -march=native"
export CXXFLAGS="$CFLAGS"

if [ -e ccache.zip ]; then
  mkdir -m 777 -p usr/local/bin
  mv ccache.zip usr/local/bin/
  pushd usr/local/bin/
  time unzip ccache.zip
  rm -f ccache.zip
  popd
  rm -f ccache-3.3.4.tar.gz
else
  time wget https://www.samba.org/ftp/ccache/ccache-3.3.4.tar.gz
  time tar xfz ccache-3.3.4.tar.gz
  pushd ccache-3.3.4
  time ./configure --prefix=${HOME2}/usr/local
  time make -j$(grep -c -e processor /proc/cpuinfo)
  time make install
  popd
fi
pushd usr/local/bin
ln -s ccache gcc
ln -s ccache g++
ln -s ccache cc
ln -s ccache c++
popd

export CCACHE_DIR=${HOME2}/ccache

ccache -s
ccache -z

if [ ! -e delegate9.9.13.tar.gz ]; then
  time wget http://delegate.hpcc.jp/anonftp/DeleGate/delegate9.9.13.tar.gz
fi
time tar xfz delegate9.9.13.tar.gz
rm -f delegate9.9.13.tar.gz
pushd delegate9.9.13

rm ./src/builtin/mssgs/news/artlistfooter.dhtml
cat << '__HEREDOC__' > ./src/builtin/mssgs/news/artlistfooter.dhtml
<HR>
__HEREDOC__

time make -j$(grep -c -e processor /proc/cpuinfo) ADMIN="admin@localhost"

cp ./src/delegated ../delegate/
cp ./src/builtin/icons/ysato/*.gif ../delegate/icons/

# apache
cp ./src/builtin/icons/ysato/*.gif ../www/icons/

cp ${HOME2}/delegate.conf ../delegate/

popd
rm -rf delegate9.9.13

ccache -s

pushd ${HOME2}

rm -rf ./usr
rm -f delegate.zip

if [ ${DEVELOP_MODE} != 'OFF' ]; then
  zip -9r ccache_cache.zip ./ccache
  mv ccache_cache.zip ./www/
fi
rm -rf ./ccache

mkdir -m 777 -p delegate/cache
mkdir -m 777 -p delegate/tmp

# ***** ttrss *****

wait

mkdir -m 777 -p www/ttrss/css
cp ttrss/css/* www/ttrss/css/

mkdir -m 777 -p www/ttrss/images
cp ttrss/images/* www/ttrss/images/

mkdir -m 777 -p www/ttrss/js
cp ttrss/js/* www/ttrss/js/

mkdir -m 777 -p www/ttrss/lib
cp -r ttrss/lib/* www/ttrss/lib/

# pushd www/ttrss/lib/dojo/nls/ja
# gzip -9c colors.js > colors.js.gz
# rm -f colors.js
# popd

find ./www/ttrss/ -name "*.css" -type f -print0 > /tmp/css_files.txt

cat /tmp/css_files.txt | xargs -0i -P $(grep -c -e processor /proc/cpuinfo) -n 1 mv {} {}.org
time cat /tmp/css_files.txt | xargs -0i -P $(grep -c -e processor /proc/cpuinfo) -n 1 \
 ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type css -o {} {}.org &

find ./www/ttrss/js/ -name "*.js" -type f -print0 > /tmp/js_files.txt

cat /tmp/js_files.txt | xargs -0i -P $(grep -c -e processor /proc/cpuinfo) -n 1 mv {} {}.org
time cat /tmp/js_files.txt | xargs -0i -P $(grep -c -e processor /proc/cpuinfo) -n 1 \
 ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type js -o {} {}.org &

wait

# mv www/ttrss/lib/prototype.js www/ttrss/lib/prototype.js.org
# time ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type js -o www/ttrss/lib/prototype.js www/ttrss/lib/prototype.js.org

# mv www/ttrss/js/functions.js www/ttrss/js/functions.js.org
# time ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type js -o www/ttrss/js/functions.js www/ttrss/js/functions.js.org

rm -rf ttrss

# ***** last update *****

pushd self_repository

last_update=$(git log | grep Date | grep -o "\w\{3\} .\+$")

echo "${last_update}" > ../www/last_update.txt

popd

rm -rf self_repository

chmod 755 ./start_web.sh
chmod 755 ./loggly.php

rm -rf jre*
rm java.tar.gz
rm yuicompressor-2.4.8.jar

popd

date
