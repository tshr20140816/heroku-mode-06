#!/bin/bash

./heroku drains:add https://logs-01.loggly.com/bulk/${LOGGLY_TOKEN}/tag/heroku -a ${APP_NAME}
