# Kırklareli Belediyesi — Mobil Uygulama

React Native / Expo uygulaması. Özellikler ve tasarım kuralları için `FEATURES.md` dosyasına bakın.

## Çalıştırma

```bash
cd mobile
npm install --legacy-peer-deps
npx expo start --localhost --clear
```

Simülatörde açmak için terminalde `i` (iOS) veya `a` (Android) tuşuna basın.

## Mimari

- **Expo Router** — dosya tabanlı navigasyon (`app/`)
- **Tasarım sistemi** — `src/theme/` (Manrope + Inter, #00668a paleti)
- **Ekranlar** — `src/screens/`
- **Bileşenler** — `src/components/`
- **API servisleri** — `src/services/` (`kirklareli.test` + fallback)

## Ekranlar

| Rota | Ekran |
|------|-------|
| `/` | Ana Sayfa (Dashboard) |
| `/haberler` | Duyuru listesi |
| `/haberler/[id]` | Duyuru detay |
| `/mudur` | Belediye Başkanı |
| `/meclis-kararlari` | Meclis Kararları |
| `/saatler` | Sefer Saatleri |
| `/requests/new` | Talep Formu |
| `/directory` | Rehber |
| `/infrastructure` | Altyapı Çalışmaları |

Bottom nav: Hizmetler · Ödeme · Kart · Altyapı · Rehber

## TestFlight (EAS olmadan)

Expo managed projede `ios/` klasörü yoktur; Xcode için önce native proje üretilir:

```bash
cd mobile
npm run prebuild:ios    # ios/ klasörünü oluşturur (ilk sefer veya plugin değişince)
npm run open:xcode      # Xcode workspace açar
```

Xcode adımları:
1. **Signing & Capabilities** → Apple Developer Team seçin
2. **+ Capability** → Push Notifications ekleyin
3. Scheme: **KrklareliBelediyesi**, hedef: **Any iOS Device (arm64)**
4. **Product → Archive**
5. **Distribute App → App Store Connect → Upload**
6. [App Store Connect](https://appstoreconnect.apple.com) → TestFlight

Not: `ios/` klasörü `.gitignore` içindedir; her geliştirici makinesinde `prebuild:ios` ile üretilir.

Plugin veya `app.config.ts` iOS/Android ayarları değişince prebuild'i tekrar çalıştırın.

## Google Play Store (EAS olmadan)

Expo managed projede `android/` klasörü repoda yoktur; Gradle build için önce native proje üretilir.

### 1. Keystore (bir kez)

```bash
cd mobile
keytool -genkeypair -v -storetype PKCS12 \
  -keystore kirklareli-release.keystore \
  -alias kirklareli \
  -keyalg RSA -keysize 2048 -validity 10000
```

Keystore dosyasını ve şifreleri güvenli yedekleyin.

### 2. İmzalama ayarları

```bash
cp android-signing.properties.example android-signing.properties
# android-signing.properties içindeki şifreleri doldurun
```

### 3. Production AAB üretimi

```bash
cd mobile
npm install --legacy-peer-deps
npm run build:android:aab
```

Çıktı: `android/app/build/outputs/bundle/release/app-release.aab`

Bu komut sırasıyla şunları yapar:
- `APP_ENV=production` ile prebuild (yalnızca HTTPS API, cleartext kapalı)
- İmzalama bilgilerini `android/gradle.properties` içine ekler
- Release App Bundle derler

Yerel test APK için: `npm run build:android:apk`

### 4. Play Console

1. [Google Play Console](https://play.google.com/console) → uygulama oluştur
2. **Test → Internal testing** → `app-release.aab` yükle
3. Mağaza listesi, gizlilik politikası URL’si, içerik derecelendirmesi, veri güvenliği formu
4. Test sonrası **Production** kanalına geç

### Sürüm güncelleme

Her yeni yüklemede `app.config.ts` içinde:
- `version` (ör. `1.0.1`)
- `android.versionCode` (ör. `2`, her seferinde +1)

Sonra `npm run build:android:aab` tekrar çalıştırın.

### Ortam değişkenleri

| Değişken | Açıklama |
|----------|----------|
| `APP_ENV=development` | Lokal API (`kirklareli.test`) + HTTP izinli (varsayılan) |
| `APP_ENV=production` | Yalnızca canlı HTTPS API; Play Store build için |

Örnek: `cp .env.example .env` (isteğe bağlı; native build'de shell'den `APP_ENV` yeterli)

### Android push (FCM)

Push bildirimleri için Firebase projesi + `google-services.json` gerekir. `expo prebuild` sonrası Firebase Console'dan Android uygulaması ekleyip dosyayı `android/app/` altına koyun. Ayrıntı: [Expo FCM credentials](https://docs.expo.dev/push-notifications/fcm-credentials/).
