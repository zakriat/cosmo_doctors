<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('frontend_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('key')->nullable();
            $table->tinyInteger('status')->nullable()->default('0');
            $table->text('value')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Handles 'deleted_at' properly
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });

        // Insert default values immediately after table creation
        DB::table('frontend_settings')->insert([
            [
                'type' => 'landing-page-setting',
                'key' => 'section_1',
                'status' => 1,
                'value' => json_encode([
                    'section_1' => 1,
                    'title' => 'Your Health, Our Priority - Book An Appointment Today!',
                    'enable_search' => 'on',
                    'instant_link' => ['doctors', 'services', 'clinics'],
                    'enable_quick_booking' => 'on',
                ]),
            ],
            [
                'type' => 'landing-page-setting',
                'key' => 'section_2',
                'status' => 1,
                'value' => json_encode([
                    'section_2' => 1,
                    "title" => "Category",
                    'subtitle' => "Discover Our Premier Specialties",
                    'category_id' => ['1', '2', '3', '4', '5'],
                ]),
            ],
            [
                'type' => 'landing-page-setting',
                'key' => 'section_3',
                'status' => 1,
                'value' => json_encode([
                    'section_3' => 1,
                    'title' => 'Services',
                    'subtitle' => "Our Popular Service",
                    'service_id' => ['1', '2', '3', '4', '5', '6', '7', '8'],
                ]),
            ],
            [
                'type' => 'landing-page-setting',
                'key' => 'section_4',
                'status' => 1,
                'value' => json_encode([
                    "section_4" => 1,
                    "title" => "Effortlessly Elevate Your amazing Experience with Our App",
                    "description" => "From effortless booking to personalized recommendations, we're here to elevate every aspect of your journey."
                ]),
            ],
            [
                'type' => 'landing-page-setting',
                'key' => 'section_5',
                'status' => 1,
                'value' => json_encode([
                    'section_5' => 1,
                    'title' => 'clinics',
                    'subtitle' => 'Find Your Perfect clinic',
                    'clinic_id' => ['1', '2', '3'],
                ]),
            ],
            [
                'type' => 'landing-page-setting',
                'key' => 'section_6',
                'status' => 1,
                'value' => json_encode([
                    'section_6' => 1,
                    'title' => 'Doctors',
                    'subtitle' => "Our Popular Doctors",
                    'doctor_id' => ['1', '2', '3'],
                ]),
            ],
            [
                'type' => 'landing-page-setting',
                'key' => 'section_7',
                'status' => 1,
                'value' => json_encode([
                    'section_7' => 1,
                    'title' => 'Faq',
                    'subtitle' => 'Frequently Asked Questions',
                    'description' => 'Ask anythings related to kivicare and we will provide you your query with a solution.',
                ]),
            ],
            [
                'type' => 'landing-page-setting',
                'key' => 'section_8',
                'status' => 1,
                'value' => json_encode([
                    'section_8' => 1,
                ]),
            ],
            [
                'type' => 'landing-page-setting',
                'key' => 'section_9',
                'status' => 1,
                'value' => json_encode([
                    'section_9' => 1,
                    'title' => 'Blog',
                    'subtitle' => "Daily Tips to Remember",
                    'blog_id' => [],
                ]),
            ],
            [
                'type' => 'heder-menu-setting',
                'key' => 'heder-menu-setting',
                'status' => '1',
                'value' => json_encode([
                    "header_setting" => 1,
                    "categories" => 1,
                    "services" => 1,
                    "clinics" => 1,
                    "doctors" => 1,
                    "appointments" => 1,
                    "incidence" => 1,
                    "enable_search" => 1,
                    "enable_language" => 1,
                    "enable_darknight_mode" => 1
                ]),
            ],
            [
                'type' => 'footer-setting',
                'key' => 'footer-setting',
                'status' => '1',
                'value' => json_encode([
                    "footer_setting" => 1,
                    'enable_quick_link' => 1,
                    "enable_top_service" => 1,
                    "service_id" => ["1", "2", "3"],
                    "enable_top_category" => 1,
                    "category_id" => ["1", "2", "3", "4", "5", "6"],
                ]),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};