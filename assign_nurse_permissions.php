<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Role;
use App\Models\Permission;

echo "🔧 Assigning nurse permissions to admin role...\n";

// Get admin role
$adminRole = Role::where('name', 'admin')->first();
if (!$adminRole) {
    echo "❌ Admin role not found!\n";
    exit(1);
}

// Get nurse permissions
$nursePermissions = Permission::where('name', 'like', '%nurse%')->get();
if ($nursePermissions->isEmpty()) {
    echo "❌ No nurse permissions found!\n";
    exit(1);
}

echo "📋 Found " . $nursePermissions->count() . " nurse permissions:\n";
foreach ($nursePermissions as $permission) {
    echo "   • " . $permission->name . "\n";
}

// Assign permissions to admin role
foreach ($nursePermissions as $permission) {
    if (!$adminRole->hasPermissionTo($permission->name)) {
        $adminRole->givePermissionTo($permission->name);
        echo "✅ Assigned: " . $permission->name . "\n";
    } else {
        echo "⚠️  Already has: " . $permission->name . "\n";
    }
}

echo "🎉 All nurse permissions assigned to admin role!\n";