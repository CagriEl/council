<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faaliyet Raporları - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
    <style>
        .reports-page {
            padding: 2rem 0 4rem;
            background: #f8fafc;
            min-height: 60vh;
        }
        .reports-title {
            color: #1a3c6e;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        .reports-subtitle {
            color: #64748b;
            margin-bottom: 2rem;
        }
        .report-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
        }
        .report-card + .report-card {
            margin-top: 0.85rem;
        }
        .report-year {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.1rem;
        }
        .report-title {
            color: #1e293b;
            font-weight: 600;
        }
        .report-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .btn-report {
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.45rem 0.9rem;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            background: #fff;
        }
        .btn-report:hover {
            background: #1a3c6e;
            color: #fff;
            border-color: #1a3c6e;
        }
        @media (max-width: 768px) {
            .report-card {
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
    <section class="reports-page">
        <div class="container">
            <h1 class="reports-title">Faaliyet Raporları</h1>
            <p class="reports-subtitle">Belediyemize ait yıllık faaliyet raporlarını buradan açabilir veya indirebilirsiniz.</p>

            @forelse($reports as $report)
                <article class="report-card">
                    <div>
                        <div class="report-year">{{ $report->year ? $report->year . ' Yılı' : 'Yıl Bilgisi Yok' }}</div>
                        <div class="report-title">{{ $report->title }}</div>
                    </div>
                    <div class="report-actions">
                        <a class="btn-report" target="_blank" href="{{ asset('storage/' . $report->file_path) }}">
                            <i class="fas fa-eye me-1"></i> Aç
                        </a>
                        <a class="btn-report" target="_blank" href="{{ asset('storage/' . $report->file_path) }}" download>
                            <i class="fas fa-download me-1"></i> İndir
                        </a>
                    </div>
                </article>
            @empty
                <div class="alert alert-light border text-muted">
                    Henüz faaliyet raporu bulunamadı.
                </div>
            @endforelse
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@include('layouts.footer')
</html>
