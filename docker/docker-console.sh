#!/bin/bash

SCRIPT_ARG=$1;
DOCK_USER=localuser;

cd $(dirname "$0")

set -e errexit
set -o pipefail
set -a
. ".env"
set +a

if [ -z "$SCRIPT_ARG" ]; then
	CONTAINER_ADDENDUM=$COMPOSE_PHP_MODULE
else
	CONTAINER_ADDENDUM=$SCRIPT_ARG

    if [ "$SCRIPT_ARG" = "db" ]; then
        DOCK_USER=root;
    fi
fi

docker exec -u $DOCK_USER -it ${COMPOSE_PROJECT_NAME}_${CONTAINER_ADDENDUM} bash