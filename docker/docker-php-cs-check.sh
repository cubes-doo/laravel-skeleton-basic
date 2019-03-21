#!/bin/bash

cd $(dirname "$0")

set -e errexit
set -o pipefail
set -a
. ".env"
set +a

docker exec -u localuser -it ${COMPOSE_PROJECT_NAME}_${COMPOSE_PHP_MODULE} \
	/opt/vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --config=/opt/.php_cs