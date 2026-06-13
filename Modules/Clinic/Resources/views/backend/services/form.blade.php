<form id="serviceForm" enctype="multipart/form-data" autocomplete="off">
    @csrf
    <input type="hidden" id="serviceId" name="id" value="">

    <div class="row">
        <!-- Image Upload -->
        <div class="col-md-6">
            <div class="mb-4">
                <label class="form-label">{{ __('clinic.lbl_image') }}</label>
                <div class="image-upload-container text-center">
                    <div class="clinic-image-preview d-flex justify-content-center align-items-center mb-2 mx-auto">
                        <img id="serviceImagePreview"
                            alt="{{ __('clinic.profile_image') }}"
                            src="{{ default_file_url() }}"
                            class="img-fluid object-fit-cover avatar-170 rounded-circle" />
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light"
                            onclick="document.getElementById('serviceImage').click();">
                            {{ __('clinic.upload') }}
                        </button>
                    </div>
                    <input type="file" 
                        name="file_url" 
                        id="serviceImage" 
                        class="d-none"
                        accept=".jpg,.jpeg,.png"
                        onchange="previewServiceImage(event)" />
                    <input type="hidden" name="remove_file" id="remove_file" value="0" />
                    <div id="file-format-error" class="text-danger mt-1 d-none"></div>
                    <span class="text-muted small">{{ __('clinic.only_jpeg_jpg_png_files_allowed') }}</span>
                </div>
                @if($errors->has('file_url'))
                <span class="text-danger">{{ $errors->first('file_url') }}</span>
                @endif
            </div>
        </div>

        <!-- Service Details -->
        <div class="col-md-6">
            @if(multiVendor())
            <div class="form-group">
                <label for="systemService" class="form-label">{{ __('clinic.system_service') }} <span class="text-danger">*</span></label>
                <select class="form-select select2" id="systemService" name="system_service_id" required>
                    <option value="" disabled selected>{{ __('clinic.select_system_service') }}</option>
                </select>
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
            @else
            <div class="form-group">
                <label for="serviceName" class="form-label">{{ __('clinic.name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="serviceName" name="name" placeholder="{{ __('clinic.name') }}" required>
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        @if(multiVendor())
        @if(auth()->user()->hasAnyRole(['admin', 'demo_admin']))
        <div class="col-md-6">
            <div class="form-group">
                <label for="clinicAdmin" class="form-label">{{ __('clinic.clinic_admin') }}</label>
                <select class="form-select select2" id="clinicAdmin" name="vendor_id">
                    <option value="" selected disabled>Select Clinic Admin</option>
                </select>
            </div>
        </div>
        @endif
        @endif
         <div class="col-md-6">
            <div class="form-group">
                <label for="serviceDuration" class="form-label">{{ __('clinic.service_duration_mins') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="serviceDuration" name="duration_min" placeholder="{{ __('clinic.service_duration_mins') }}" inputmode="numeric" pattern="[0-9]*" autocomplete="off"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" required />
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="timeSlot" class="form-label">{{ __('clinic.lbl_time_slot') }} <span class="text-danger">*</span></label>
                <select class="form-select select2" id="timeSlot" name="time_slot" required></select>
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
            <div class="form-group">
                <div class="d-flex align-items-center mb-1">
                    <label for="clinicSelect" class="form-label mb-0 me-2">
                        {{ __('clinic.lbl_clinic') }} <span class="text-danger">*</span>
                    </label>
                    <div class="form-check ms-auto">
                        <input type="checkbox" class="form-check-input" id="selectAllClinics">
                        <label class="form-check-label small ms-1" for="selectAllClinics" style="user-select:none;">
                            {{ __('clinic.select_all') ?? 'Select All' }}
                        </label>
                    </div>
                </div>
            
                <select 
                    class="form-select select2" 
                    id="clinicSelect" 
                    name="clinic_id[]" 
                    multiple 
                    required
                    data-placeholder="{{ __('clinic.select_clinics') }}"
                >
                    <option></option>
                </select>
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="defaultPrice" class="form-label">{{ __('clinic.default_price') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="defaultPrice" name="charges" placeholder="{{ __('clinic.default_price') }}" inputmode="decimal" pattern="^\d*\.?\d*$" autocomplete="off"
                    oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required />
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="serviceType" class="form-label">{{ __('clinic.type') }}</label>
                <select class="form-select select2" id="serviceType" name="service_type">
                    <option value="" selected disabled>{{ __('clinic.select_type') }}</option>
                    <option value="in_clinic">{{ __('clinic.in_clinic') }}</option>
                    <option value="online">{{ __('clinic.online') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 d-none" id="onlineConsultancyWrapper">
            <div class="form-group">
                <label for="onlineConsultancy" class="form-label">{{ __('clinic.online_consultancy') }} <span class="text-danger">*</span></label>
                <select class="form-select select2" id="onlineConsultancy" name="is_video_consultancy">
                    <option value="0" selected>{{ __('clinic.no') }}</option>
                    <option value="1">{{ __('clinic.yes') }}</option>
                </select>
            </div>    
        </div>

        <!-- Description -->
        <div class="col-md-12">
            <div class="mb-3">
                <label for="description" class="form-label">{{ __('clinic.lbl_description') }}</label>
                <div class="form-floating">
                    <textarea class="form-control" placeholder="{{ __('clinic.description') }}" id="description" name="description" style="height: 100px" maxlength="250"></textarea>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted" id="service-description-counter">0/250</small>
                </div>
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
        </div>

        <!-- Discount and Advance Payment Section -->
        <div class="col-md-6">
            <label class="form-label">{{ __('clinic.discount') }}</label>
            <div class="d-flex align-items-center justify-content-between form-control">
                <span class="mb-0">{{ __('clinic.discount') }}</span>
                <div class="form-check form-switch m-0">
                    <input type="hidden" name="discount" value="0">
                    <input class="form-check-input" type="checkbox" id="discountToggle" name="discount" value="1">
                </div>
            </div>
        </div>

        <div class="col-md-6 d-none" id="discountTypeWrapper">
            <div class="form-group">
                <label for="discountType" class="form-label">{{ __('clinic.discount_type') }} <span class="text-danger">*</span></label>
                <select class="form-select select2" id="discountType" name="discount_type">
                    <option value="" selected disabled>{{ __('clinic.discount_type') }}</option>
                </select>
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
        </div>

        <div class="col-md-6 d-none" id="discountValueWrapper">
            <div class="form-group">
                <label for="discountValue" class="form-label">{{ __('clinic.discount_value') }} <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="discountValue" name="discount_value" placeholder="0" min="0" value="0">
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
        </div>
        
        <div class="col-md-6">
            <label class="form-label">{{ __('clinic.status') ?? 'Status' }}</label>
            <div class="d-flex align-items-center justify-content-between form-control">
                <span class="mb-0">{{ __('clinic.status') ?? 'Status' }}</span>
                <div class="form-check form-switch m-0">
                    <input type="hidden" name="status" value="0">
                    <input class="form-check-input" type="checkbox" id="statusToggle" name="status" value="1" checked>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <label class="form-label">{{ __('clinic.advance_payment_for_services') }}</label>
            <div class="d-flex align-items-center justify-content-between form-control">
                <span class="mb-0">{{ __('clinic.advance_payment_for_services') }}</span>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" id="advancePaymentToggle" name="is_enable_advance_payment" value="1">
                </div>
            </div>
        </div>

        <div class="col-md-6 d-none" id="advancePaymentValueWrapper">
            <div class="form-group">
                <label for="advancePaymentAmount" class="form-label">{{ __('clinic.advance_payment_amount_percent') }} <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="advancePaymentAmount" name="advance_payment_amount" placeholder="0" min="0" value="0">
                <div class="validation-error text-danger mt-1 small d-none"></div>
            </div>
        </div>
        
        <div class="col-md-6" id="inclusiveTaxRightWrapper">
            <label class="form-label">{{ __('service.inclusive_tax') }}</label>
            <div class="d-flex align-items-center justify-content-between form-control">
                <span class="mb-0">{{ __('service.inclusive_tax') }}</span>
                <div class="form-check form-switch m-0">
                    <input type="hidden" name="is_inclusive_tax" value="0">
                    <input class="form-check-input" type="checkbox" id="inclusiveTaxToggle" name="is_inclusive_tax" value="1">
                </div>
            </div>
        </div>
        
        <div class="col-md-6 d-none" id="inclusiveTaxLeftWrapper">
            <label class="form-label">{{ __('service.inclusive_tax') }}</label>
            <div class="d-flex align-items-center justify-content-between form-control">
                <span class="mb-0">{{ __('service.inclusive_tax') }}</span>
                <div class="form-check form-switch m-0">
                    <input type="hidden" name="is_inclusive_tax_left" value="0">
                    <input class="form-check-input" type="checkbox" id="inclusiveTaxToggleLeft" name="is_inclusive_tax_left" value="1">
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mt-4">
        <button type="button" class="btn btn-light fw-semibold px-4 py-2" data-bs-dismiss="offcanvas">{{ __('clinic.cancel') }}</button>
        <button type="button" id="saveServiceBtn" class="btn btn-secondary fw-semibold px-4 py-2" data-mode="create">{{ __('clinic.save') }}</button>
    </div>
</form>

<style>
.validation-error {
    font-size: 0.875em;
    display: block !important;
}
.is-invalid {
    border-color: #dc3545 !important;
}
.form-group {
    margin-bottom: 1rem;
    position: relative;
}
</style>

<script>
// Optimized Service Form Handler
class ServiceFormHandler {
    constructor() {
        this.baseUrl = '{{ url("/") }}';
        this.cache = {
            categories: null,
            systemServices: null,
            clinicAdmins: null,
            allClinics: null,
            clinicsByAdmin: {},
            subcategoriesByCategory: {}
        };
        this.isSubmitting = false;
        this.suppressEvents = false;
        this.init();
    }

    init() {
        this.initSelect2();
        this.bindEvents();
        this.loadInitialData();
        // Safety cleanup on load (in case of stuck backdrops from previous navigation)
        this.cleanBackdropsAndOverlays();
        this.forceClearThirdPartyLoaders();
    }

    initSelect2() {
        const dropdownIds = [
            'systemService', 'clinicAdmin', 
            'timeSlot', 'onlineConsultancy', 'clinicSelect', 'serviceType', 'discountType'
        ];
        
        dropdownIds.forEach(id => {
            const $el = $(`#${id}`);
            if ($el.length) {
                const config = {
                    width: '100%',
                    dropdownParent: $el.closest('.offcanvas, .modal').length ? 
                                 $el.closest('.offcanvas, .modal') : $(document.body)
                };

                if (['timeSlot', 'discountType', 'serviceType', 'onlineConsultancy'].includes(id)) {
                    config.minimumResultsForSearch = Infinity;
                }

                $el.select2(config);
            }
        });
    }

    bindEvents() {
        // Toggle handlers
        this.bindToggle('#discountToggle', ['#discountValueWrapper', '#discountTypeWrapper']);
        this.bindToggle('#advancePaymentToggle', ['#advancePaymentValueWrapper']);
        
        // Field change handlers
        $(document)
            .on('change', '#serviceType', this.handleServiceTypeChange.bind(this))
            .on('change', '#systemService', this.handleSystemServiceChange.bind(this))
            .on('change', '#clinicAdmin', this.handleClinicAdminChange.bind(this))
            .on('change', '#selectAllClinics', this.handleSelectAllClinics.bind(this))
            .on('change', '#clinicSelect', this.handleClinicSelectChange.bind(this))
            .on('input', '#description', this.updateDescriptionCounter.bind(this))
            .on('click', '#saveServiceBtn', this.saveService.bind(this))
            .on('click', '.edit-service-btn', this.editService.bind(this));

        // Input validation
        $('#serviceDuration, #defaultPrice').on('input', this.validateNumericInput.bind(this));
        
        // Offcanvas events
        const offcanvasEl = document.getElementById('createServiceForm');
        if (offcanvasEl) {
            offcanvasEl.addEventListener('show.bs.offcanvas', this.handleOffcanvasShow.bind(this));
            offcanvasEl.addEventListener('hide.bs.offcanvas', () => {
                // Clear any overlays immediately when user triggers hide (e.g., Cancel button)
                this.hideProcessing();
                this.cleanBackdropsAndOverlays();
                this.forceClearThirdPartyLoaders();
            });
            offcanvasEl.addEventListener('hidden.bs.offcanvas', this.handleOffcanvasHide.bind(this));
        }

        // Ensure cancel button clears any overlays pre-dismiss and suppresses events
        $(document).on('click', 'button[data-bs-dismiss="offcanvas"]', () => {
            this.suppressEvents = true;
            this.hideProcessing();
            this.cleanBackdropsAndOverlays();
            this.forceClearThirdPartyLoaders();
            setTimeout(() => { this.suppressEvents = false; }, 0);
        });
    }

    bindToggle(toggleId, targetIds) {
        const toggle = document.querySelector(toggleId);
        if (toggle) {
            toggle.addEventListener('change', (e) => {
                const show = e.target.checked;
                targetIds.forEach(targetId => {
                    const target = document.querySelector(targetId);
                    if (target) {
                        target.classList.toggle('d-none', !show);
                    }
                });

                // Special handling for advance payment toggle
                if (toggleId === '#advancePaymentToggle') {
                    document.getElementById('inclusiveTaxLeftWrapper').classList.toggle('d-none', !show);
                    document.getElementById('inclusiveTaxRightWrapper').classList.toggle('d-none', show);
                }

                // Reset values when hidden
                if (!show) {
                    if (toggleId === '#discountToggle') {
                        $('#discountValue').val(0);
                        $('#discountType').val('').trigger('change');
                    } else if (toggleId === '#advancePaymentToggle') {
                        $('#advancePaymentAmount').val(0);
                    }
                }
            });
        }
    }

    handleServiceTypeChange(e) {
        if (this.suppressEvents) return;
        const wrapper = document.getElementById('onlineConsultancyWrapper');
        if (e.target.value === 'online') {
            wrapper.classList.remove('d-none');
        } else {
            wrapper.classList.add('d-none');
            $('#onlineConsultancy').val('0').trigger('change');
        }
    }

    async handleSystemServiceChange(e) {
        if (this.suppressEvents) return;
        const serviceId = e.target.value;
        if (!serviceId) return;

        try {
            const response = await fetch(`${this.baseUrl}/app/system-service/${serviceId}/edit`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            const serviceData = data.data || data;

            // Update price and description
            if (serviceData.default_price || serviceData.charges) {
                $('#defaultPrice').val(serviceData.default_price || serviceData.charges);
            }
            if (serviceData.description) {
                $('#description').val(serviceData.description);
                this.updateDescriptionCounter();
            }
        } catch (error) {
            console.error('Error loading system service:', error);
        }
    }

    async handleClinicAdminChange(e) {
        if (this.suppressEvents) return;
        const adminId = e.target.value;
        await this.loadClinicsByAdmin(adminId);
    }

    handleSelectAllClinics(e) {
        if (this.suppressEvents) return;
        const $select = $('#clinicSelect');
        if (e.target.checked) {
            $select.find('option').prop('selected', true);
        } else {
            $select.find('option').prop('selected', false);
        }
        $select.trigger('change');
    }

    handleClinicSelectChange(e) {
        if (this.suppressEvents) return;
        const total = $(e.target).find('option').length;
        const selected = $(e.target).val()?.length || 0;
        $('#selectAllClinics').prop('checked', total > 0 && total === selected);
    }

    validateNumericInput(e) {
        const $input = $(e.target);
        const value = $input.val();
        
        if (!value || !/^\d*\.?\d*$/.test(value) || Number(value) <= 0) {
            this.showError($input, this.getValidationMessage(e.target.id));
        } else {
            this.clearError($input);
        }
    }

    getValidationMessage(fieldId) {
        const messages = {
            serviceDuration: "{{ __('clinic.service_duration_required') }}",
            defaultPrice: "{{ __('clinic.default_price_required') }}",
            discountValue: "{{ __('clinic.discount_value_must_be_greater_than_zero') }}",
            advancePaymentAmount: "{{ __('clinic.advance_payment_amount_required') }}"
        };
        return messages[fieldId] || "Invalid input";
    }

    // Validation methods
    showError($element, message) {
        $element.addClass('is-invalid');
        const $errorContainer = $element.closest('.form-group').find('.validation-error');
        $errorContainer.text(message).removeClass('d-none');
    }

    clearError($element) {
        $element.removeClass('is-invalid');
        $element.closest('.form-group').find('.validation-error').addClass('d-none');
    }

    clearAllErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.validation-error').addClass('d-none');
    }

    validateForm() {
        this.clearAllErrors();
        let isValid = true;
        let firstErrorElement = null;

        const validations = [
            {
                condition: () => @if(multiVendor()) !$('#systemService').val() @else !$('#serviceName').val().trim() @endif,
                element: @if(multiVendor()) '#systemService' @else '#serviceName' @endif,
                message: @if(multiVendor()) "{{ __('clinic.system_service_is_required') }}" @else "{{ __('clinic.service_name_is_required') }}" @endif
            },
            {
                condition: () => !$('#clinicSelect').val() || $('#clinicSelect').val().length === 0,
                element: '#clinicSelect',
                message: "{{ __('clinic.select_at_least_one_clinic') }}"
            },
            {
                condition: () => !$('#serviceDuration').val() || Number($('#serviceDuration').val()) <= 0,
                element: '#serviceDuration',
                message: "{{ __('clinic.service_duration_required') }}"
            },
            {
                condition: () => !$('#defaultPrice').val() || Number($('#defaultPrice').val()) <= 0,
                element: '#defaultPrice',
                message: "{{ __('clinic.default_price_required') }}"
            },
            {
                condition: () => !$('#timeSlot').val(),
                element: '#timeSlot',
                message: "{{ __('clinic.time_slot_is_required') }}"
            },
            {
                condition: () => $('#discountToggle').is(':checked') && !$('#discountType').val(),
                element: '#discountType',
                message: "{{ __('clinic.discount_type_is_required') }}"
            },
            {
                condition: () => $('#discountToggle').is(':checked') && (!$('#discountValue').val()),
                element: '#discountValue',
                message: "{{ __('clinic.discount_value_must_be_required') }}"
            },
            {
                condition: () => $('#discountToggle').is(':checked') && $('#discountType').val() == 'percentage' && (Number($('#discountValue').val()) <= 0 || Number($('#discountValue').val()) > 100),
                element: '#discountValue',
                message: "{{ __('clinic.discount_value_must_be_greater_than_zero_less_than_hundred') }}"
            },
            {
                condition: () => $('#discountToggle').is(':checked') && $('#discountType').val() == 'fixed' && (Number($('#discountValue').val()) <= 0 || Number($('#discountValue').val()) > Number($('#defaultPrice').val())),
                element: '#discountValue',
                message: "{{ __('clinic.discount_value_must_be_greater_than_zero_less_equal_default_price') }}"
            },
            {
                condition: () => $('#advancePaymentToggle').is(':checked') && (!$('#advancePaymentAmount').val() || Number($('#advancePaymentAmount').val()) <= 0),
                element: '#advancePaymentAmount',
                message: "{{ __('clinic.advance_payment_amount_required') }}"
            }
        ];

        validations.forEach(validation => {
            if (validation.condition()) {
                isValid = false;
                this.showError($(validation.element), validation.message);
                if (!firstErrorElement) {
                    firstErrorElement = validation.element;
                }
            }
        });

        if (firstErrorElement) {
            this.scrollToElement(firstErrorElement);
        }

        return isValid;
    }

    scrollToElement(selector) {
        const element = document.querySelector(selector);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    // Data loading methods
    async loadInitialData() {
        try {
            const response = await fetch(`${this.baseUrl}/app/services/init-data`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            this.cache.categories = data.categories?.data || data.categories || [];
            this.cache.systemServices = data.systemServices?.data || data.systemServices || [];
            this.cache.clinicAdmins = data.clinicAdmins?.data || data.clinicAdmins || [];

            this.fillDropdown('#systemService', this.cache.systemServices, "{{ __('clinic.select_system_service') }}");
            this.fillDropdown('#clinicAdmin', this.cache.clinicAdmins, "Select Clinic Admin");

            this.initializeTimeSlots();
            this.initializeStaticDropdowns();

            @if(!multiVendor())
            if (data.clinics) {
                this.cache.allClinics = data.clinics?.data || data.clinics || [];
                this.fillDropdown('#clinicSelect', this.cache.allClinics, "{{ __('clinic.select_clinics') }}");
            }
            @endif
        } catch (error) {
            console.error('Error loading initial data:', error);
        }
    }

    initializeTimeSlots() {
        const timeSlot = document.getElementById('timeSlot');
        if (!timeSlot) return;

        const options = [
            { value: 'clinic_slot', text: "{{ __('clinic.default_clinic_time_slot') }}" }
        ];

        for (let i = 5; i <= 60; i += 5) {
            options.push({ value: String(i), text: String(i) });
        }

        this.fillDropdown('#timeSlot', options, "{{ __('clinic.select_time_slot') }}");
    }

    initializeStaticDropdowns() {
        const discountTypes = [
            { id: 'percentage', name: "{{ __('clinic.percentage') }}" },
            { id: 'fixed', name: "{{ __('clinic.fixed') }}" }
        ];
        
        const serviceTypes = [
            { id: 'in_clinic', name: "{{ __('clinic.in_clinic') }}" },
            { id: 'online', name: "{{ __('clinic.online') }}" }
        ];

        this.fillDropdown('#discountType', discountTypes, "{{ __('clinic.select_discount_type') }}");
        this.fillDropdown('#serviceType', serviceTypes, "{{ __('clinic.select_type') }}");
    }

    async loadSubcategories(categoryId, selectedId = null) {
        try {
            const response = await fetch(`${this.baseUrl}/app/category/index_list?parent_id=${categoryId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            const subcategories = data.data || data || [];
            
            this.cache.subcategoriesByCategory[categoryId] = subcategories;
            this.fillDropdown('#subCategory', subcategories, "{{ __('clinic.select_sub_category') }}");
            
            if (selectedId) {
                $('#subCategory').val(selectedId).trigger('change');
            }
        } catch (error) {
            console.error('Error loading subcategories:', error);
        }
    }

    async loadClinicsByAdmin(adminId) {
        if (!adminId) {
            try {
            const response = await fetch(`${this.baseUrl}/app/clinics/index_list`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            const clinics = data.data || data || [];
            
            this.cache.clinicsByAdmin[adminId] = clinics;
            this.fillDropdown('#clinicSelect', clinics, "{{ __('clinic.select_clinics') }}");
            } catch (error) {
                console.error('Error loading clinics:', error);
            }
        }else{
            try {
            const response = await fetch(`${this.baseUrl}/app/clinics/index_list?vendor_id=${adminId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            const clinics = data.data || data || [];
            
            this.cache.clinicsByAdmin[adminId] = clinics;
            this.fillDropdown('#clinicSelect', clinics, "{{ __('clinic.select_clinics') }}");
            } catch (error) {
                console.error('Error loading clinics:', error);
            }
        }

       
    }

    fillDropdown(selector, items, placeholder) {
        const $el = $(selector);
        if (!$el.length) return;

        $el.empty();

        if (!$el.prop('multiple')) {
            $el.append(`<option value="" disabled selected>${placeholder}</option>`);
        }

        items.forEach(item => {
            const text = item.name || item.clinic_name || item.text || String(item.id);
            $el.append(new Option(text, item.id || item.value));
        });

        $el.trigger('change');
    }

    updateDescriptionCounter() {
        const textarea = document.getElementById('description');
        const counter = document.getElementById('service-description-counter');
        if (textarea && counter) {
            const length = textarea.value.length;
            counter.textContent = `${length}/250`;
            counter.classList.toggle('text-danger', length > 250);
        }
    }

    // Form submission
    async saveService(e) {
        e.preventDefault();
        
        if (this.isSubmitting) return;
        
        if (!this.validateForm()) {
            // Swal.fire({
            //     icon: 'error',
            //     title: "{{ __('clinic.validation_error') }}",
            //     text: "{{ __('clinic.please_check_form_errors') }}",
            //     toast: true,
            //     position: 'top-end',
            //     showConfirmButton: false,
            //     timer: 3000
            // });
            return;
        }

        this.isSubmitting = true;
        const $btn = $('#saveServiceBtn');
        $btn.prop('disabled', true).text("{{ __('clinic.saving') }}");
        this.showProcessing();

        try {
            const formData = this.prepareFormData();
            const result = await this.submitForm(formData);
            console.log('Form submission result:', result);
            if (result.status == true) {
                this.handleSuccess();
            } else {
                throw new Error(result.message || 'Submission failed');
            }
        } catch (error) {
            this.handleError(error);
        } finally {
            this.isSubmitting = false;
            $btn.prop('disabled', false).text("{{ __('clinic.save') ?? 'Save' }}");
            this.hideProcessing();
        }
    }

    prepareFormData() {
        const formData = new FormData(document.getElementById('serviceForm'));
        
        // Handle toggles
        if (!$('#discountToggle').is(':checked')) {
            formData.set('discount', '0');
            formData.set('discount_value', '0');
        }
        
        if (!$('#advancePaymentToggle').is(':checked')) {
            formData.set('is_enable_advance_payment', '0');
            formData.set('advance_payment_amount', '0');
        }

        // Handle inclusive tax
        const isAdvanceOn = $('#advancePaymentToggle').is(':checked');
        const inclusiveTaxValue = isAdvanceOn ? 
            ($('#inclusiveTaxToggleLeft').is(':checked') ? '1' : '0') :
            ($('#inclusiveTaxToggle').is(':checked') ? '1' : '0');
        formData.set('is_inclusive_tax', inclusiveTaxValue);

        @if(multiVendor())
        const clinicAdminValue = $('#clinicAdmin').val();
        if (clinicAdminValue) {
            formData.set('vendor_id', clinicAdminValue);
        } else if (this.originalClinicAdminId) {
            formData.set('vendor_id', this.originalClinicAdminId);
        } else {
            formData.set('vendor_id', "{{ auth()->user()->id }}");
        }
        @endif

        return formData;
    }

    async submitForm(formData) {
        const mode = $('#saveServiceBtn').data('mode');
        const id = $('#saveServiceBtn').data('id');
        const url = mode === 'edit' ? 
            `${this.baseUrl}/app/services/${id}` : 
            `${this.baseUrl}/app/services`;

        if (mode === 'edit') {
            formData.append('_method', 'PUT');
        }

        const response = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        console.log('Response status:', response);
        return await response.json();
    }

    handleSuccess() {
        this.closeOffcanvas();
        this.resetForm();
        
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Service saved successfully!',
            timer: 1500,
            showConfirmButton: false
        });

        // Reload DataTable if exists
        if (window.renderedDataTable) {
            window.renderedDataTable.ajax.reload(null, false);
        }
    }

    handleError(error) {
        Swal.fire({
            icon: 'error',
            title: "{{ __('clinic.error') }}",
            text: error.message || "Something went wrong!",
            timer: 3000
        });
    }

    closeOffcanvas() {
        const offcanvasEl = document.getElementById('createServiceForm');
        if (offcanvasEl) {
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (bsOffcanvas) {
                bsOffcanvas.hide();
            }
        }
    }

    resetForm() {
        this.suppressEvents = true;
        
        // Reset the form
        $('#serviceForm')[0].reset();
        
        // Clear all Select2 dropdowns properly
        $('#serviceForm .select2').each(function() {
            $(this).val(null).trigger('change.select2');
        });
        
        // Clear specific fields
        $('#serviceId').val('');
        $('#serviceName').val('');
        $('#serviceDuration').val('');
        $('#defaultPrice').val('');
        $('#description').val('');
        $('#discountValue').val(0);
        $('#advancePaymentAmount').val(0);
        
        // Reset image
        this.removeServiceImage();
        
        // Reset toggles
        $('#discountToggle, #advancePaymentToggle, #inclusiveTaxToggle, #inclusiveTaxToggleLeft').prop('checked', false);
        $('#statusToggle').prop('checked', true);
        
        // Hide conditional sections
        $('#discountValueWrapper, #discountTypeWrapper, #advancePaymentValueWrapper, #onlineConsultancyWrapper, #inclusiveTaxLeftWrapper').addClass('d-none');
        $('#inclusiveTaxRightWrapper').removeClass('d-none');
        
        // Uncheck select all checkbox
        $('#selectAllClinics').prop('checked', false);
        
        // Clear validation errors
        this.clearAllErrors();
        
        // Reset description counter
        $('#service-description-counter').text('0/250').removeClass('text-danger');
        
        // Reset mode and title
        $('#saveServiceBtn').data('mode', 'create').data('id', '');
        $('.offcanvas-title').text("{{ __('clinic.create_service') ?? 'Create Service' }}");
        this.originalClinicAdminId = null;
        
        setTimeout(() => { this.suppressEvents = false; }, 100);
    }

    // Image handling
    previewServiceImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 5 * 1024 * 1024;

        if (!validTypes.includes(file.type)) {
            this.showImageError("{{ __('clinic.invalid_file_type') }}");
            event.target.value = '';
            return;
        }

        if (file.size > maxSize) {
            this.showImageError("{{ __('clinic.file_too_large') }}");
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            $('#serviceImagePreview').attr('src', e.target.result);
            $('#remove_file').val('0');
        };
        reader.readAsDataURL(file);
    }

    showImageError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }

    removeServiceImage() {
        $('#serviceImage').val('');
        $('#serviceImagePreview').attr('src', "{{ default_file_url() }}");
        $('#remove_file').val('1');
    }

    // Edit service
    async editService(e) {
        const id = $(e.currentTarget).data('crud-id');
        $('#saveServiceBtn').data('mode', 'edit').data('id', id);
        $('.offcanvas-title').text("{{ __('clinic.edit_service') ?? 'Edit Service' }}");
        
        this.openOffcanvas();
        this.showProcessing();
        await this.loadServiceData(id);
        this.hideProcessing();
    }

    openOffcanvas() {
        const offcanvasEl = document.getElementById('createServiceForm');
        if (offcanvasEl) {
            const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);
            bsOffcanvas.show();
        }
    }

    async loadServiceData(id) {
        try {
            const response = await fetch(`${this.baseUrl}/app/services/${id}/edit`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            const serviceData = data.data || data;

            await this.populateForm(serviceData);
        } catch (error) {
            this.showImageError("{{ __('clinic.failed_to_load_service_data') }}");
        }
    }

    async populateForm(data) {
        // Suppress events during form population to prevent unwanted triggers
        this.suppressEvents = true;
        
        try {
            // Basic fields
            $('#serviceId').val(data.id || '');
            
            // Set system service (multivendor) or service name (non-multivendor)
            @if(multiVendor())
            if (data.system_service_id) {
                // Ensure the option exists in dropdown, add it if missing
                const $systemServiceSelect = $('#systemService');
                if ($systemServiceSelect.find(`option[value="${data.system_service_id}"]`).length === 0 && data.system_service_name) {
                    // Add the option if it doesn't exist
                    const newOption = new Option(data.system_service_name, data.system_service_id, true, true);
                    $systemServiceSelect.append(newOption);
                }
                $systemServiceSelect.val(data.system_service_id).trigger('change.select2');
            }
            @else
            $('#serviceName').val(data.name || '');
            @endif
            
            $('#serviceDuration').val(data.duration_min || '');
            $('#defaultPrice').val(data.charges || '');
            $('#description').val(data.description || '');
            $('#serviceType').val(data.service_type || '').trigger('change.select2');
            $('#timeSlot').val(data.time_slot || '').trigger('change.select2');

            // Toggles - Use native events to trigger the native event listeners
            const discountToggle = document.getElementById('discountToggle');
            if (discountToggle) {
                discountToggle.checked = !!Number(data.discount);
                discountToggle.dispatchEvent(new Event('change', { bubbles: true }));
            }
            $('#discountValue').val(data.discount_value || 0);
            $('#discountType').val(data.discount_type || '').trigger('change.select2');
            
            const advancePaymentToggle = document.getElementById('advancePaymentToggle');
            if (advancePaymentToggle) {
                advancePaymentToggle.checked = !!Number(data.is_enable_advance_payment);
                advancePaymentToggle.dispatchEvent(new Event('change', { bubbles: true }));
            }
            $('#advancePaymentAmount').val(data.advance_payment_amount || 0);
            
            const inclusiveTaxValue = !!Number(data.is_inclusive_tax);
            $('#inclusiveTaxToggle').prop('checked', inclusiveTaxValue);
            $('#inclusiveTaxToggleLeft').prop('checked', inclusiveTaxValue);

            // Image
            if (data.file_url) {
                $('#serviceImagePreview').attr('src', data.file_url);
                $('#remove_file').val('0');
            } else {
                this.removeServiceImage();
            }

            @if(multiVendor())
            if (data.vendor_id) {
                this.originalClinicAdminId = data.vendor_id;
                // Set clinic admin without triggering change (to avoid loading clinics twice)
                $('#clinicAdmin').val(data.vendor_id).trigger('change.select2');
            }
            @endif

            // Set category without triggering change event
            if (data.category_id) {
                $('#category').val(data.category_id).trigger('change.select2');
                
                // Load subcategory if exists
                if (data.subcategory_id) {
                    await this.loadSubcategories(data.category_id, data.subcategory_id);
                }
            }
            
            // Now load clinics based on vendor (multiVendor) or all clinics (non-multiVendor)
            @if(multiVendor())
            if (data.vendor_id) {
                await this.loadClinicsByAdmin(data.vendor_id);
            }
            @else
            await this.loadClinicsByAdmin();
            @endif
            
            // Set selected clinics after clinics are loaded
            if (data.clinic_id && Array.isArray(data.clinic_id) && data.clinic_id.length > 0) {
                // Use slight delay to ensure Select2 has rendered the options
                setTimeout(() => {
                    $('#clinicSelect').val(data.clinic_id).trigger('change');
                    // Update select all checkbox
                    const total = $('#clinicSelect').find('option').length;
                    const selected = data.clinic_id.length;
                    $('#selectAllClinics').prop('checked', total > 0 && total === selected);
                }, 150);
            }

            this.updateDescriptionCounter();
        } finally {
            // Re-enable events after a short delay
            setTimeout(() => {
                this.suppressEvents = false;
            }, 200);
        }
    }

    // Offcanvas event handlers
    handleOffcanvasShow() {
        this.initSelect2();
        this.initializeTimeSlots();
    }

    handleOffcanvasHide() {
        console.log('Offcanvas hidden, resetting form');
        this.resetForm();
        $('.select2').val(null).trigger('change');
        // Ensure any stuck bootstrap backdrops/scroll locks are cleared
        this.cleanBackdropsAndOverlays();
    }

    // UI helpers
    showProcessing() {
        // Loader disabled per request; ensure any previous overlay is removed
        const overlay = document.getElementById('global-processing-overlay');
        if (overlay) overlay.remove();
        document.body.classList.remove('overflow-hidden');
    }

    hideProcessing() {
        const overlay = document.getElementById('global-processing-overlay');
        if (overlay) overlay.remove();
        document.body.classList.remove('overflow-hidden');
    }

    cleanBackdropsAndOverlays() {
        // Remove any Bootstrap offcanvas backdrops left in DOM
        document.querySelectorAll('.offcanvas-backdrop').forEach(el => el.remove());
        // Remove modal backdrops too, defensively
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        // Remove any processing overlay if present
        const overlay = document.getElementById('global-processing-overlay');
        if (overlay) overlay.remove();
        // Clear scroll lock classes/styles
        document.body.classList.remove('offcanvas-backdrop', 'modal-open', 'overflow-hidden');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('paddingRight');
    }

    forceClearThirdPartyLoaders() {
        try {
            if (window.NProgress && typeof window.NProgress.done === 'function') {
                window.NProgress.done(true);
                window.NProgress.remove();
            }
        } catch (_) {}

        try {
            if (window.Pace && window.Pace.stop) {
                window.Pace.stop();
                const paceEls = document.querySelectorAll('.pace, .pace-progress, .pace-activity');
                paceEls.forEach(el => el.remove());
            }
        } catch (_) {}

        // Common custom loader elements
        const selectors = [
            '#cover-spin', '#preloader', '#loader', '.preloader', '.loader', '.loading-overlay', '.processing-overlay'
        ];
        selectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(el => {
                el.style.display = 'none';
                el.classList.add('d-none');
            });
        });

        // Remove elements that contain only the text "Processing..."
        document.querySelectorAll('body *').forEach(el => {
            try {
                if (el.childElementCount === 0) {
                    const text = (el.textContent || '').trim();
                    if (/^processing\.{0,3}$/i.test(text)) {
                        el.style.display = 'none';
                        el.classList.add('d-none');
                    }
                }
            } catch (_) {}
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.serviceFormHandler = new ServiceFormHandler();
});

// Global functions for HTML onclick handlers
function previewServiceImage(event) {
    if (window.serviceFormHandler) {
        window.serviceFormHandler.previewServiceImage(event);
    }
}

function removeServiceImage() {
    if (window.serviceFormHandler) {
        window.serviceFormHandler.removeServiceImage();
    }
}
</script>