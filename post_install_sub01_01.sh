#!/bin/bash

set -x

file=$1

mv ${file} ${file}.org
php ./20_yui_compressor/get_file.php ${file} $(sha512sum ${file}.org | awk '{print $1}')
if [ $? -eq 0 ]; then
  mv ${file}.org ${file}
fi
