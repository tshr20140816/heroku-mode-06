#!/bin/bash

set -x

file=$1
ext=$2

if [ -e ${file}.org ]; then
  return
fi

mv ${file} ${file}.org
hash=$(sha512sum ${file}.org | awk '{print $1}')
php ./get_file2.php ${file} ${hash}
if [ $? -ne 0 ]; then
  # ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type ${ext} -o ${file} ${file}.org
  /tmp/jre*/bin/java -jar /tmp/yuicompressor-2.4.8.jar --type ${ext} -o ${file} ${file}.org
  php update.php ${file} ${hash}
  /tmp/brotli -q 11 ${file}
  php update2.php ${file}.br ${hash}
else
  echo -e "pass\n"
fi
