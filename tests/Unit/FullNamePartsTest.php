<?php

namespace Tests\Unit;

use App\Support\Api\FullNameParts;
use PHPUnit\Framework\TestCase;

class FullNamePartsTest extends TestCase
{
    public function test_splits_on_last_space(): void
    {
        $this->assertSame(['Derya', 'BULUT'], FullNameParts::split('Derya BULUT'));
        $this->assertSame(['Ayşe Fatma', 'KAYA'], FullNameParts::split('Ayşe Fatma KAYA'));
        $this->assertSame(['Ali', 'Veli'], FullNameParts::split('Ali Veli'));
    }

    public function test_single_token_has_null_soyad(): void
    {
        $this->assertSame(['Ahmet', null], FullNameParts::split('Ahmet'));
    }

    public function test_empty_string(): void
    {
        $this->assertSame(['', null], FullNameParts::split(''));
        $this->assertSame(['', null], FullNameParts::split(null));
    }
}
