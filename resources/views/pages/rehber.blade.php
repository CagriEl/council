<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telefon Rehberi - T.C. Kırklareli Belediyesi</title>
    @include('layouts.frontend-head')
</head>
<body>

@include('partials.accessibility')
@include('partials.header', ['style' => 'solid'])

<main id="main-content" tabindex="-1" class="outline-none">
    <div class="container py-5">
        <h1 class="mb-4">Telefon Rehberi</h1>
        <p class="text-muted mb-4">
            Sık kullanılan birim ve iletişim bilgilerine aşağıdan ulaşabilirsiniz.
            Ayrıntılı bilgi için iletişim sayfasını kullanabilirsiniz.
        </p>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Birim</th>
                        <th>Telefon</th>
                        <th>Dahili</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Çözüm Merkezi</td>
                        <td><a href="tel:4440139">444 01 39</a></td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Yazı İşleri Müdürlüğü</td>
                        <td><a href="tel:02882141045">0 (288) 214 10 45</a></td>
                        <td>1201</td>
                    </tr>
                    <tr>
                        <td>Fen İşleri Müdürlüğü</td>
                        <td><a href="tel:02882141045">0 (288) 214 10 45</a></td>
                        <td>1305</td>
                    </tr>
                    <tr>
                        <td>Zabıta Müdürlüğü</td>
                        <td><a href="tel:02882141045">0 (288) 214 10 45</a></td>
                        <td>1402</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

@include('layouts.footer')
</body>
</html>
