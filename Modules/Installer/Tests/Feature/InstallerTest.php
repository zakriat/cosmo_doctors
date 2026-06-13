<?php

namespace Modules\Installer\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InstallerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test Installer.
     *
     * @return void
     */
    public function test_backend_installers_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/installers');

        $response->assertStatus(200);
    }
}
