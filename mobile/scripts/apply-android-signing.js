#!/usr/bin/env node
/**
 * android-signing.properties içeriğini android/gradle.properties dosyasına ekler.
 */
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const signingFile = path.join(root, 'android-signing.properties');
const gradleFile = path.join(root, 'android', 'gradle.properties');

if (!fs.existsSync(signingFile)) {
  console.error(
    'Hata: android-signing.properties bulunamadı.\n' +
      '  cp android-signing.properties.example android-signing.properties\n' +
      'komutuyla oluşturup keystore bilgilerinizi girin.',
  );
  process.exit(1);
}

if (!fs.existsSync(gradleFile)) {
  console.error('Hata: android/gradle.properties yok. Önce: npm run prebuild:android:prod');
  process.exit(1);
}

const signingBlock = fs.readFileSync(signingFile, 'utf8').trim();
const gradleContents = fs.readFileSync(gradleFile, 'utf8');

if (gradleContents.includes('KIRKLARELI_UPLOAD_STORE_FILE')) {
  console.log('İmzalama ayarları zaten android/gradle.properties içinde.');
  process.exit(0);
}

fs.appendFileSync(
  gradleFile,
  `\n# --- Kırklareli release imzalama (apply-android-signing.js) ---\n${signingBlock}\n`,
);
console.log('İmzalama ayarları android/gradle.properties dosyasına eklendi.');
