@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection
@section('content')
    <div class="row">
        <div class="col-xxl-3 col-lg-4 col-md-5">
            <h4 class=" mb-3">{{ __('appointment.encounter_detail') }}</h4>
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-1">{{ __('appointment.about_clinic') }}</h6>
                </div>
                <div class="card-body">
                    <ul class="list-inline m-0 p-0">
                        <li class="item mb-5 pb-5 border-bottom">
                            <div>
                                <div class="d-flex gap-3 align-items-center">
                                    <img src="{{ optional($data->clinic)->file_url }}" alt="avatar"
                                        class="avatar avatar-64 rounded">
                                    <div class="text-start">
                                        <h5 class="m-0">{{ optional($data->clinic)->name ?? '--' }}</h6>
                                            <p class="mb-2"> {{ optional($data->clinic)->email ?? '--' }}</p>
                                            <h5 class="m-0">Dr. {{ optional($data->doctor)->full_name ?? '--' }}</h6>
                                    </div>
                                </div>
                                <div class="mb-1">{{ $data->description }}</div>
                            </div>
                        </li>
                        <li class="item mb-1">
                            <div>
                                <h4 class="mb-3">{{ __('appointment.about_patient') }}</h6>
                                    <div class="d-flex gap-3 align-items-center">
                                        <img src="{{ optional($data->user)->profile_image ?? default_user_avatar() }}"
                                            alt="avatar" class="avatar avatar-64 rounded-pill">
                                        <div class="text-start">
                                            <h5 class="m-0">
                                                {{ optional($data->user)->full_name ?? default_user_name() }}</h5>
                                            <p class="mb-0">{{ optional($data->user)->email ?? '--' }}</p>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-4 mb-3">
                                        <p class="mb-0">{{ __('appointment.encounter_date') }}:</p>
                                        <span class="heading-color">{{ formatDate($data->encounter_date) ?? '--' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-4 mb-3">
                                        <p class="mb-0">{{ __('service.title') }}:</p>
                                        <p class="mb-0">{{ $data->appointmentdetail->clinicservice->systemservice->name ?? '--' }} </p>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-4 mb-3">
                                        <p class="mb-0">{{ __('appointment.lbl_appitment_id') }}:</p>
                                        <p class="mb-0">{{ $data->appointmentdetail->id ?? '--' }}</p>
                                    </div>
                                    @if (!empty($data->user) && (!empty($data->user->address) || !empty($data->user->cities?->name) || !empty($data->user->countries?->name) || !empty($data->user->pincode)))
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-3">
                                        <p class="mb-0">{{ __('appointment.address') }}:</p>
                                        <div class="heading-color text-end">
                                            {{ $data->user->address ?? '' }}
                                            @if (!empty($data->user->address) && (!empty($data->user->cities?->name) || !empty($data->user->countries?->name) || !empty($data->user->pincode))),@endif
                                            {{ $data->user->cities->name ?? '' }}
                                            @if (!empty($data->user->cities?->name) && (!empty($data->user->countries?->name) || !empty($data->user->pincode))),@endif
                                            {{ $data->user->countries->name ?? '' }}
                                            @if (!empty($data->user->countries?->name) && !empty($data->user->pincode)),@endif
                                            {{ $data->user->pincode ?? '' }}
                                        </div>
                                    </div>
                                    @endif
                         </div>
                        </li>
                        <li class="item">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                <p class="mb-0">{{ __('appointment.encounter_status') }}:</p>
                                @if ($data->status == 1)
                                    <span class="text-success">{{ __('appointment.open') }}</span>
                                @else
                                    <span v-else class="text-danger"> {{ __('appointment.close') }}</span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            @if ($data['status'] == 1)
                <div class="card encounter-temeplate">
                    <div class="card-body">
                        <h6>{{ __('appointment.select_encounter_templates') }}</h6>
                        <select name="template_id" id="template_id" class="select2 form-select"
                            placeholder="{{ __('clinic.lbl_select_template') }}" data-filter="select">
                            <option value="">{{ __('clinic.lbl_select_template') }}</option>
                            @foreach ($template_data as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
        </div>
         <div class="col-xxl-9 col-lg-8 col-md-7">
            <h4 class=" mb-3">{{ __('appointment.other_detail') }}</h4>

            <div>

                <nav class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div class="nav nav-tabs bg-transparent gap-4" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                            type="button" role="tab" aria-controls="nav-home" aria-selected="true">{{ __('appointment.clinic_details') }}
                            </button>
                        @if(soap() == 1)
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                            type="button" role="tab" aria-controls="nav-profile" aria-selected="false">{{ __('appointment.soap') }}</button>
                        @endif
                        @if(bodychart() == 1)
                        <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact"
                            type="button" role="tab" aria-controls="nav-contact" aria-selected="false">{{ __('appointment.body_chart') }}</button>
                        @endif

                        @if (count($data['customform']) > 0)
                            <button class="nav-link" id="nav-custom-form-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-custom-form" type="button" role="tab"
                                aria-controls="nav-custom-form" aria-selected="false">Custom Form</button>
                        @endif

                    </div>
                    @if ($data['status'] == 1)
                        {{-- Original billing modal button - commented out --}}
                        {{-- <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#generate_invoice">
                            <div class="d-inline-flex align-items-center gap-1">
                                <i class="ph ph-plus"></i>
                                {{ __('appointment.close_encounter') }} & {{ __('appointment.check_out') }}
                            </div>
                        </button> --}}
                        
                        {{-- New direct close button --}}
                        <button class="btn btn-secondary" onclick="closeEncounterDirectly({{ $data['id'] }})">
                            <div class="d-inline-flex align-items-center gap-1">
                                <i class="ph ph-plus"></i>
                                {{ __('appointment.close_encounter') }} & {{ __('appointment.check_out') }}
                            </div>
                        </button>
                    @else
                        <a href="{{ url('app/billing-record/encounter_billing_detail') }}?id={{ $data['id'] }}">
                            <button class="btn btn-primary">
                                <i class="ph ph-file-text me-1"></i>
                                {{ __('appointment.billing_details') }}
                            </button>
                        </a>
                    @endif
                </nav>
                <div class="card"  >
                    <div class="card-body">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                aria-labelledby="nav-home-tab" tabindex="0">

                                <div class="row">

                                    @if ($encounter_data['is_encounter_problem'] == 1)
                                        <div class="col-xl-4 col-lg-6" id="encounter_problem">
                                            @include(
                                                'appointment::backend.patient_encounter.component.encounter_problem',
                                                ['data' => $data, 'problem_list' => $problem_list]
                                            )
                                        </div>
                                    @endif

                                    @if ($encounter_data['is_encounter_observation'] == 1)
                                        <div class="col-xl-4 col-lg-6" id="encounter_observation">
                                            @include(
                                                'appointment::backend.patient_encounter.component.encounter_observation',
                                                ['data' => $data, 'observation_list' => $observation_list]
                                            )
                                        </div>
                                    @endif

                                    @if ($encounter_data['is_encounter_note'] == 1)
                                        <div class="col-xl-4" id="encounter_note">
                                            @include(
                                                'appointment::backend.patient_encounter.component.encounter_note',
                                                ['data' => $data]
                                            )
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-4">

                                    <div class="card-header px-0 mb-3 d-flex justify-content-between flex-wrap gap-3">
                                        <h5 class="card-title">{{ __('appointment.medical_report') }}</h5>
                                        <span>Please upload medical records, scans, any test results or medical reports</span>
                                        @if ($data['status'] == 1)
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addMedicalreport">
                                                <div class="d-inline-flex align-items-center gap-1">
                                                    <i class="ph ph-plus"></i>
                                                    {{ __('appointment.add_medical_report') }}
                                                </div>
                                            </button>
                                        @endif
                                    </div>

                                    <div class="card-body bg-body" style="padding: 1px">
                                        <div id="medical_report_table">
                                            @include(
                                                'appointment::backend.patient_encounter.component.medical_report_table',
                                                ['data' => $data]
                                            )
                                        </div>
                                    </div>
                                </div>

                                @if(prescription() == 1)
                                    <div class="mb-4">
                                        <div class="card-header d-flex justify-content-between flex-wrap gap-3 px-0 mb-3  ">
                                            <h5 class="card-title">{{ __('appointment.prescription') }}</h5>
                                            <div>
                                                <div class="d-flex align-items-center flex-wrap gap-3">
                                                    @if ($data['status'] == 1)
                                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                            data-bs-target="#addprescription">
                                                            <div class="d-inline-flex align-items-center gap-1">
                                                                <i class="ph ph-plus"></i>
                                                                {{ __('appointment.add_prescription') }}
                                                            </div>
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body bg-body" style="padding: 1px">
                                            <div id="prescription_table">
                                                @include(
                                                    'appointment::backend.patient_encounter.component.prescription_table',
                                                    ['data' => $data]
                                                )
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="other-detail">
                                    <div class="card-header px-0 mb-3">
                                        <h6 class="card-title mb-0">{{ __('appointment.other_information') }}</h6>
                                    </div>
                                    <div class="">
                                        <textarea 
                                            class="form-control h-auto bg-body" 
                                            rows="3"
                                            placeholder="{{ __('appointment.enter_other_details') }}" 
                                            name="other_details" 
                                            id="other_details"
                                            style="min-height: max-content"
                                            @if(isset($data['status']) && $data['status'] != 1) disabled @endif
                                        >{{ old('other_details', $data['EncounterOtherDetails']['other_details'] ?? '') }}</textarea>
                                    </div>
                                </div>

                                @if ($data['status'] == 1)
                                    <div class="offcanvas-footer border-top pt-4" id="save_button">
                                        <div class="d-grid d-sm-flex justify-content-sm-end gap-3">
                                            <button class="btn btn-secondary" type="submit">
                                                {{ __('messages.save') }}
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                aria-labelledby="nav-profile-tab" tabindex="0">
                                <div id="soap">

                                    @include('appointment::backend.patient_encounter.component.soap', [
                                        'data' => $data,
                                    ])

                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-contact" role="tabpanel"
                                aria-labelledby="nav-contact-tab" tabindex="0">

                                <div id="body_chart_list">

                                    @include(
                                        'appointment::backend.patient_encounter.component.body_chart_list',
                                        ['data' => $data]
                                    )

                                </div>


                                {{-- <div id="add_body_chart" class="" >

                                    @include('appointment::backend.clinic_appointment.apointment_bodychartform', [
                                        'encounter_id' => $data['id'],
                                        'appointment_id' => $data['appointment_id'],
                                        'patient_id' => $data['user_id']
                                    ])

                                </div> --}}


                            </div>

                            <div class="tab-pane fade" id="nav-custom-form" role="tabpanel"
                                aria-labelledby="nav-custom-form-tab" tabindex="0">
                                <div id="custom_form">

                                    @include(
                                        'appointment::backend.patient_encounter.component.custom_form',
                                        [
                                            'data' => $data['customform'],
                                            'encounter_id' => $data['id'],
                                            'appointment_id' => $data['appointment_id'],
                                        ]
                                    )
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        @include('appointment::backend.patient_encounter.component.prescription', ['data' => $data])

        @include('appointment::backend.patient_encounter.component.medical_report', ['data' => $data])

        {{-- Billing modal commented out - direct close functionality implemented --}}
        {{-- @include('appointment::backend.patient_encounter.component.billing_details', ['data' => $data]) --}}
    </div>

    @endsection

    @push('after-styles')
     
    @endpush

    @push('after-scripts')
        <script>
            // Direct encounter close function
            function closeEncounterDirectly(encounterId) {
                confirmSwal('Are you sure you want to close this encounter? This action cannot be undone.').then((result) => {
                    if (!result.isConfirmed) return;
                    
                    const data = {
                        encounter_id: encounterId,
                        _token: '{{ csrf_token() }}'
                    };

                    fetch('{{ route('backend.encounter.close-encounter-direct') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': data._token
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.successSnackbar('Encounter closed successfully');
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            window.errorSnackbar(data.message || 'Something went wrong! Please check.');
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        window.errorSnackbar('Something went wrong! Please check.');
                    });
                });
            }

            document.getElementById('save_button').addEventListener('click', function() {
                const encounterId = {{ $data->id }};
                const userId = {{ $data->user_id }};
                const template_id = document.getElementById('template_id').value;
                const other_details = document.getElementById('other_details').value;

                const data = {
                    encounter_id: encounterId,
                    template_id: template_id,
                    other_details: other_details,
                    user_id: userId,
                    _token: '{{ csrf_token() }}'
                };

                fetch('{{ route('backend.encounter.save-encounter') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': data._token
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data) {

                            window.successSnackbar(`Encounter saved successfully`);

                        } else {
                            window.errorSnackbar('Something went wrong! Please check.');
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const hash = window.location.hash;
                if (hash) {
                    const tabButton = document.querySelector(`[data-bs-target="${hash}"]`);
                    if (tabButton) {

                        tabButton.click();

                        const tabContent = document.querySelector(hash);
                        if (tabContent) {
                            tabContent.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }
                    }
                }
            });

            $(document).ready(function() {
                var baseUrl = '{{ url('/') }}';
                $('#template_id').change(function() {
                    let templateId = $(this).val();
                    let additionalData = {
                        user_id: '{{ $data['user_id'] ?? '' }}', // Use null coalescing operator for safety
                        encounter_id: '{{ $data['id'] ?? '' }}', // Same for the encounter ID
                        status: '{{ $data['status'] ?? '' }}',

                    };
                    // Clear the components section


                    if (templateId) {
                        $.ajax({
                            url: baseUrl + `/app/encounter/get-template-data/${templateId}`,
                            type: 'GET',
                            data: additionalData,
                            success: function(response) {
                                console.log(response);
                                // Append problems if available
                                if (response.is_encounter_problem) {
                                    $('#encounter_problem').html('');
                                    $('#encounter_problem').append(response.problem_html);
                                }
                                // Append observations if available
                                if (response.is_encounter_observation) {

                                    console.log(response.observation_html);
                                    $('#encounter_observation').html('');
                                    $('#encounter_observation').append(response.observation_html);
                                }
                                // Append notes if available
                                if (response.is_encounter_note) {
                                    $('#encounter_note').html('');
                                    $('#encounter_note').append(response.note_html);
                                }

                                if (response.is_encounter_precreption) {
                                    $('#prescription_table').html('');
                                    $('#prescription_table').append(response.precreption_html);
                                }
                                if (response.is_encounter_otherdetail) {

                                    $('#other_details').val(response.other_detail_html);
                                }
                            },
                            error: function() {
                                console.error('Failed to load template data.');
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
