<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UniversalForm;
use Illuminate\Http\Request;

class UniversalFormController extends Controller
{
    public function submit(Request $request)
    {
        if (count($request->all()) === 0) {
            return response()->json(['status' => 'error', 'message' => 'Veri gönderilmedi.'], 400);
        }

        $platform = $request->header('X-Platform', $request->input('platform', 'web'));
        $source = $request->input('source', 'bilinmiyor');

        try {
            UniversalForm::create([
                'source' => $source,
                'platform' => $platform,
                'ip_address' => $request->ip(),
                'data' => $request->except(['source', 'platform', '_token']),
            ]);

            return response()->json(['status' => 'success', 'message' => 'Mesaj başarıyla alındı.'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
