#!/bin/bash

set -x

git clone --depth 1 -b 17.4 https://tt-rss.org/git/tt-rss.git /tmp/ttrss

mkdir -m 777 -p /tmp/www/ttrss
mv /tmp/ttrss/css /tmp/www/ttrss/css
mv /tmp/ttrss/images /tmp/www/ttrss/images
mv /tmp/ttrss/js /tmp/www/ttrss/js
mv /tmp/ttrss/lib /tmp/www/ttrss/lib

cp ./80_install/post_install_sub01_01.sh /tmp/post_install_sub01_01.sh
cp ./20_yui_compressor/get_file.php /tmp/get_file.php
chmod +x /tmp/get_file.php

pushd /tmp
find www/ttrss/ -name "*.css" -type f -print0 | xargs -0i -P 5 -n 20 php /tmp/get_file.php
find www/ttrss/ -name "*.js" -type f -print0 | xargs -0i -P 5 -n 20 php /tmp/get_file.php
# find www/ttrss/ -name "*.css" -type f -print0 | xargs -0i -P 1 -n 2 bash /tmp/post_install_sub01_01.sh
# find www/ttrss/ -name "*.js" -type f -print0 | xargs -0i -P 20 -n 20 bash /tmp/post_install_sub01_01.sh
# find www/ttrss/ -name "*.css" -type f -print0 | xargs -0i -P 20 -n 1 bash /tmp/post_install_sub01_01.sh {}
# find www/ttrss/ -name "*.js" -type f -print0 | xargs -0i -P 20 -n 1 bash /tmp/post_install_sub01_01.sh {}
popd

mv /tmp/www/ttrss www/ttrss
mkdir -m 777 -p www/ttrss/plugins/note
mv /tmp/ttrss/plugins/note/note.png www/ttrss/plugins/note/
