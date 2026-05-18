<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // Temel Controller sınıfını import et
use App\Models\Mayor;
use Illuminate\Http\Request;

class MayorController extends Controller
{
    public function show()
    {
        // Veritabanından ilk kaydı çek. 
        // Eğer kayıt yoksa hata vermemesi için "new Mayor()" ile boş bir model döndürürüz.
        $mayor = Mayor::where('is_active', true)->first() ?? new Mayor();

        // 'pages.baskan' view dosyasını (resources/views/pages/baskan.blade.php) çağırır
        return view('pages.baskan', compact('mayor'));
    }
}