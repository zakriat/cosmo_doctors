<?php

namespace Modules\Earning\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EarningTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test Earning.
     *
     * @return void
     */
    public function test_backend_earnings_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/earnings');

        $response->assertStatus(200);
    }
}
