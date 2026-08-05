<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ContactMessagePrintController extends Controller
{
    public function __invoke(ContactMessage $contactMessage): Response
    {
        Gate::authorize('view', $contactMessage);

        $payload = is_array($contactMessage->payload) ? $contactMessage->payload : [];

        $sourceLabel = match ((string) $contactMessage->source) {
            'iletisim-sayfasi' => 'İletişim Sayfası',
            'baskan-sayfasi' => 'Başkan Sayfası',
            'mobil-app' => 'Mobil Uygulama',
            default => $contactMessage->source ?: '—',
        };

        return response()->view('filament.contact-messages.print', [
            'message' => $contactMessage,
            'payload' => $payload,
            'sourceLabel' => $sourceLabel,
        ]);
    }
}
