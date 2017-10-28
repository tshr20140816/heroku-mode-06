#!/bin/bash

set -x

export TZ=JST-9

sed -i -e "s/__MAIL_ACCOUNT__/${MAIL_ACCOUNT}/g" ./delegate/delegate.conf
sed -i -e "s/__PORT__/${PORT}/g" ./delegate/delegate.conf

export KEYWORD01="+pop.${__MAIL_ACCOUNT__}.pop.mail.yahoo.co.jp" 

cat ./delegate/delegate.conf

./delegate/delegated -r -v -P${PORT} +=./delegate/delegate.conf
