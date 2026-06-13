<?php

namespace App\Trait;

use App\Helper\Files;
use Carbon\Carbon;
use Modules\Appointment\Models\Appointment;
use Modules\Commission\Models\Commission;
use Modules\Commission\Models\CommissionEarning;

use Currency;

trait ReportTrait
{
    public function getCommission($data)
    {
        $total_earning=0;

       if($data->clinicappointment->isNotEmpty()){

           $appointment_ids=$data->clinicappointment->pluck('id')->toArray();

    
           $total_earning=CommissionEarning::whereIn('commissionable_id',$appointment_ids)
                               ->where('user_type',$data->commission_type)
                               ->where('commission_status','!=','pending')
                               ->sum('commission_amount');

          }

        return $total_earning;

    }
    public function calculateAdminCommission($data)
    {
        // Fetch the admin commission from the database (assuming Commission model has 'type' and 'value' attributes)
        $commission = Commission::find(1);
        $commission_type = $commission->commission_type ?? '';
        $commission_value = $commission->commission_value ?? 0;
        // Calculate the doctor's commission
        $doctorCommission = calculateDoctorCommission($data);
    
        // Fetch the total amount for appointments for the clinic
        $appointments = Appointment::with('appointmenttransaction')
            ->where('clinic_id', $data->id)
            ->get();
    
        $service_amount = 0;
    
        foreach ($appointments as $appointment) {
            $service_amount += optional($appointment->appointmenttransaction)->total_amount ?? 0;
        }
    
        // Calculate the total amount after deducting the doctor's commission
        $total_amount = $service_amount - $doctorCommission;
    
        // Calculate the admin commission based on commission type
        $adminCommission = 0;
        if ($appointments->contains('clinic_id', $data->id)) {
            $clinicAppointmentCount = $appointments->where('clinic_id', $data->id)->count();
        
            if ($commission_type == 'fix') {
                // If commission type is fixed, use the fixed value
                $adminCommission = $commission_value * $clinicAppointmentCount;
            } elseif ($commission_type == 'percentage') {
                // If commission type is percentage, calculate based on total amount
                $adminCommission = $commission_value * $total_amount / 100;
            }
        }
    
    
        return $adminCommission;
    }
    
    
    public function calculateClinicCommission($data)
    {
        // Fetch the doctor and admin commission amounts
        $doctorCommission = calculateDoctorCommission($data);
        $adminCommission = calculateAdminCommission($data);
    
        // Fetch the total amount for appointments for the clinic
        $appointments = Appointment::with('appointmenttransaction')
            ->where('clinic_id', $data->id)
            ->get();
    
        $service_amount = 0;
    
        foreach ($appointments as $appointment) {
            $service_amount += optional($appointment->appointmenttransaction)->total_amount ?? 0;
        }
    
        // Calculate the total amount after deducting the doctor and admin commissions
        $total_amount = $service_amount - $doctorCommission - $adminCommission;
    
        // Return the clinic commission
        return $total_amount;
    }
}
