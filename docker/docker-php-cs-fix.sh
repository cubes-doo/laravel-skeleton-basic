#!/bin/bash

cd $(dirname "$0")

set -e errexit
set -o pipefail
set -a
. ".env"
set +a

docker exec -u localuser -it ${COMPOSE_PROJECT_NAME}_phpfpm \
	/opt/code/vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --config=/opt/.php_cs