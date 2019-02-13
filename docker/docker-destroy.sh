#!/bin/bash

cd $(dirname "$0")

set -e errexit
set -o pipefail
set -a
. ".env"
set +a

read -p "Are you sure you want do destroy docker containers for project \"$COMPOSE_PROJECT_NAME\"? (y/n): " answer
case ${answer:0:1} in
    y|Y )
        echo "Start destroying docker containers"
    ;;
    * )
        exit 0;
    ;;
esac

docker-compose stop
docker-compose rm
