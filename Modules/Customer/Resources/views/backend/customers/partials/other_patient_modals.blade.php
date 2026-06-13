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
                                    <input type="radio" class="btn-check" name="gender" id="prefer_not_to_say" value="prefer_not_to_say" autocomplete="off" />
                                    <label class="btn btn-outline-primary rounded-pill px-4" for="prefer_not_to_say">{{ __('messages.prefer_not_to_say') }}</label>
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
                                    <input type="radio" class="btn-check" name="gender" id="prefer_not_to_say_{{ $otherPatient->id }}" value="prefer_not_to_say" autocomplete="off" {{ $otherPatient->gender == 'prefer_not_to_say' ? 'checked' : '' }} />
                                    <label class="btn btn-outline-primary rounded-pill px-4" for="prefer_not_to_say_{{ $otherPatient->id }}">{{ __('messages.prefer_not_to_say') }}</label>
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