<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mobil API — Haberler, Duyurular, Başkan — {{ config('app.name') }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@@400;500;600;700&family=JetBrains+Mono:wght@@400;500&display=swap" rel="stylesheet">
  @verbatim
  <style>
    :root {
      --bg: #0c0e12;
      --bg-elevated: #141820;
      --bg-card: #1a1f2a;
      --border: #2a3142;
      --text: #e8eaef;
      --text-muted: #8b95a8;
      --accent: #38bdf8;
      --accent-dim: rgba(56, 189, 248, 0.15);
      --get: #22c55e;
      --post: #f59e0b;
      --code-bg: #050608;
      --sidebar-w: 260px;
      --radius: 12px;
      --shadow: 0 8px 32px rgba(0,0,0,.45);
    }
    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      margin: 0;
      font-family: "DM Sans", system-ui, sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.55;
      font-size: 15px;
    }
    .layout {
      display: grid;
      grid-template-columns: var(--sidebar-w) 1fr;
      min-height: 100vh;
    }
    @media (max-width: 960px) {
      .layout {
        grid-template-columns: 1fr;
      }
      aside.nav {
        position: sticky;
        top: 0;
        z-index: 40;
        border-bottom: 1px solid var(--border);
        max-height: none;
      }
      .nav-inner {
        display: flex;
        flex-wrap: nowrap;
        gap: 0.5rem;
        overflow-x: auto;
        padding: 0.75rem 1rem;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
      }
      .nav-inner a {
        flex: 0 0 auto;
        white-space: nowrap;
        font-size: 0.85rem;
      }
      .nav-title { display: none; }
    }
    aside.nav {
      background: var(--bg-elevated);
      border-right: 1px solid var(--border);
      padding: 1.25rem 0;
      max-height: 100vh;
      overflow-y: auto;
    }
    .nav-inner {
      padding: 0 1rem 1.5rem;
    }
    .nav-title {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      color: var(--text-muted);
      padding: 0 1rem 0.5rem;
      margin-top: 1rem;
    }
    .nav-title:first-child { margin-top: 0; }
    aside.nav a {
      display: block;
      padding: 0.45rem 0.85rem;
      border-radius: 8px;
      color: var(--text-muted);
      text-decoration: none;
      font-size: 0.9rem;
      transition: background .15s, color .15s;
    }
    aside.nav a:hover, aside.nav a:focus-visible {
      background: var(--accent-dim);
      color: var(--accent);
      outline: none;
    }
    main {
      padding: 2rem clamp(1rem, 4vw, 3rem) 4rem;
      max-width: 900px;
    }
    .hero {
      margin-bottom: 2.5rem;
      padding-bottom: 2rem;
      border-bottom: 1px solid var(--border);
    }
    .hero h1 {
      margin: 0 0 0.5rem;
      font-size: clamp(1.5rem, 4vw, 2rem);
      font-weight: 700;
      letter-spacing: -0.02em;
    }
    .hero p {
      margin: 0;
      color: var(--text-muted);
      max-width: 52ch;
    }
    .base-url {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 1rem;
      padding: 0.5rem 0.85rem;
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: 8px;
      font-family: "JetBrains Mono", monospace;
      font-size: 0.8rem;
      color: var(--accent);
    }
    section {
      margin-bottom: 3rem;
      scroll-margin-top: 1rem;
    }
    section > h2 {
      font-size: 1.15rem;
      font-weight: 700;
      margin: 0 0 1.25rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid var(--border);
      color: var(--text);
    }
    .endpoint {
      background: var(--bg-card);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 1.25rem 1.35rem;
      margin-bottom: 1.25rem;
      box-shadow: var(--shadow);
    }
    .endpoint-head {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.65rem;
      margin-bottom: 0.75rem;
    }
    .method {
      font-family: "JetBrains Mono", monospace;
      font-size: 0.72rem;
      font-weight: 600;
      padding: 0.25rem 0.5rem;
      border-radius: 6px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    .method-get { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
    .method-post { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }
    .url {
      font-family: "JetBrains Mono", monospace;
      font-size: 0.82rem;
      word-break: break-all;
      color: var(--text);
    }
    .desc {
      color: var(--text-muted);
      font-size: 0.9rem;
      margin: 0 0 1rem;
    }
    .params {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.85rem;
      margin-bottom: 1rem;
    }
    .params th, .params td {
      text-align: left;
      padding: 0.5rem 0.65rem;
      border: 1px solid var(--border);
    }
    .params th {
      background: var(--bg-elevated);
      color: var(--text-muted);
      font-weight: 600;
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .params code {
      font-family: "JetBrains Mono", monospace;
      font-size: 0.8rem;
      color: var(--accent);
    }
    .json-label {
      font-size: 0.72rem;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-muted);
      margin-bottom: 0.4rem;
    }
    pre.json {
      margin: 0;
      padding: 1rem 1.1rem;
      background: var(--code-bg);
      border: 1px solid #1f2937;
      border-radius: 8px;
      overflow-x: auto;
      font-family: "JetBrains Mono", monospace;
      font-size: 0.78rem;
      line-height: 1.5;
      color: #d1d5db;
    }
    .note {
      margin-top: 0.75rem;
      padding: 0.65rem 0.85rem;
      background: var(--accent-dim);
      border-radius: 8px;
      font-size: 0.82rem;
      color: var(--text-muted);
    }
    footer {
      margin-top: 3rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border);
      font-size: 0.8rem;
      color: var(--text-muted);
    }
  </style>
  @endverbatim
</head>
<body>
  <div class="layout">
    <aside class="nav" aria-label="İçindekiler">
      <div class="nav-inner">
        <div class="nav-title">Genel</div>
        <a href="#overview">Genel bilgi</a>
        <div class="nav-title">Haberler</div>
        <a href="#news-list">Liste</a>
        <a href="#news-detail">Detay</a>
        <div class="nav-title">Duyurular</div>
        <a href="#ann-list">Liste (JSON)</a>
        <a href="#ann-detail">Detay</a>
        <a href="#ann-official">Resmî (HTML scrape)</a>
        <div class="nav-title">Başkan</div>
        <a href="#mayor">Profil</a>
      </div>
    </aside>
    <main>
      <header class="hero" id="overview">
        <h1>Mobil API — Haberler, Duyurular, Başkan</h1>
        <p>
          Bu sayfa, Laravel uygulamasındaki JSON uçlarının özetidir. Örneklerde <code>{BASE_URL}</code> yerine uygulama kökünüzü kullanın
          (bu ortam: <code style="color:var(--accent)">{{ rtrim(config('app.url'), '/') }}</code>).
        </p>
        <div class="base-url" id="base-display">{{ url('/api') }}</div>
        <p class="note" style="margin-top:1rem;">
          Okuma uçları <code>throttle:120,1</code> ile sınırlıdır. Liste yanıtları yalnızca ön yüzde yayında olan
          (<code>publishedForPublic</code>) kayıtları içerir. Görseller için sunucuda <code>php artisan storage:link</code> ve doğru <code>APP_URL</code> gerekir.
        </p>
      </header>

      <section id="news-list">
        <h2>Haberler — Liste</h2>
        <article class="endpoint">
          <div class="endpoint-head">
            <span class="method method-get">GET</span>
            <span class="url">{BASE_URL}/api/news</span>
          </div>
          <p class="desc">Sayfalanmış haber listesi. İsteğe bağlı kategori filtresi.</p>
          <table class="params">
            <thead>
              <tr><th>Parametre</th><th>Konum</th><th>Açıklama</th></tr>
            </thead>
            <tbody>
              <tr><td><code>page</code></td><td>query</td><td>Sayfa numarası (varsayılan 1)</td></tr>
              <tr><td><code>per_page</code></td><td>query</td><td>Sayfa başına kayıt, 1–50 (varsayılan 15)</td></tr>
              <tr><td><code>kategori</code></td><td>query</td><td>Opsiyonel: <code>belediye</code>, <code>kultur</code>, <code>spor</code>, <code>cevre</code>, <code>sosyal</code></td></tr>
            </tbody>
          </table>
          <div class="json-label">Örnek JSON çıktısı</div>
          <pre class="json">{
  "data": [
    {
      "id": 12,
      "title": "Örnek haber başlığı",
      "slug": "ornek-haber-slug",
      "summary": "Kısa özet metni…",
      "category": "belediye",
      "category_label": "Belediye",
      "image_url": "{{ rtrim(config('app.url'), '/') }}/storage/haberler/ornek.jpg",
      "published_at": "2026-04-01T10:00:00+00:00",
      "unpublished_at": null,
      "is_headline": true,
      "updated_at": "2026-04-02T08:00:00+00:00"
    }
  ],
  "links": {
    "first": "{{ url('/api/news') }}?page=1",
    "last": "{{ url('/api/news') }}?page=3",
    "prev": null,
    "next": "{{ url('/api/news') }}?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 3,
    "path": "{{ url('/api/news') }}",
    "per_page": 15,
    "to": 15,
    "total": 42
  }
}</pre>
        </article>
      </section>

      <section id="news-detail">
        <h2>Haberler — Detay</h2>
        <article class="endpoint">
          <div class="endpoint-head">
            <span class="method method-get">GET</span>
            <span class="url">{BASE_URL}/api/news/{slug}</span>
          </div>
          <p class="desc">Tekil haber. <code>{slug}</code> liste yanıtındaki <code>slug</code> alanıdır.</p>
          <table class="params">
            <thead>
              <tr><th>Parametre</th><th>Konum</th><th>Açıklama</th></tr>
            </thead>
            <tbody>
              <tr><td><code>slug</code></td><td>path</td><td>Haber URL parçası (örn. <code>ornek-haber-slug</code>)</td></tr>
            </tbody>
          </table>
          <div class="json-label">Örnek JSON çıktısı</div>
          <pre class="json">{
  "data": {
    "id": 12,
    "title": "Örnek haber başlığı",
    "slug": "ornek-haber-slug",
    "summary": "Kısa özet…",
    "content_html": "&lt;p&gt;Haber gövdesi HTML olarak.&lt;/p&gt;",
    "category": "belediye",
    "category_label": "Belediye",
    "image_url": "{{ rtrim(config('app.url'), '/') }}/storage/haberler/ornek.jpg",
    "published_at": "2026-04-01T10:00:00+00:00",
    "unpublished_at": null,
    "is_headline": true,
    "updated_at": "2026-04-02T08:00:00+00:00"
  }
}</pre>
        </article>
      </section>

      <section id="ann-list">
        <h2>Duyurular — Liste (veritabanı JSON)</h2>
        <article class="endpoint">
          <div class="endpoint-head">
            <span class="method method-get">GET</span>
            <span class="url">{BASE_URL}/api/announcements</span>
          </div>
          <p class="desc">Sayfalanmış duyuru listesi. İsteğe bağlı tip filtresi.</p>
          <table class="params">
            <thead>
              <tr><th>Parametre</th><th>Konum</th><th>Açıklama</th></tr>
            </thead>
            <tbody>
              <tr><td><code>page</code></td><td>query</td><td>Sayfa numarası</td></tr>
              <tr><td><code>per_page</code></td><td>query</td><td>1–50, varsayılan 15</td></tr>
              <tr><td><code>tip</code></td><td>query</td><td>Opsiyonel: <code>duyuru</code>, <code>resmi</code>, <code>ihale</code></td></tr>
            </tbody>
          </table>
          <div class="json-label">Örnek JSON çıktısı</div>
          <pre class="json">{
  "data": [
    {
      "id": 5,
      "title": "Örnek duyuru",
      "slug": "ornek-duyuru-slug",
      "type": "resmi",
      "type_label": "Resmî ilan",
      "excerpt": "İçerikten üretilmiş düz metin özeti (maks. 220 karakter)…",
      "image_url": "{{ rtrim(config('app.url'), '/') }}/storage/duyurular/kapak.jpg",
      "date": "2026-03-20T00:00:00+00:00",
      "published_at": "2026-03-20T00:00:00+00:00",
      "unpublished_at": null,
      "has_attachment": true,
      "updated_at": "2026-03-21T12:00:00+00:00"
    }
  ],
  "links": {
    "first": "{{ url('/api/announcements') }}?page=1",
    "last": "{{ url('/api/announcements') }}?page=2",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "path": "{{ url('/api/announcements') }}",
    "per_page": 15,
    "to": 15,
    "total": 18
  }
}</pre>
        </article>
      </section>

      <section id="ann-detail">
        <h2>Duyurular — Detay</h2>
        <article class="endpoint">
          <div class="endpoint-head">
            <span class="method method-get">GET</span>
            <span class="url">{BASE_URL}/api/announcements/{slug}</span>
          </div>
          <p class="desc">Tekil duyuru; PDF varsa <code>file_url</code> dolu olabilir.</p>
          <table class="params">
            <thead>
              <tr><th>Parametre</th><th>Konum</th><th>Açıklama</th></tr>
            </thead>
            <tbody>
              <tr><td><code>slug</code></td><td>path</td><td>Duyuru slug değeri</td></tr>
            </tbody>
          </table>
          <div class="json-label">Örnek JSON çıktısı</div>
          <pre class="json">{
  "data": {
    "id": 5,
    "title": "Örnek duyuru",
    "slug": "ornek-duyuru-slug",
    "type": "resmi",
    "type_label": "Resmî ilan",
    "content_html": "&lt;p&gt;Duyuru metni HTML.&lt;/p&gt;",
    "image_url": "{{ rtrim(config('app.url'), '/') }}/storage/duyurular/kapak.jpg",
    "file_url": "{{ rtrim(config('app.url'), '/') }}/storage/duyurular/ek.pdf",
    "date": "2026-03-20T00:00:00+00:00",
    "published_at": "2026-03-20T00:00:00+00:00",
    "unpublished_at": null,
    "updated_at": "2026-03-21T12:00:00+00:00"
  }
}</pre>
        </article>
      </section>

      <section id="ann-official">
        <h2>Duyurular — Resmî liste (HTML scrape)</h2>
        <article class="endpoint">
          <div class="endpoint-head">
            <span class="method method-get">GET</span>
            <span class="url">{BASE_URL}/api/announcements/official</span>
          </div>
          <p class="desc">
            Web sayfasındaki resmî duyuru kartlarından üretilen liste. Sonuç <strong>3600 sn</strong> önbelleğe alınır.
            Kaynak URL varsayılan olarak <code>APP_URL/duyurular?tip=resmi</code> (<code>ANNOUNCEMENTS_SCRAPER_URL</code> ile değiştirilebilir).
          </p>
          <table class="params">
            <thead>
              <tr><th>Parametre</th><th>Konum</th><th>Açıklama</th></tr>
            </thead>
            <tbody>
              <tr><td colspan="3">Path/query parametresi yok (önbellek sunucu tarafında).</td></tr>
            </tbody>
          </table>
          <div class="json-label">Örnek JSON çıktısı</div>
          <pre class="json">{
  "data": [
    {
      "title": "Resmî ilan başlığı",
      "image_url": "{{ rtrim(config('app.url'), '/') }}/storage/duyurular/gorsel.jpg",
      "detail_url": "{{ url('/duyurular/resmi-ilan-slug') }}"
    }
  ]
}</pre>
          <p class="note">Siteye ulaşılamazsa veya ayrıştırma boş kalırsa <code>data</code> dizisi <code>[]</code> olabilir.</p>
        </article>
      </section>

      <section id="mayor">
        <h2>Belediye başkanı</h2>
        <article class="endpoint">
          <div class="endpoint-head">
            <span class="method method-get">GET</span>
            <span class="url">{BASE_URL}/api/mayor</span>
          </div>
          <p class="desc">Önce <code>is_active = true</code> kayıt; yoksa ilk kayıt. Kayıt yoksa HTTP 404.</p>
          <table class="params">
            <thead>
              <tr><th>Parametre</th><th>Konum</th><th>Açıklama</th></tr>
            </thead>
            <tbody>
              <tr><td colspan="3">Parametre yok.</td></tr>
            </tbody>
          </table>
          <div class="json-label">Örnek JSON çıktısı (200)</div>
          <pre class="json">{
  "data": {
    "id": 1,
    "name": "Örnek Başkan",
    "title": "Belediye Başkanı",
    "image_url": "{{ rtrim(config('app.url'), '/') }}/storage/baskan/foto.jpg",
    "description_html": "&lt;p&gt;Kısa tanıtım HTML.&lt;/p&gt;",
    "message_html": "&lt;p&gt;Başkan mesajı HTML.&lt;/p&gt;",
    "is_active": true
  }
}</pre>
          <div class="json-label" style="margin-top:1rem;">Örnek JSON (404)</div>
          <pre class="json">{
  "message": "Başkan kaydı bulunamadı."
}</pre>
        </article>
      </section>

      <footer>
        Canlı şema: <code>docs/openapi.yaml</code> — Swagger UI:
        <a href="{{ url('/api/docs') }}" style="color:var(--accent)">{{ url('/api/docs') }}</a>
      </footer>
    </main>
  </div>
</body>
</html>
