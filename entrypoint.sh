#!/bin/sh
set -e

if [ ! -f /app/config/jwt/private.pem ]; then
    php /app/bin/console lexik:jwt:generate-keypair
fi

exec "$@"
