#!/bin/bash

set -x

export TZ=JST-9

apachectl -v
php --version

./delegate/delegated -r -v -P${PORT} +=./delegate/delegate.conf
