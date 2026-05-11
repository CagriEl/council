<?php

namespace App\Services;

use RuntimeException;
use SoapClient;
use Throwable;

class EOdemeService
{
    /**
     * @param  array{mukellef_tipi:string,mukellef_no:string,indirimli_odenecek_mi:int,sadece_su_borclari:int}  $query
     * @return array<string, mixed>
     */
    public function borcSorgula(array $query): array
    {
        if (! $this->hasEndpointConfiguration()) {
            return $this->fallbackNoConfigurationResponse();
        }

        $payload = [
            'bankaKodu' => (string) config('services.e_odeme.banka_kodu'),
            'bankaSifresi' => (string) config('services.e_odeme.banka_sifresi'),
            'kurumKodu' => (string) config('services.e_odeme.kurum_kodu'),
            'mukellefTipi' => $query['mukellef_tipi'],
            'mukellefNo' => $query['mukellef_no'],
            'indirimliOdenecekMi' => $query['indirimli_odenecek_mi'],
            'sadeceSuBorclari' => $query['sadece_su_borclari'],
        ];

        try {
            $client = $this->soapClient();
            $response = $client->__soapCall((string) config('services.e_odeme.borc_sorgula_method', 'borcSorgula'), [$payload]);

            return $this->normalize((array) $response);
        } catch (Throwable $e) {
            throw new RuntimeException(
                'Borç sorgulama servisine bağlanılamadı: '.$e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }

    protected function resolveWsdl(): string
    {
        $primary = trim((string) config('services.e_odeme.wsdl', ''));
        if ($primary !== '') {
            return $primary;
        }

        return trim((string) config('services.e_odeme.tahakkuk_wsdl', ''));
    }

    protected function soapClient(): SoapClient
    {
        $wsdl = $this->resolveWsdl();
        $options = [
            'trace' => false,
            'exceptions' => true,
            'connection_timeout' => (int) config('services.e_odeme.timeout', 20),
            'cache_wsdl' => WSDL_CACHE_MEMORY,
            'encoding' => 'UTF-8',
        ];

        $streamContext = $this->soapStreamContext();
        if ($streamContext !== null) {
            $options['stream_context'] = $streamContext;
        }

        $location = config('services.e_odeme.location');
        $uri = config('services.e_odeme.uri');

        if (is_string($location) && $location !== '') {
            $options['location'] = $location;
        }

        if (is_string($uri) && $uri !== '') {
            $options['uri'] = $uri;
        }

        if ($wsdl !== '') {
            return new SoapClient($wsdl, $options);
        }

        return new SoapClient(null, $options);
    }

    protected function soapStreamContext(): mixed
    {
        if ((bool) config('services.e_odeme.soap_verify_ssl', true)) {
            return null;
        }

        return stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);
    }

    protected function guardConfiguration(): void
    {
        if (! $this->hasEndpointConfiguration()) {
            throw new RuntimeException(
                'E-odeme servisi henuz yapilandirilmamis.'
            );
        }
    }

    protected function hasEndpointConfiguration(): bool
    {
        $hasWsdl = $this->resolveWsdl() !== '';
        $hasLocation = trim((string) config('services.e_odeme.location', '')) !== '';
        $hasUri = trim((string) config('services.e_odeme.uri', '')) !== '';

        return $hasWsdl || ($hasLocation && $hasUri);
    }

    /**
     * @return array<string, mixed>
     */
    protected function fallbackNoConfigurationResponse(): array
    {
        return [
            'sonucKodu' => 1003,
            'sonucAciklamasi' => 'E-odeme servisi henuz yapilandirilmamis. Lutfen daha sonra tekrar deneyin.',
            'kayitSayisi' => 0,
            'tahakkukListesi' => [],
        ];
    }

    protected function normalize(mixed $value): mixed
    {
        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $key => $child) {
                $normalized[$key] = $this->normalize($child);
            }

            return $normalized;
        }

        if (is_object($value)) {
            return $this->normalize((array) $value);
        }

        return $value;
    }
}
