<?php

namespace Modules\CustomForm\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\PatientEncounter;
use Modules\Appointment\Transformers\AppointmentResource;
use Modules\Appointment\Transformers\EncounterResource;

class CustomFormDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {      
        $module_data = null;

        if($this->module == 'encounter')
        {
            $module_data = PatientEncounter::where('id',$this->module_id)->first();
            $module_data = new EncounterResource($module_data);
        }
        elseif($this->module == 'appointment')
        {
            $module_data = Appointment::where('id',$this->module_id)->first();
            $module_data = new AppointmentResource($module_data);
        }

        return [
            'id' => $this->id,
            'module' => $this->module,
            'form_data' => json_decode($this->form_data),
            'module_data' => $module_data,
      
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
