<?php

// Simple test to check if our API endpoints are working
echo "Testing Enhanced Booking API Endpoints\n";
echo "=====================================\n\n";

// Test the API endpoints
$baseUrl = 'http://127.0.0.1:8000';

// Get the first service ID from database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
    
    // Get a service that has categories
    $stmt = $pdo->query("
        SELECT s.id, s.name, COUNT(c.id) as category_count 
        FROM clinics_services s 
        LEFT JOIN clinics_categories c ON c.parent_id = s.id 
        WHERE s.status = 1 
        GROUP BY s.id, s.name 
        HAVING category_count > 0 
        LIMIT 1
    ");
    
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($service) {
        echo "Testing service: {$service['name']} (ID: {$service['id']})\n";
        echo "Categories found: {$service['category_count']}\n\n";
        
        // Test categories API
        $url = "{$baseUrl}/api/services/{$service['id']}/categories";
        echo "Testing: {$url}\n";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'ignore_errors' => true
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if ($data && isset($data['success']) && $data['success']) {
                echo "✓ Categories API working\n";
                echo "  Found {count($data['categories'])} categories\n";
                
                // Test first category's doctors
                if (!empty($data['categories'])) {
                    $firstCategory = $data['categories'][0];
                    $categoryUrl = "{$baseUrl}/api/categories/{$firstCategory['id']}/doctors";
                    echo "\nTesting: {$categoryUrl}\n";
                    
                    $doctorResponse = file_get_contents($categoryUrl, false, $context);
                    if ($doctorResponse !== false) {
                        $doctorData = json_decode($doctorResponse, true);
                        if ($doctorData && isset($doctorData['success'])) {
                            echo "✓ Doctors API working\n";
                            echo "  Found " . count($doctorData['doctors'] ?? []) . " doctors\n";
                        } else {
                            echo "✗ Doctors API returned error\n";
                        }
                    } else {
                        echo "✗ Doctors API request failed\n";
                    }
                }
            } else {
                echo "✗ Categories API returned error: " . ($data['message'] ?? 'Unknown error') . "\n";
            }
        } else {
            echo "✗ Categories API request failed\n";
        }
    } else {
        echo "No services with categories found in database\n";
        echo "Run the seeder first: php artisan db:seed --class=\"Modules\\Clinic\\Database\\Seeders\\EnhancedBookingSeeder\"\n";
    }
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    echo "Please update database credentials in this test file\n";
}

echo "\n\nTo test the booking page:\n";
echo "1. Visit: {$baseUrl}/booking/[service_id]\n";
echo "2. Replace [service_id] with an actual service ID\n";
echo "3. Check browser console for any JavaScript errors\n";
echo "4. Verify that categories load if the service has them\n";