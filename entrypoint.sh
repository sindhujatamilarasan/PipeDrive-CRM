#!/bin/bash

# Generate Laravel APP_KEY if not set
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# Start Supervisor
exec /usr/bin/supervisord -n
