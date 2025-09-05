#!/usr/bin/env sh
set -e

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

# Only create Passport keys if they don't exist (don't regenerate existing keys)
if [ ! -f "storage/oauth-private.key" ] || [ ! -f "storage/oauth-public.key" ]; then
    echo "Creating Passport keys..."
    php artisan passport:keys --force
else
    echo "Passport keys already exist, skipping generation"
fi

# Ensure password client exists
php artisan oauth:ensure-password-client --no-interaction || true

# Cache config AFTER all environment variables are set
php artisan config:cache || true

exec "$@"
