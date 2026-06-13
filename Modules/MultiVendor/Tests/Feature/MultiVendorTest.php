<?php

namespace Modules\MultiVendor\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MultiVendorTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test MultiVendor.
     *
     * @return void
     */
    public function test_backend_multivendors_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/multivendors');

        $response->assertStatus(200);
    }
}
