# Kırklareli Belediyesi — Mobil Uygulama Özellik Dökümanı

> **Uygulama:** `Kırklareli Belediyesi` · **Platform:** React Native / Expo 54 · **Versiyon:** 1.0.0  
> **Paket Tanımlayıcı:** `com.corporateportal.app`  
> **Mimari Konsept:** "The Architectural Calm" (bkz. [DESIGN.md](DESIGN.md))

---

## İçindekiler

1. [Proje Yapısı](#1-proje-yapısı)
2. [Tasarım Sistemi](#2-tasarım-sistemi)
3. [Ekranlar ve Özellikler](#3-ekranlar-ve-özellikler)
4. [Bileşenler](#4-bileşenler)
5. [Servisler ve API](#5-servisler-ve-api)
6. [Navigasyon](#6-navigasyon)
7. [Teknik Notlar](#7-teknik-notlar)

---

## 1. Proje Yapısı

```
mbb/
├── app/                        # Expo Router dosya-tabanlı rotalar
│   ├── _layout.tsx             # Kök layout (SafeArea, font yükleme)
│   ├── (tabs)/                 # Tab grubu rotaları
│   │   ├── index.tsx           # Ana Sayfa → DashboardScreen
│   │   ├── payment.tsx         # E-Belediye yönlendirme
│   │   ├── card.tsx
│   │   ├── directory.tsx
│   │   ├── haberler.tsx        # → HaberlerScreen
│   │   ├── meclis-kararlari.tsx
│   │   ├── mudur.tsx
│   │   └── saatler.tsx
│   ├── haberler/
│   │   ├── _layout.tsx
│   │   ├── index.tsx           # → HaberlerScreen
│   │   └── [id].tsx            # Duyuru Detay Ekranı
│   ├── investments/
│   ├── mudur/
│   ├── requests/
│   └── saatler/
│
├── src/
│   ├── screens/                # Ekran bileşenleri
│   ├── components/             # Paylaşılan UI bileşenleri
│   ├── services/               # API ve veri servisleri
│   ├── theme/                  # Renk, tipografi, spacing token'ları
│   └── mock/                   # Fallback/demo verileri
│
├── assets/                     # Görseller (logo.png, baskan.jpg, splash.png)
├── app.json                    # Expo konfigürasyonu
├── DESIGN.md                   # Tasarım sistemi kuralları
└── FEATURES.md                 # Bu dosya
```

---

## 2. Tasarım Sistemi

### Renk Paleti (`src/theme/colors.ts`)

| Token | Hex | Kullanım |
|---|---|---|
| `primary` | `#00668a` | Butonlar, badge, aksent |
| `primaryContainer` | `#6cb8e2` | Yumuşak arka plan, ikonlar |
| `background` / `surface` | `#f7f9fb` | Ekran tabanı |
| `surfaceContainerLowest` | `#ffffff` | Etkileşimli kartlar |
| `surfaceContainerLow` | `#f2f4f6` | Yükseltilmiş bölümler |
| `onSurface` | `#191c1e` | Ana metin (saf siyah yok) |
| `onSurfaceVariant` | `#3f484e` | İkincil metin |
| `tertiary` | `#875205` | "Altın an" aksent rengi |
| `error` | `#ba1a1a` | Hata durumları |

### Tipografi (`src/theme/typography.ts`)

- **Manrope** — Başlıklar, headline'lar (geometrik otorite)
- **Inter** — Gövde metni, etiketler (okunabilirlik)
- Tight letter-spacing (`-0.02em`) büyük başlıklarda kullanılır

### Spacing & Radius (`src/theme/index.ts`)

- Spacing: `4, 8, 12, 16, 20, 24, 28, 32, 40, 48, 56, 64` px
- Radius: `xs(2)` → `full(9999)`, ana kart = `xl(12)` veya `2xl(16)`

### Tasarım Kuralları

- **No-Line Kuralı:** 1px solid border kullanılmaz; bölünme renk farkıyla yapılır
- **Ghost Border:** Gerektiğinde `outlineVariant` %15 opacity
- **Glassmorphism:** `BlurView` (iOS) / yarı saydam arka plan (Android)
- **Ambient Shadow:** `Y:8, Blur:24, opacity:4%` — doğal ortam ışığı
- **"The Breath":** Geniş whitespace kullanımı, kalabalık ekranlardan kaçınılır

---

## 3. Ekranlar ve Özellikler

### 3.1 Ana Sayfa — Dashboard (`src/screens/DashboardScreen.tsx`)

**Rota:** `/` (index)

**Özellikler:**
- **HeroBanner:** API'den çekilen duyurular carousel (sayfalandırmalı, otomatik geçiş)
- **QuickAccessGrid:** 2 sütun bento grid; 6 hızlı erişim kartı
- **AnnouncementFooter:** Resmi duyurular etiketi + 153 telefon butonu + sosyal medya ikonları
- **Pull-to-Refresh:** Tüm dashboard verilerini paralel yeniler
- **TopAppBar:** Glassmorphism floating header, bildirim rozeti
- **BottomNavBar:** Glassmorphism tab bar (4 sekme)

**Hızlı Erişim Öğeleri:**
| Etiket | İkon | Rota |
|---|---|---|
| Başkan | badge | `/mudur` |
| Haberler | newspaper | `/haberler` |
| Talep | pending_actions | `/requests/new` |
| Saatler | schedule | `/saatler` |
| Rehber | contact_page | `/directory` |
| Meclis Kararları | gavel | `/meclis-kararlari` |

**Bottom Nav Özel Davranışlar:**
- `Ödeme` butonu → `Linking.openURL('https://e-belediye.kirklareli.bel.tr')` (harici tarayıcı)
- Diğer sekmeler → `router.push(route)`

---

### 3.2 Duyurular / Haberler (`src/screens/HaberlerScreen.tsx`)

**Rota:** `/haberler`  
**API:** `GET /api/announcements?page=1&per_page=500`

**Özellikler:**
- **İkon Tabanlı Kart Tasarımı:** Görsel yerine 48×48 dairesel ikon, `type_label` rozeti, tarih, başlık, özet
- **Infinite Scroll:** `onEndReached` → sonraki sayfayı yükler, dedup koruması (ID seti)
- **Pull-to-Refresh:** İlk sayfadan yeniden çeker
- **Filtreleme:** Yalnızca `type === 'duyuru'` kayıtları gösterilir (resmi ilan, ihale vb. hariç)
- **Animasyon:** Kart görünümü `fade-in` (280ms)
- **Scroll-Reactive Header:** Kaydırınca %97 opak arka plan + alt çizgi belirir
- **Boş / Hata Durumu:** Şık `EmptyState` bileşeni

**Tür İkonu Eşleştirme:**
```
ihale       → 🏛️
resm / ilan → 📜
meclis      → ⚖️
imar / ruhsat → 🏗️
zabıta      → 🛡️
çevre       → 🌿
diğer       → 📢
```

---

### 3.3 Duyuru Detay (`app/haberler/[id].tsx`)

**Rota:** `/haberler/[id]`

**Özellikler:**
- **Anında Gösterim:** Liste ekranından gelen `params` ile sıfır bekleme süresi (API çağrısı yapılmaz)
- **Derin Link Desteği:** Params yoksa API'den çeker (timeout: 8 sn)
- **Hero Görsel:** Tam genişlik + `LinearGradient` overlay
- **İçerik:** Kategori rozeti, Türkçe tarih formatı, başlık, tam metin
- **HTML Temizleme:** `stripHtml()` ile `<br>`, `<p>`, `<li>` → düz metin
- **Görsel Hata Fallback:** Yüklenemezse placeholder görseline düşer
- **Geri Butonu:** Yarı saydam dairesel buton (`rgba(0,0,0,0.35)`)

**Desteklenen API Formatları:**
- `{ data: { id, title, ... } }` — tekil nesne
- `{ data: [...] }` — dizi → ID ile eşleştirilir
- `{ data: { data: [...], meta } }` — Laravel paginate

**Tam Metin Alan Önceliği:**  
`description → content_html → content → body → full_text → text → detail → excerpt → summary`

---

### 3.4 Belediye Başkanı (`src/screens/MudurScreen.tsx`)

**Rota:** `/mudur`  
**Veri:** Tamamen statik (fetch yok)

**Özellikler:**
- **Hero Portrait:** Edge-to-edge fotoğraf, ekran yüksekliğinin %48'i, parallax zoom efekti
- **Scroll-Reactive Header:** Saydam → opak geçiş; geri ikon rengi beyaz → primary
- **Kimlik Kartı:** İsim + unvan, sol aksent şeridi, yüksek gölgeli kart
- **Biyografi Cam Kartı:** `BlurView` glassmorphism (iOS) / yarı saydam (Android)
- **Başkanın Mesajı:** Degrade arka plan + tırnak sembolü vizyonu

**İçerik:**
- Ad: Derya Bulut
- Unvan: Kırklareli Belediye Başkanı
- Biyografi: Doğum, eğitim, MHP kariyer, 2024 seçim bilgileri
- Mesaj: Şehir vizyonu alıntısı

---

### 3.5 Meclis Kararları (`src/screens/MeclisKararlariScreen.tsx`)

**Rota:** `/meclis-kararlari`  
**API:** `GET /api/council/decisions?page=N&per_page=15`

**Özellikler:**
- **Glassmorphism Kartlar:** `BlurView` (iOS) / yarı saydam (Android)
- **Yıl Rozeti:** Her kararda yıl badge'i
- **3'lü Belge Butonu:** Meclis Gündemi (📄) · Meclis Kararları (🔨) · Komisyon Raporları (👥)
- **Disabled Buton:** URL yoksa buton silik/pasif
- **Belge Açma:** `Linking.openURL()` ile PDF/harici link
- **Infinite Scroll:** Sayfalandırma desteği, ID bazlı dedup
- **Pull-to-Refresh**
- **Boş / Hata Durumu**

---

### 3.6 Sefer Saatleri (`src/screens/SaatlerScreen.tsx`)

**Rota:** `/saatler`  
**Veri:** Statik mock (API bağlantısı planlanıyor)

**Özellikler:**
- **Sonraki Sefer Kartı:** Glassmorphism kart, büyük saat gösterimi, geri sayım
- **Geri Sayım Animasyonu:** ≤15 dakika kaldıysa pulse (scale) animasyonu
- **Hafta İçi / Hafta Sonu Toggle:** Segmented control
- **Güzergah Seçici:** Yatay scroll chip listesi (renk kodlu)
- **Zaman Çizelgesi:** Geçmiş (soluk) / Aktif / Sonraki (vurgulu) departure satırları
- **Otomatik Yenileme:** Her dakikada bir re-render (geri sayım güncellenir)
- **Hat Badge'leri:** Her sefer satırında hat (A/B) rozeti
- **Not Etiketleri:** "Ekspres", "Yoğun Saat", "Öğle" vb.

**Güzergahlar:**
| ID | Etiket | Renk |
|---|---|---|
| sehir | Şehir İçi | `#00668a` |
| servis1 | Servis 1 | `#476272` |
| servis2 | Servis 2 | `#875205` |

---

### 3.7 Talep Formu (`src/screens/RequestFormScreen.tsx`)

**Rota:** `/requests/new`

**Özellikler:**
- **Form Alanları:** Ad Soyad (zorunlu), Telefon (zorunlu, numerik), Açıklama (zorunlu, çok satırlı)
- **Fotoğraf Ekleme:** `expo-image-picker` ile opsiyonel görsel seçimi
- **Validasyon:** Ad min 3 karakter, telefon format kontrolü
- **Hata Gösterimi:** Alan bazlı hata mesajları
- **Gönderim:** `Alert` + `router.back()` (backend entegrasyonu için hazır)
- **KeyboardAvoidingView:** Klavye açıkken form yukarı kayar

---

### 3.8 Ödeme (`app/(tabs)/payment.tsx`)

**Rota:** `/payment`

**Özellikler:**
- **Otomatik Yönlendirme:** Ekran açılınca `Linking.openURL('https://e-belediye.kirklareli.bel.tr')`
- **Manuel Buton:** Yönlendirme çalışmazsa kullanıcı el ile açabilir
- Bottom nav'dan basılınca doğrudan harici tarayıcı açılır (tab ekrana hiç gidilmez)

---

## 4. Bileşenler

### `TopAppBar` (`src/components/TopAppBar.tsx`)
- Glassmorphism floating header
- Belediye logosu + uygulama adı
- Bildirim ikonu (rozet sayısı)

### `BottomNavBar` (`src/components/BottomNavBar.tsx`)
- 4 sekme: Services, Ödeme, Kart, Directory
- `BlurView` glassmorphism arka plan
- Aktif sekme renk geçişi

### `HeroBanner` (`src/components/HeroBanner.tsx`)
- Otomatik geçişli görsel carousel
- Loading skeleton
- `buildFallbackImageUrl` ile çift URL denemesi

### `QuickAccessGrid` / `QuickAccessCard` (`src/components/`)
- 2 sütun bento grid
- Materyal ikon + etiket
- Badge desteği (bildirim sayısı)

### `AnnouncementFooter` (`src/components/AnnouncementFooter.tsx`)
- 3 kart yan yana:
  1. "Resmi duyurular" etiket kartı
  2. **153 Alo Belediye** — `Linking.openURL('tel:153')`
  3. Sosyal medya ikonları (Instagram)

### `FormField` (`src/components/FormField.tsx`)
- `surface-container-low` zemin, border yok
- Odaklanınca `surface-container-lowest` + primary ghost border
- Hata mesajı desteği

### `PhotoPicker` (`src/components/PhotoPicker.tsx`)
- `expo-image-picker` ile galeri erişimi
- Seçilen görsel önizleme
- Kaldır butonu

### `GalleryLightbox` / `GlassProgress` / `InvestmentCard`
- Yatırımlar modülü bileşenleri (lansman sonrası aktifleşecek)

---

## 5. Servisler ve API

### Dashboard Service (`src/services/dashboardService.ts`)

**API Base:** `http://yeni.kirklarelidev.com.tr/public/api`  
**Zaman Aşımı:** 8 saniye  
**Fallback Stratejisi:** Hata → mock veriye düş, uygulama asla çökmez

| Fonksiyon | Kaynak | Açıklama |
|---|---|---|
| `fetchAnnouncements()` | API → Mock | Son 3 duyuruyu çeker (HeroBanner için) |
| `fetchQuickAccessItems()` | Mock | 6 hızlı erişim öğesi |
| `fetchDashboardStats()` | Mock | `announcementCount: 155` |
| `fetchSocialLinks()` | Mock | Yalnızca Instagram |

### News Service (`src/services/newsService.ts`)

**API Base:** `https://yeni.kirklarelidev.com.tr/public/api`  
**Endpoint:** `/announcements?page=N&per_page=500`  
**Zaman Aşımı:** 12 saniye  
**Headers:** `Cache-Control: no-cache`

| Fonksiyon | Açıklama |
|---|---|
| `fetchNews(page)` | Duyuruları çeker, `type === 'duyuru'` filtresi, `NewsPaginatedResult` döner |
| `buildNewsFallbackUrl(url)` | `/public/storage/` → `/storage/` alternatif URL |
| `formatDateTR(raw)` | ISO tarih → "15 Haziran 2025" (Hermes uyumlu — `new Date()` kullanılmaz) |
| `resolveImageUrl(path)` | `http://` → `https://`, göreli yol → tam URL |

**`NewsItem` Alanları:**
```typescript
id, title, excerpt, imageUrl, publishedAt,
formattedDate, isHeadline, categoryLabel, announcementType
```

### API Zarf Formatları (Desteklenen)

```
[...]                           → Düz dizi
{ data: [...] }                 → Standart Laravel
{ data: { data: [...], meta } } → Laravel paginate
```

---

## 6. Navigasyon

### Expo Router (Dosya Tabanlı)

```
/                     → DashboardScreen
/haberler             → HaberlerScreen (duyuru listesi)
/haberler/[id]        → HaberDetayScreen (duyuru detay)
/mudur                → MudurScreen (belediye başkanı)
/meclis-kararlari     → MeclisKararlariScreen
/saatler              → SaatlerScreen (sefer saatleri)
/requests/new         → RequestFormScreen (talep formu)
/payment              → Harici: e-belediye.kirklareli.bel.tr
/investments          → InvestmentsScreen (lansman sonrası)
```

### Liste → Detay Geçişi (Duyurular)

```typescript
router.push({
  pathname: '/haberler/[id]' as never,
  params: {
    id, title, imageUrl, excerpt, formattedDate, categoryLabel
  },
});
```

Detay ekranı params'tan anında içeriği gösterir; derin link senaryosunda API çağrısı yapar.

---

## 7. Teknik Notlar

### Hermes JS Engine Uyumu

`new Date("2025-06-15T00:00:00+00:00")` → Hermes'te **çöküyor**.  
Tüm tarih parse işlemleri string split ile yapılır:

```typescript
const [year, month, day] = raw.split('T')[0].split('-').map(Number);
return `${day} ${TR_MONTHS[month - 1]} ${year}`;
```

### HTTP / SSL

- **iOS:** `NSAllowsArbitraryLoads: true` (`app.json` infoPlist)
- **Android:** `usesCleartextTraffic: true` (`app.json`)
- Tüm görsel URL'lerinde `http://` → `https://` normalizasyonu uygulanır

### Görsel Boyutlandırma

`width: '100%'` FlatList içinde çalışmaz. Tüm görsel container'lar piksel sabit değer kullanır:
```typescript
const { width: SW } = Dimensions.get('window');
const CARD_W = SW - 40; // paddingHorizontal: 20 × 2
```

### Sayfalandırma Mimarisi

Duyurular per_page=500 ile tek seferde çekilir, client-side filtre uygulanır.  
Meclis Kararları per_page=15 ile server-side sayfalandırma kullanır.

### Bağımlılıklar

| Paket | Versiyon | Kullanım |
|---|---|---|
| expo | ~54.0.33 | Platform |
| expo-router | ~6.0.23 | Dosya-tabanlı navigasyon |
| expo-blur | ~15.0.8 | Glassmorphism BlurView |
| expo-linear-gradient | ~15.0.8 | Hero gradientlar |
| expo-image-picker | ~17.0.10 | Talep fotoğrafı |
| react-native-reanimated | ~4.1.1 | Animasyonlar |
| react-native-safe-area-context | ~5.6.0 | Safe area insets |
| @expo-google-fonts/manrope | ^0.2.3 | Başlık fontu |
| @expo-google-fonts/inter | ^0.2.3 | Gövde fontu |

---

*Son güncelleme: Haziran 2026*
