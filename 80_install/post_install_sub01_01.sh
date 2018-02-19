#!/bin/bash

set -x

basepath=$1
file=$2

mv ${file} ${file}.org
php ${basepath}/20_yui_compressor/get_file.php ${file} $(sha512sum ${file}.org | awk '{print $1}')
if [ $? -ne 0 ]; then
  mv ${file}.org ${file}
fi
