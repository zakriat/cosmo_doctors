<form id="doctor-form" enctype="multipart/form-data" autocomplete="off" novalidate>
    @csrf
    <input type="hidden" name="doctor_id" id="doctor_id" value="{{ isset($doctor) ? $doctor->id : '' }}">
    <input type="hidden" name="_method" id="form_method" value="POST">
    <input type="hidden" name="email_verified_at" id="email_verified_at" value="{{ old('email_verified_at', $doctor->email_verified_at ?? '') }}">

    <div class="row">
        {{-- Profile Image --}}
        <div class="col-md-6 create-service-image">
            <label for="profile_image" class="form-label w-100">{{ __('clinic.lbl_image') }}</label>
            <div class="image-upload-container text-center">
                <div class="avatar-preview mx-auto mb-2">
                    <img id="imagePreview"
                        src="{{ isset($doctor) && $doctor->profile_image ? asset('storage/'.$doctor->profile_image) : asset('img/avatar/avatar.webp') }}"
                        alt="{{ __('clinic.profile_image') }}"
                        class="img-fluid object-fit-cover avatar-170 rounded-circle">
                </div>
                <button type="button" class="btn btn-light mt-2" id="uploadImageBtn">
                    {{ __('clinic.upload') }}
                </button>
                {{-- <button type="button" class="btn btn-danger mt-2 ms-2" id="removeImageBtn">
                    {{ __('messages.remove') }}
                </button> --}}
                <input type="file" id="profile_image" name="profile_image" accept=".jpeg,.jpg,.png,.gif" class="d-none">
                <input type="hidden" id="remove_profile_image" name="remove_profile_image" value="0">
            </div>
            <span class="text-danger text-center d-none">{{ __('clinic.only_jpeg_jpg_png_files_allowed') }}</span>
            @error('profile_image')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- First Name, Last Name, Email --}}
        <div class="col-md-6">
            <div class="mb-3">
                <label for="first_name" class="form-label">{{ __('clinic.lbl_first_name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                    id="first_name" name="first_name"
                    value="{{ old('first_name', $doctor->first_name ?? '') }}"
                    placeholder="{{ __('clinic.lbl_first_name') }}">
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback d-none" id="first_name_error">
                    {{ __('clinic.first_name_is_required_field') }}
                </div>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">{{ __('clinic.lbl_last_name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                    id="last_name" name="last_name"
                    value="{{ old('last_name', $doctor->last_name ?? '') }}"
                    placeholder="{{ __('clinic.lbl_last_name') }}">
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback d-none" id="last_name_error">
                    {{ __('clinic.last_name_is_required_field') }}
                </div>
            </div>
            <div class="mb-3">
                <label for="doctor_email" class="form-label">{{ __('clinic.lbl_Email') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('doctor_email') is-invalid @enderror"
                    id="doctor_email"
                    name="doctor_email"
                    value="{{ old('doctor_email', $doctor->email ?? '') }}"
                    placeholder="{{ __('clinic.lbl_Email') }}">

                @error('doctor_email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback d-none" id="doctor_email_error">
                    {{ __('clinic.email_is_required_field') }}
                </div>
            </div>
        </div>

        {{-- Gender --}}
        <div class="col-md-6">
            <div class="mb-3">
                <label for="gender" class="form-label">{{ __('clinic.lbl_gender') }}</label>
                <select class="form-select select2"
                        id="gender" name="gender"
                        data-placeholder="{{ __('clinic.lbl_select_gender') }}">
                    <option value="">{{ __('clinic.lbl_select_gender') }}</option>
                    <option value="male" {{ old('gender', $doctor->gender ?? '') == 'male' ? 'selected' : '' }}>{{ __('clinic.lbl_male') }}</option>
                    <option value="female" {{ old('gender', $doctor->gender ?? '') == 'female' ? 'selected' : '' }}>{{ __('clinic.lbl_female') }}</option>
                    <option value="intersex" {{ old('gender', $doctor->gender ?? '') == 'intersex' ? 'selected' : '' }}>{{ __('clinic.intersex') }}</option>
                </select>
            </div>
        </div>

        {{-- Contact Number --}}
        <div class="col-md-6">
            <div class="mb-3">
                <label for="mobile" class="form-label">{{ __('clinic.contact_number') }} <span class="text-danger">*</span></label>
                <input type="tel"
                    class="form-control @error('mobile') is-invalid @enderror"
                    id="mobile"
                    name="mobile"
                    value="{{ old('mobile', $doctor->mobile ?? '') }}"
                    placeholder="{{ __('Enter phone number') }}"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                >
                <input type="hidden" id="dial_code" name="dial_code" value="{{ old('dial_code', $doctor->dial_code ?? '') }}">
                @error('mobile')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback d-none text-danger mt-1 font-size-12" id="mobile_error">
                    Contact number is required.
                </div>
            </div>
        </div>

        {{-- Password (only for create) --}}
        <div class="col-md-6 {{ isset($doctor) ? 'd-none' : '' }}" id="password-field-group">
            <div class="mb-3">
                <label for="password" class="form-label">{{ __('clinic.lbl_password') }} <span class="text-danger">*</span></label>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                    id="password" name="password"
                    placeholder="{{ __('clinic.lbl_password') }}">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback d-none" id="password_error">
                    {{ __('Password must be 8 to 14 characters and contain at least one uppercase, one lowercase, one number and one symbol.') }}
                </div>
            </div>
        </div>

        <div class="col-md-6 {{ isset($doctor) ? 'd-none' : '' }}" id="confirm-password-field-group">
            <div class="mb-3">
                <label for="confirm_password" class="form-label">{{ __('clinic.lbl_confirm_password') }} <span class="text-danger">*</span></label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                    id="confirm_password" name="confirm_password"
                    placeholder="{{ __('clinic.lbl_confirm_password') }}"
                    autocomplete="new-password">
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback d-none" id="confirm_password_error">
                    {{ __('Passwords do not match.') }}
                </div>
            </div>
        </div>

        {{-- About Self --}}
        <div class="col-md-6">
            <div class="mb-3">
                <label for="about_self" class="form-label">{{ __('clinic.lbl_about_self') }}</label>
                <input type="text" class="form-control"
                    id="about_self" name="about_self"
                    value="{{ old('about_self', $doctor->about_self ?? '') }}"
                    placeholder="{{ __('clinic.lbl_about_self') }}">
            </div>
        </div>

        {{-- Expert --}}
        <div class="col-md-6">
            <div class="mb-3">
                <label for="expert" class="form-label">{{ __('clinic.lbl_expert') }}</label>
                <input type="text" class="form-control"
                    id="expert" name="expert"
                    value="{{ old('expert', $doctor->expert ?? '') }}"
                    placeholder="{{ __('clinic.lbl_expert') }}">
            </div>
        </div>

        {{-- Other Details --}}
        <div class="col-md-12 mt-4">
            <legend class="px-0 text-capitalize">{{ __('clinic.other_detail') }}</legend>
        </div>

        {{-- Commission (multi select) --}}
        <div class="col-md-6">
            <div class="mb-3">
                <label for="commission_id" class="form-label fw-semibold">
                    {{ __('clinic.lbl_select_commission') }} <span class="text-danger">*</span>
                </label>
                <select class="form-select select2 @error('commission_id') is-invalid @enderror"
                        id="commission_id"
                        name="commission_id[]"
                        multiple="multiple"
                        data-placeholder="{{ __('clinic.lbl_select_commission') }}">
                    @foreach($commissions as $commission)
                        <option value="{{ $commission->id }}"
                            {{ collect(old('commission_id', $doctor->commission_id ?? []))->contains($commission->id) ? 'selected' : '' }}>
                            {{ $commission->title }}
                            ({{ $commission->commission_type === 'percentage' ? $commission->commission_value . ' %' : Currency::format($commission->commission_value) }})
                        </option>
                    @endforeach
                </select>
                @error('commission_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="invalid-feedback d-none" id="commission_id_error">
                    {{ __('clinic.please_select_at_least_one_commission') }}
                </div>
            </div>
        </div>


        {{-- Vendor --}}
        @if(multivendor() && auth()->user()->hasAnyRole(['admin', 'demo_admin']))
        <div class="col-md-6">
            <div class="mb-3">
                <label for="vendor_id" class="form-label">{{ __('clinic.clinic_admin') }}</label>
                <select class="form-select select2 @error('vendor_id') is-invalid @enderror"
                        id="vendor_id" name="vendor_id"
                        data-placeholder="{{ __('clinic.clinic_admin') }}">
                    <option value="">{{ __('clinic.clinic_admin') }}</option>
                    @foreach($vendor as $vendors)
                        <option value="{{ $vendors->id }}"
                            {{ old('vendor_id', $doctor->vendor_id ?? '') == $vendors->id ? 'selected' : '' }}>
                            {{ $vendors->first_name }} {{ $vendors->last_name }}
                        </option>
                    @endforeach
                </select>
                @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        @endif

        {{-- Clinic Center (multi select with "Select All") --}}
        <div class="col-md-6">
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label for="clinic_id_doctor" class="form-label fw-semibold mb-0">
                        {{ __('clinic.lbl_select_clinic_center') }} <span class="text-danger">*</span>
                    </label>
                    <div class="form-check form-check-inline m-0">
                        <input type="checkbox" id="select_all_clinics" class="form-check-input">
                        <label for="select_all_clinics" class="form-check-label small text-muted">
                            {{ __('Select All') }}
                        </label>
                    </div>
                </div>
                <select class="form-select select2 @error('clinic_id') is-invalid @enderror"
                        id="clinic_id_doctor"
                        name="clinic_id[]"
                        multiple="multiple"
                        data-placeholder="{{ __('clinic.select_clinic_center') }}">
                    {{-- Options will be loaded via AJAX based on vendor selection --}}
                </select>
                @error('clinic_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="invalid-feedback d-none" id="clinic_id_error">
                    {{ __('clinic.please_select_at_least_one_clinic_center') }}
                </div>
            </div>
        </div>

        {{-- Services (multi select with "Select All") --}}
        <div class="col-md-6">
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <label for="service_id" class="form-label fw-semibold mb-0">
                        {{ __('clinic.lbl_select_service') }} <span class="text-danger">*</span>
                    </label>
                    <div class="form-check form-check-inline m-0">
                        <input type="checkbox" id="select_all_services" class="form-check-input">
                        <label for="select_all_services" class="form-check-label small text-muted">
                            {{ __('Select All') }}
                        </label>
                    </div>
                </div>
                <select class="form-select select2 @error('service_id') is-invalid @enderror"
                        id="service_id"
                        name="service_id[]"
                        multiple="multiple"
                        data-placeholder="{{ __('clinic.lbl_select_service') }}">
                    {{-- Options will be filled dynamically via AJAX --}}
                </select>
                @error('service_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="invalid-feedback d-none" id="service_id_error">
                    {{ __('clinic.please_select_at_least_one_service') }}
                </div>
            </div>
        </div>
        {{-- Experience --}}
        <div class="col-md-6">
            <div class="mb-3">
                <label for="experience" class="form-label">{{ __('clinic.experience') }}</label>
                <input type="text" class="form-control"
                    id="experience" name="experience"
                    value="{{ old('experience', $doctor->experience ?? '') }}"
                    placeholder="{{ __('clinic.experience') }}">
            </div>
        </div>

        {{-- Postal Code --}}
        <div class="col-md-6">
            <div class="mb-3">
                <label for="pincode" class="form-label">{{ __('clinic.lbl_postal_code') }}</label>
                <input type="text" class="form-control"
                    id="pincode" name="pincode"
                    value="{{ old('pincode', $doctor->pincode ?? '') }}"
                    placeholder="{{ __('clinic.lbl_postal_code') }}"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                >
                <div class="invalid-feedback d-none" id="pincode-error">
                    {{ __('Only digits are allowed for postal code.') }}
                </div>
            </div>
        </div>

        {{-- Country / State / City - Country hidden, UK auto-set --}}
        <input type="hidden" id="country" name="country" value="229">
        <div class="col-md-6 mb-3">
            <label for="state" class="form-label">{{ __('clinic.lbl_state') }}</label>
            <select class="form-select select2"
                    id="state" name="state" data-placeholder="{{ __('clinic.lbl_state') }}">
                <option value="">{{ __('clinic.lbl_state') }}</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label for="city" class="form-label">{{ __('clinic.lbl_city') }}</label>
            <select class="form-select select2"
                    id="city" name="city" data-placeholder="{{ __('clinic.lbl_city') }}">
                <option value="">{{ __('clinic.lbl_city') }}</option>
            </select>
        </div>

        {{-- Address --}}
        <div class="col-md-12 mt-3">
            <div class="mb-3">
                <label for="address" class="form-label">{{ __('clinic.lbl_address') }}</label>
                <textarea class="form-control"
                    id="address" name="address" rows="3"
                    placeholder="{{ __('clinic.lbl_address') }}">{{ old('address', $doctor->address ?? '') }}</textarea>
            </div>
        </div>

        {{-- Latitude --}}
        <div class="col-md-4">
            <div class="mb-3">
                <label for="latitude" class="form-label">{{ __('clinic.lbl_lat') }}</label>
                <input type="text" class="form-control"
                    id="latitude" name="latitude"
                    value="{{ old('latitude', $doctor->latitude ?? '') }}"
                    placeholder="{{ __('clinic.lbl_lat') }}">
                <div class="invalid-feedback d-none" id="latitude-error">
                    {{ __('Only decimal values are allowed for latitude.') }}
                </div>
            </div>
        </div>

        {{-- Longitude --}}
        <div class="col-md-4">
            <div class="mb-3">
                <label for="longitude" class="form-label">{{ __('clinic.lbl_long') }}</label>
                <input type="text" class="form-control"
                    id="longitude" name="longitude"
                    value="{{ old('longitude', $doctor->longitude ?? '') }}"
                    placeholder="{{ __('clinic.lbl_long') }}">
                <div class="invalid-feedback d-none" id="longitude-error">
                    {{ __('Only decimal values are allowed for longitude.') }}
                </div>
            </div>
        </div>

        {{-- Social Media --}}
        <div class="col-md-12 mt-4">
            <legend class="px-0 text-capitalize">{{ __('clinic.social_media') }}</legend>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="facebook_link" class="form-label">{{ __('clinic.lbl_facebook_link') }}</label>
                <input type="url" class="form-control"
                    id="facebook_link" name="facebook_link"
                    value="{{ old('facebook_link', $doctor->facebook_link ?? '') }}"
                    placeholder="{{ __('clinic.lbl_facebook_link') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="instagram_link" class="form-label">{{ __('clinic.lbl_instagram_link') }}</label>
                <input type="url" class="form-control"
                    id="instagram_link" name="instagram_link"
                    value="{{ old('instagram_link', $doctor->instagram_link ?? '') }}"
                    placeholder="{{ __('clinic.lbl_instagram_link') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="twitter_link" class="form-label">{{ __('clinic.lbl_twitter_link') }}</label>
                <input type="url" class="form-control"
                    id="twitter_link" name="twitter_link"
                    value="{{ old('twitter_link', $doctor->twitter_link ?? '') }}"
                    placeholder="{{ __('clinic.lbl_twitter_link') }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="dribbble_link" class="form-label">{{ __('clinic.lbl_dribbble_link') }}</label>
                <input type="url" class="form-control"
                    id="dribbble_link" name="dribbble_link"
                    value="{{ old('dribbble_link', $doctor->dribbble_link ?? '') }}"
                    placeholder="{{ __('clinic.lbl_dribbble_link') }}">
            </div>
        </div>

        {{-- Qualifications --}}
        <div class="col-md-12 mt-4">
            <legend class="px-0 text-capitalize">{{ __('clinic.qualification') }}</legend>
        </div>
        <div id="qualifications-container">
            @php
                $qualifications = [];
                if (isset($doctor) && $doctor->qualifications) {
                    if (is_array($doctor->qualifications)) {
                        $qualifications = $doctor->qualifications;
                    } elseif (is_string($doctor->qualifications)) {
                        $decoded = json_decode($doctor->qualifications, true);
                        $qualifications = is_array($decoded) ? $decoded : [];
                    }
                }
                if (empty($qualifications)) $qualifications = [['degree'=>'', 'university'=>'', 'year'=>'']];
            @endphp
            @foreach($qualifications as $index => $qualification)
                <div class="qualification-row" data-index="{{ $index }}">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('clinic.degree') }}</label>
                            <input type="text" class="form-control"
                                name="qualifications[{{ $index }}][degree]"
                                value="{{ $qualification['degree'] ?? '' }}"
                                placeholder="{{ __('clinic.degree') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('clinic.university') }}</label>
                            <input type="text" class="form-control"
                                name="qualifications[{{ $index }}][university]"
                                value="{{ $qualification['university'] ?? '' }}"
                                placeholder="{{ __('clinic.university') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">{{ __('clinic.year') }}</label>
                            <select class="form-select select2 no-clear" name="qualifications[{{ $index }}][year]" autocomplete="off">
                                <option value="">{{ __('clinic.year') }}</option>
                                @for($year = date('Y'); $year >= 1900; $year--)
                                    <option value="{{ $year }}" {{ ($qualification['year'] ?? '') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        @if($index > 0)
                        <div class="col-md-1 text-end mb-3">
                            <button type="button" class="btn btn-danger px-3 qualification-remove-btn">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        </div>
                        @endif
                    </div>
                    <div class="col-12">
                        <div class="border-top mt-3 pb-3"></div>
                    </div>
                </div>
            @endforeach

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.jQuery && window.jQuery.fn.select2) {
                        window.jQuery('#doctor-form select.no-clear').select2({
                            allowClear: false,
                            dropdownParent: $('#form-offcanvas'),
                            placeholder: "{{ __('clinic.year') }}"
                        });
                    }
                });
            </script>
        </div>
        <div class="col-md-12 text-end mb-3">
            <button type="button" class="btn btn-primary" id="addQualificationBtn">
                <i class="fa-solid fa-plus"></i> {{ __('clinic.add_qualification') }}
            </button>
        </div>

        {{-- Signature --}}
        <div class="col-md-12 mb-3">
            <label class="form-label">{{ __('clinic.lbl_signature') }}</label>
            <div class="signature-pad-container">
                <div id="signaturePadWrapper">
                    <canvas id="signature-pad" class="bg-white rounded w-100"></canvas>
                    <input type="hidden" id="signature_data" name="signature" value="{{ old('signature', $doctor->signature ?? '') }}">
                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-sm btn-secondary" id="clearSignatureBtn">{{__('clinic.clear')}}</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="col-md-12 mt-4">
            <div class="d-flex justify-content-between align-items-center form-control">
                <label class="form-label mb-0">{{ __('clinic.lbl_status') }}</label>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                        {{ old('status', isset($doctor) ? $doctor->status : 1) ? 'checked' : '' }}>
                </div>
            </div>
        </div>
    </div>
    
    <div class="offcanvas-footer border-top pt-4">
        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2">
            <!-- Cancel -->
            <button
                type="button"
                class="btn btn-white px-4 py-2 fw-semibold"
                data-bs-dismiss="offcanvas"
                id="cancel-doctor-service-btn">
                {{ __('messages.cancel') }}
            </button>
        
            <!-- Save -->
            <button
                type="submit"
                class="btn px-4 py-2 fw-semibold btn btn-secondary"
                id="save-doctor-service-btn">
                <span id="save-doctor-service-btn-text">{{ __('messages.save') }}</span>
                <span id="save-doctor-service-btn-saving" class="d-none">{{ __('clinic.saving') }}</span>
                <span id="save-doctor-service-btn-updating" class="d-none">{{ __('clinic.updating') }}</span>
                <span id="save-doctor-service-btn-loader"
                    class="spinner-border spinner-border-sm d-none ms-2"
                    role="status" aria-hidden="true"></span>
            </button>
        </div>
        
    </div>
    
</form>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/css/intlTelInput.css"/>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/intlTelInput.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = "{{ url('/') }}";
    const routes = {
        countryList: "{{ route('backend.country.index_list') }}",
        stateList: "{{ route('backend.state.index_list') }}",
        cityList: "{{ route('backend.city.index_list') }}",
        serviceList: "{{ route('backend.doctor.service_list') }}",
        doctorEdit: "{{ route('backend.doctor.edit', ':id') }}",
        doctorStore: "{{ route('backend.doctor.store') }}",
        doctorUpdate: "{{ route('backend.doctor.update', ':id') }}"
    };
    const $ = window.jQuery;

    // --- Phone input with dial code ---
    // Common country codes supported by intl-tel-input:
    // +1 (US/Canada), +44 (UK), +91 (India), +86 (China), +49 (Germany),
    // +33 (France), +39 (Italy), +34 (Spain), +81 (Japan), +82 (South Korea),
    // +61 (Australia), +55 (Brazil), +52 (Mexico), +7 (Russia), +20 (Egypt),
    // +27 (South Africa), +234 (Nigeria), +254 (Kenya), +966 (Saudi Arabia),
    // +971 (UAE), +974 (Qatar), +965 (Kuwait), +973 (Bahrain), +968 (Oman)
    var iti = null;
    const input = document.querySelector("#mobile");
    const dialCodeHidden = document.querySelector("#dial_code");
    if (input) {
        iti = window.intlTelInput(input, {
            initialCountry: "{{ old('dial_code', $doctor->dial_code ?? '') ? 'auto' : 'gb' }}",
            separateDialCode: true,
            nationalMode: false, // Set to false to allow international format
            autoPlaceholder: "aggressive",
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js"
        });

        // Set initial dial code and number if editing (using clinic form approach)
        const oldDialCode = "{{ old('dial_code', $doctor->dial_code ?? '') }}";
        const oldMobile = "{{ old('mobile', $doctor->mobile ?? '') }}";
        if (oldDialCode && oldMobile) {
            // Format the phone number properly for editing (same as clinic form)
            let formattedNumber = '';
            if (oldMobile.startsWith('+')) {
                // If mobile already has country code, use it directly
                if (oldMobile.includes(' ')) {
                    formattedNumber = oldMobile;
                } else {
                    // If no space, add one after country code
                    formattedNumber = oldMobile.replace(/(\+\d{1,4})(\d+)/, '$1 $2');
                }
            } else if (oldDialCode) {
                // Combine dial code with mobile number and add space
                formattedNumber = oldDialCode + ' ' + oldMobile;
            } else {
                formattedNumber = oldMobile;
            }

            console.log('Setting doctor phone number:', formattedNumber);

            // Set the number in the intl-tel-input
            iti.setNumber(formattedNumber);

            // Force country detection if the number has a country code
            if (formattedNumber.startsWith('+')) {
                // Extract country code from the number
                const countryCodeMatch = formattedNumber.match(/^\+(\d{1,4})/);
                if (countryCodeMatch) {
                    const targetDialCode = countryCodeMatch[1];
                    console.log('Target dial code from number:', targetDialCode);

                    // Wait a bit for the number to be processed
                    setTimeout(function() {
                        const currentCountryData = iti.getSelectedCountryData();
                        console.log('Current country dial code:', currentCountryData.dialCode);

                        // If the current country doesn't match the number's country code, fix it
                        if (currentCountryData.dialCode !== targetDialCode) {
                            console.log('Country mismatch detected. Fixing...');

                            // Find the correct country by dial code
                            const allCountries = iti.getCountryData();
                            let correctCountry = null;

                            for (let i = 0; i < allCountries.length; i++) {
                                if (allCountries[i].dialCode === targetDialCode) {
                                    correctCountry = allCountries[i];
                                    break;
                                }
                            }

                            if (correctCountry) {
                                console.log('Setting country to:', correctCountry.name, '(' + correctCountry.iso2 + ')');

                                // Set the country and force update
                                iti.setCountry(correctCountry.iso2);

                                // Force the country change event to trigger
                                setTimeout(function() {
                                    // Re-set the number to ensure it's properly formatted with the new country
                                    iti.setNumber(formattedNumber);

                                    // Update the hidden dial code field
                                    if (dialCodeHidden) {
                                        dialCodeHidden.value = "+" + correctCountry.dialCode;
                                    }

                                    // Trigger change event to update the UI
                                    const countryChangeEvent = new Event('countrychange', { bubbles: true });
                                    input.dispatchEvent(countryChangeEvent);

                                    // Verify the country was actually set
                                    setTimeout(function() {
                                        const verifyCountryData = iti.getSelectedCountryData();
                                        console.log('Verification - Current country:', verifyCountryData.name, 'Dial code:', verifyCountryData.dialCode);

                                        if (verifyCountryData.dialCode !== targetDialCode) {
                                            console.warn('Country setting failed. Trying alternative approach...');
                                            // Try setting the country again
                                            iti.setCountry(correctCountry.iso2);
                                        } else {
                                            console.log('Country successfully set to:', correctCountry.name);
                                        }
                                    }, 50);
                                }, 100);
                            } else {
                                console.warn('Country not found for dial code:', targetDialCode);
                            }
                        } else {
                            console.log('Country already correct:', currentCountryData.name);
                            if (dialCodeHidden) {
                                dialCodeHidden.value = "+" + currentCountryData.dialCode;
                            }
                        }
                    }, 200);
                }
            } else {
                // If no country code in number, just update the hidden field
                setTimeout(function() {
                    if (iti) {
                        const countryData = iti.getSelectedCountryData();
                        console.log('Doctor selected country data:', countryData);
                        if (dialCodeHidden) {
                            dialCodeHidden.value = "+" + countryData.dialCode;
                        }
                    }
                }, 300);
            }
        }

        // Set initial dial code hidden input
        const initData = iti.getSelectedCountryData();
        if (initData && initData.dialCode) {
            dialCodeHidden.value = "+" + initData.dialCode;
        }

        // Update dial code on country change
        input.addEventListener("countrychange", function () {
            const countryData = iti.getSelectedCountryData();
            dialCodeHidden.value = "+" + countryData.dialCode;
        });

        // Prevent non-digit input for contact number
        input.addEventListener('input', function(e) {
            let val = input.value;
            // Remove all non-digit characters
            input.value = val.replace(/[^0-9]/g, '');
        });
        // Also block pasting non-digits
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            let text = (e.clipboardData || window.clipboardData).getData('text');
            text = text.replace(/[^0-9]/g, '');
            document.execCommand('insertText', false, text);
        });
        // Block keypress for non-digits
        input.addEventListener('keypress', function(e) {
            if (e.which < 48 || e.which > 57) {
                e.preventDefault();
            }
        });
    }

    // Prevent non-digit input for postal code
    const pincodeInput = document.getElementById('pincode');
    if (pincodeInput) {
        pincodeInput.addEventListener('input', function(e) {
            let val = pincodeInput.value;
            pincodeInput.value = val.replace(/[^0-9]/g, '');
        });
        pincodeInput.addEventListener('paste', function(e) {
            e.preventDefault();
            let text = (e.clipboardData || window.clipboardData).getData('text');
            text = text.replace(/[^0-9]/g, '');
            document.execCommand('insertText', false, text);
        });
        pincodeInput.addEventListener('keypress', function(e) {
            if (e.which < 48 || e.which > 57) {
                e.preventDefault();
            }
        });
    }

    /* ---------- Disable ALL native HTML5 validation completely ---------- */
    const $form = $('#doctor-form');
    $form.attr('novalidate', 'novalidate');
    $form.find('[required]').removeAttr('required');
    $form.find('[pattern]').removeAttr('pattern');
    $form.on('invalid', 'input, select, textarea', function (e) { e.preventDefault(); });

    /* ---------- Select2 for Doctor Form Only ---------- */
    // Initialize Select2 only for elements within the doctor form
    function initializeDoctorFormSelect2() {
        // Destroy any existing Select2 instances within the doctor form
        $('#doctor-form .select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        // Initialize Select2 for single select dropdowns in doctor form
        $('#doctor-form .select2:not([multiple])').select2({
            width: '100%',
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownParent: $('#form-offcanvas'), // Important for offcanvas positioning
            placeholder: function(){ return $(this).data('placeholder'); },
            closeOnSelect: true,
            escapeMarkup: function (markup) { return markup; },
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }
                return data.text;
            },
            templateSelection: function(data) {
                return data.text;
            }
        });

        // Initialize Select2 for multi-select dropdowns in doctor form
        $('#doctor-form .select2[multiple]').select2({
            width: '100%',
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownParent: $('#form-offcanvas'), // Important for offcanvas positioning
            placeholder: function(){ return $(this).data('placeholder'); },
            closeOnSelect: false,
            escapeMarkup: function (markup) { return markup; },
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }
                return data.text;
            },
            templateSelection: function(data) {
                return data.text;
            },
            language: {
                noResults: function() {
                    return "{{ __('clinic.no_results_found') }}";
                },
                searching: function() {
                    return "{{ __('clinic.searching') }}";
                }
            }
        });
    }

    // Initialize Select2 for doctor form
    initializeDoctorFormSelect2();

    /* ---------- JS validators ---------- */
    function isValidEmail(email){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email).toLowerCase()); }
    function isValidName(name){ return /^[a-zA-Z\s]+$/.test(name.trim()); }
    function isValidPhone(phone){ return /^[0-9]{6,15}$/.test(phone.trim()); }

    // Password must be 8-14 chars, at least one lowercase, one uppercase, one digit, one special char
    function isValidPassword(pw) {
        if (typeof pw !== 'string' || pw.length < 8 || pw.length > 14) {
            return false;
        }
        if (!/[a-z]/.test(pw)) {
            return false;
        }
        if (!/[A-Z]/.test(pw)) {
            return false;
        }
        if (!/[0-9]/.test(pw)) {
            return false;
        }
        if (!/[@$!%*#?&]/.test(pw)) {
            return false;
        }
        return true;
    }

    function getPasswordErrorMessage(pw) {
        if (typeof pw !== 'string' || pw.length < 8 || pw.length > 14) {
            return "{{ __('clinic.password_length_error') }}";
        }
        if (!/[a-z]/.test(pw)) {
            return "{{ __('clinic.password_lowercase_error') }}";
        }
        if (!/[A-Z]/.test(pw)) {
            return "{{ __('clinic.password_uppercase_error') }}";
        }
        if (!/[0-9]/.test(pw)) {
            return "{{ __('clinic.password_number_error') }}";
        }
        if (!/[@$!%*#?&]/.test(pw)) {
            return "{{ __('clinic.password_symbol_error') }}";
        }
        return '';
    }

    // First Name
    $('#first_name').on('input blur', function(){
        const v=$(this).val().trim();
        if(v===''||!isValidName(v)){ $(this).addClass('is-invalid'); $('#first_name_error').removeClass('d-none'); }
        else{ $(this).removeClass('is-invalid'); $('#first_name_error').addClass('d-none'); }
    });
    // Last Name
    $('#last_name').on('input blur', function(){
        const v=$(this).val().trim();
        if(v===''||!isValidName(v)){ $(this).addClass('is-invalid'); $('#last_name_error').removeClass('d-none'); }
        else{ $(this).removeClass('is-invalid'); $('#last_name_error').addClass('d-none'); }
    });
    // Email
    $('#doctor_email').on('input blur', function(){
        const v=$(this).val().trim();
        if(v===''||!isValidEmail(v)){
            $(this).addClass('is-invalid');
            $('#doctor_email_error').removeClass('d-none').text('{{ __("clinic.please_enter_valid_email_address") }}');
        }
        else{
            $(this).removeClass('is-invalid');
            $('#doctor_email_error').addClass('d-none');
        }
    });
    // Phone
    $('#mobile').on('input blur', function(){
        const v=$(this).val().trim();

        if(v===''){
            $(this).addClass('is-invalid');
            $('#mobile_error').removeClass('d-none').css('display', 'block').text('{{ __("clinic.contact_number_is_required") }}');
        }
        else{
            $(this).removeClass('is-invalid');
            $('#mobile_error').addClass('d-none').css('display', 'none');
        }
    });
    // Password
    // --- Password Validation (complex requirements) ---
    $('#password').on('input blur', function(){
        const v = $(this).val();
        if($('#password-field-group').is(':visible')) {
            if(!isValidPassword(v)){
                $(this).addClass('is-invalid');
                $('#password_error')
                    .removeClass('d-none')
                    .text(getPasswordErrorMessage(v));
            } else {
                $(this).removeClass('is-invalid');
                $('#password_error').addClass('d-none');
            }
        }
    });

    // --- Confirm Password Validation (match + complex requirements) ---
    $('#confirm_password').on('input blur', function(){
        const pw = $('#password').val();
        const cpw = $(this).val();

        if($('#confirm-password-field-group').is(':visible')) {
            if(!isValidPassword(cpw)){
                $(this).addClass('is-invalid');
                $('#confirm_password_error')
                    .removeClass('d-none')
                    .text(getPasswordErrorMessage(cpw));
            } else if(pw !== cpw){
                $(this).addClass('is-invalid');
                $('#confirm_password_error')
                    .removeClass('d-none')
                    .text('Passwords do not match.');
            } else {
                $(this).removeClass('is-invalid');
                $('#confirm_password_error').addClass('d-none');
            }
        }
    });

    // Commission / Clinic / Service
    $('#commission_id').on('change blur', function(){
        const v=$(this).val(); if(!v||v.length===0){ $(this).addClass('is-invalid'); $('#commission_id_error').removeClass('d-none'); }
        else{ $(this).removeClass('is-invalid'); $('#commission_id_error').addClass('d-none'); }
    });
    $('#clinic_id_doctor').on('change blur', function(){
        const v=$(this).val(); if(!v||v.length===0){ $(this).addClass('is-invalid'); $('#clinic_id_error').removeClass('d-none'); }
        else{ $(this).removeClass('is-invalid'); $('#clinic_id_error').addClass('d-none'); }
    });
    $('#service_id').on('change blur', function(){
        const v=$(this).val(); if(!v||v.length===0){ $(this).addClass('is-invalid'); $('#service_id_error').removeClass('d-none'); }
        else{ $(this).removeClass('is-invalid'); $('#service_id_error').addClass('d-none'); }
    });

    /* ---------- Select/Deselect All (Clinics & Services) ---------- */
    // Note: "Select All" only selects the CURRENTLY FILTERED/VISIBLE options in the dropdown
    // For clinics: selects only clinics belonging to the selected vendor (if multivendor enabled)
    // For services: selects only services belonging to the selected clinic(s)

    $('#select_all_clinics').on('change', function(){
        const $sel = $('#clinic_id_doctor');
        if(this.checked){
            // Get all CURRENTLY VISIBLE options (already filtered by vendor) as an ARRAY
            const vals = $sel.find('option').map(function(){
                return $(this).val();
            }).get();

            // Select all clinics (sets array value)
            $sel.val(vals).trigger('change.select2');

            // Manually trigger services loading for all selected clinics
            if (vals && vals.length > 0) {
                const currentServices = $('#service_id').val() || [];
                loadServices(vals, currentServices);
            }
        }
        else{
            // Clear all selections
            $sel.val([]).trigger('change.select2');
            // Clear services when deselecting all clinics
            loadServices([], []);
        }
    });
    $('#clinic_id_doctor').on('change', function(){
        const total=$(this).find('option').length, selected=$(this).val()?$(this).val().length:0;
        $('#select_all_clinics').prop('checked', selected===total && total>0);
    });

    $('#select_all_services').on('change', function(){
        const $sel=$('#service_id');
        if(this.checked){
            // Get all CURRENTLY VISIBLE options (already filtered by selected clinics)
            const vals=$sel.find('option').map(function(){return $(this).val();}).get();
            $sel.val(vals).trigger('change.select2');
        }
        else{
            $sel.val([]).trigger('change.select2');
        }
    });
    $('#service_id').on('change', function(){
        const total=$(this).find('option').length, selected=$(this).val()?$(this).val().length:0;
        $('#select_all_services').prop('checked', selected===total && total>0);
    });

    /* ---------- Clinics fetcher (filter by vendor if multivendor enabled) ---------- */
    function loadClinics(vendorId = '', selectedClinics = []) {
        const $clinic = $('#clinic_id_doctor');
        const url = "{{ route('backend.doctor.get_clinics_by_vendor') }}";

        // Show loading state
        $clinic.prop('disabled', true);

        // Build query params: filter by vendor_id if provided (multivendor)
        const params = vendorId ? '?vendor_id=' + vendorId : '';

        fetch(url + params, {
            headers: {'Accept': 'application/json'}
        })
        .then(r => r.json())
        .then(response => {
            $clinic.empty();

            if (response.status && Array.isArray(response.data)) {
                response.data.forEach(clinic => {
                    const isSelected = selectedClinics.includes(String(clinic.id)) ||
                                     selectedClinics.includes(Number(clinic.id));
                    $clinic.append(new Option(clinic.name, clinic.id, isSelected, isSelected));
                });
            }

            $clinic.val(selectedClinics).trigger('change.select2');
            $clinic.prop('disabled', false);

            // Update "Select All" checkbox state
            const total = $clinic.find('option').length;
            const selected = selectedClinics.length;
            $('#select_all_clinics').prop('checked', selected === total && total > 0);
        })
        .catch(err => {
            console.error('Error loading clinics:', err);
            $clinic.prop('disabled', false);
        });
    }

    /* ---------- Country/State/City fetchers ---------- */
    function loadCountries(selected=''){
        fetch(routes.countryList, { headers:{'Accept':'application/json'} })
        .then(r=>r.json()).then(list=>{
            const $country=$('#country');
            $country.empty().append(new Option("{{ __('clinic.lbl_country') }}",'',true,false));
            if(Array.isArray(list)){ list.forEach(c=> $country.append(new Option(c.name, c.id, false, String(c.id)===String(selected)))); }
            $country.val(selected).trigger('change.select2');
        });
    }
    function loadStates(countryId, selected=''){
        const $state=$('#state');
        $state.empty().append(new Option("{{ __('clinic.lbl_state') }}",'',true,false));
        $('#city').empty().append(new Option("{{ __('clinic.lbl_city') }}",'',true,false)).trigger('change.select2');
        if(!countryId){ $state.trigger('change.select2'); return; }
        fetch(routes.stateList + '?country_id='+countryId, { headers:{'Accept':'application/json'} })
        .then(r=>r.json()).then(list=>{
            if(Array.isArray(list)){ list.forEach(s=> $state.append(new Option(s.name, s.id, false, String(s.id)===String(selected)))); }
            $state.val(selected).trigger('change.select2');
        });
    }
    function loadCities(stateId, selected=''){
        const $city=$('#city');
        $city.empty().append(new Option("{{ __('clinic.lbl_city') }}",'',true,false));
        if(!stateId){ $city.trigger('change.select2'); return; }
        fetch(routes.cityList + '?state_id='+stateId, { headers:{'Accept':'application/json'} })
        .then(r=>r.json()).then(list=>{
            if(Array.isArray(list)){ list.forEach(c=> $city.append(new Option(c.name, c.id, false, String(c.id)===String(selected)))); }
            $city.val(selected).trigger('change.select2');
        });
    }
    let oldCountry=229; // UK - auto-set
    let oldState="{{ old('state', $doctor->state ?? '') }}";
    let oldCity="{{ old('city', $doctor->city ?? '') }}";
    // Auto-load UK states on page load
    loadStates(229, oldState);
    if(oldState) loadCities(oldState, oldCity);
    $('#state').on('change', function(){ loadCities(this.value); });

    /* ---------- Initialize Clinics ---------- */
    @if(multivendor())
        let oldVendorId = "{{ old('vendor_id', $doctor->vendor_id ?? '') }}";
        let oldClinicIds = @json(old('clinic_id', $doctor->clinic_id ?? []));

        // Load clinics filtered by vendor on page load
        loadClinics(oldVendorId, oldClinicIds);

        // Reload clinics when vendor/clinic admin changes
        $('#vendor_id').on('change', function() {
            const selectedVendorId = $(this).val();

            // Clear current clinic and service selections
            $('#clinic_id_doctor').val([]).trigger('change.select2');
            $('#service_id').val([]).trigger('change.select2');

            // Uncheck "Select All" checkboxes
            $('#select_all_clinics').prop('checked', false);
            $('#select_all_services').prop('checked', false);

            // Reload clinics for the selected vendor (only vendor's clinics will be available)
            loadClinics(selectedVendorId, []);

            // Clear services (will be loaded when clinics are selected)
            loadServices([], []);
        });
    @else
        // If multivendor is not enabled, load all clinics
        let oldClinicIds = @json(old('clinic_id', $doctor->clinic_id ?? []));
        loadClinics('', oldClinicIds);
    @endif

   /* ---------- Image preview with validation ---------- */
    function previewImage(input){
        const allowedExtensions = ['jpeg','jpg','png','gif'];
        const file = input.files[0];

        if(!file) return;

        const fileExt = file.name.split('.').pop().toLowerCase();

        if(!allowedExtensions.includes(fileExt)){
            // Show error
            $('.create-service-image .text-danger').text("{{ __('clinic.only_jpeg_jpg_png_files_allowed') }}").removeClass('d-none');
            input.value = ''; // reset input
            return;
        }

        // Hide error if valid
        $('.create-service-image .text-danger').addClass('d-none');

        const reader = new FileReader();
        reader.onload = e => $('#imagePreview').attr('src', e.target.result);
        reader.readAsDataURL(file);
        $('#remove_profile_image').val("0");
    }

    function removeImage(){
        $('#imagePreview').attr('src', "{{ asset('img/avatar/avatar.webp') }}");
        $('#profile_image').val('');
        $('#remove_profile_image').val('1');
        $('.create-service-image .text-danger').addClass('d-none'); // hide error
    }

    // $('#removeImageBtn').on('click', removeImage);
    $('#uploadImageBtn').on('click', ()=> $('#profile_image').click());
    $('#profile_image').on('change', function(){ previewImage(this); });

    /* ---------- Qualifications add/remove ---------- */
    function getQualificationRowHtml(index, degree='', university='', year=''){
        const currentYear=(new Date()).getFullYear();
        let yearOptions=`<option value="">{{ __('clinic.year') }}</option>`;
        for(let y=currentYear; y>=1900; y--){ yearOptions+=`<option value="${y}"${year==y?' selected':''}>${y}</option>`; }
        return `
            <div class="qualification-row" data-index="${index}">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('clinic.degree') }}</label>
                        <input type="text" class="form-control" name="qualifications[${index}][degree]" value="${degree ?? ''}" placeholder="{{ __('clinic.degree') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('clinic.university') }}</label>
                        <input type="text" class="form-control" name="qualifications[${index}][university]" value="${university ?? ''}" placeholder="{{ __('clinic.university') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('clinic.year') }}</label>
                        <select class="form-select select2 no-clear" name="qualifications[${index}][year]">${yearOptions}</select>
                    </div>
                    <div class="col-md-1 text-end mb-3">
                        <button type="button" class="btn btn-danger px-3 qualification-remove-btn">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12"><div class="border-top mt-3 pb-3"></div></div>
            </div>`;
    }
    function updateQualificationRemoveButtons(){
        const rows=$('#qualifications-container .qualification-row');
        rows.each(function(idx,row){ const $btn=$(row).find('.qualification-remove-btn'); $btn.toggle(rows.length>1); });
        // Initialize Select2 for qualification year dropdowns within doctor form
        $('#doctor-form #qualifications-container .select2').select2({
            width:'100%',
            allowClear:false,
            dropdownParent: $('#form-offcanvas'),
            placeholder:function(){ return $(this).data('placeholder'); }
        });
    }
    $('#addQualificationBtn').on('click', function(){
        const $container=$('#qualifications-container');
        let maxIndex=-1;
        $container.find('.qualification-row').each(function(){ const idx=parseInt($(this).attr('data-index')); if(!isNaN(idx)&&idx>maxIndex) maxIndex=idx; });
        $container.append(getQualificationRowHtml(maxIndex+1));
        updateQualificationRemoveButtons();
    });
    $('#qualifications-container').on('click', '.qualification-remove-btn', function(){ $(this).closest('.qualification-row').remove(); updateQualificationRemoveButtons(); });
    updateQualificationRemoveButtons();

    /* ---------- Service list load (filter by selected clinics) ---------- */
    function loadServices(clinicIds = [], selectedServiceIds = []) {
        let $serviceSelect = $('#service_id');

        // Ensure clinicIds is always an array
        if (!Array.isArray(clinicIds)) {
            clinicIds = clinicIds ? [clinicIds] : [];
        }

        // Show loading state
        $serviceSelect.prop('disabled', true);
        $serviceSelect.empty().append(new Option('Loading services...', '', false, false));
        $serviceSelect.trigger('change.select2');

        // Build query string to filter services by selected clinic(s)
        let query = '';
        if (clinicIds && clinicIds.length > 0) {
            // Pass clinic IDs as comma-separated string for backend filtering
            // Backend will use: WHERE clinic_id IN (1,2,3,4,5)
            query = '?clinic_id=' + clinicIds.join(',');
        } else {
            $serviceSelect.empty().append(new Option('Please select clinic(s) first', '', false, false));
            $serviceSelect.prop('disabled', true);
            return; // Don't fetch if no clinics selected
        }

        fetch(routes.serviceList + query, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(list => {
                // Clear loading message
                $serviceSelect.empty();

                if (Array.isArray(list)) {
                    if (list.length === 0) {
                        // No services found for selected clinics
                        $serviceSelect.append(new Option('No services available for selected clinic(s)', '', false, false));
                    } else {
                        list.forEach(service => {
                            if (service.name && service.name.trim() !== '') {
                                let selected = selectedServiceIds.includes(String(service.id));
                                $serviceSelect.append(new Option(service.name, service.id, selected, selected));
                            }
                        });
                    }
                }

                $serviceSelect.prop('disabled', false);
                $serviceSelect.trigger('change.select2');

                // Update "Select All Services" checkbox
                const total = $serviceSelect.find('option').length;
                const selected = $serviceSelect.val() ? $serviceSelect.val().length : 0;
                $('#select_all_services').prop('checked', selected === total && total > 0);
            })
            .catch(err => {
                console.error('Error loading services:', err);
                $serviceSelect.empty().append(new Option('Error loading services', '', false, false));
                $serviceSelect.prop('disabled', false);
            });
    }

    // Reload services when clinic selection changes (including when "Select All Clinics" is used)
    $('#clinic_id_doctor').on('change', function() {
        // Get selected clinic IDs as array
        let selectedClinics = $(this).val();

        // Ensure it's always an array
        if (!selectedClinics) {
            selectedClinics = [];
        } else if (!Array.isArray(selectedClinics)) {
            selectedClinics = [selectedClinics];
        }

        const currentServices = $('#service_id').val() || [];

        // Reload services filtered by the ARRAY of selected clinic IDs
        // Backend will receive: ?clinic_id=1,2,3,4,5 and filter services accordingly
        if (selectedClinics.length > 0) {
            loadServices(selectedClinics, currentServices);
        } else {
            loadServices([], []);
        }
    });

    // On page load, load services based on selected clinics (if any)
    const initialClinics = $('#clinic_id_doctor').val() || [];
    if (initialClinics && initialClinics.length > 0) {
        loadServices(initialClinics);
    }


    /* ---------- Signature pad ---------- */
    let signaturePad;
    const canvas=document.getElementById("signature-pad");
    if(canvas){
        signaturePad=new SignaturePad(canvas);
        function resizeCanvas(){
            const ratio=Math.max(window.devicePixelRatio||1,1);
            const dataUrl = signaturePad && !signaturePad.isEmpty() ? signaturePad.toDataURL("image/png") : null;

            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);

            if (dataUrl) {
                const img = new Image();
                img.onload = function () {
                    canvas.getContext("2d").drawImage(img, 0, 0, canvas.width, canvas.height);
                };
                img.src = dataUrl;
            } else {
                signaturePad.clear();
            }
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();
        $('#clearSignatureBtn').on('click', function(){ signaturePad.clear(); $('#signature_data').val(""); });
        function updateSignatureInput(){ $('#signature_data').val(!signaturePad.isEmpty()?signaturePad.toDataURL("image/png"):""); }
        canvas.addEventListener("mouseup", updateSignatureInput);
        canvas.addEventListener("touchend", updateSignatureInput);
        const val=$('#signature_data').val();
        if(val){
            const img=new window.Image();
            img.onload=function(){ signaturePad.clear(); canvas.getContext("2d").drawImage(img,0,0,canvas.width,canvas.height); };
            img.src=val;
        } else { signaturePad.clear(); }
    }

    /* ---------- Lat/Long & Pincode validation ---------- */
    function isDecimal(val){ return /^-?\d+(\.\d+)?$/.test(val.trim()); }
    function validateLatLongField($input, $errorDiv){
        const val=$input.val().trim();
        if(val===''){ $input.removeClass('is-invalid'); $errorDiv.addClass('d-none'); return true; }
        if(!isDecimal(val)){ $input.addClass('is-invalid'); $errorDiv.removeClass('d-none'); return false; }
        $input.removeClass('is-invalid'); $errorDiv.addClass('d-none'); return true;
    }
    $('#latitude').on('input blur', function(){ validateLatLongField($('#latitude'), $('#latitude-error')); });
    $('#longitude').on('input blur', function(){ validateLatLongField($('#longitude'), $('#longitude-error')); });

    function isDigits(val){ return /^\d*$/.test(val.trim()); }
    function validatePincodeField($input, $errorDiv){
        const val=$input.val().trim();
        if(val===''){ $input.removeClass('is-invalid'); $errorDiv.addClass('d-none'); return true; }
        if(!isDigits(val)){ $input.addClass('is-invalid'); $errorDiv.removeClass('d-none'); return false; }
        $input.removeClass('is-invalid'); $errorDiv.addClass('d-none'); return true;
    }
    $('#pincode').on('input blur', function(){ validatePincodeField($('#pincode'), $('#pincode-error')); });

    /* ---------- Offcanvas (Create/Edit) ---------- */
    const offcanvasEl=document.getElementById('form-offcanvas');
    const titleEl=document.getElementById('offcanvas-title');
    const form=document.getElementById('doctor-form');

    // Function to reset form completely
    function resetForm() {
        // Destroy Select2 instances in doctor form before reset
        $('#doctor-form .select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        // Reset all form fields
        form.reset();

        // Clear hidden fields
                $('#doctor_id').val('');
                $('#form_method').val('POST');
                $('#email_verified_at').val('');
        $('#remove_profile_image').val('0');
        $('#signature_data').val('');

        // Reset image preview
                removeImage();

        // Clear signature pad
        if(signaturePad) {
            signaturePad.clear();
        }

        // Reset all select2 fields
        $('#doctor-form .select2').val('').trigger('change.select2');

        // Reset qualifications to default single row
                $('#qualifications-container').html(getQualificationRowHtml(0));
                updateQualificationRemoveButtons();

        // Reset country/state/city - UK auto-set
                $('#state').empty().append(new Option("{{ __('clinic.lbl_state') }}",'',true,false)).trigger('change.select2');
                $('#city').empty().append(new Option("{{ __('clinic.lbl_city') }}",'',true,false)).trigger('change.select2');
                loadStates(229); // Auto-load UK states

        // Reset vendor and reload clinics accordingly
                $('#vendor_id').val('').trigger('change.select2');
                @if(multivendor())
                    loadClinics('', []); // Load clinics without vendor filter (will be empty or all depending on backend)
                @else
                    loadClinics('', []); // Load all clinics
                @endif

        // Show password fields for create mode
                $('#password-field-group, #confirm-password-field-group').removeClass('d-none');
                $('#password, #confirm_password').prop('disabled', false);

        // Clear all validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').addClass('d-none');

        // Reset checkboxes
                $('#select_all_clinics, #select_all_services').prop('checked', false);

        // Reset phone input
                if(iti) {
                    iti.setNumber('');
                    setTimeout(function() {
                        if (iti) {
                            var code = iti.getSelectedCountryData().dialCode;
                            dialCodeHidden.value = "+" + code;
                        }
                    }, 100);
                }

        // Clear services when resetting (will be loaded when clinics are selected)
                loadServices([], []);

        // Reset status to active
        $('#status').prop('checked', true);

        // Reset button text to Save
        $('#save-doctor-service-btn-text').text("{{ __('messages.save') }}");

        // Re-initialize Select2 for doctor form after reset
        setTimeout(function() {
            initializeDoctorFormSelect2();
        }, 100);
    }

    if(offcanvasEl){
        // Initialize Select2 when offcanvas is shown
        offcanvasEl.addEventListener('shown.bs.offcanvas', function (event){
            // Re-initialize Select2 for doctor form only
            initializeDoctorFormSelect2();
        });

        // Reset form when offcanvas is hidden (after successful save or manual close)
        offcanvasEl.addEventListener('hidden.bs.offcanvas', function (event){
            // Destroy Select2 instances in doctor form to prevent interference with other forms
            $('#doctor-form .select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });

            // Small delay to ensure all animations are complete
            setTimeout(function() {
                resetForm();
            }, 100);
        });

        offcanvasEl.addEventListener('show.bs.offcanvas', function (event){
            const button=event.relatedTarget;
            const mode=button?.getAttribute('data-mode');
            const doctorId=button?.getAttribute('data-id');
            if(mode==='create'){
                titleEl.textContent="{{ __('messages.create') }} {{ __('clinic.doctor_title') }}";
                // Update button text to Save
                $('#save-doctor-service-btn-text').text("{{ __('messages.save') }}");
                // Use the centralized reset function
                resetForm();
            }
            else if(mode==='edit' && doctorId){
                titleEl.textContent="{{ __('messages.edit') }} {{ __('clinic.doctor_title') }}";
                // Keep button text as Save for both create and edit
                $('#save-doctor-service-btn-text').text("{{ __('messages.save') }}");
                $('#doctor_id').val(doctorId);
                $('#form_method').val('PUT');

                // Hide password fields when editing
                $('#password-field-group, #confirm-password-field-group').addClass('d-none');
                $('#password, #confirm_password').prop('disabled', true);

                fetch(routes.doctorEdit.replace(':id', doctorId), { headers:{ 'Accept':'application/json' } })
                .then(r=>r.json())
                .then(response=>{
                    if(!response.data) return;
                    const data=response.data;

                    /** ---------- Explicit text fields ---------- **/
                    $('#first_name').val(data.first_name || '');
                    $('#last_name').val(data.last_name || '');
                    $('#doctor_email').val(data.email || '');
                    $('#mobile').val(data.mobile || '');
                    $('#about_self').val(data.about_self || '');
                    $('#expert').val(data.expert || '');
                    $('#experience').val(data.experience || '');
                    $('#facebook_link').val(data.facebook_link || '');
                    $('#instagram_link').val(data.instagram_link || '');
                    $('#twitter_link').val(data.twitter_link || '');
                    $('#dribbble_link').val(data.dribbble_link || '');
                    $('#pincode').val(data.pincode || '');
                    $('#address').val(data.address || '');
                    $('#latitude').val(data.latitude || '');
                    $('#longitude').val(data.longitude || '');
                    $('#email_verified_at').val(data.email_verified_at || '');

                    /** ---------- Select2 fields (simple ones) ---------- **/
                    $('#gender').val(data.gender || '').trigger('change.select2');
                    $('#commission_id').val(Array.isArray(data.commission_id) ? data.commission_id : []).trigger('change.select2');
                    $('#vendor_id').val(data.vendor_id || '').trigger('change.select2');
                    $('#country').val(data.country || '').trigger('change.select2');
                    $('#state').val(data.state || '').trigger('change.select2');
                    $('#city').val(data.city || '').trigger('change.select2');

                    /** ---------- Image ---------- **/
                    if (data.profile_image){
                        let imgSrc=data.profile_image;
                        if(!/^https?:\/\//.test(imgSrc)) imgSrc="{{ asset('storage') }}/"+data.profile_image;
                        $('#imagePreview').attr('src', imgSrc);
                        $('#remove_profile_image').val("0");
                    } else { removeImage(); }


                    /** ---------- Signature ---------- **/
                    if (signaturePad) {
                        if (data.signature) {
                            const val = data.signature; // already has data:image/png;base64,...
                            $('#signature_data').val(val);

                            const img = new Image();
                            img.onload = function () {
                                // Do not clear after draw
                                signaturePad.clear();
                                const ctx = canvas.getContext("2d");
                                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                            };
                            img.src = val;
                        } else {
                            signaturePad.clear();
                            $('#signature_data').val("");
                        }
                    }




                    /** ---------- Qualifications ---------- **/
                    if (data.doctor_document && Array.isArray(data.doctor_document)){
                        const $container=$('#qualifications-container');
                        $container.html('');
                        data.doctor_document.forEach((q,i)=>{
                            $container.append(getQualificationRowHtml(i, q.degree ?? '', q.university ?? '', q.year ?? ''));
                        });
                        updateQualificationRemoveButtons();
                    } else {
                        $('#qualifications-container').html(getQualificationRowHtml(0));
                        updateQualificationRemoveButtons();
                    }

                    /** ---------- Status ---------- **/
                    if (typeof data.status!=='undefined'){
                        $('[name="status"]').prop('checked', !!data.status);
                    }

                    /** ---------- Clinics & Services ---------- **/
                    // Load clinics and services with proper filtering
                    const vendorIdForEdit = data.vendor_id || '';
                    const clinicIdsForEdit = (data.clinic_id && Array.isArray(data.clinic_id)) ? data.clinic_id : [];
                    const serviceIdsForEdit = (data.service_id && Array.isArray(data.service_id)) ? data.service_id.map(String) : [];

                    // Load clinics filtered by vendor (if multivendor enabled)
                    @if(multivendor())
                        loadClinics(vendorIdForEdit, clinicIdsForEdit);
                    @else
                        loadClinics('', clinicIdsForEdit);
                    @endif

                    // Load services filtered by selected clinics with preselected values
                    setTimeout(function(){
                        loadServices(clinicIdsForEdit, serviceIdsForEdit);
                    }, 300);

                    /** ---------- Country / State / City ---------- **/
                    // UK is auto-set (229), load states
                    loadStates(229, data.state);
                    if (data.state){
                        setTimeout(function(){
                            $('#state').val(data.state).trigger('change.select2');
                            if (data.city){
                                setTimeout(function(){
                                    loadCities(data.state, data.city);
                                    $('#city').val(data.city).trigger('change.select2');
                                }, 300);
                            }
                        }, 300);
                    } else {
                        $('#state').empty().append(new Option("{{ __('clinic.lbl_state') }}",'',true,false)).trigger('change.select2');
                        $('#city').empty().append(new Option("{{ __('clinic.lbl_city') }}",'',true,false)).trigger('change.select2');
                    }

                    /** ---------- Phone input ---------- **/
                    if(iti && data.dial_code && data.mobile) {
                        iti.setNumber(data.dial_code + data.mobile);
                        setTimeout(function() {
                            if (iti) {
                                var code = iti.getSelectedCountryData().dialCode;
                                dialCodeHidden.value = "+" + code;
                            }
                        }, 100);
                    }
                });
            }


        });
    }

    /* ---------- Submit (AJAX) ---------- */
    $('#doctor-form').on('submit', function (e) {
        let valid=true;

        const firstNameVal=$('#first_name').val().trim();
        if(firstNameVal===''||!isValidName(firstNameVal)){ $('#first_name').addClass('is-invalid'); $('#first_name_error').removeClass('d-none'); valid=false; }
        else{ $('#first_name').removeClass('is-invalid'); $('#first_name_error').addClass('d-none'); }

        const lastNameVal=$('#last_name').val().trim();
        if(lastNameVal===''||!isValidName(lastNameVal)){ $('#last_name').addClass('is-invalid'); $('#last_name_error').removeClass('d-none'); valid=false; }
        else{ $('#last_name').removeClass('is-invalid'); $('#last_name_error').addClass('d-none'); }

        const emailVal=$('#doctor_email').val().trim();
        if(emailVal===''||!isValidEmail(emailVal)){
            $('#doctor_email').addClass('is-invalid');
            $('#doctor_email_error').removeClass('d-none').text(emailVal==='' ? '{{ __("clinic.email_is_required") }}' : '{{ __("clinic.please_enter_valid_email_address") }}');
            valid=false;
        }
        else{
            $('#doctor_email').removeClass('is-invalid');
            $('#doctor_email_error').addClass('d-none');
        }

        // Phone validation: must be digits only, length 6-15, and dial code must be present
        let phoneVal = '';
        let dialCodeVal = '';
        if (iti) {
            // Get the number in E.164 format and split
            const e164 = iti.getNumber(intlTelInputUtils.numberFormat.E164);
            const countryData = iti.getSelectedCountryData();
            dialCodeVal = "+" + countryData.dialCode;
            // Remove dial code from e164 to get only the number part
            if (e164 && dialCodeVal && e164.startsWith(dialCodeVal)) {
                phoneVal = e164.substring(dialCodeVal.length);
            } else {
                phoneVal = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL).replace(/\D/g, '');
            }
        } else {
            phoneVal = $('#mobile').val().trim();
            dialCodeVal = $('#dial_code').val().trim();
        }

        if(phoneVal===''){
            $('#mobile').addClass('is-invalid');
            $('#mobile_error').removeClass('d-none').css('display', 'block').text('{{ __("clinic.contact_number_is_required") }}');
            valid=false;
        }
        else{
            $('#mobile').removeClass('is-invalid');
            $('#mobile_error').addClass('d-none').css('display', 'none');
        }

        if($('#password-field-group').is(':visible')){
            const pwVal=$('#password').val();
            if(!isValidPassword(pwVal)){
                $('#password').addClass('is-invalid');
                $('#password_error')
                    .removeClass('d-none')
                    .text(getPasswordErrorMessage(pwVal));
                valid=false;
            }
            else{ $('#password').removeClass('is-invalid'); $('#password_error').addClass('d-none'); }
        }
        if($('#confirm-password-field-group').is(':visible')){
            const pwVal=$('#password').val(), cpwVal=$('#confirm_password').val();
            if(!isValidPassword(cpwVal)){
                $('#confirm_password').addClass('is-invalid');
                $('#confirm_password_error')
                    .removeClass('d-none')
                    .text(getPasswordErrorMessage(cpwVal));
                valid=false;
            }
            else if(pwVal!==cpwVal){
                $('#confirm_password').addClass('is-invalid');
                $('#confirm_password_error')
                    .removeClass('d-none')
                    .text('Passwords do not match.');
                valid=false;
            }
            else{ $('#confirm_password').removeClass('is-invalid'); $('#confirm_password_error').addClass('d-none'); }
        }

        const commissionVal=$('#commission_id').val();
        if(!commissionVal||commissionVal.length===0){ $('#commission_id').addClass('is-invalid'); $('#commission_id_error').removeClass('d-none'); valid=false; }
        else{ $('#commission_id').removeClass('is-invalid'); $('#commission_id_error').addClass('d-none'); }

        const clinicVal=$('#clinic_id_doctor').val();
        if(!clinicVal||clinicVal.length===0){ $('#clinic_id_doctor').addClass('is-invalid'); $('#clinic_id_error').removeClass('d-none'); valid=false; }
        else{ $('#clinic_id_doctor').removeClass('is-invalid'); $('#clinic_id_error').addClass('d-none'); }

        const serviceVal=$('#service_id').val();
        if(!serviceVal||serviceVal.length===0){ $('#service_id').addClass('is-invalid'); $('#service_id_error').removeClass('d-none'); valid=false; }
        else{ $('#service_id').removeClass('is-invalid'); $('#service_id_error').addClass('d-none'); }

        const latValid=validateLatLongField($('#latitude'), $('#latitude-error'));
        const longValid=validateLatLongField($('#longitude'), $('#longitude-error'));
        const pincodeValid=validatePincodeField($('#pincode'), $('#pincode-error'));
        if(!latValid || !longValid || !pincodeValid){ if(!latValid) $('#latitude').focus(); else if(!longValid) $('#longitude').focus(); else if(!pincodeValid) $('#pincode').focus(); valid=false; }

        if(!valid){ e.preventDefault(); return false; }

        e.preventDefault();


        // 🔹 Ensure signature is saved before submit
        if (signaturePad) {
            $('#signature_data').val(!signaturePad.isEmpty() ? signaturePad.toDataURL("image/png") : "");
        }


        $('#save-doctor-service-btn').prop('disabled', true);
        $('#save-doctor-service-btn-loader').removeClass('d-none');

        const doctorId=$('#doctor_id').val();
        if(doctorId){
            $('#save-doctor-service-btn-text').addClass('d-none');
            $('#save-doctor-service-btn-saving').addClass('d-none');
            $('#save-doctor-service-btn-updating').removeClass('d-none');
        } else {
            $('#save-doctor-service-btn-text').addClass('d-none');
            $('#save-doctor-service-btn-updating').addClass('d-none');
            $('#save-doctor-service-btn-saving').removeClass('d-none');
        }

        // Always update dial_code and mobile fields before submit
        if(iti) {
            const countryData = iti.getSelectedCountryData();
            const dialCode = "+" + countryData.dialCode;
            $('#dial_code').val(dialCode);

            // Store the full number with country code in mobile field
            const fullNumber = iti.getNumber(intlTelInputUtils.numberFormat.E164);
            if (fullNumber) {
                // Format with space: +44 7414220479
                const formattedNumber = fullNumber.replace(/(\+\d{1,4})(\d+)/, '$1 $2');
                $('#mobile').val(formattedNumber);
            } else {
                // Fallback: get national number and add dial code with space
                const nationalNumber = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL).replace(/\D/g, '');
                $('#mobile').val(dialCode + ' ' + nationalNumber);
            }
        }

        const formData=new FormData(this);
        let url = routes.doctorStore;
        if(doctorId){
            url = routes.doctorUpdate.replace(':id', doctorId);
            formData.set('_method','PUT');
            $('#form_method').val('PUT');
        }
        else{ formData.set('_method','POST'); $('#form_method').val('POST'); }
        formData.set('email_verified_at', $('#email_verified_at').val());

        // Ensure dial_code and mobile are set in formData
        formData.set('dial_code', $('#dial_code').val());
        formData.set('mobile', $('#mobile').val());

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(r=>r.json())
        .then(response=>{
            $('#save-doctor-service-btn').prop('disabled', false);
            $('#save-doctor-service-btn-loader').addClass('d-none');
            $('#save-doctor-service-btn-text').removeClass('d-none');
            $('#save-doctor-service-btn-saving').addClass('d-none');
            $('#save-doctor-service-btn-updating').addClass('d-none');

            if(response.status){
                const offcanvasEl=document.getElementById('form-offcanvas');
                if(offcanvasEl) {
                    bootstrap.Offcanvas.getInstance(offcanvasEl).hide();
                    // Form will be reset automatically by the hidden.bs.offcanvas event
                }
                // Reload datatable after successful form submission
                if(window.renderedDataTable){
                    window.renderedDataTable.ajax.reload(null,false);
                } else if(window.$ && $.fn.DataTable && $('#datatable').length){
                    // Fallback: try to get datatable instance by ID
                    const $table = $('#datatable').DataTable();
                    if($table){
                        $table.ajax.reload(null,false);
                    }
                } else {
                    // Last resort: reload the page if datatable reload fails
                    console.warn('Datatable reload failed, refreshing page...');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                // Handle validation errors
                if(response.errors) {
                    // Clear previous validation errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').addClass('d-none');

                    // Display new validation errors
                    Object.keys(response.errors).forEach(field => {
                        const $field = $(`[name="${field}"]`);
                        const $errorDiv = $(`#${field}_error`);

                        if($field.length && $errorDiv.length) {
                            $field.addClass('is-invalid');
                            $errorDiv.removeClass('d-none').css('display', 'block').text(response.errors[field][0]);
                        }
                    });
                } else {
                    alert('{{ __("clinic.error") }}: ' + (response.message || '{{ __("clinic.an_error_occurred_try_again") }}'));
                }
            }
        })
        .catch(err=>{
            $('#save-doctor-service-btn').prop('disabled', false);
            $('#save-doctor-service-btn-loader').addClass('d-none');
            $('#save-doctor-service-btn-text').removeClass('d-none');
            $('#save-doctor-service-btn-saving').addClass('d-none');
            $('#save-doctor-service-btn-updating').addClass('d-none');

            // Try to parse error response for validation errors
            if(err.response) {
                err.response.json().then(response => {
                    if(response.errors) {
                        // Clear previous validation errors
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').addClass('d-none');

                        // Display new validation errors
                        Object.keys(response.errors).forEach(field => {
                            const $field = $(`[name="${field}"]`);
                            const $errorDiv = $(`#${field}_error`);

                            if($field.length && $errorDiv.length) {
                                $field.addClass('is-invalid');
                                $errorDiv.removeClass('d-none').css('display', 'block').text(response.errors[field][0]);
                            }
                        });
                    } else {
                        alert('{{ __("clinic.error") }}: ' + (response.message || '{{ __("clinic.an_error_occurred_try_again") }}'));
                    }
                }).catch(() => {
                    alert('{{ __("clinic.error") }}: ' + (err.message || '{{ __("clinic.an_error_occurred_try_again") }}'));
                });
            } else {
                alert('{{ __("clinic.error") }}: ' + (err.message || '{{ __("clinic.an_error_occurred_try_again") }}'));
            }
            console.error('Submit failed:', err);
        });
    });
});
</script>
