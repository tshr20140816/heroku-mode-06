#!/bin/bash

set -x

git clone --depth 1 -b 17.4 https://tt-rss.org/git/tt-rss.git /tmp/ttrss

mkdir -m 777 -p /tmp/www/ttrss
mv /tmp/ttrss/css /tmp/www/ttrss/css
mv /tmp/ttrss/images /tmp/www/ttrss/images
mv /tmp/ttrss/js /tmp/www/ttrss/js
mv /tmp/ttrss/lib /tmp/www/ttrss/lib

CURRENT_DIR=${PWD}

pushd /tmp
find www/ttrss/ -name "*.css" -type f -print0 | xargs -0i -P 20 -n 1 bash ${CURRENT_DIR}/80_install/post_install_sub01_01.sh ${CURRENT_DIR} {}
find www/ttrss/ -name "*.js" -type f -print0 | xargs -0i -P 20 -n 1 bash ${CURRENT_DIR}/80_install/post_install_sub01_01.sh ${CURRENT_DIR} {}
popd

mv /tmp/www/ttrss www/ttrss
