<?php

namespace App\Support\Push;

use App\Models\Announcement;
use App\Models\News;
use App\Models\PushNotificationLog;
use App\Models\PushToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ExpoPushService
{
    private const CHUNK_SIZE = 100;

    /**
     * @return array{sent: int, failed: int, total: int}
     */
    public function sendAnnouncement(Announcement $announcement, ?string $customBody = null): array
    {
        $body = $customBody ?: Str::limit(strip_tags((string) $announcement->content), 160);

        return $this->dispatch(
            title: $announcement->title,
            body: $body,
            data: [
                'type' => 'announcement',
                'id' => $announcement->id,
                'slug' => $announcement->slug,
            ],
            contentType: 'announcement',
            contentId: $announcement->id,
        );
    }

    /**
     * @return array{sent: int, failed: int, total: int}
     */
    public function sendNews(News $news, ?string $customBody = null): array
    {
        $body = $customBody ?: Str::limit(strip_tags((string) ($news->summary ?: $news->content)), 160);

        return $this->dispatch(
            title: $news->title,
            body: $body,
            data: [
                'type' => 'news',
                'id' => $news->id,
                'slug' => $news->slug,
            ],
            contentType: 'news',
            contentId: $news->id,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{sent: int, failed: int, total: int}
     */
    private function dispatch(
        string $title,
        string $body,
        array $data,
        string $contentType,
        int $contentId,
    ): array {
        $tokens = PushToken::query()->pluck('token')->all();
        $total = count($tokens);

        if ($total === 0) {
            return ['sent' => 0, 'failed' => 0, 'total' => 0];
        }

        $sent = 0;
        $failed = 0;

        foreach (array_chunk($tokens, self::CHUNK_SIZE) as $chunk) {
            $messages = array_map(
                fn (string $token) => [
                    'to' => $token,
                    'title' => $title,
                    'body' => $body,
                    'data' => $data,
                    'sound' => 'default',
                    'priority' => 'high',
                ],
                $chunk,
            );

            $response = Http::timeout(20)
                ->acceptJson()
                ->post('https://exp.host/--/api/v2/push/send', $messages);

            if (! $response->successful()) {
                $failed += count($chunk);

                continue;
            }

            $results = $response->json('data') ?? [];
            foreach ($results as $result) {
                if (($result['status'] ?? '') === 'ok') {
                    $sent++;
                } else {
                    $failed++;
                    $this->removeInvalidToken($result['details']['expoPushToken'] ?? null);
                }
            }
        }

        PushNotificationLog::create([
            'content_type' => $contentType,
            'content_id' => $contentId,
            'title' => $title,
            'body' => $body,
            'sent_count' => $sent,
            'failed_count' => $failed,
            'sent_by' => Auth::id(),
        ]);

        return compact('sent', 'failed', 'total');
    }

    private function removeInvalidToken(?string $token): void
    {
        if (! $token) {
            return;
        }

        PushToken::query()->where('token', $token)->delete();
    }
}
