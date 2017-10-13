#!/bin/bash

set -x

export TZ=JST-9

echo delegate
./delegate/delegated -f -r -vvv -P50080 +=./delegate/delegate.conf
