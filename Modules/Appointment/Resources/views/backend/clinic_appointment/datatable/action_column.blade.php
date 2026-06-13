<div class="text-end d-flex gap-3 align-items-center">
    @php
    $appointmentDate = Carbon\Carbon::parse($data->appointment_date);
    $googleMeetSetting = App\Models\Setting::where('name', 'google_meet_method')->first();
    $googleMeetEnabled = $googleMeetSetting ? $googleMeetSetting->val == 1 : false;

    $zoomSetting = App\Models\Setting::where('name', 'is_zoom')->first();
    $zoomEnabled = $zoomSetting ? $zoomSetting->val == 1 : false;
    $pay_status = optional($data->payment)->payment_status;
    @endphp

    {{-- NEW: View Full Appointment Details Button --}}
    <button type="button" class="btn btn-icon text-primary p-0 fs-5" data-bs-toggle="tooltip" title="View Full Details" onclick="viewAppointmentDetails({{ $data->id }})">
        <i class="ph ph-eye"></i>
    </button>

    {{-- @if (!in_array($data->status, ['pending', 'confirm', 'cancel']) && $data->status !== null)
    @if($data->patientEncounter !=null)
    <button type='button' data-assign-module="{{$data->patientEncounter->id}}" data-assign-target='#patient-encounter-offcanvas' data-assign-event='patient-dashboard' class='btn text-primary p-0 fs-5' data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('appointment.patient_encounter')}}"><i class="icon ph ph-squares-four align-middle"></i></button>
    @endif
    @endif --}}



    @if (!in_array($data->status, ['pending', 'confirm', 'cancel']) && $data->status !== null)
    @if($data->patientEncounter !=null)
    <a href="{{ route('backend.encounter.encounter-detail-page', ['id' => $data->patientEncounter->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('appointment.patient_encounter') }}"><i class="icon ph ph-squares-four align-middle"></i></a>
    @endif
    @endif


    @if(optional($data->clinicservice)->is_video_consultancy == 1 && $appointmentDate->isToday())
    @if($googleMeetEnabled && $data->meet_link != null)
    <a href="{{ route("backend.google_connect", ['id' => $data->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('clinic.google_meet') }}"><i class="fa-solid fa-video"></i></a>
    @endif

    @if($zoomEnabled && $data->start_video_link != null)
    <a href="{{ route("backend.zoom_connect", ['id' => $data->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('clinic.zoom_meet') }}"><i class="fa-solid fa-video"></i></a>
    @endif
    @endif
    <!-- @if(setting('view_patient_soap') == 1)
    <a href="{{route("backend.patient-record", ['id' => $data->id])}}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('clinic.appointment_patient_records') }}"><i class="fa-solid fa-notepad"></i></a>
    @endif -->

    <!-- <button type='button' data-assign-module="{{$data->id}}" data-assign-target='#appointment-offcanvas' data-assign-event='appointment-details' class='btn text-primary p-0 fs-5' data-bs-toggle='tooltip' title='Clinic Session'><i class="fa-solid fa-eye"></i></a> -->
    </button>
    @if($pay_status == 1 && $data->status == 'checkout')
    <a href="{{ route('backend.appointments.invoice_detail', ['id' => $data->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('clinic.invoice_detail') }}">
    <i class="ph ph-file-pdf"></i>
    </a>
    @endif
    @unless(auth()->user()->hasRole('doctor'))
    @hasPermission('delete_clinic_appointment_list')
    <a href="{{ route('backend.appointment.destroy', $data->id) }}"
       id="delete-{{ $module_name }}-{{ $data->id }}"
       class="btn text-danger p-0 fs-5"
       data-type="ajax"
       data-method="DELETE"
       data-token="{{ csrf_token() }}"
       data-bs-toggle="tooltip"
       title="{{ __('messages.delete') }}"
       data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->user->full_name ?? default_user_name()), 'module' => __('appointment.singular_title')]) }}">
       <i class="ph ph-trash"></i>
    </a>
    @endhasPermission
@endunless


@if($customform)

    @foreach($customform as $form)

        @php
        $formdata=json_decode($form->formdata);
        $appointment_status= json_decode($form->appointment_status);

        // Normalize the appointment status and adjust 'confirm' to 'confirmed'
        $AppointmentStatus = array_map(function ($status) {
            return strtolower(trim($status)) === 'confirm' ? 'confirmed' : strtolower(trim($status));
        }, (array) $appointment_status);
        @endphp

        @if (in_array(strtolower($data->status), $AppointmentStatus) && $data->status !== null)

        <button type="button"
                data-assign-target="#appointment-customform"
                data-assign-event="custom_form_assign"
                data-appointment-type="appointment"
                data-appointment-id="{{ $data->id }}"
                data-form-id="{{ $form->id }}"
                class="btn text-info p-0 fs-5"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ $formdata->form_title }}"
                onclick="dispatchCustomEvent(this)">
                <i class="icon ph ph-file align-middle"></i>
            </button>
        @endif
    @endforeach
@endif

</div>
