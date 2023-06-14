#!/bin/bash

php bin/console cache:clear --env=dev
php bin/console doctrine:database:drop --force --env=dev
php bin/console doctrine:database:create --env=dev
php bin/console doctrine:schema:update --force --complete --env=dev
# php bin/console doctrine:migrations:migrate --no-interaction --env=dev
php bin/console hautelook:fixtures:load --no-interaction --env=dev
