#!/bin/bash

set -x

if [ ! -v APPNAME ]; then
  echo "Error : APPNAME not defined."
  exit
fi

cd ~
cd heroku-cli-*
cd bin

./heroku labs:enable runtime-dyno-metadata -a ${APPNAME}
