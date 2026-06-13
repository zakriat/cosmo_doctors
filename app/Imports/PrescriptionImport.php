<?php

namespace App\Imports;

use Modules\Appointment\Models\EncounterPrescription;
use Modules\Constant\Models\Constant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class PrescriptionImport implements ToModel, WithHeadingRow
{
    protected $userId;
    protected $encounterId;

    public function __construct($userId, $encounterId)
    {
        $this->userId = $userId;
        $this->encounterId = $encounterId;
    }

    public function model(array $row)
    {
      
        $encounterPrescription = EncounterPrescription::updateOrCreate([
            'user_id' => $this->userId,
            'encounter_id' => $this->encounterId,
            'name' => $row['name'],
            'frequency' => $row['frequency'],
            'duration' => $row['duration'],
            'instruction' => $row['instruction'],
        ]);

        // Create or update the Constant record
        Constant::updateOrCreate(
            ['value' => str_replace(' ', '_', $row['name']), 'type' => 'encounter_prescription'],
            ['name' => $row['name']] // You can add more fields here if needed
        );

        return $encounterPrescription;
    }
}



?>