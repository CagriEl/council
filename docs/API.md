# API dokümantasyonu

## OpenAPI dosyası

Tüm uç noktalar, parametreler ve örnek yanıt şemaları şu dosyada tanımlıdır:

**[`openapi.yaml`](openapi.yaml)** (OpenAPI 3.0)

## Tarayıcıda (yerel / sunucu)

Uygulama çalışırken (ör. Valet ile `http://kirklareli.test`):

- **Swagger arayüzü:** `http://kirklareli.test/api/docs`
- **Ham OpenAPI dosyası:** `http://kirklareli.test/api/docs/openapi.yaml`

`docs/openapi.yaml` içinde `servers.url` varsayılan olarak **`http://kirklareli.test/api`** (şema **http**) tanımlıdır; Swagger’da “Try it out” / Computed URL buna göre üretilir. Üretimde YAML’da bu adresi güncelleyin.

## Nasıl görüntülenir?

1. **Yerel:** Yukarıdaki `/api/docs` adresi (proje içi Swagger UI).
2. **Swagger Editor (çevrimiçi)**  
   [editor.swagger.io](https://editor.swagger.io) → `File` → `Import file` → `docs/openapi.yaml` seçin.  
   Yerelde `servers` zaten `http://kirklareli.test/api` olabilir; dışarıdan import ediyorsanız `servers.url` satırını kendi API kökünüze göre düzenleyin.

3. **Postman**  
   `Import` → `File` → `openapi.yaml` seçin; koleksiyon otomatik oluşur.

4. **VS Code / Cursor**  
   “OpenAPI (Swagger) Editor” veya “Swagger Viewer” eklentisi ile YAML önizlemesi açabilirsiniz.

5. **Redocly CLI** (isteğe bağlı)  
   `npx @redocly/cli preview-docs docs/openapi.yaml`

## Önemli notlar

| Konu | Açıklama |
|------|-----------|
| `APP_URL` | Görseller ve PDF için dönen tam URL’ler `.env` içindeki `APP_URL` ile üretilir. |
| `Accept` | İstemci `Accept: application/json` gönderirse hata yanıtları da JSON olma eğilimindedir. |
| Kısıtlama | Haber, duyuru, ana sayfa paketi, sayfalar, menü, başkan, meclis, müdürlükler vb. okuma uçları dakikada 120 istek (`throttle:120,1`). `POST /contact/submit` ve `POST /forms/submit` bu limite dahil değildir. |
| Kimlik doğrulama | Okuma uçları şu an herkese açıktır; ileride token eklenebilir. |

## Uç nokta özeti

| Yöntem | Yol | Açıklama |
|--------|-----|----------|
| GET | `/api/home` | Açılış ekranı: slider, hızlı linkler, başkan, manşet haberler, duyuru önizlemeleri. `?include=...` ile modül seçimi. |
| GET | `/api/news`, `/api/news/{slug}` | Haberler (`kategori`, sayfalama). |
| GET | `/api/announcements`, `/api/announcements/{slug}` | Duyurular (`tip`, sayfalama). |
| GET | `/api/pages`, `/api/pages/{slug}` | Paneldeki statik sayfalar (web’deki `/sayfa/{slug}` ile aynı içerik). |
| GET | `/api/menus?location=header` veya `footer` | Menü ağacı; `page_slug` ile sayfa içeriği çekilir. |
| GET | `/api/mayor` | Belediye başkanı. |
| GET | `/api/council/members` | Meclis üyeleri. |
| GET | `/api/council/decisions` | Meclis kararları (PDF URL’leri; `year`, sayfalama). |
| GET | `/api/directorates`, `/api/directorates/{slug}` | Müdürlük listesi ve detay (+ son duyurular). |
| GET | `/api/organisation/tree` | Başkan yardımcıları ve bağlı müdürlükler. |
| GET | `/api/test` | Basit sağlık kontrolü (throttle dışı). |
| POST | `/api/contact/submit` | İletişim (zorunlu: `name`, `message`). |
| POST | `/api/forms/submit` | Genel form (panel: Gelen Formlar). |

## Hızlı örnek istekler

```http
GET /api/home?include=sliders,quick_links,mayor,headline_news,announcements_by_type
GET /api/news?per_page=10&page=1
GET /api/news?kategori=belediye
GET /api/news/{slug}

GET /api/announcements?tip=duyuru
GET /api/announcements/{slug}

GET /api/pages
GET /api/pages/hakkimizda
GET /api/menus?location=header

GET /api/test
POST /api/contact/submit
Content-Type: application/json

{"name":"Test","message":"Merhaba"}
```

Tam yol: `{APP_URL}/api/...` (Laravel `api` öneki otomatiktir).
