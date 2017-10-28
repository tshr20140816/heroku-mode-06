#!/bin/bash

set -x

export TZ=JST-9

sed -i -e "s/__MAIL_ACCOUNT__/${MAIL_ACCOUNT}/g" ./delegate/delegate.conf
sed -i -e "s/__APP_NAME__/${APP_NAME}/g" ./delegate/delegate.conf

cat ./delegate/delegate.conf

./delegate/delegated -r -v -P${PORT} +=./delegate/delegate.conf
