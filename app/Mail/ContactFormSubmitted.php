<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{
     *     platform: string,
     *     source: string,
     *     name: string,
     *     phone?: string|null,
     *     email?: string|null,
     *     subject?: string|null,
     *     message: string,
     *     photo_url?: string|null,
     *     ip_address?: string|null
     * }  $data
     */
    public function __construct(public array $data) {}

    public function envelope(): Envelope
    {
        $platform = $this->platformLabel($this->data['platform'] ?? 'web');
        $name = $this->data['name'] ?? 'Bilinmiyor';

        $replyTo = [];
        if (filled($this->data['email'] ?? null)) {
            $replyTo[] = new Address((string) $this->data['email'], $name);
        }

        return new Envelope(
            subject: "İletişim formu ({$platform}) — {$name}",
            replyTo: $replyTo,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.contact-form-submitted',
            text: 'emails.contact-form-submitted-text',
            with: [
                'platform' => $this->platformLabel($this->data['platform'] ?? 'web'),
                'source' => $this->sourceLabel($this->data['source'] ?? 'genel'),
                'name' => $this->data['name'] ?? '-',
                'phone' => $this->data['phone'] ?? null,
                'email' => $this->data['email'] ?? null,
                'subjectLine' => $this->data['subject'] ?? null,
                'messageBody' => $this->data['message'] ?? '',
                'photoUrl' => $this->data['photo_url'] ?? null,
                'ipAddress' => $this->data['ip_address'] ?? null,
            ],
        );
    }

    private function platformLabel(string $platform): string
    {
        return match (mb_strtolower($platform)) {
            'web' => 'Web',
            'ios' => 'iOS Uygulama',
            'android' => 'Android Uygulama',
            default => $platform,
        };
    }

    private function sourceLabel(string $source): string
    {
        return match ($source) {
            'iletisim-sayfasi' => 'İletişim Sayfası',
            'baskan-sayfasi' => 'Başkan Sayfası',
            'mobile-talep', 'mobil-app', 'mobil-talep' => 'Mobil Talep',
            default => $source,
        };
    }
}
