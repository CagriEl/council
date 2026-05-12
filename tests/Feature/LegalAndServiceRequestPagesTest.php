<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalAndServiceRequestPagesTest extends TestCase
{
    public function test_talep_sikayet_page_loads(): void
    {
        $response = $this->get('/talep-sikayet');

        $response->assertOk();
        $response->assertSee('Talep / Şikayet', false);
    }

    public function test_kvkk_page_loads(): void
    {
        $response = $this->get('/yasal/kvkk');

        $response->assertOk();
        $response->assertSee('KVKK', false);
    }

    public function test_debt_query_processing_page_loads(): void
    {
        $response = $this->get('/yasal/borc-sorgulama-veri-isleme');

        $response->assertOk();
        $response->assertSee('Borç sorgulama', false);
    }
}
