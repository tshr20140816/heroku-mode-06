#!/bin/bash

set -x

wget $(curl https://java.com/en/download/manual.jsp \
 | grep 'Download Java software for Linux x64"' \
 | head -n 1 \
 | grep -oP 'http:.+?BundleId=[0-9a-z_]+') -O java.tar.gz

if [ ! -e ./java.tar.gz ]; then
  exit
fi

tar xvfz java.tar.gz

wget https://github.com/yui/yuicompressor/releases/download/v2.4.8/yuicompressor-2.4.8.jar

# for file in $(find /app/www/ttrss/ -name "*.css" -type f -print); do
#   mv ${file} ${file}.org
#   time ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type css -o ${file} ${file}.org
# done

exts[0]='css'
exts[1]='js'

for ext in "${exts[@]}" ; do
  #for file in $(find /app/www/ttrss/ -name "*.${ext}" -type f -print); do
  #  if [ -e ${file}.org ]; then
  #    continue
  #  fi
  #  mv ${file} ${file}.org
  #  hash=$(sha512sum ${file}.org | awk '{print $1}')
  #  php ./get_file.php ${file} ${hash}
  #  if [ $? -ne 0 ]; then
  #    ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type ${ext} -o ${file} ${file}.org
  #    php update.php ${file} ${hash}
  #  else
  #    echo -e "pass\n"
  #  fi
  #done
  find /app/www/ttrss/ -name "*.${ext}" -type f -print0 | \
    xargs -0i -P $(grep -c -e processor /proc/cpuinfo) -n 1 bash ./sub01_compress.sh {} ${ext}
done
