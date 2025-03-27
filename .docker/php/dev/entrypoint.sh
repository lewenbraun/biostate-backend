#!/bin/sh
set -e

echo "Setting permissions for the storage directory..."

# Change ownership and permissions for the storage directory
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage

# Execute the command passed as arguments (CMD)
exec "$@"
