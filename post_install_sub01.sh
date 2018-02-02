#!/bin/bash

set -x

git clone --depth 1 -b 17.4 https://tt-rss.org/git/tt-rss.git ttrss

mkdir -m 777 -p www/ttrss/css
cp ttrss/css/* www/ttrss/css/

mkdir -m 777 -p www/ttrss/images
cp ttrss/images/* www/ttrss/images/

mkdir -m 777 -p www/ttrss/js
cp ttrss/js/* www/ttrss/js/

mkdir -m 777 -p www/ttrss/lib
cp -r ttrss/lib/* www/ttrss/lib/

exts[0]='css'
# exts[1]='js'

for ext in "${exts[@]}" ; do
  #for file in $(find ./www/ttrss/ -name "*.${ext}" -type f -print); do
  #  mv ${file} ${file}.org
  #  hash=$(sha512sum ${file}.org | awk '{print $1}')
  #  php ./20_yui_compressor/get_file.php ${file} ${hash}
  #  if [ $? -eq 0 ]; then
  #    mv ${file}.org ${file}
  #  fi
  #done
  find ./www/ttrss/ -name "*.${ext}" -type f -print0 | xargs -0i -P 4 -n 1 bash ./post_install_sub01_01.sh {}
done

rm -rf ttrss
