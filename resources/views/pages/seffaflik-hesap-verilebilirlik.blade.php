<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şeffaflık ve Hesap Verilebilirlik - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
    <style>
        .transparency-page {
            padding: 2rem 0 4rem;
            background: #f8fafc;
            min-height: 60vh;
        }

        .transparency-shell {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .transparency-sidebar {
            background: #f1f5f9;
            border-right: 1px solid #e2e8f0;
            padding: 1.25rem;
        }

        .transparency-sidebar-title {
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 0.85rem;
        }

        .transparency-nav {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .transparency-nav li + li {
            margin-top: 0.35rem;
        }

        .transparency-nav a {
            display: block;
            padding: 0.75rem 0.9rem;
            border-radius: 10px;
            text-decoration: none;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.92rem;
            line-height: 1.35;
            transition: background 0.2s, color 0.2s;
        }

        .transparency-nav a:hover,
        .transparency-nav a.active {
            background: #1a3c6e;
            color: #fff;
        }

        .transparency-content {
            padding: 1.75rem;
        }

        .transparency-heading {
            color: #1a3c6e;
            font-weight: 800;
            margin-bottom: 0.35rem;
        }

        .transparency-subtitle {
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .transparency-doc {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.15rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
        }

        .transparency-doc + .transparency-doc {
            margin-top: 0.75rem;
        }

        .transparency-doc-title {
            font-weight: 700;
            color: #0f172a;
            line-height: 1.4;
        }

        .transparency-doc-actions {
            display: flex;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .btn-transparency {
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.45rem 0.9rem;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            background: #fff;
            white-space: nowrap;
        }

        .btn-transparency:hover {
            background: #1a3c6e;
            color: #fff;
            border-color: #1a3c6e;
        }

        @media (max-width: 991px) {
            .transparency-sidebar {
                border-right: 0;
                border-bottom: 1px solid #e2e8f0;
            }

            .transparency-doc {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <section class="transparency-page">
        <div class="container">
            <div class="transparency-shell">
                <div class="row g-0">
                    <div class="col-lg-4 col-xl-3">
                        <aside class="transparency-sidebar">
                            <div class="transparency-sidebar-title">Şeffaflık ve Hesap Verilebilirlik</div>
                            <ul class="transparency-nav">
                                @foreach($sections as $section)
                                    <li>
                                        <a
                                            href="{{ route('transparency.show', $section['slug']) }}"
                                            @class(['active' => $section['slug'] === $activeSection['slug']])
                                        >
                                            {{ $section['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </aside>
                    </div>

                    <div class="col-lg-8 col-xl-9">
                        <div class="transparency-content">
                            <h1 class="transparency-heading">{{ $activeSection['title'] }}</h1>
                            <p class="transparency-subtitle">
                                İlgili dokümanları görüntüleyebilir veya indirebilirsiniz.
                            </p>

                            @forelse($activeSection['documents'] as $document)
                                <article class="transparency-doc">
                                    <div class="transparency-doc-title">{{ $document['title'] }}</div>
                                    <div class="transparency-doc-actions">
                                        <a class="btn-transparency" href="{{ $document['url'] }}" target="_blank" rel="noopener noreferrer">
                                            <i class="fas fa-eye me-1"></i> Aç
                                        </a>
                                        <a class="btn-transparency" href="{{ $document['url'] }}" target="_blank" rel="noopener noreferrer" download>
                                            <i class="fas fa-download me-1"></i> İndir
                                        </a>
                                    </div>
                                </article>
                            @empty
                                <div class="alert alert-light border text-muted mb-0">
                                    Bu bölüm için henüz doküman bulunamadı.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@include('layouts.footer')
</html>
