#!/bin/bash

set -x

pushd /tmp
git clone --depth 1 -b 17.4 https://tt-rss.org/git/tt-rss.git ttrss
popd

mkdir -m 777 -p www/ttrss/css
cp /tmp/ttrss/css/* www/ttrss/css/

mkdir -m 777 -p www/ttrss/images
cp /tmp/ttrss/images/* www/ttrss/images/

mkdir -m 777 -p www/ttrss/js
cp /tmp/ttrss/js/* www/ttrss/js/

mkdir -m 777 -p www/ttrss/lib
cp -r /tmp/ttrss/lib/* www/ttrss/lib/

# find ../www/ttrss/ -name "*.css" -type f -print0 | xargs -0i -P 20 -n 1 bash ./post_install_sub01_01.sh {}
# find ../www/ttrss/ -name "*.js" -type f -print0 | xargs -0i -P 20 -n 1 bash ./post_install_sub01_01.sh {}

rm -rf ttrss
