#!/bin/bash

if [ -z "$1" ]; then
	CONSOLE_CONTAINER="phpfpm"
else
	CONSOLE_CONTAINER=$1
fi


cd $(dirname "$0")

set -e errexit
set -o pipefail
set -a
. ".env"
set +a

docker exec -u localuser -it ${COMPOSE_PROJECT_NAME}_${CONSOLE_CONTAINER} bash