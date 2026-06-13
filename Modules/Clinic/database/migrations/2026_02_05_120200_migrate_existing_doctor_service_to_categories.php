<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration populates doctor_category_mappings from existing doctor_service_mappings
     * for backward compatibility. It assigns doctors to all categories within their services
     * that require doctors.
     */
    public function up(): void
    {
        // Only migrate if doctor_category_mappings is empty
        $existingMappings = DB::table('doctor_category_mappings')->count();
        
        if ($existingMappings > 0) {
            return;
        }
        
        // Get all doctor-service mappings
        $doctorServiceMappings = DB::table('doctor_service_mappings')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get();
        
        $insertedCount = 0;
        
        foreach ($doctorServiceMappings as $mapping) {
            // Get all categories for this service that require doctors
            $categories = DB::table('clinics_categories')
                ->where('parent_id', $mapping->service_id)
                ->where('status', 1)
                ->where('service_classification', 'doctor_required')
                ->whereNull('deleted_at')
                ->get();
            
            // Create doctor-category mapping for each category
            foreach ($categories as $category) {
                DB::table('doctor_category_mappings')->insert([
                    'doctor_id' => $mapping->doctor_id,
                    'category_id' => $category->id,
                    'clinic_id' => $mapping->clinic_id,
                    'charges' => $mapping->charges,
                    'status' => $mapping->status,
                    'created_by' => $mapping->created_by,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $insertedCount++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear all doctor_category_mappings created by this migration
        DB::table('doctor_category_mappings')->truncate();
    }
};
