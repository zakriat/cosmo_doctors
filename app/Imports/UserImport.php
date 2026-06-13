<?php
namespace App\Imports;

use App\Models\User;
use App\Notifications\UserAccountCreated;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class UserImport implements ToModel, WithHeadingRow
{
    use SkipsErrors;

    private $importedUsers = [];
    private $requiredHeaders = ['first_name', 'last_name', 'email', 'mobile', 'password'];

    /**
     * Validate headers before processing rows.
     */
    public function headingRow(): int
    {
        return 1; // Assuming the first row contains headers
    }

    public function validateHeaders(array $headers)
    {
        foreach ($this->requiredHeaders as $requiredHeader) {
            if (!in_array($requiredHeader, $headers)) {
                throw ValidationException::withMessages([
                    'headers' => "Missing required column: $requiredHeader",
                ]);
            }
        }
    }

    /**
     * Process each row.
     */
    public function model(array $row)
    {
        // Check headers before processing the rows
        static $headersValidated = false;

        if (!$headersValidated) {
            $headersValidated = true;
            $this->validateHeaders(array_keys($row));
        }

        // Define required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'mobile','password'];

        foreach ($requiredFields as $field) {
            if (empty($row[$field])) {
                // Skip this row due to missing data
                return null;
            }
        }

        if (!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages([
                'headers' => "Invalid email format: {$row['email']}",
            ]);
        }
    
        // Check for unique email
        $existingUser = User::where('email', $row['email'])->first();
        if ($existingUser) {
            throw ValidationException::withMessages([
                'headers' => "Email already exists: {$row['email']}",
            ]);
        }
         
        $dateOfBirth = null;
        if (!empty($row['date_of_birth'])) {
            try {
                if (is_numeric($row['date_of_birth'])) {
                    $dateOfBirth = Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', ($row['date_of_birth'] - 25569) * 86400))->format('Y-m-d');
                } else {
                    $dateOfBirth = Carbon::parse($row['date_of_birth'])->format('Y-m-d');
                }
           
                  // Check if the date of birth is in the future
                  if (Carbon::parse($dateOfBirth)->isFuture()) {
                    throw ValidationException::withMessages([
                        'headers' => "Date of birth cannot be in the future: {$row['date_of_birth']}",
                    ]);
                }
            } catch (\Exception $e) {
                if (Carbon::parse($dateOfBirth)->isFuture()) {
                    throw ValidationException::withMessages([
                        'headers' => "Date of birth cannot be in the future: {$row['date_of_birth']}",
                    ]);
                }
                throw ValidationException::withMessages([
                    'headers' => "Invalid date format for date_of_birth: {$row['date_of_birth']}",
                ]);
            }
        }

        $user = User::updateOrCreate(
            ['email' => $row['email']],
            [
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'mobile' => $row['mobile'],
                'gender' => strtolower($row['gender']) ?? null,
                'date_of_birth' => $dateOfBirth ?? null,
                'password' => $row['password'] ? Hash::make($row['password']) : Hash::make('12345678'),
                'user_type' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        if ($user) {

            if ($user->wasRecentlyCreated) {
                // Assign role only when the user is newly created
                $user->assignRole('user');
            }
                $this->importedUsers[] = [
                'user' => $user,
                'plaintext_password' => $row['password'],
            ];
        }

        return $user;
    }

    public function getImportedUsers()
    {
        return $this->importedUsers;
    }
}

