#!/bin/bash
set -e

echo "==> Starting Cosmo Doctors CRM..."

# ── Wait for MySQL to be ready ────────────────────────────────────────────────
echo "==> Waiting for database connection..."
until php -r "
    try {
        new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        echo 'ok';
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null | grep -q 'ok'; do
    echo "   Database not ready, retrying in 3s..."
    sleep 3
done
echo "   Database connected."

# ── Generate APP_KEY if missing ───────────────────────────────────────────────
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:CHANGE_ME" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# ── Run migrations ────────────────────────────────────────────────────────────
echo "==> Running migrations..."
php artisan migrate --force

# ── Seed triage categories + permissions (idempotent) ────────────────────────
echo "==> Seeding triage data..."
php artisan db:seed --class="Modules\Triage\database\seeders\TriageDatabaseSeeder" --force 2>/dev/null || true
php artisan db:seed --class="Modules\Triage\database\seeders\TriagePermissionSeeder" --force 2>/dev/null || true

# ── Enable Triage module ──────────────────────────────────────────────────────
php artisan module:enable Triage 2>/dev/null || true

# ── Cache config / routes / views ────────────────────────────────────────────
echo "==> Caching..."
php artisan config:cache
# php artisan route:cache
# php artisan view:cache
# php artisan event:cache

# ── Storage symlink ───────────────────────────────────────────────────────────
# php artisan storage:link 2>/dev/null || true
# ── Storage: remove symlink, use real directory instead ───────────────────────
# rm -rf /var/www/html/public/storage
# mkdir -p /var/www/html/public/storage
# cp -r /var/www/html/storage/app/public/. /var/www/html/public/storage/ 2>/dev/null || true
# chown -R www-data:www-data /var/www/html/public/storage

rm -rf /var/www/html/public/storage
php artisan storage:link
chown -h www-data:www-data /var/www/html/public/storage || true
chown -R www-data:www-data /var/www/html/storage/app/public


# ── Fix permissions after cache ──────────────────────────────────────────────
chown -R www-data:www-data /var/www/html/storage \
                            /var/www/html/bootstrap/cache

echo "==> Starting services via Supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
