<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stratejik Planlar - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
    <style>
        .plans-page {
            padding: 2rem 0 4rem;
            background: #f8fafc;
            min-height: 60vh;
        }
        .plans-title {
            color: #1a3c6e;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        .plans-subtitle {
            color: #64748b;
            margin-bottom: 2rem;
        }
        .plan-card {
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
        .plan-card + .plan-card {
            margin-top: 0.85rem;
        }
        .plan-year {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.05rem;
        }
        .plan-title {
            color: #1e293b;
            font-weight: 600;
        }
        .plan-note {
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 0.2rem;
        }
        .plan-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .btn-plan {
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.45rem 0.9rem;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            background: #fff;
        }
        .btn-plan:hover {
            background: #1a3c6e;
            color: #fff;
            border-color: #1a3c6e;
        }
        .btn-plan.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }
        @media (max-width: 768px) {
            .plan-card {
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
    <section class="plans-page">
        <div class="container">
            <h1 class="plans-title">Stratejik Planlar</h1>
            <p class="plans-subtitle">Kurumumuza ait stratejik plan dokumanlarini bu sayfadan acabilir veya indirebilirsiniz.</p>

            @forelse($plans as $plan)
                <article class="plan-card">
                    <div>
                        <div class="plan-year">{{ $plan->year ? $plan->year . ' Donemi' : 'Donem Bilgisi Yok' }}</div>
                        <div class="plan-title">{{ $plan->title }}</div>
                        @if($plan->note)
                            <div class="plan-note">{{ $plan->note }}</div>
                        @endif
                    </div>
                    <div class="plan-actions">
                        @if($plan->file_path)
                            <a class="btn-plan" target="_blank" href="{{ asset('storage/' . $plan->file_path) }}">
                                <i class="fas fa-eye me-1"></i> Ac
                            </a>
                            <a class="btn-plan" target="_blank" href="{{ asset('storage/' . $plan->file_path) }}" download>
                                <i class="fas fa-download me-1"></i> Indir
                            </a>
                        @else
                            <a class="btn-plan disabled" href="#">Dosya baglantisi yok</a>
                        @endif
                        @if($plan->source_url)
                            <a class="btn-plan" target="_blank" href="{{ $plan->source_url }}">
                                <i class="fas fa-link me-1"></i> Kaynak
                            </a>
                        @endif
                    </div>
                </article>
            @empty
                <div class="alert alert-light border text-muted">
                    Henuz stratejik plan bulunamadi.
                </div>
            @endforelse
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
@include('layouts.footer')
</html>
