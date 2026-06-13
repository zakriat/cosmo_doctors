@extends('backend.layouts.app')
@section('title')
  {{ __($module_title) }}
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
    <h4>{{$data['patientInfo']['name']}}  {{ __('customer.overview') }}</h4>
    <a href="{{ route('backend.customers.index') }}" class="btn btn-primary">{{ __('messages.back') }}
    </a>
</div>


<ul class="nav nav-pills mb-4 patient-overview-tab" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
        <div class="d-flex align-items-center">
            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                <i class="ph ph-notebook"></i>
                <span>{{ __('customer.overview') }}</span></button>
        </div>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
      <i class="ph ph-users-three"></i>
      <span>{{ __('customer.book_for_other') }}</span></button>
    </li>

  </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
            <div class="card">
                <div class="card-body">

                    <div class="row gy-3 mb-5 pb-2">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary-subtle">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                        <h5 class="mb-0">{{ __('customer.total_appointment') }}</h5>
                                        <div class="avatar-60 badge rounded-circle bg-icon fs-2">
                                            <i class="ph ph-calendar-dots text-primary"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-secondary mb-0">{{ $data['totalAppointments'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary-subtle">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                        <h5 class="mb-0">{{ __('customer.cancelled_appointments') }}</h5>
                                        <div class="avatar-60 badge rounded-circle bg-icon fs-2">
                                            <i class="ph ph-calendar-x text-primary"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-secondary mb-0">{{ $data['cancelledAppointments'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary-subtle">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                        <h5 class="mb-0">{{ __('customer.completed_appointments') }}</h5>
                                        <div class="avatar-60 badge rounded-circle bg-icon fs-2">
                                            <i class="ph ph-calendar-check text-primary"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-secondary mb-0">{{ $data['completedAppointments'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary-subtle">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between gap-1">
                                        <h5 class="mb-0">{{ __('customer.upcoming_appointments') }}</h5>
                                        <div class="avatar-60 badge rounded-circle bg-icon fs-2">
                                            <i class="ph ph-users-three text-primary"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-secondary mb-0">{{ $data['upcomingAppointments'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="mb-3">{{ __('customer.patient_basic_info') }}</h4>
                        <div class="d-flex gap-3 align-items-center p-4 bg-body roubded flex-md-nowrap flex-wrap">
                        <div>
                            <img src="{{ $patient->profile_image ?? default_user_avatar() }}" alt="Profile Image"
                                    class="avatar avatar-80 rounded-pill">
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="m-0">{{ $data['patientInfo']['name'] }}</h4>
                            <div class="d-flex align-items-center column-gap-3 row-gap-2 mt-3 flex-md-nowrap flex-wrap">
                                <div class="d-flex align-items-center gap-2 text-break">
                                    <i class="ph ph-envelope-simple text-heading"></i>
                                    <a href="#" class="text-secondary text-decoration-underline font-size-16">
                                            {{ $data['patientInfo']['email'] }}
                                    </a>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <i class="ph ph-phone text-heading"></i>
                                    <a href="#" class="text-primary text-decoration-underline font-size-16">
                                            {{ $data['patientInfo']['contact'] }}
                                    </a>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <i class="ph ph-cake text-heading"></i>
                                    <span class="font-size-16"> {{ \Carbon\Carbon::parse($data['patientInfo']['dob'])->format('d-m-Y') }}</span>
                                </div>

                            </div>
                        </div>
                      </div>

                    @if(isset($data['topDoctors']) && $data['topDoctors']->isNotEmpty())
                    <div class="mt-5 pt-2">
                        <h4 class="mb-3">{{ __('customer.most_booked_doctors') }}</h4>
                        <div class="row gy-3">
                            @foreach($data['topDoctors'] as $doctor)

                            <div class="col-xl-3 col-md-6">
                                <div class="d-flex gap-3 align-items-center p-4 bg-body roubded flex-md-nowrap flex-wrap">
                                    <div>
                                        <img src="{{ $doctor->profile_image ?? default_user_avatar() }}" alt="Profile Image" class="avatar avatar-60 rounded-pill">
                                    </div>
                                    <div class="d-flex row-gap-2  flex-column">

                                        <h5 class="mb-0">Dr.{{ $doctor->first_name }} {{ $doctor->last_name }}</h5>
                                        <span class="font-size-12 text-transform-uppercase">{{ $doctor->email }}</span>
                                        @php
                                            $patientAppointmentsCount = Modules\Appointment\Models\Appointment::where('doctor_id', $doctor->id)
                                                ->where('user_id', $data['patientInfo']['id'])
                                                ->count();
                                        @endphp
                                        <span class="text-primary font-size-12 fw-semibold">{{ $patientAppointmentsCount }} {{ __('messages.appointment') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                        </div>
                    </div>
                    @endif


                    @if(isset($data['topClinics']) && $data['topClinics']->isNotEmpty())
                    <div class="mt-5 pt-2">
                    <h4 class="mb-3">{{ __('customer.most_visited_clinics') }}</h4>
                        <div class="row gy-3">
                            @foreach($data['topClinics'] as $clinic)

                            <div class="col-xl-3 col-md-6">
                                <div class="d-flex gap-3 align-items-center p-4 bg-body roubded flex-md-nowrap flex-wrap">
                                    <div>
                                        <img src="{{ $clinic->file_url}}" alt="Profile Image" class="avatar avatar-60 rounded-3">
                                    </div>
                                    <div class="d-flex row-gap-2  flex-column">
                                        <h5 class="mb-0">{{ $clinic->name }}</h5>
                                        <div class="d-flex align-items-center gap-2">
                                        <i class="ph ph-map-pin text-heading"></i>
                                        <span class="font-size-12">{{ $clinic->address }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ph ph-phone text-heading"></i>
                                        <a href="#" class="text-decoration-none text-primary font-size-12 fw-semibold">
                                            {{ $clinic->contact_number }}
                                        </a>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                    </div>

                    @endif

                </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-end mb-3">
                        <button class="btn btn-secondary"  data-bs-toggle="modal" data-bs-target="#addOtherPatientModal">
                            {{ __('customer.add_other_patient') }}
                        </button>
                    </div>
                    <div class="row gy-3">
                        @if ($otherPatients->isNotEmpty())
                            @foreach ($otherPatients as $otherPatient)
                                <div class="col-lg-12">

                                    <div class="card rounded-3 card-end bg-body">
                                        <div class="card-body">
                                            <div class="d-flex flex-sm-nowrap flex-wrap gap-3">
                                                <div class="avatar-wrapper">
                                                    <img src="{{ $otherPatient->getFirstMediaUrl('profile_image') ? asset($otherPatient->getFirstMediaUrl('profile_image')) : default_user_avatar() }}"
                                                    alt="profile image" class="rounded-circle me-3" width="60" height="60">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                                        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                                            <h5 class="font-size-18 mb-0">{{ $otherPatient->first_name }} {{ $otherPatient->last_name }}</h5>
                                                            <span class="badge bg-secondary-subtle rounded-pill">{{ $otherPatient->relation }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center column-gap-3 row-gap-2">
                                                            <button type="button editBtn" class="btn btn-link p-0 editBtn text-icon" data-id="{{$otherPatient->id}}" title="Edit" data-bs-toggle="modal" data-bs-target="#editModal_{{ $otherPatient->id }}">
                                                                    <i class="ph ph-pencil-simple font-size-18"></i>
                                                            </button>
                                                            {{-- <button class="btn btn-link p-0 text-icon deleteBtn delete-patient"  data-id="{{ $otherPatient->id }}" data-name="{{ $otherPatient->first_name }} {{ $otherPatient->last_name }}" title="Delete">
                                                            <i class="ph ph-trash font-size-18"></i>
                                                            </button> --}}

                                                            <form method="POST" action="{{ route('backend.customers.otherPatient.delete', $otherPatient->id) }}" class="d-inline delete-form">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                    class="btn btn-link p-0 text-icon deleteBtn delete-patient"
                                                                    data-id="{{ $otherPatient->id }}"
                                                                    data-name="{{ $otherPatient->first_name }} {{ $otherPatient->last_name }}"
                                                                    data-url="{{ route('backend.customers.otherPatient.delete', $otherPatient->id) }}"
                                                                    title="Delete">
                                                                    <i class="ph ph-trash font-size-18"></i>
                                                                </button>
                                                            </form>

                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                                        <div class="d-flex align-items-center gap-3 font-size-14">
                                                            <i class="ph ph-user text-heading"></i>{{ $otherPatient->gender }}
                                                        </div>
                                                        <div class="d-flex align-items-center gap-3 font-size-14">
                                                            <i class="ph ph-phone text-heading"></i>{{ $otherPatient->contactNumber }}
                                                        </div>
                                                        <div class="d-flex align-items-center gap-3 font-size-14">
                                                            <i class="ph ph-cake text-heading"></i> {{ \Carbon\Carbon::parse($otherPatient->dob)->format('d-m-Y') }}
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info col-12">
                                {{ __('customer.no_patient_available') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Add Other Patient Modal -->
     <div class="modal fade" id="addOtherPatientModal" tabindex="-1" aria-labelledby="addOtherPatientLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addOtherPatientLabel">{{ __('customer.add_new_patient') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPatientForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <img id="add-patient-preview" src="{{ default_file_url() }}" class="img-fluid avatar avatar-120 avatar-rounded mb-2" />
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <input type="file" class="form-control d-none" id="add-patient-profile" name="profile_image" accept=".jpeg, .jpg, .png, .gif" />
                                        <label class="btn btn-info" for="add-patient-profile">{{ __('messages.upload') }}</label>
                                        <input type="button" class="btn btn-danger" id="add-patient-remove-image" value="{{ __('settings.remove') }}" />
                                    </div>
                                    <span class="text-danger" id="add_profile_image_error"></span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">{{ __('customer.lbl_first_name') }}</label><span class="required-star text-danger">*</span>
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="{{ __('clinic.lbl_first_name') }}">
                                    <span class="error text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">{{ __('customer.lbl_last_name') }}</label><span class="required-star text-danger">*</span>
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="{{ __('clinic.lbl_last_name') }}">
                                    <span class="error text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="dob" class="form-label">{{ __('customer.lbl_date_of_birth') }}</label><span class="required-star text-danger">*</span>
                                    <input type="text" class="form-control flatpickr-dob" id="dob" name="dob" placeholder="{{ __('customer.select_date_of_birth') }}" readonly>
                                    <span class="error text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="contactNumber" class="form-label">{{ __('customer.lbl_phone_number') }}</label><span class="required-star text-danger">*</span>
                                    <input type="tel" class="form-control phone-input" id="contactNumber" name="contactNumber" placeholder="{{ __('employee.lbl_phone_number_placeholder') }}">
                                    <span class="error text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('customer.lbl_gender') }}</label>
                                    <div class="d-flex gap-2">
                                        <input type="radio" class="btn-check" name="gender" id="male" value="Male" autocomplete="off" />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="male">{{ __('customer.male') }}</label>
                                        <input type="radio" class="btn-check" name="gender" id="female" value="Female" autocomplete="off" />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="female">{{ __('customer.female') }}</label>
                                        <input type="radio" class="btn-check" name="gender" id="other" value="Other" autocomplete="off" />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="other">{{ __('customer.other') }}</label>
                                    </div>
                                    <span class="error text-danger gender-error"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('customer.relation') }}</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <input type="radio" class="btn-check" name="relation" id="parents" value="Parents" autocomplete="off" />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="parents">{{ __('customer.parents') }}</label>
                                        <input type="radio" class="btn-check" name="relation" id="siblings" value="Siblings" autocomplete="off" />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="siblings">{{ __('customer.siblings') }}</label>
                                        <input type="radio" class="btn-check" name="relation" id="spouse" value="Spouse" autocomplete="off" />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="spouse">{{ __('customer.spouse') }}</label>
                                        <input type="radio" class="btn-check" name="relation" id="others" value="Others" autocomplete="off" />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="others">{{ __('customer.other') }}</label>
                                    </div>
                                    <span class="error text-danger relation-error"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('customer.close') }}</button>
                    <button type="button" class="btn btn-primary" id="add-patient-submit-btn">{{ __('customer.save_patient') }}</button>
                </div>
            </div>
        </div>
    </div>

    @if ($otherPatients->isNotEmpty())
    @foreach ($otherPatients as $otherPatient)
    <div class="modal fade" id="editModal_{{ $otherPatient->id }}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('backend.customers.otherPatient.update', $otherPatient->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="edit-form"
                        data-patient-id="{{ $otherPatient->id }}">

                    @csrf
                @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">{{ __('customer.edit_patient_details') }} </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <img id="edit-patient-preview-{{ $otherPatient->id }}" src="{{ $otherPatient->getFirstMediaUrl('profile_image') ? asset($otherPatient->getFirstMediaUrl('profile_image')) : default_user_avatar() }}" class="img-fluid avatar avatar-120 avatar-rounded mb-2" />
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <input type="file" class="form-control d-none" id="profile_image_{{ $otherPatient->id }}" name="profile_image" accept=".jpeg, .jpg, .png, .gif" />
                                        <label class="btn btn-info" for="profile_image_{{ $otherPatient->id }}">{{ __('messages.upload') }}</label>
                                        <input type="button" class="btn btn-danger" id="edit-patient-remove-image-{{ $otherPatient->id }}" value="{{ __('settings.remove') }}" />
                                    </div>
                                    <span class="text-danger" id="edit_profile_image_error_{{ $otherPatient->id }}"></span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="first_name_{{ $otherPatient->id }}" class="form-label">{{ __('customer.lbl_first_name') }}</label><span class="required-star text-danger" style="display:none;">*</span>
                                    <input type="text" class="form-control" id="first_name_{{ $otherPatient->id }}" name="first_name" value="{{ $otherPatient->first_name }}" placeholder="{{ __('clinic.lbl_first_name') }}">
                                    <span class="error text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="last_name_{{ $otherPatient->id }}" class="form-label">{{ __('customer.lbl_last_name') }}</label><span class="required-star text-danger" style="display:none;">*</span>
                                    <input type="text" class="form-control" id="last_name_{{ $otherPatient->id }}" name="last_name" value="{{ $otherPatient->last_name }}" placeholder="{{ __('clinic.lbl_last_name') }}">
                                    <span class="error text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="dob_{{ $otherPatient->id }}" class="form-label">{{ __('customer.lbl_date_of_birth') }}</label><span class="required-star text-danger" style="display:none;">*</span>
                                    <input type="text" class="form-control flatpickr-dob" id="dob_{{ $otherPatient->id }}" name="dob" value="{{ $otherPatient->dob }}" placeholder="{{ __('customer.select_date_of_birth') }}" readonly>
                                    <span class="error text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label for="contactNumber_{{ $otherPatient->id }}" class="form-label">{{ __('customer.lbl_phone_number') }}</label><span class="required-star text-danger" style="display:none;">*</span>
                                    <input type="tel" class="form-control intl-tel-input" id="contactNumber_{{ $otherPatient->id }}" name="contactNumber" value="{{ $otherPatient->contactNumber }}" placeholder="{{ __('employee.lbl_phone_number_placeholder') }}">
                                    <span class="error text-danger"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('customer.lbl_gender') }}</label><span class="required-star text-danger" style="display:none;">*</span>
                                    <div class="d-flex gap-2">
                                        <input type="radio" class="btn-check" name="gender" id="male_{{ $otherPatient->id }}" value="Male" autocomplete="off" {{ $otherPatient->gender == 'Male' ? 'checked' : '' }} />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="male_{{ $otherPatient->id }}">{{ __('customer.male') }}</label>
                                        <input type="radio" class="btn-check" name="gender" id="female_{{ $otherPatient->id }}" value="Female" autocomplete="off" {{ $otherPatient->gender == 'Female' ? 'checked' : '' }} />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="female_{{ $otherPatient->id }}">{{ __('customer.female') }}</label>
                                        <input type="radio" class="btn-check" name="gender" id="other_{{ $otherPatient->id }}" value="Other" autocomplete="off" {{ $otherPatient->gender == 'Other' ? 'checked' : '' }} />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="other_{{ $otherPatient->id }}">{{ __('customer.other') }}</label>
                                    </div>
                                    <span class="error text-danger gender-error"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">{{ __('customer.relation') }}</label><span class="required-star text-danger" style="display:none;">*</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        <input type="radio" class="btn-check" name="relation" id="parents_{{ $otherPatient->id }}" value="Parents" autocomplete="off" {{ $otherPatient->relation == 'Parents' ? 'checked' : '' }} />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="parents_{{ $otherPatient->id }}">{{ __('customer.parents') }}</label>
                                        <input type="radio" class="btn-check" name="relation" id="siblings_{{ $otherPatient->id }}" value="Siblings" autocomplete="off" {{ $otherPatient->relation == 'Siblings' ? 'checked' : '' }} />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="siblings_{{ $otherPatient->id }}">{{ __('customer.siblings') }}</label>
                                        <input type="radio" class="btn-check" name="relation" id="spouse_{{ $otherPatient->id }}" value="Spouse" autocomplete="off" {{ $otherPatient->relation == 'Spouse' ? 'checked' : '' }} />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="spouse_{{ $otherPatient->id }}">{{ __('customer.spouse') }}</label>
                                        <input type="radio" class="btn-check" name="relation" id="others_{{ $otherPatient->id }}" value="Others" autocomplete="off" {{ $otherPatient->relation == 'Others' ? 'checked' : '' }} />
                                        <label class="btn btn-outline-primary rounded-pill px-4" for="others_{{ $otherPatient->id }}">{{ __('customer.other') }}</label>
                                    </div>
                                    <span class="error text-danger relation-error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('customer.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('customer.save_changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endif

@endsection

@push('after-scripts')
<script src="{{ mix('modules/customer/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.16/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
// Initialize Select2 for relation selects with proper dropdown parent in modals
document.addEventListener('DOMContentLoaded', function() {
    if (window.jQuery && window.jQuery.fn.select2) {
        // Add Patient modal
        var addModal = jQuery('#addOtherPatientModal');
        addModal.on('shown.bs.modal', function () {
            var $select = addModal.find('select#relation.select2');
            if ($select.length) {
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    width: '100%',
                    dropdownParent: addModal
                });
            }
        });

        // Edit modals
        jQuery('[id^="editModal_"]').on('shown.bs.modal', function () {
            var $modal = jQuery(this);
            var $select = $modal.find('select[name="relation"].select2');
            if ($select.length) {
                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }
                $select.select2({
                    width: '100%',
                    dropdownParent: $modal
                });
            }
        });
    }
});

  document.addEventListener("DOMContentLoaded", function () {
    const overviewBtn = document.getElementById('overview-btn');
    const detailsBtn = document.getElementById('details-btn');
    const overviewContent = document.getElementById('overview-content');
    const detailsContent = document.getElementById('details-content');

    // Default state: Show overview, hide details
    overviewContent.style.display = 'block';
    detailsContent.style.display = 'none';

    // Overview button click
    overviewBtn.addEventListener('click', () => {
      overviewContent.style.display = 'block';
      detailsContent.style.display = 'none';
      overviewBtn.classList.add('btn-primary');
      overviewBtn.classList.remove('btn-secondary');
      detailsBtn.classList.remove('btn-primary');
      detailsBtn.classList.add('btn-secondary');
    });

    // Details button click
    detailsBtn.addEventListener('click', () => {
      overviewContent.style.display = 'none';
      detailsContent.style.display = 'block';
      detailsBtn.classList.add('btn-primary');
      detailsBtn.classList.remove('btn-secondary');
      overviewBtn.classList.remove('btn-primary');
      overviewBtn.classList.add('btn-secondary');
    });
  });

  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("addPatientForm");
    const modalElement = document.getElementById("addOtherPatientModal");
    const modal = new bootstrap.Modal(modalElement); // Initialize Bootstrap Modal

    // Extract ID from URL
    const urlSegments = window.location.pathname.split('/');
    const userId = urlSegments[urlSegments.length - 1]; // Get last part of the URL

    // Image upload functionality
    const profileImageInput = document.getElementById('add-patient-profile');
    const profileImagePreview = document.getElementById('add-patient-preview');
    const removeImageBtn = document.getElementById('add-patient-remove-image');

    if (profileImageInput && profileImagePreview) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeImageBtn && profileImagePreview) {
        removeImageBtn.addEventListener('click', function() {
            profileImagePreview.src = "{{ default_file_url() }}";
            if (profileImageInput) {
                profileImageInput.value = '';
            }
        });
    }
    function formatIntlWithSpace(inputEl, itiInstance) {
        if (!inputEl || !itiInstance) return inputEl ? inputEl.value : '';
        try {
            var dial = (itiInstance.getSelectedCountryData && itiInstance.getSelectedCountryData().dialCode) || '';
            var full = (itiInstance.getNumber && typeof itiInstance.getNumber === 'function') ? itiInstance.getNumber() : '';
            // if we have a full number from plugin, normalize to digits then reinsert space
            if (full && typeof full === 'string') {
                var digits = full.replace(/\D/g, '');
                if (dial && digits.startsWith(dial)) {
                    var rest = digits.slice(dial.length);
                    return rest ? '+' + dial + ' ' + rest : '+' + dial;
                }
                // if full doesn't contain dial, try to use selected dial and remaining digits
                if (dial) {
                    var remaining = digits;
                    return remaining ? '+' + dial + ' ' + remaining : '+' + dial;
                }
                return full;
            }
            // fallback: build from input value and selected dial
            var raw = (inputEl.value || '').replace(/\D/g, '');
            if (dial && raw) {
                if (raw.startsWith(dial)) raw = raw.slice(dial.length);
                return '+' + dial + (raw ? ' ' + raw : '');
            }
        } catch (e) {}
        return inputEl.value || '';
    }

    // Add event listener to handle form submission
    document.getElementById('add-patient-submit-btn').addEventListener('click', function(e) {
        e.preventDefault();

        // Clear previous error messages
        form.querySelectorAll(".error").forEach(el => el.textContent = '');

        const firstName = form.querySelector('[name="first_name"]');
        const lastName = form.querySelector('[name="last_name"]');
        const dob = form.querySelector('[name="dob"]');
        const contactNumber = form.querySelector('[name="contactNumber"]');
        const gender = form.querySelector('[name="gender"]:checked');
        const relation = form.querySelector('[name="relation"]:checked');

        // Reset validation if there are missing fields
        if (!firstName.value.trim() || !lastName.value.trim() || !dob.value.trim() || !contactNumber.value.trim() || !gender || !relation) {
            if (!firstName.value.trim()) {
                const container = firstName.closest('.mb-3');
                container.querySelector('.error').textContent = 'First Name field is required.';

            } else {
                const container = firstName.closest('.mb-3');
                container.querySelector('.error').textContent = '';
                container.querySelector('.required-star').style.display = 'none';
            }

            if (!lastName.value.trim()) {
                const container = lastName.closest('.mb-3');
                container.querySelector('.error').textContent = 'Last Name field is required.';

            } else {
                const container = lastName.closest('.mb-3');
                container.querySelector('.error').textContent = '';
                container.querySelector('.required-star').style.display = 'none';
            }

            if (!dob.value.trim()) {
                const container = dob.closest('.mb-3');
                container.querySelector('.error').textContent = 'Date of Birth field is required.';

            } else {
                const container = dob.closest('.mb-3');
                container.querySelector('.error').textContent = '';
                container.querySelector('.required-star').style.display = 'none';
            }

            if (!contactNumber.value.trim()) {
                const container = contactNumber.closest('.mb-3');
                container.querySelector('.error').textContent = 'Phone Number field is required.';

            } else {
                const container = contactNumber.closest('.mb-3');
                container.querySelector('.error').textContent = '';
                container.querySelector('.required-star').style.display = 'none';
            }

            if (!gender) {
                const genderContainer = form.querySelector('.mb-3 .gender-error').closest('.mb-3');
                const genderError = genderContainer.querySelector('.gender-error');
                genderError.textContent = 'Gender field is required.';

            } else {
                const genderContainer = form.querySelector('.mb-3 .gender-error').closest('.mb-3');
                const genderError = genderContainer.querySelector('.gender-error');
                genderError.textContent = '';
            }

            if (!relation) {
                const relationContainer = form.querySelector('.mb-3 .relation-error').closest('.mb-3');
                const relationError = relationContainer.querySelector('.relation-error');
                relationError.textContent = 'Relation field is required.';

            } else {
                const relationContainer = form.querySelector('.mb-3 .relation-error').closest('.mb-3');
                const relationError = relationContainer.querySelector('.relation-error');
                relationError.textContent = '';
            }

            return; // Stop form submission if validation fails
        }

    // Proceed with form submission if all fields are valid
    // Ensure we submit E.164 (or prefixed dial code)
    try {
        if (window.intlTelInputGlobals && contactNumber) {
            const itiInstance = window.intlTelInputGlobals.getInstance(contactNumber);
            if (itiInstance) {
                contactNumber.value = formatIntlWithSpace(contactNumber, itiInstance);
            }
        } 
    } catch (err) {}

    const formData = new FormData(form);
        formData.append("user_id", userId); // Pass extracted ID as user_id

        fetch("{{ route('backend.appointment.other_patient') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json"
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                $("#addOtherPatientModal").modal("hide");
                successSnackbar("Patient added successfully!");

                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Add event listener to hide validation and required star when the modal is closed
    modalElement.addEventListener('hidden.bs.modal', function () {
        // Clear error messages
        form.querySelectorAll(".error").forEach(el => el.textContent = '');

        // Hide the red required asterisk
        form.querySelectorAll(".required-star").forEach(el => el.style.display = 'none');

        // Reset form
        form.reset();
        profileImagePreview.src = "{{ default_file_url() }}";
    });
});


  // Update the delete confirmation handler
//   document.querySelectorAll('.delete-patient').forEach(button => {
//       button.addEventListener('click', function(e) {
//           e.preventDefault(); // Prevent form submission
//           const patientId = this.dataset.id;
//           const patientName = this.dataset.name;
//           const deleteForm = this.closest('form'); // Get the parent form

//           Swal.fire({
//               title: '{{ __("messages.are_you_sure") }}',
//               html: `{{ __("messages.delete_confirm") }} <br><strong>${patientName}</strong>?`,
//               icon: 'warning',
//               showCancelButton: true,
//               confirmButtonColor: '#d33',
//               cancelButtonColor: '#3085d6',
//               confirmButtonText: '{{ __("messages.yes_delete") }}',
//               cancelButtonText: '{{ __("messages.cancel") }}'
//           }).then((result) => {
//               if (result.isConfirmed) {
//                   // Show loading state


//                   // Submit form with fetch to handle response
//                   fetch(deleteForm.action, {
//                       method: 'POST',
//                       body: new FormData(deleteForm),
//                       headers: {
//                           'X-Requested-With': 'XMLHttpRequest'
//                       }
//                   })
//                   .then(response => response.json())
//                   .then(data => {
//                       Swal.fire({
//                           title: '{{ __("messages.deleted") }}',
//                           text: '{{ __("messages.delete_success") }}',
//                           icon: 'success',
//                           timer: 2000,
//                           showConfirmButton: false
//                       }).then(() => {
//                           // Reload page after success message
//                           window.location.reload();
//                       });
//                   })
//                   .catch(error => {
//                       Swal.fire({
//                           title: '{{ __("messages.error") }}',
//                           text: '{{ __("messages.delete_error") }}',
//                           icon: 'error'
//                       });
//                   });
//               }
//           });
//       });
//   });


  document.querySelectorAll('.delete-patient').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();

        const deleteUrl = this.dataset.url;
        const patientName = this.dataset.name;

        Swal.fire({
            title: '{{ __("messages.are_you_sure") }}',
            html: `{{ __("messages.delete_confirm") }} <br><strong>${patientName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '{{ __("messages.yes_delete") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        'Accept': 'application/json'
                    }
                })

                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        Swal.fire({
                            title: '{{ __("messages.deleted") }}',
                            text: '{{ __("messages.delete_success") }}',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: '{{ __("messages.error") }}',
                            text: data.message ?? '{{ __("messages.delete_error") }}',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({
                        title: '{{ __("messages.error") }}',
                        text: '{{ __("messages.delete_error") }}',
                        icon: 'error'
                    });
                });
            }
        });
    });
});

 document.querySelectorAll('.edit-form').forEach(function (form) {
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        form.querySelectorAll(".error").forEach(el => el.remove());
        form.querySelectorAll(".required-star").forEach(el => el.style.display = "none");

        let hasError = false;
        const patientId = form.dataset.patientId;

        const firstName = form.querySelector('[name="first_name"]');
        const lastName = form.querySelector('[name="last_name"]');
        const dob = form.querySelector('[name="dob"]');
        const contactNumber = form.querySelector('[name="contactNumber"]');
        const gender = form.querySelector('[name="gender"]:checked');
        const relation = form.querySelector('[name="relation"]');

        // First Name
        if (!firstName.value.trim()) {
            firstName.insertAdjacentHTML('afterend', '<small class="text-danger error">First Name is required.</small>');

            hasError = true;
        }

        // Last Name
        if (!lastName.value.trim()) {
            lastName.insertAdjacentHTML('afterend', '<small class="text-danger error">Last Name is required.</small>');

            hasError = true;
        }

        // DOB
        if (!dob.value.trim()) {
            dob.insertAdjacentHTML('afterend', '<small class="text-danger error">Date of Birth is required.</small>');

            hasError = true;
        }

        // Contact
        if (!contactNumber.value.trim()) {
            contactNumber.insertAdjacentHTML('afterend', '<small class="text-danger error">Phone Number is required.</small>');

            hasError = true;
        }

        // Gender
        if (!gender) {
            const genderWrapper = form.querySelector('.mb-3 .required-star').closest('.mb-3');

            form.querySelector('.gender-error').innerText = 'Gender is required.';
            hasError = true;
        }

        // Relation
        if (!relation.value.trim()) {
            relation.insertAdjacentHTML('afterend', '<small class="text-danger error">Relation is required.</small>');

            hasError = true;
        }

        if (hasError) return;

        // Normalize phone once more on submit (E.164 or prefixed)
         try {
            if (window.intlTelInputGlobals && contactNumber) {
                const itiInstance = window.intlTelInputGlobals.getInstance(contactNumber);
                if (itiInstance) {
                    contactNumber.value = formatIntlWithSpace(contactNumber, itiInstance);
                }
            }
        } catch (err) {}

        const formData = new FormData(form);
        const actionUrl = form.getAttribute("action");

        fetch(actionUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json"
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                successSnackbar("Patient updated successfully!");
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    // Reset validation when modal is closed
    const modal = new bootstrap.Modal(form.closest('.modal')); // Assuming each edit-form is inside a modal
    const modalElement = form.closest('.modal');

    modalElement.addEventListener('hidden.bs.modal', function () {
        // Reset error messages and required-star display when modal closes
        form.querySelectorAll(".error").forEach(el => el.remove());
        form.querySelectorAll(".required-star").forEach(el => el.style.display = "none");
    });
});

// Initialize Flatpickr for DOB fields
document.addEventListener("DOMContentLoaded", function () {
    // Initialize Flatpickr for the main DOB field in Add Patient modal
    const dobInput = document.getElementById('dob');
    if (dobInput) {
        flatpickr(dobInput, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            allowInput: false,
            clickOpens: true,
            placeholder: "Select date of birth"
        });
    }

    // Initialize Flatpickr for all edit modal DOB fields
    document.querySelectorAll('.flatpickr-dob').forEach(function(input) {
        if (input.id !== 'dob') { // Skip the main DOB field as it's already initialized
            flatpickr(input, {
                dateFormat: "Y-m-d",
                maxDate: "today",
                allowInput: false,
                clickOpens: true,
                placeholder: "Select date of birth"
            });
        }
    });

    // Initialize International Telephone Input for phone number fields
    function initializePhoneInputs() {
        const phoneInputs = document.querySelectorAll('.phone-input, .intl-tel-input'); // ✅ include both
        phoneInputs.forEach(function(input) {
            if (input.getAttribute('data-initialized') === 'true') {
                return;
            }

            const iti = intlTelInput(input, {
                initialCountry: "gb", // Default; will auto-correct from existing value below
                preferredCountries: ["gb", "us", "in", "au", "ca"],
                separateDialCode: true,   // ✅ show +91 separately
                autoPlaceholder: "aggressive",
                nationalMode: false,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.2.16/build/js/utils.js"
            });

            input.setAttribute('data-initialized', 'true');

            // If an existing value includes a country code, set selection from it
            try {
                const existing = (input.value || '').trim();
                if (existing) {
                    // Try direct set; plugin will infer country
                    iti.setNumber(existing);
                }
            } catch (e) {}

            function updateFullNumber() {
                const countryData = iti.getSelectedCountryData();
                const dialCode = countryData.dialCode;

                // Remove any existing +countrycode and spaces
                let number = input.value.replace(/^\+\d+\s*/, '').trim();

                // Now format with the new one
                const formattedNumber = `+${dialCode} ${number}`;
                input.value = formattedNumber;
            }

            input.addEventListener("countrychange", updateFullNumber);
            input.addEventListener("blur", updateFullNumber);
        });
    }

    // Initialize immediately
    initializePhoneInputs();

    // Re-initialize when modals are shown
    document.addEventListener('shown.bs.modal', function () {
        setTimeout(initializePhoneInputs, 100);
    });

    // Image preview/remove for edit modals
    document.querySelectorAll('input[type="file"][id^="profile_image_"]').forEach(function(fileInput){
        const idSuffix = fileInput.id.replace('profile_image_', '');
        const preview = document.getElementById(`edit-patient-preview-${idSuffix}`);
        const removeBtn = document.getElementById(`edit-patient-remove-image-${idSuffix}`);
        if (fileInput && preview) {
            fileInput.addEventListener('change', function(e){
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function(ev){
                    preview.src = ev.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
        if (removeBtn && preview) {
            removeBtn.addEventListener('click', function(){
                // Reset to default avatar; file input cleared
                preview.src = "{{ default_user_avatar() }}";
                fileInput.value = '';
            });
        }
    });

});

</script>
@endpush


<!-- Include Appointment Details Modal -->
@include('appointment::backend.appointment.details_modal')

<script>
// Function to view appointment details in modal
function viewAppointmentDetails(appointmentId) {
    const modal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));
    const contentDiv = document.getElementById('appointmentDetailsContent');
    
    // Show loading state
    contentDiv.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading appointment details...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch appointment details
    fetch(`/app/appointment/view-details/${appointmentId}`)
        .then(response => response.json())
        .then(result => {
            if (result.status) {
                contentDiv.innerHTML = renderAppointmentDetails(result.data);
            } else {
                contentDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="ph ph-warning-circle me-2"></i>
                        Failed to load appointment details. Please try again.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="ph ph-warning-circle me-2"></i>
                    An error occurred while loading appointment details.
                </div>
            `;
        });
}

function renderAppointmentDetails(data) {
    const patientName = data.other_patient ? data.other_patient.name : data.patient.name;
    const patientEmail = data.other_patient ? data.other_patient.email : data.patient.email;
    const patientPhone = data.other_patient ? data.other_patient.phone : data.patient.phone;
    
    let html = `
        <div class="row">
            <!-- Appointment Information -->
            <div class="col-md-6">
                <div class="detail-card">
                    <h6><i class="ph ph-calendar-check me-2"></i>Appointment Information</h6>
                    <div class="detail-row">
                        <span class="detail-label">Appointment ID:</span>
                        <span class="detail-value">#${data.appointment.id}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Date:</span>
                        <span class="detail-value">${data.appointment.appointment_date}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Time:</span>
                        <span class="detail-value">${data.appointment.appointment_time}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Duration:</span>
                        <span class="detail-value">${data.appointment.duration} minutes</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">
                            <span class="status-badge bg-${getStatusColor(data.appointment.status)}">${data.appointment.status}</span>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Patient Information -->
            <div class="col-md-6">
                <div class="detail-card">
                    <h6><i class="ph ph-user me-2"></i>Patient Information</h6>
                    <div class="detail-row">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value">${patientName}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">${patientEmail || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">${patientPhone || 'N/A'}</span>
                    </div>
                </div>
            </div>
            
            <!-- Doctor & Service Information -->
            <div class="col-md-6">
                <div class="detail-card">
                    <h6><i class="ph ph-user-circle me-2"></i>Doctor Information</h6>
                    <div class="detail-row">
                        <span class="detail-label">Doctor:</span>
                        <span class="detail-value">${data.doctor.name}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">${data.doctor.email || 'N/A'}</span>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="detail-card">
                    <h6><i class="ph ph-hospital me-2"></i>Service & Clinic</h6>
                    <div class="detail-row">
                        <span class="detail-label">Service:</span>
                        <span class="detail-value">${data.service.name || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Category:</span>
                        <span class="detail-value">${data.category.name || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Clinic:</span>
                        <span class="detail-value">${data.clinic.name || 'N/A'}</span>
                    </div>
                </div>
            </div>
            
            <!-- Payment Information -->
            <div class="col-md-6">
                <div class="detail-card">
                    <h6><i class="ph ph-currency-circle-dollar me-2"></i>Payment Information</h6>
                    <div class="detail-row">
                        <span class="detail-label">Total Amount:</span>
                        <span class="detail-value">${data.appointment.total_amount || '0'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Advance Paid:</span>
                        <span class="detail-value">${data.appointment.advance_paid_amount || '0'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Payment Status:</span>
                        <span class="detail-value">
                            <span class="status-badge bg-${data.payment.payment_status == 1 ? 'success' : 'warning'}">
                                ${data.payment.payment_status == 1 ? 'Paid' : 'Pending'}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Medical History -->
            <div class="col-12">
                <div class="detail-card">
                    <h6><i class="ph ph-file-text me-2"></i>Medical History</h6>
                    ${data.medical_data.medical_history_text ? 
                        `<div class="medical-history-text">${data.medical_data.medical_history_text}</div>` : 
                        '<div class="no-data">No medical history recorded</div>'
                    }
                </div>
            </div>
            
            <!-- Audio Recordings -->
            ${data.medical_data.audio_recordings && data.medical_data.audio_recordings.length > 0 ? `
                <div class="col-12">
                    <div class="detail-card">
                        <h6><i class="ph ph-microphone me-2"></i>Audio Recordings</h6>
                        ${data.medical_data.audio_recordings.map((recording, index) => `
                            <div class="mb-3">
                                <label class="form-label">Recording ${index + 1}</label>
                                <audio controls class="audio-player">
                                    <source src="${recording.url}" type="audio/webm">
                                    <source src="${recording.url}" type="audio/wav">
                                    Your browser does not support the audio element.
                                </audio>
                                ${recording.transcription ? `
                                    <div class="mt-2">
                                        <small class="text-muted">Transcription:</small>
                                        <p class="mb-0">${recording.transcription}</p>
                                    </div>
                                ` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
            
            <!-- Documents -->
            ${data.documents && data.documents.length > 0 ? `
                <div class="col-12">
                    <div class="detail-card">
                        <h6><i class="ph ph-file me-2"></i>Uploaded Documents</h6>
                        ${data.documents.map(doc => `
                            <div class="document-item">
                                <i class="ph ph-file-pdf document-icon"></i>
                                <div class="document-info">
                                    <div class="document-name">${doc.name}</div>
                                    <div class="document-size">${formatFileSize(doc.size)}</div>
                                </div>
                              <div class="d-flex gap-2">
                                <a href="${doc.url}" target="_blank" class="btn btn-sm btn-info">
                                <i class="ph ph-eye"></i> View
                                </a>
                                    <a href="${doc.download_url}" class="btn btn-sm btn-primary" download>
                                        <i class="ph ph-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
            
            <!-- Additional Notes -->
            ${data.appointment.appointment_extra_info ? `
                <div class="col-12">
                    <div class="detail-card">
                        <h6><i class="ph ph-note me-2"></i>Additional Notes</h6>
                        <div class="medical-history-text">${data.appointment.appointment_extra_info}</div>
                    </div>
                </div>
            ` : ''}
        </div>
    `;
    
    return html;
}

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'confirmed': 'info',
        'check_in': 'primary',
        'checkout': 'success',
        'cancelled': 'danger',
        'rejected': 'danger'
    };
    return colors[status] || 'secondary';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}
</script>
