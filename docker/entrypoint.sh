#!/usr/bin/env sh
set -e

# Cache config for performance
php artisan config:cache || true

# Retry migrations a few times in case DB isn't ready yet
MAX_RETRIES=10
SLEEP_SECONDS=5
COUNT=0

while [ $COUNT -lt $MAX_RETRIES ]; do
  if php artisan migrate --force && php artisan module:migrate --force; then
    echo "Migrations completed"
    break
  else
    COUNT=$((COUNT+1))
    echo "Migration attempt $COUNT/$MAX_RETRIES failed, retrying in ${SLEEP_SECONDS}s..."
    sleep $SLEEP_SECONDS
  fi

done

# If still failing after retries, continue to start app (optional: exit 1 to fail startup)
# echo "Migrations failed after ${MAX_RETRIES} attempts" >&2
# exit 1

exec "$@" 