<?php

namespace Modules\Promotion\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test Promotion.
     *
     * @return void
     */
    public function test_backend_promotions_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/promotions');

        $response->assertStatus(200);
    }
}
