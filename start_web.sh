#!/bin/bash

set -x

export TZ=JST-9

apachectl -v
php --version

sed -i -e "s/__PORT__/${PORT}/g" ./delegate/delegate.conf

cat ./delegate/delegate.conf

./delegate/delegated -r -v -P${PORT} +=./delegate/delegate.conf
