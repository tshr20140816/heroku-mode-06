#!/bin/bash

set -x

file=$1

mv ${file} ${file}.org
php /tmp/get_file.php ${file} $(sha512sum ${file}.org | awk '{print $1}')
if [ $? -ne 0 ]; then
  mv ${file}.org ${file}
fi

args=("$@")

for file in ${args[@]}; do
  echo AAAAABBBBBCCCCC${file} 
#  php /tmp/get_file.php ${file} $(sha512sum ${file}.org | awk '{print $1}')
#  if [ $? -ne 0 ]; then
#    mv ${file}.org ${file}
#  fi
done
