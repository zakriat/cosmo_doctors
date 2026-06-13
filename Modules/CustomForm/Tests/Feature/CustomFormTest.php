<?php

namespace Modules\CustomForm\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomFormTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test CustomForm.
     *
     * @return void
     */
    public function test_backend_customforms_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/customforms');

        $response->assertStatus(200);
    }
}
