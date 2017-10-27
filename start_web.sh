#!/bin/bash

set -x

export TZ=JST-9

#printenv

#hostname

#uname -a

#cat /proc/version

#cat /proc/cpuinfo

#export IP_ADDR=$(ip -4 address | grep global | sed 's/\// /' | awk '{print $2}')
#echo ${IP_ADDR}

whereis php

./delegate/delegated -r -vv -P${PORT} +=./delegate/delegate.conf
