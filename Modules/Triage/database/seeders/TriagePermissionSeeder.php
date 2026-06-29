<?php

namespace Modules\Triage\database\seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TriagePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Nurse-facing
            'view_triage_queue',
            'add_triage',
            'edit_triage',
            'escalate_triage',
            // Admin-facing
            'view_triage_category',
            'add_triage_category',
            'edit_triage_category',
            'delete_triage_category',
            // Extra
            'delete_triage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Nurse gets queue + triage CRUD + escalate
        $nursePerms = [
            'view_triage_queue',
            'add_triage',
            'edit_triage',
            'escalate_triage',
        ];
        $nurseRole = Role::where('name', 'nurse')->first();
        if ($nurseRole) {
            $nurseRole->givePermissionTo($nursePerms);
        }

        // Admin + demo_admin get everything
        $adminPerms = $permissions;
        foreach (['admin', 'demo_admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($adminPerms);
            }
        }

        // Receptionist gets view + add + edit (same as nurse, no delete)
        $receptionistPerms = [
            'view_triage_queue',
            'add_triage',
            'edit_triage',
        ];
        $receptionistRole = Role::where('name', 'receptionist')->first();
        if ($receptionistRole) {
            $receptionistRole->givePermissionTo($receptionistPerms);
        }

        // Doctor gets view only
        $doctorRole = Role::where('name', 'doctor')->first();
        if ($doctorRole) {
            $doctorRole->givePermissionTo(['view_triage_queue']);
        }
    }
}
