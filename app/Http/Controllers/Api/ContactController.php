<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        // 1. Basit Validasyon
        if (!$request->has('name') || !$request->has('message')) {
            return response()->json(['status' => 'error', 'message' => 'Eksik veri.'], 400);
        }

        // 2. Platform Bilgisi (Header'dan gelir, yoksa 'web' kabul edilir)
        $platform = $request->header('X-Platform', 'web');
        
        // 3. Kaynak Bilgisi (Formun içinden gelir)
        $source = $request->input('source', 'genel');

        // 4. Kayıt
        try {
            ContactMessage::create([
                'platform' => $platform,
                'source'   => $source,
                'ip_address' => $request->ip(),
                // Gelen tüm veriyi (Ad, Soyad, Mesaj vs.) JSON paketine koy
                'payload'  => $request->except(['source', '_token']), 
            ]);

            return response()->json(['status' => 'success', 'message' => 'Mesaj alındı.'], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Sunucu hatası.'], 500);
        }
    }
}