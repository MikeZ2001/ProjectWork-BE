#!/usr/bin/env sh
set -e

# Cache config for performance
php artisan config:cache || true

# Retry migrations a few times in case DB isn't ready yet
MAX_RETRIES=10
SLEEP_SECONDS=5
COUNT=0

while [ $COUNT -lt $MAX_RETRIES ]; do
  if php artisan migrate --force && php artisan module:migrate --force --all; then
    echo "Migrations completed"
    break
  else
    COUNT=$((COUNT+1))
    echo "Migration attempt $COUNT/$MAX_RETRIES failed, retrying in ${SLEEP_SECONDS}s..."
    sleep $SLEEP_SECONDS
  fi

done

# Create Passport keys if missing and ensure password client exists
php artisan passport:keys --force || true
php artisan oauth:ensure-password-client --no-interaction || true

exec "$@" 