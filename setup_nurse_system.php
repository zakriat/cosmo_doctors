<?php
/**
 * Nurse System Setup Script
 * Run this script to set up the complete nurse system
 * 
 * Usage: php setup_nurse_system.php
 */

echo "🏥 Nurse System Setup\n";
echo "====================\n\n";

echo "This script will:\n";
echo "✅ Run database migrations\n";
echo "✅ Create nurse role and permissions\n";
echo "✅ Create sample nurse users\n";
echo "✅ Set up all relationships\n\n";

echo "📋 Prerequisites:\n";
echo "• Laravel application is set up\n";
echo "• Database is configured\n";
echo "• Spatie Permission package is installed\n\n";

$confirm = readline("Do you want to continue? (y/N): ");

if (strtolower($confirm) !== 'y') {
    echo "❌ Setup cancelled.\n";
    exit(1);
}

echo "\n🚀 Starting setup...\n\n";

// Step 1: Run migrations
echo "📝 Running database migrations...\n";
$output = shell_exec('php artisan migrate --force 2>&1');
echo $output;

// Step 2: Run nurse seeder
echo "\n👩‍⚕️ Setting up nurse system...\n";
$output = shell_exec('php artisan db:seed --class=NurseSeeder --force 2>&1');
echo $output;

echo "\n🎉 Setup Complete!\n\n";

echo "📋 What was created:\n";
echo "✅ nurses table\n";
echo "✅ nurse role with permissions\n";
echo "✅ 5 sample nurse users\n";
echo "✅ User model relationships\n\n";

echo "🔐 Test Login Credentials:\n";
echo "Email: sarah.johnson@clinic.com\n";
echo "Password: password123\n\n";

echo "🏥 Next Steps:\n";
echo "1. Create nurse management controllers\n";
echo "2. Add nurse menu items to your admin panel\n";
echo "3. Implement nurse-specific features\n";
echo "4. Test the login with sample nurse accounts\n\n";

echo "📚 Files Created:\n";
echo "• database/migrations/2025_12_30_000001_create_nurses_table.php\n";
echo "• database/seeders/Auth/NurseRoleSeeder.php\n";
echo "• database/seeders/Auth/NurseDataSeeder.php\n";
echo "• database/seeders/NurseSeeder.php\n";
echo "• app/Models/Nurse.php\n\n";

echo "✨ Nurse system is ready to use!\n";
?>