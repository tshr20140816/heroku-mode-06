#!/bin/bash

set -x

file=${1}

size_org=$(wc -c < ${file})
size_comp=$(wc -c < ${file}.compress)

if [ ${size_org} -gt ${size_comp} ]; then
  mv ${file} ${file}.org
  mv ${file}.compress ${file}
fi
