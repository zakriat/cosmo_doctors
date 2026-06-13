<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billing_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billing_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name')->nullable();
            $table->double('discount_value')->nullable();
            $table->string('discount_type')->nullable();
            $table->integer('quantity')->default(0);
            $table->double('service_amount')->default(0);
            $table->double('total_amount')->default(0);

            $table->timestamps();
        });

        $providerRole = Role::where('name', 'doctor')->first();

        if ($providerRole) {
                $permissions = Permission::whereIn('name', [
                    'view_doctor_payouts',
                ])->get();
                foreach ($permissions as $permission) {
                    $perm = Permission::where('name', $permission)->first();
                    if ($perm) {
                        // Assign the permission to the provider role
                        DB::table('role_has_permissions')->updateOrCreate(
                            [
                                'permission_id' => $perm->id,
                                'role_id' => $providerRole->id,
                            ]
                        );
                    }
                }

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_item');
    }
};
