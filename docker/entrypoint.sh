#!/bin/sh
set -e

cd /var/www/html

if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force --no-interaction
fi

export DB_HOST=mysql
export DB_PORT=3306

php artisan config:clear --no-interaction 2>/dev/null || true

echo "Aguardando MySQL..."
until php -r "
  \$host = getenv('DB_HOST') ?: '127.0.0.1';
  \$port = getenv('DB_PORT') ?: '3306';
  \$db   = getenv('DB_DATABASE');
  \$user = getenv('DB_USERNAME');
  \$pass = getenv('DB_PASSWORD');
  try {
    new PDO(\"mysql:host={\$host};port={\$port};dbname={\$db}\", \$user, \$pass);
    exit(0);
  } catch (Throwable \$e) {
    exit(1);
  }
"; do
  sleep 2
done

php artisan migrate --force --no-interaction

exec "$@"
