#!/bin/bash

cd $(dirname "$0")

set -e errexit
set -o pipefail
set -a
. ".env"
set +a

docker exec -u localuser ${COMPOSE_PROJECT_NAME}_${COMPOSE_PHP_MODULE} \
    vendor/bin/phpunit tests --testdox