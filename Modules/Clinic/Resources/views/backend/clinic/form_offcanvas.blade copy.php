{{-- Offcanvas Form --}}
<form enctype="multipart/form-data" id="clinic-form-offcanvas">
    @csrf
    <input type="hidden" name="_method" id="form_method" value="POST">
    <input type="hidden" name="id" id="clinic_id">

    <div class="offcanvas offcanvas-end offcanvas-w-40"
         tabindex="-1"
         id="form-offcanvas"
         aria-labelledby="form-offcanvasLabel">
        {{-- Form Header --}}
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="form-offcanvasLabel">
                {{ __('Create Clinic') }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        {{-- Form Body --}}
        <div class="offcanvas-body position-relative">
            {{-- Loader overlay for edit --}}
            <div id="clinic-form-loader" style="display:none; position:absolute; z-index:10; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); align-items:center; justify-content:center;">
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    <div class="spinner-border text-primary" role="status" style="width:3rem; height:3rem;">
                        <span class="visually-hidden">{{ __('clinic.loading') }}</span>
                    </div>
                    <div class="mt-2 text-primary">{{ __('clinic.loading') }}</div>
                </div>
            </div>
            {{-- General error message area --}}
          
            <div class="row">
                {{-- Clinic Image --}}
                <div class="row align-items-start">
                    <div class="col-md-6">
                        <div class="image-upload-container text-center">
                            <div class="clinic-image-preview d-flex justify-content-center align-items-center mb-2 mx-auto">
                                <img id="clinicImagePreview"
                                    alt="Clinic Image Preview"
                                    src="{{ default_file_url() }}"
                                    class="img-fluid object-fit-cover avatar-170 rounded-circle" />
                            </div>
                            <div class="d-flex gap-2 justify-content-center mt-3 mb-2">
                                <button type="button" class="btn btn-light"
                                        onclick="document.getElementById('fileInput').click();">
                                    {{ __('clinic.upload') }}
                                </button>
                                <button type="button"
                                        class="btn btn-danger"
                                        disabled
                                        onclick="removeClinicImage();"
                                        id="removeClinicImageBtn">
                                    {{ __('messages.remove') }}
                                </button>
                            </div>
                            <input type="file" name="file_url" id="fileInput" style="display: none;"
                                   accept=".jpeg,.jpg,.png,.gif"
                                   onchange="previewClinicImage(this);" />
                            <input type="hidden" name="remove_file" id="remove_file" value="0" />
                        </div>
                        <span class="text-danger">@error('file_url'){{ $message }}@enderror</span>
                    </div>

                    {{-- Right Column: Name + Description --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('clinic.lbl_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="{{ __('clinic.lbl_name') }}">
                            <span class="validation-error text-danger"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="description">{{ __('clinic.lbl_description') }}</label>
                            <textarea class="form-control" name="description" id="description"
                                      placeholder="{{ __('clinic.lbl_description') }}"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('clinic.lbl_Email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="clinic-email" class="form-control"
                           placeholder="{{ __('clinic.lbl_Email') }}">
                    <span id="email-error" class="text-danger"></span>
                    <span class="validation-error text-danger"></span> 
                </div>

                {{-- Contact Number --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('clinic.lbl_contact_number') }} <span class="text-danger">*</span></label>
                    <input type="text" name="contact_number" id="clinic-contact-number" class="form-control"
                           placeholder="{{ __('clinic.lbl_contact_number') }}"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    <span id="contact-number-error" class="text-danger"></span>
                    <span class="validation-error text-danger"></span> 
                </div>

                {{-- clinic admin --}}
                @if(multivendor())
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="vendor_id" class="form-label">{{ __('clinic.clinic_admin') }}</label>
                            <select class="form-select select2 @error('vendor_id') is-invalid @enderror"
                                    id="vendor_id" name="vendor_id"
                                    data-placeholder="Select Vendor">
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}"
                                        {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->first_name }} {{ $vendor->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vendor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                @endif

                {{-- System Service Category Dropdown (was Speciality) --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('clinic.lbl_speciality') }} <span class="text-danger">*</span></label>
                    <select name="system_service_category" class="form-select select2" id="form_speciality" data-placeholder="{{ __('clinic.lbl_speciality') }}">
                        <option value="">{{ __('clinic.lbl_speciality') }}</option>
                        {{-- Specialities will be loaded via AJAX --}}
                    </select>
                    <span class="validation-error text-danger"></span>
                </div>


                {{-- Hidden input for system_service_category_name --}}
                <input type="hidden" name="system_service_category_name" id="system_service_category_name" value="">

                {{-- Time Slot --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('clinic.lbl_time_slot') }} <span class="text-danger">*</span></label>
                    <select name="time_slot" class="form-select select2" data-placeholder="{{ __('clinic.lbl_time_slot') }}">
                        <option value="">{{ __('clinic.lbl_time_slot') }}</option>
                        @foreach([5,10,15,20,25,30,35,40,45,55,60] as $slot)
                            <option value="{{ $slot }}">{{ $slot }}</option>
                        @endforeach
                    </select>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <label class="form-label d-block">{{ __('clinic.lbl_status') }}</label>
                    <div class="form-control d-flex align-items-center justify-content-between">
                        <span>{{ __('messages.active') }}</span>
                        <div class="form-check form-switch m-0">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input" id="clinic-status" name="status" type="checkbox" value="1" checked>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="margin-top: 10px;"></div>
                <div class="col-md-12">
                    <label class="form-label" style="font-size: 1.5rem; font-weight: bold;">{{ __('clinic.other_detail') }}</label>
                </div>

                {{-- Address --}}
                <div class="col-md-12">
                    <label class="form-label">{{ __('clinic.lbl_address') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="address" placeholder="{{ __('clinic.lbl_address') }}"></textarea>
                    <span class="validation-error text-danger"></span>
                </div>

                {{-- Country --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_country') }} <span class="text-danger">*</span></label>
                    <select name="country" class="select2 form-select" id="form_country" data-placeholder="{{ __('clinic.lbl_country') }}">
                        <option value="">{{ __('clinic.lbl_country') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                    <span class="validation-error text-danger"></span> 
                </div> 
                {{-- State --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_state') }} <span class="text-danger">*</span></label>
                    <select name="state" id="form_state" class="select2 form-select" data-placeholder="{{ __('clinic.lbl_state') }}">
                        <option value="">{{ __('clinic.lbl_state') }}</option>
                    </select>
                    <span class="validation-error text-danger"></span> 
                </div>

                {{-- City --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_city') }} <span class="text-danger">*</span></label>
                    <select name="city" id="form_city" class="select2 form-select" data-placeholder="{{ __('clinic.lbl_city') }}">
                        <option value="">{{ __('clinic.lbl_city') }}</option>
                    </select>
                    <span class="validation-error text-danger"></span> 
                </div>

                {{-- Postal Code --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_postal_code') }}</label>
                    <input type="text" name="pincode" class="form-control" placeholder="{{ __('clinic.lbl_postal_code') }}">
                    <span class="validation-error text-danger"></span> 
                </div>

                {{-- Latitude / Longitude --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_lat') }}</label>
                    <input type="text" name="latitude" class="form-control" placeholder="{{ __('clinic.lbl_lat') }}">
                    <span class="validation-error text-danger"></span>
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('clinic.lbl_long') }}</label>
                    <input type="text" name="longitude" class="form-control" placeholder="{{ __('clinic.lbl_long') }}">
                    <span class="validation-error text-danger"></span>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="offcanvas-footer p-3 d-flex justify-content-end">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-white" id="clinic-form-cancel-btn">{{ __('messages.cancel') }}</button>
                <button type="submit" class="btn btn-secondary" id="clinic-form-save-btn">{{ __('messages.save') }}</button>
            </div>
        </div>
    </div>
</form>

  <script>
        // Global Select2 configuration to prevent conflicts
        $(document).ready(function() {
            // Override Select2 defaults to ensure search is always enabled
            // $.fn.select2.defaults.set('minimumResultsForSearch', -1);
            // $.fn.select2.defaults.set('allowClear', false); // Remove X (cross) icon globally
            // $.fn.select2.defaults.set('width', '100%');
            
            // Ensure search field is always enabled when dropdown opens
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    const searchField = $('.select2-dropdown .select2-search__field');
                    if (searchField.length) {
                        searchField.prop('disabled', false);
                        searchField.attr('placeholder', '{{ __("clinic.type_to_search") }}');
                    }
                }, 10);
            });
        });
    </script>