#!/bin/bash

set -x

export TZ=JST-9

sed -e "s/__MAIL_ACCOUNT__/${MAIL_ACCOUNT}/g" ./delegate/delegate.conf

cat ./delegate/delegate.conf

./delegate/delegated -r -v -P${PORT} +=./delegate/delegate.conf
