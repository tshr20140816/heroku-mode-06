#!/bin/bash

set -x

echo "START\n"

for file in "$@"; do
  mv ${file} ${file}.org
  # php /tmp/get_file.php ${file}
  /tmp/get_file.php ${file}
  if [ $? -ne 0 ]; then
    mv ${file}.org ${file}
  fi
done

echo "FINISH\n"
