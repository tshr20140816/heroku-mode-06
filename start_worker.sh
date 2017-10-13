#!/bin/bash

set -x

export TZ=JST-9

export IP_ADDR=$(ip -4 address | grep global | sed 's/\// /' | awk '{print $2}')
echo ${IP_ADDR}

hostname

echo delegate
# ./delegate/delegated -f -r -vvv -P${IP_ADDR}:50080 +=./delegate/delegate.conf
./delegate/delegated -f -r -vvv -P127.0.0.1:50080 +=./delegate/delegate.conf
