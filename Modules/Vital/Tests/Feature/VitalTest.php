<?php

namespace Modules\Vital\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VitalTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test Vital.
     *
     * @return void
     */
    public function test_backend_vitals_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/vitals');

        $response->assertStatus(200);
    }
}
