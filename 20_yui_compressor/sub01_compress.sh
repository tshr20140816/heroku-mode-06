#!/bin/bash

set -x

file=$1
ext=$2

if [ -e ${file}.org ]; then
  return
fi

hash=$(sha512sum ${file} | awk '{print $1}')
mv ${file} ${file}.org
php ./get_file2.php ${file} ${hash}
if [ $? -ne 0 ]; then
  # ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type ${ext} -o ${file} ${file}.org
  /tmp/jre*/bin/java -jar /tmp/yuicompressor-2.4.8.jar --type ${ext} -o ${file} ${file}.org
  php update.php ${file} ${hash}
  /tmp/brotli -q 11 ${file}
  php update2.php ${file} ${hash}
else
  echo -e "pass\n"
fi
