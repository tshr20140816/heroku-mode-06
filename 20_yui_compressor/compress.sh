#!/bin/bash

set -x

# mv www/ttrss/lib/prototype.js www/ttrss/lib/prototype.js.org
# time ./jre*/bin/java -jar ./yuicompressor-2.4.8.jar --type js -o www/ttrss/lib/prototype.js www/ttrss/lib/prototype.js.org

for file in $(find ./www/ttrss/ -name "*.css" -type f -print); do
  echo $file;
done
