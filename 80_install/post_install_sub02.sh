#!/bin/bash

set -x

wget https://cirt.net/nikto/nikto-2.1.5.tar.bz2
tar xf nikto-2.1.5.tar.bz2
rm nikto-2.1.5.tar.bz2

pushd nikto-*
perl ./nikto.pl -update
popd
