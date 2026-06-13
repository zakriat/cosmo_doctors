<?php

namespace Database\Seeders\Auth;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seed.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $admin = Role::firstOrCreate(['name' => 'admin', 'title' => 'Admin', 'is_fixed' => true]);
        $demo_admin = Role::firstOrCreate(['name' => 'demo_admin', 'title' => 'Demo Admin', 'is_fixed' => true]);
        $user = Role::firstOrCreate(['name' => 'user', 'title' => 'Patient', 'is_fixed' => true]);
        $doctor = Role::firstOrCreate(['name' => 'doctor', 'title' => 'doctor', 'is_fixed' => true]);
        $vendor = Role::firstOrCreate(['name' => 'vendor', 'title' => 'Clinic Admin', 'is_fixed' => true]);
        $shopmanager = Role::firstOrCreate(['name' => 'shopmanager', 'title' => 'shopmanager', 'is_fixed' => true]);
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'title' => 'receptionist', 'is_fixed' => true]);

        Permission::firstOrCreate(['name' => 'edit_settings', 'is_fixed' => true]);
        Permission::firstOrCreate(['name' => 'view_logs', 'is_fixed' => true]);

        $modules = config('constant.MODULES');

        foreach ($modules as $key => $module) {
            $permissions = ['view', 'add', 'edit', 'delete'];
            $module_name = strtolower(str_replace(' ', '_', $module['module_name']));
            foreach ($permissions as $key => $value) {
                $permission_name = $value . '_' . $module_name;
                Permission::firstOrCreate(['name' => $permission_name, 'is_fixed' => true]);
            }
            if (isset($module['more_permission']) && is_array($module['more_permission'])) {
                foreach ($module['more_permission'] as $key => $value) {
                    $permission_name = $module_name . '_' . $value;
                    Permission::firstOrCreate(['name' => $permission_name, 'is_fixed' => true]);
                }
            }

            if ($module['module_name'] === 'Clinic Categories') {
                $permission_name = 'view_' . $module_name;
                Permission::firstOrCreate(['name' => $permission_name, 'is_fixed' => true]);
            }
        }

        // Assign Permissions to Roles
        $admin->givePermissionTo(Permission::get());

        $allPermissions = Permission::get();
        $excludePermissions = ['setting_payment_method', 'setting_other_setting'];

        $filteredPermissions = $allPermissions->filter(function ($permission) use ($excludePermissions) {
            return !in_array($permission->name, $excludePermissions);
        });

        $demo_admin->givePermissionTo($filteredPermissions);
        $vendor->givePermissionTo([
            'view_clinics_center',
            'add_clinics_center',
            'edit_clinics_center',
            'delete_clinics_center',
            'view_setting',
            'setting_quick_booking',
            'setting_holiday',
            'setting_telemed_service',
            'view_clinics_service',
            'add_clinics_service',
            'edit_clinics_service',
            'delete_clinics_service',
            'view_doctors',
            'add_doctors',
            'edit_doctors',
            'delete_doctors',
            'view_doctors_session',
            'add_doctors_session',
            'edit_doctors_session',
            'delete_doctors_session',
            'view_request_service',
            'edit_request_service',
            'add_request_service',
            'delete_request_service',
            'view_doctor_earning',
            'view_doctor_payouts',
            'view_clinic_appointment_list',
            'add_clinic_appointment_list',
            'delete_clinic_appointment_list',
            'view_customer',
            'edit_customer',
            'add_customer',
            'delete_customer',
            'view_clinic_receptionist_list',
            'add_clinic_receptionist_list',
            'edit_clinic_receptionist_list',
            'delete_clinic_receptionist_list',
            'view_clinic_nurse_list',
            'add_clinic_nurse_list',
            'edit_clinic_nurse_list',
            'delete_clinic_nurse_list',
            'view_encounter',
            'add_encounter',
            'edit_encounter',
            'delete_encounter',
            'view_vendor_payouts',
            'add_vendor_payouts',
            'edit_vendor_payouts',
            'delete_vendor_payouts',
            'view_doctor_payouts',
            'add_doctor_payouts',
            'edit_doctor_payouts',
            'delete_doctor_payouts',
            'view_request_service',
            'add_request_service',
            'edit_request_service',
            'delete_request_service',
            'view_setting',
            'add_setting',
            'edit_setting',
            'delete_setting',
            'view_billing_record',
            'add_billing_record',
            'edit_billing_record',
            'delete_billing_record',

        ]);
        $doctor->givePermissionTo([
            'view_clinics_center',
            'view_clinics_service',
            'view_clinic_appointment_list',
            'add_clinic_appointment_list',
            'add_customer',
            'view_customer',
            'view_setting',
            'add_setting',
            'edit_setting',
            'delete_setting',
            'setting_doctor_holiday',
            'view_encounter',
            'add_encounter',
            'edit_encounter',
            'delete_encounter',
            'view_billing_record',
            'add_billing_record',
            'edit_billing_record',
            'delete_billing_record',
            'view_doctors_session',
            'edit_doctors_session',
            'view_reviews',
            'view_notification',
            'view_doctor_payouts',

        ]);

        $receptionist->givePermissionTo([
            'view_clinics_center',
            'view_clinic_appointment_list',
            'add_clinic_appointment_list',
            'delete_clinic_appointment_list',
            'view_doctors',
            'add_doctors',
            'edit_doctors',
            'delete_doctors',
            'view_doctors_session',
            'add_doctors_session',
            'edit_doctors_session',
            'delete_doctors_session',
            'view_customer',
            'edit_customer',
            'add_customer',
            'delete_customer',
            'view_clinics_service',
            'add_clinics_service',
            'edit_clinics_service',
            'delete_clinics_service',
            'edit_clinics_center',
            'view_encounter',
            'view_billing_record',

        ]);

        $user->givePermissionTo([
            'view_clinic_appointment_list',
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
