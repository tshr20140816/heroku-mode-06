#!/bin/bash

set -x

wget $(curl https://java.com/en/download/manual.jsp \
 | grep 'Download Java software for Linux x64"' \
 | head -n 1 \
 | grep -oP 'http:.+?BundleId=[0-9a-z_]+') -O java.tar.gz
tar xvfz java.tar.gz

wget https://github.com/yui/yuicompressor/releases/download/v2.4.8/yuicompressor-2.4.8.jar

# for file in $(find /app/www/ttrss/ -name "*.css" -type f -print); do
#   mv ${file} ${file}.org
#   time ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type css -o ${file} ${file}.org
# done

for file in $(find /app/www/ttrss/ -name "*.js" -type f -print); do
  mv ${file} ${file}.org
  time ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type js -o ${file} ${file}.org
done

