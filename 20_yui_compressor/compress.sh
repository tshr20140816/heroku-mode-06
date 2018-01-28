#!/bin/bash

set -x

# mv www/ttrss/lib/prototype.js www/ttrss/lib/prototype.js.org
# time ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type js -o www/ttrss/lib/prototype.js www/ttrss/lib/prototype.js.org

curl https://java.com/en/download/manual.jsp \
 | grep 'Download Java software for Linux x64"' \
 | head -n 1 \
 | grep -oP 'http:.+?BundleId=[0-9a-z_]+'

wget $(curl https://java.com/en/download/manual.jsp \
 | grep 'Download Java software for Linux x64"' \
 | head -n 1 \
 | grep -oP 'http:.+?BundleId=[0-9a-z_]+') -O java.tar.gz
tar xvfz java.tar.gz

wget https://github.com/yui/yuicompressor/releases/download/v2.4.8/yuicompressor-2.4.8.jar

for file in $(find /app/www/ttrss/ -name "*.css" -type f -print); do
  # echo $file;
  mv ${file} ${file}.org
  ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type css -o ${file} ${file}.org
done
