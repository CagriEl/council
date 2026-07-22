#!/usr/bin/env bash
# Manşet upload 500 için sunucu düzeltmesi
# Kullanım: cd /home/kirklareli.bel.tr/public_html && bash scripts/fix-upload-storage.sh

set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "==> Dizinler"
mkdir -p \
  storage/app/public/livewire-tmp \
  storage/app/private/livewire-tmp \
  storage/app/public/sliders \
  storage/app/public/sliders/videos \
  storage/framework/cache \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

echo "==> İzinler (777 — shared hosting yazma sorununu çözer)"
chmod -R 777 storage bootstrap/cache || true

echo "==> storage link"
if [ ! -e public/storage ]; then
  php artisan storage:link || ln -sf ../storage/app/public public/storage || true
fi

echo "==> Cache temizle (ESKI CONFIG KALIRSA UPLOAD 500 DEVAM EDER)"
php artisan optimize:clear || {
  php artisan config:clear || true
  php artisan cache:clear || true
  php artisan view:clear || true
  php artisan route:clear || true
}

echo ""
echo "=== APP_URL (https://kirklareli.bel.tr OLMALI) ==="
grep -E '^APP_URL=' .env || true

echo ""
echo "=== Yazma testi ==="
TEST="storage/app/public/livewire-tmp/_t_$$"
if echo ok > "$TEST" 2>/dev/null; then
  rm -f "$TEST"
  echo "OK: livewire-tmp yazılabiliyor"
else
  echo "HATA: yazılamıyor — hosting destekine storage yazma izni sorun"
fi

echo ""
echo "=== PHP limit ==="
php -r 'echo "upload_max=".ini_get("upload_max_filesize")." post_max=".ini_get("post_max_size").PHP_EOL;'

php artisan uploads:diagnose 2>/dev/null || true
echo ""
echo "Bitti. Admin > Manşet > küçük JPG dene."
