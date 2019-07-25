#!/bin/bash

source ./.kick_env.sh

SCRIPT_DIR=$(cd $(dirname $0); pwd)

function fn_test () {
  sudo docker-compose run -d weq
  echo "wait for stating database"
  sleep 20
  sudo docker-compose run phpunit
  sudo docker-compose down
}

if [ -z "${1}" ]; then
  CMD="test"
else
  CMD="${1}"
  shift
fi

LEVEL=4
if [ ${CMD} = "phpstan" ]; then
    if [ ! -z "${1}" ]; then
        LEVEL="${1}"
    fi
fi

case ${CMD} in
  composer ) sudo docker run --rm -v $(pwd):/app -w /app composer:${COMPOSER_TAG} ${@} ;;
  test_build ) sudo docker-compose build ;;
  test ) fn_test ;;
  phpstan ) sudo docker run --rm -v $(pwd):/app -w /app phpstan/phpstan:0.11 analyse -l ${LEVEL} /app/src ;;
  * ) echo "cannot kicked [${ARG}]" >&2;;
esac

