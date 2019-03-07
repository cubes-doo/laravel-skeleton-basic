#!/bin/bash

CONSOLE_CONTAINER="phpfpm"

cd $(dirname "$0")

set -e errexit
set -o pipefail
set -a
. ".env"
set +a

docker exec -u localuser ${COMPOSE_PROJECT_NAME}_${CONSOLE_CONTAINER} vendor/bin/phpunit tests --testdox