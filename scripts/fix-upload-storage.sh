#!/usr/bin/env bash
# Manşet / Livewire yükleme dizinlerini hazırlar.
# Sunucuda: bash scripts/fix-upload-storage.sh

set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

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

# 0700 livewire-tmp web kullanıcısının yazmasını engeller — 775 şart
chmod -R ug+rwx storage bootstrap/cache || true
chmod 775 storage/app/public/livewire-tmp storage/app/private/livewire-tmp \
  storage/app/public/sliders storage/app/public/sliders/videos || true

# .gitignore placeholder (boş klasör git'e girsin diye gerekmez; tmp için)
touch storage/app/public/livewire-tmp/.gitignore
echo '*' > storage/app/public/livewire-tmp/.gitignore
echo '!.gitignore' >> storage/app/public/livewire-tmp/.gitignore

if [ ! -e public/storage ]; then
  php artisan storage:link || true
fi

php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

echo ""
echo "=== Upload teşhis ==="
echo "APP_URL (kirklareli.bel.tr olmalı):"
grep -E '^APP_URL=' .env || true
echo ""
echo "Dizinler:"
ls -la storage/app/public/livewire-tmp storage/app/public/sliders public/storage 2>&1 | head -20
echo ""
echo "PHP limitleri (en az 8M olmalı):"
php -r 'echo "upload_max_filesize=".ini_get("upload_max_filesize")." post_max_size=".ini_get("post_max_size").PHP_EOL;'
echo ""
echo "Yazma testi:"
TESTFILE="storage/app/public/livewire-tmp/_write_test_$$"
if echo ok > "$TESTFILE" 2>/dev/null; then
  rm -f "$TESTFILE"
  echo "OK: livewire-tmp yazılabilir"
else
  echo "HATA: livewire-tmp yazılamıyor — chmod/chown kontrol edin"
fi
