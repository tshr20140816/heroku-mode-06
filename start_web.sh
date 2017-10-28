#!/bin/bash

set -x

export TZ=JST-9

./delegate/delegated -r -v -P${PORT} +=./delegate/delegate.conf
