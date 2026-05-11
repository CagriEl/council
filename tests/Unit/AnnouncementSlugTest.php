<?php

namespace Tests\Unit;

use App\Support\AnnouncementSlug;
use PHPUnit\Framework\TestCase;

class AnnouncementSlugTest extends TestCase
{
    public function test_base_from_title_turkish(): void
    {
        $this->assertSame(
            'kirklareli-belediyesi-duyurusu',
            AnnouncementSlug::baseFromTitle('Kırklareli Belediyesi Duyurusu')
        );
    }

    public function test_base_empty_falls_back(): void
    {
        $this->assertSame('duyuru', AnnouncementSlug::baseFromTitle('   '));
    }

    public function test_unique_increments_suffix(): void
    {
        $n = 0;
        $slug = AnnouncementSlug::uniqueFromTitle('Test Duyurusu', function (string $s) use (&$n): bool {
            $n++;

            // İlk aday dolu, ikinci serbest
            return $s === 'test-duyurusu';
        });
        $this->assertSame('test-duyurusu-2', $slug);
        $this->assertSame(2, $n);
    }

    public function test_with_numeric_suffix_respects_length(): void
    {
        $base = str_repeat('a', 250);
        $out = AnnouncementSlug::withNumericSuffix($base, 2);
        $this->assertLessThanOrEqual(255, strlen($out));
        $this->assertStringEndsWith('-2', $out);
    }
}
