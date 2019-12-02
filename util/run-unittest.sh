#!/bin/sh

set -e

php vendor/bin/phpunit \
  --coverage-clover build/logs/clover.xml \
  --coverage-html build/coverage/html \
  -c phpunit.xml.dist \
  "$@"
