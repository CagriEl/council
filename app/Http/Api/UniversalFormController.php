<?php

namespace App\Http\Controllers; // Dikkat: "Api" eki yok, direkt ana klasör

use App\Http\Controllers\Controller;
use App\Models\UniversalForm;
use Illuminate\Http\Request;

class ApiFormController extends Controller
{
    public function submit(Request $request)
    {
        // 1. Veri kontrolü
        if (count($request->all()) === 0) {
            return response()->json(['status' => 'error', 'message' => 'Veri gönderilmedi.'], 400);
        }

        // 2. Bilgileri al
        $platform = $request->header('X-Platform', $request->input('platform', 'web'));
        $source = $request->input('source', 'bilinmiyor');

        // 3. Veritabanına kaydet
        try {
            UniversalForm::create([
                'source' => $source,
                'platform' => $platform,
                'ip' => $request->ip(),
                'data' => $request->except(['source', 'platform', '_token']),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Mesaj başarıyla alındı.'], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}