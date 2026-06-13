<?php

namespace Modules\Appointment\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test Appointment.
     *
     * @return void
     */
    public function test_backend_appointments_list_page()
    {
        $this->signInAsAdmin();

        $response = $this->get('app/appointments');

        $response->assertStatus(200);
    }
}
