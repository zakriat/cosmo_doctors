<?php

namespace Modules\RequestService\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RequestServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test RequestService.
     *
     * @return void
     */
    public function test_backend_requestservices_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/requestservices');

        $response->assertStatus(200);
    }
}
