#!/bin/sh

# Generate app key on container start, but only if APP_KEY is empty or not set
if [ -z "$APP_KEY" ]; then
  echo "Generating app key..."
  php artisan key:generate --force
else
  echo "APP_KEY already set, skipping key generation."
fi

# Run supervisord (or your main command)
exec /usr/bin/supervisord -n
