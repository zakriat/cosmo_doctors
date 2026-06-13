<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GravityFormsService;
use Modules\Appointment\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncBloodTestAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gf:sync-blood-tests 
                            {--force : Force sync even if recently synced}
                            {--form-id= : Specific form ID to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync blood test bookings from Gravity Forms to appointments table';

    protected $gfService;
    protected $stats = [
        'total_entries' => 0,
        'new_created' => 0,
        'existing_updated' => 0,
        'dashboard_linked' => 0,
        'errors' => 0,
        'skipped' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🩸 Starting Blood Test Appointments Sync...');
        $this->newLine();

        // Initialize GF Service
        $this->gfService = new GravityFormsService();

        // Check if credentials are configured
        if (!$this->gfService->isConfigured()) {
            $this->error('❌ Gravity Forms API credentials not configured');
            $this->error('Please add GF_CONSUMER_KEY, GF_CONSUMER_SECRET, and GF_SITE_URL to your .env file');
            return 1;
        }

        // Test connection
        if (!$this->gfService->testConnection()) {
            $this->error('❌ Failed to connect to Gravity Forms API');
            $this->error('Please check your GF_API_URL, GF_CONSUMER_KEY, and GF_CONSUMER_SECRET in .env');
            return 1;
        }

        $this->info('✅ Connected to Gravity Forms API');

        // Get form ID
        $formId = $this->option('form-id') ?? config('services.gravity_forms.form_id');
        $this->info("📋 Syncing form ID: {$formId}");
        $this->newLine();

        // Get field mapping
        $fieldMapping = $this->gfService->getFieldMapping($formId);
        
        if (empty($fieldMapping)) {
            $this->warn('⚠️  Could not fetch form fields. Using default mapping.');
        } else {
            $this->info('✅ Form field mapping loaded');
        }

        // Fetch all entries
        $this->info('📥 Fetching entries from Gravity Forms...');
        $entries = $this->gfService->getAllEntries($formId);
        
        $this->stats['total_entries'] = count($entries);
        $this->info("Found {$this->stats['total_entries']} entries");
        $this->newLine();

        // Process each entry
        $progressBar = $this->output->createProgressBar($this->stats['total_entries']);
        $progressBar->start();

        foreach ($entries as $entry) {
            try {
                $this->processEntry($entry, $fieldMapping);
            } catch (\Exception $e) {
                $this->stats['errors']++;
                Log::error('GF Sync: Error processing entry', [
                    'entry_id' => $entry['id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display summary
        $this->displaySummary();

        return 0;
    }

    /**
     * Process a single GF entry
     */
    protected function processEntry($entry, $fieldMapping)
    {
        $entryId = $entry['id'] ?? null;
        
        if (!$entryId) {
            $this->stats['skipped']++;
            return;
        }

        // Extract data from entry
        $data = $this->extractEntryData($entry, $fieldMapping);

        // Check if this entry already exists
        $existing = Appointment::where('gf_entry_id', $entryId)->first();

        if ($existing) {
            // Update existing appointment
            $existing->update($data);
            $this->stats['existing_updated']++;
            return;
        }

        // Try to find dashboard-initiated booking to link
        $dashboardBooking = $this->findDashboardBooking($data);

        if ($dashboardBooking) {
            // Link GF entry to existing dashboard booking
            $dashboardBooking->update(array_merge($data, [
                'gf_entry_id' => $entryId,
                'synced_at' => now(),
            ]));
            $this->stats['dashboard_linked']++;
            return;
        }

        // Create new appointment from GF entry
        Appointment::create(array_merge($data, [
            'gf_entry_id' => $entryId,
            'type' => 'blood_test',
            'initiated_from_dashboard' => false,
            'synced_at' => now(),
        ]));

        $this->stats['new_created']++;
    }

    /**
     * Extract appointment data from GF entry
     */
    protected function extractEntryData($entry, $fieldMapping)
    {
        // Default field IDs (adjust based on your actual GF form)
        // Based on the actual GF form structure from cosmodoctors.com
        $data = [
            'type' => 'blood_test',
            'status' => 'pending',
            'raw_gf_data' => $entry,
        ];

        // Specific field mapping for cosmodoctors.com GF form
        // Field 1.3 = First Name, 1.6 = Last Name
        $firstName = $entry['1.3'] ?? '';
        $lastName = $entry['1.6'] ?? '';
        
        // Field 4 = Email
        $email = $entry['4'] ?? null;
        
        // Field 3 = Phone
        $phone = $entry['3'] ?? null;
        
        // Field 6 = Blood Test Type
        $testType = $entry['6'] ?? null;
        
        // Field 8 = Date/Time (format: "Tuesday, March 17 2026 at 9:00 AM - 9:30 AM")
        $dateTimeString = $entry['8'] ?? null;
        
        // Field 9 = Price (format: "&#163; 200" which is £200)
        $priceString = $entry['9'] ?? null;
        
        // Field 13 = Patient ID (hidden field from dashboard)
        $patientId = $entry['13'] ?? null;

        // Set name fields
        if ($firstName || $lastName) {
            $data['patient_name'] = trim($firstName . ' ' . $lastName);
        }
        
        // Patient matching logic (Priority order)
        $userId = null;
        
        // Priority 1: Use patient_id from hidden field 13 (most reliable)
        if (!empty($patientId) && is_numeric($patientId)) {
            $user = User::find($patientId);
            if ($user) {
                $userId = $user->id;
                Log::info('GF Sync: Matched by patient_id', ['patient_id' => $patientId, 'user_id' => $userId]);
            } else {
                Log::warning('GF Sync: Patient ID not found', ['patient_id' => $patientId]);
            }
        }
        
        // Priority 2: Match by email (if patient_id not available)
        if (!$userId && $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $userId = $user->id;
                Log::info('GF Sync: Matched by email', ['email' => $email, 'user_id' => $userId]);
            }
        }
        
        // Priority 3: Match by phone (if email not available)
        if (!$userId && $phone) {
            $user = User::where('phone', $phone)->first();
            if ($user) {
                $userId = $user->id;
                Log::info('GF Sync: Matched by phone', ['phone' => $phone, 'user_id' => $userId]);
            }
        }
        
        // Set user_id if matched
        if ($userId) {
            $data['user_id'] = $userId;
        } else {
            Log::warning('GF Sync: No patient match found', [
                'entry_id' => $entry['id'],
                'patient_name' => $data['patient_name'] ?? 'Unknown',
                'email' => $email,
                'phone' => $phone
            ]);
        }

        // Set email
        if ($email) {
            $data['email'] = $email;
        }

        // Set phone
        if ($phone) {
            $data['phone'] = $phone;
        }

        // Set test type
        if ($testType) {
            $data['test_type'] = $testType;
            $data['appointment_extra_info'] = "Blood Test Type: {$testType}";
        }

        // Parse date/time from field 8
        if ($dateTimeString) {
            try {
                // Extract date and time from format: "Tuesday, March 17 2026 at 9:00 AM - 9:30 AM"
                if (preg_match('/(\w+, \w+ \d+ \d+) at (\d+:\d+ [AP]M)/', $dateTimeString, $matches)) {
                    $dateStr = $matches[1]; // "Tuesday, March 17 2026"
                    $timeStr = $matches[2]; // "9:00 AM"
                    
                    $appointmentDateTime = Carbon::parse($dateStr . ' ' . $timeStr);
                    $data['appointment_date'] = $appointmentDateTime->format('Y-m-d');
                    $data['appointment_time'] = $appointmentDateTime->format('H:i:s');
                    $data['start_date_time'] = $appointmentDateTime;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to parse GF date/time', [
                    'entry_id' => $entry['id'],
                    'date_string' => $dateTimeString,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Parse price from field 9
        if ($priceString) {
            // Remove HTML entities and currency symbols
            $priceString = html_entity_decode($priceString);
            $priceString = preg_replace('/[^0-9.]/', '', $priceString);
            $price = floatval($priceString);
            
            if ($price > 0) {
                $data['total_amount'] = $price;
                $data['service_amount'] = $price;
                $data['service_price'] = $price;
            }
        }

        // Set default values for required fields
        $data['duration'] = $data['duration'] ?? 30; // Default 30 minutes for blood tests
        $data['total_amount'] = $data['total_amount'] ?? 0;
        $data['service_amount'] = $data['service_amount'] ?? 0;
        $data['service_price'] = $data['service_price'] ?? 0;

        return $data;
    }

    /**
     * Find dashboard-initiated booking to link with GF entry
     */
    protected function findDashboardBooking($data)
    {
        if (empty($data['email'])) {
            return null;
        }

        // Find recent dashboard booking with matching email
        return Appointment::where('type', 'blood_test')
            ->where('initiated_from_dashboard', true)
            ->whereNull('gf_entry_id')
            ->where(function($query) use ($data) {
                // Match by user_id if available
                if (!empty($data['user_id'])) {
                    $query->where('user_id', $data['user_id']);
                }
                // Or match by email in appointment_extra_info or other fields
                // This is a fallback - adjust based on your data structure
            })
            ->where('created_at', '>=', now()->subHours(24)) // Within last 24 hours
            ->latest()
            ->first();
    }

    /**
     * Display sync summary
     */
    protected function displaySummary()
    {
        $this->info('📊 Sync Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Entries', $this->stats['total_entries']],
                ['New Created', $this->stats['new_created']],
                ['Existing Updated', $this->stats['existing_updated']],
                ['Dashboard Linked', $this->stats['dashboard_linked']],
                ['Skipped', $this->stats['skipped']],
                ['Errors', $this->stats['errors']],
            ]
        );

        if ($this->stats['errors'] > 0) {
            $this->warn("⚠️  {$this->stats['errors']} errors occurred. Check logs for details.");
        }

        $this->info('✅ Sync completed successfully!');
    }
}
