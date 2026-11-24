#!/bin/sh
set -eu
php artisan config:cache > /dev/null
php artisan route:cache > /dev/null
exec frankenphp run --config /etc/caddy/Caddyfile
