#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/var/www/undangan"
PHP="php8.3"
ARTISAN="$PHP $APP_DIR/artisan"

echo "==> [1/8] Pulling latest code..."
git -C "$APP_DIR" pull origin main

echo "==> [2/8] Installing Composer dependencies (no-dev)..."
composer install --no-dev --optimize-autoloader --no-interaction --working-dir="$APP_DIR"

echo "==> [3/8] Running database migrations..."
$ARTISAN migrate --force

echo "==> [4/8] Running seeders (idempotent)..."
$ARTISAN db:seed --force

echo "==> [5/8] Clearing and re-caching config/routes/views..."
$ARTISAN optimize:clear
$ARTISAN config:cache
$ARTISAN route:cache
$ARTISAN view:cache
$ARTISAN event:cache

echo "==> [6/8] Building frontend assets..."
npm --prefix "$APP_DIR" ci --silent
npm --prefix "$APP_DIR" run build

echo "==> [7/8] Restarting queue workers..."
$ARTISAN queue:restart

echo "==> [8/8] Verifying health check..."
sleep 2
STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/health)
if [ "$STATUS" != "200" ]; then
  echo "ERROR: Health check returned HTTP $STATUS — deployment may have issues!"
  exit 1
fi

echo ""
echo "✅ Deploy complete — health check OK (HTTP 200)"
