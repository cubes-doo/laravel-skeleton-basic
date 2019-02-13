#!/bin/bash

cd $(dirname "$0")

set -e errexit
set -o pipefail
set -a
. ".env"
set +a

read -p "Are you sure you want do rebuild docker containers for project \"$COMPOSE_PROJECT_NAME\"? (y/n): " answer
case ${answer:0:1} in
    y|Y )
        echo "Start rebuilding docker containers"
    ;;
    * )
        exit 0;
    ;;
esac

docker-compose stop
docker-compose rm
docker-compose up -d --force-recreate
