#!/usr/bin/env bash
# Pull latest code + sync production (Aiven) DB into local DB.
# Requires: .env.production (git-ignored) with REMOTE_DB_* vars, and local .env with DB_* vars.
set -e

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

if [ ! -f .env.production ]; then
  echo "Missing .env.production. Copy .env.production.example and fill in Railway/Aiven credentials."
  exit 1
fi

set -a
source .env.production
source .env
set +a

echo "==> git pull origin master"
git pull origin master

echo "==> Dumping remote DB ($REMOTE_DB_DATABASE)"
mysqldump -h "$REMOTE_DB_HOST" -P "$REMOTE_DB_PORT" -u "$REMOTE_DB_USERNAME" -p"$REMOTE_DB_PASSWORD" \
  --ssl-mode=REQUIRED --no-tablespaces "$REMOTE_DB_DATABASE" > storage/app/production_dump.sql

echo "==> Importing into local DB ($DB_DATABASE)"
mysql -h "${DB_HOST:-127.0.0.1}" -P "${DB_PORT:-3306}" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < storage/app/production_dump.sql

rm -f storage/app/production_dump.sql

echo "==> Clearing cache"
php artisan cache:clear

echo "Done. Code + DB are in sync with production."
