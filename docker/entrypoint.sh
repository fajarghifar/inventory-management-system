#!/bin/sh
set -e

cd /var/www/html

# Ensure storage directories exist (needed when using a fresh Docker volume)
mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Create .env from template if it does not exist
if [ ! -f .env ]; then
    cp .env.docker .env
fi

# Ensure APP_KEY is set
if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --no-interaction
fi

# Wait for PostgreSQL container
echo "Waiting for PostgreSQL..."
for i in $(seq 1 30); do
    if php -r "
        \$dbUrl = getenv('DATABASE_URL') ?: getenv('DB_URL');
        if (\$dbUrl) {
            \$parts = parse_url(\$dbUrl);
            \$host = \$parts['host'] ?? 'postgres';
            \$port = \$parts['port'] ?? '5432';
            \$db   = ltrim(\$parts['path'] ?? 'inventory', '/');
            \$user = \$parts['user'] ?? 'inventory';
            \$pass = \$parts['pass'] ?? '';
        } else {
            \$host = getenv('DB_HOST') ?: 'postgres';
            \$port = getenv('DB_PORT') ?: '5432';
            \$db = getenv('DB_DATABASE') ?: 'inventory';
            \$user = getenv('DB_USERNAME') ?: 'inventory';
            \$pass = getenv('DB_PASSWORD') ?: 'secret';
        }
        new PDO(\"pgsql:host=\$host;port=\$port;dbname=\$db\", \$user, \$pass);
    " 2>/dev/null; then
        echo "PostgreSQL is ready."
        break
    fi
    sleep 2
done

# Laravel setup
php artisan package:discover --ansi --no-interaction
php artisan config:clear --no-interaction
php artisan storage:link --force --no-interaction

php artisan migrate --force --no-interaction

USER_COUNT=$(php -r "
    \$dbUrl = getenv('DATABASE_URL') ?: getenv('DB_URL');
    if (\$dbUrl) {
        \$parts = parse_url(\$dbUrl);
        \$host = \$parts['host'] ?? 'postgres';
        \$port = \$parts['port'] ?? '5432';
        \$db   = ltrim(\$parts['path'] ?? 'inventory', '/');
        \$user = \$parts['user'] ?? 'inventory';
        \$pass = \$parts['pass'] ?? '';
    } else {
        \$host = getenv('DB_HOST') ?: 'postgres';
        \$port = getenv('DB_PORT') ?: '5432';
        \$db = getenv('DB_DATABASE') ?: 'inventory';
        \$user = getenv('DB_USERNAME') ?: 'inventory';
        \$pass = getenv('DB_PASSWORD') ?: 'secret';
    }
    \$pdo = new PDO(\"pgsql:host=\$host;port=\$port;dbname=\$db\", \$user, \$pass);
    echo (int) \$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
")

if [ "${USER_COUNT:-0}" -gt 0 ]; then
    echo "Database already seeded."
else
    echo "Running database seeders..."
    php artisan db:seed --force --no-interaction
fi

chown -R www-data:www-data storage bootstrap/cache

echo "Application is ready at ${APP_URL:-http://localhost:8080}"

exec /usr/bin/supervisord -c /etc/supervisord.conf
