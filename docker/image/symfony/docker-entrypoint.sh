#!/bin/sh

composer install
php bin/console doctrine:schema:update
php bin/console doctrine:fixtures:load
php bin/console lexik:jwt:generate-keypair --skip-if-exists