#!/bin/bash

set -x

git clone --depth 1 -b 17.4 https://tt-rss.org/git/tt-rss.git /tmp/ttrss

mkdir -m 777 -p www/ttrss/css
cp /tmp/ttrss/css/* www/ttrss/css/

mkdir -m 777 -p www/ttrss/images
cp /tmp/ttrss/images/* www/ttrss/images/

mkdir -m 777 -p www/ttrss/js
cp /tmp/ttrss/js/* www/ttrss/js/

mkdir -m 777 -p www/ttrss/lib
cp -r /tmp/ttrss/lib/* www/ttrss/lib/

find www/ttrss/ -name "*.css" -type f -print0 | xargs -0i -P 20 -n 1 bash ./80_install/post_install_sub01_01.sh {}
find www/ttrss/ -name "*.js" -type f -print0 | xargs -0i -P 20 -n 1 bash ./80_install/post_install_sub01_01.sh {}

rm -rf /tmp/ttrss
