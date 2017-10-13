#!/bin/bash

set -x

export TZ=JST-9

echo delegate
./delegate/delegated -f -r -v -P127.0.0.1:50080 +=./delegate/delegate.conf
