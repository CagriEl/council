<?php

namespace App\Http\Resources\Api;

use App\Support\Api\FullNameParts;
use App\Support\Api\StorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Mayor */
class MayorResource extends JsonResource
{
    /**
     * Başkan sayfası: web panelindeki alanlar + mobil için Türkçe anahtarlar.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        [$ad, $soyad] = FullNameParts::split($this->name);
        $fotografUrl = StorageUrl::fromPath($this->image_path);
        $ozgecmisHtml = $this->description;
        $mesajHtml = $this->message;
        $ozgecmisPlain = self::htmlToPlain($ozgecmisHtml);

        return [
            'id' => $this->id,
            'updated_at' => $this->updated_at?->toIso8601String(),

            // Türkçe (mobil başkan ekranı)
            'ad_soyad' => $this->name,
            'ad' => $ad,
            'soyad' => $soyad,
            'unvan' => $this->title,
            'fotograf_url' => $fotografUrl,
            'ozgecmis_html' => $ozgecmisHtml,
            'ozgecmis_plain' => $ozgecmisPlain,
            'mesaj_html' => $mesajHtml,

            // İngilizce / geriye dönük uyumluluk
            'name' => $this->name,
            'title' => $this->title,
            'image_url' => $fotografUrl,
            'description_html' => $ozgecmisHtml,
            'message_html' => $mesajHtml,

            'is_active' => (bool) $this->is_active,
        ];
    }

    private static function htmlToPlain(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return null;
        }

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text) === '' ? null : trim($text);
    }
}
