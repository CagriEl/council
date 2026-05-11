<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vefat İlanları - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container py-5">
        <div class="mb-4">
            <h1 class="mb-2">Vefat İlanları</h1>
            <p class="text-muted mb-0">
                Güncel vefat duyurularına buradan ulaşabilirsiniz.
            </p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Ad Soyad</th>
                                <th>Vefat Tarihi</th>
                                <th>Namaz Saati</th>
                                <th>Camii</th>
                                <th>Defin Yeri</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($obituaries as $obituary)
                                <tr>
                                    <td>{{ $obituary->full_name }}</td>
                                    <td>{{ optional($obituary->death_date)->format('d.m.Y') }}</td>
                                    <td>{{ $obituary->prayer_time ? substr($obituary->prayer_time, 0, 5) : '-' }}</td>
                                    <td>{{ $obituary->mosque }}</td>
                                    <td>
                                        @if ($obituary->burial_place_type === 'city_cemetery')
                                            Şehir Mezarlığı
                                        @else
                                            {{ $obituary->burial_place_other ?: 'Diğer' }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Aktif vefat ilanı bulunmamaktadır.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

@include('layouts.footer')
</body>
</html>
