<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telefon Rehberi - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
    <style>
        .rehber-table { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.05); }
        .rehber-table th { background: #1a3c6e; color: #fff; font-weight: 600; white-space: nowrap; }
        .rehber-table td, .rehber-table th { padding: 0.85rem 1rem; vertical-align: middle; }
        .rehber-table tr:hover td { background: #f7f9fc; }
    </style>
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container mb-5">
        <h1 class="page-title">Telefon Rehberi</h1>
        <div class="title-divider"></div>
        <p class="text-muted mb-4">Belediye müdürlükleri ve iletişim bilgileri</p>

        @if(($directorates ?? collect())->isEmpty())
            <div class="alert alert-light border">
                Rehber kaydı bulunamadı.
                <a href="{{ route('iletisim') }}">İletişim formu</a> üzerinden bize yazabilirsiniz.
                Çağrı merkezi: <a href="tel:4440139">444 01 39</a>
            </div>
        @else
            <div class="table-responsive rehber-table">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Müdürlük</th>
                            <th>Yetkili</th>
                            <th>Telefon</th>
                            <th>E-posta</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($directorates as $d)
                            <tr>
                                <td class="fw-semibold">{{ $d->name }}</td>
                                <td>{{ $d->manager_name ?: '—' }}</td>
                                <td>
                                    @if($d->phone)
                                        <a href="tel:{{ preg_replace('/\s+/', '', $d->phone) }}">{{ $d->phone }}</a>
                                    @else
                                        <a href="tel:4440139">444 01 39</a>
                                    @endif
                                </td>
                                <td>
                                    @if($d->email)
                                        <a href="mailto:{{ $d->email }}">{{ $d->email }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($d->slug)
                                        <a href="{{ route('mudurluk.detay', $d->slug) }}" class="btn btn-sm btn-outline-primary">Detay</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</main>

@include('layouts.footer')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
