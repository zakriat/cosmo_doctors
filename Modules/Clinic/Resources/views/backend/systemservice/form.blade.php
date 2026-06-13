<form id="system-service-form" enctype="multipart/form-data" method="POST" action="{{ route('backend.system-service.store') }}">
    @csrf
    <input type="hidden" name="_method" value="POST">

    <div class="container-fluid">
        <div class="row g-4">
            <!-- Left Column: Image Upload and Price -->
            <div class="col-md-6">
                <!-- Image Upload -->
                <label class="form-label fw-semibold">{{ __('service.lbl_image') }}</label>
                <div class="border rounded position-relative p-3 bg-gray-900" style="min-height: 220px;">
                    <!-- Hidden flag to explicitly remove existing image on update -->
                    <input type="hidden" name="remove_image" id="remove_image" value="0">
                    <input 
                        type="file" 
                        name="file_url" 
                        id="file_url"
                        class="position-absolute w-100 h-100 opacity-0 cursor-pointer top-0 start-0 z-3"
                        accept="image/*" 
                        onchange="previewImage(event)"
                    >


                    <!-- Preview Card -->
                    <div id="file-preview-card" class="d-none d-flex align-items-center gap-3 position-relative">
                        <img id="preview-image" class="rounded avatar-60 object-fit-cover" />
                        <div>
                            <div id="file-name" class="fw-semibold small heading-color"></div>
                            <div id="file-size" class="text-muted small"></div>
                        </div>
                        <button 
                            type="button"
                            id="remove-image-btn"
                            class="btn btn-link text-danger p-0 m-0 position-absolute top-0 end-0"
                            style="font-size: 1.5rem; z-index: 5;"
                            onclick="removePreviewImage()"
                        >
                            &times;
                        </button>
                    </div>
                 

                    <!-- Upload Placeholder -->
                    <div id="upload-placeholder"
                        class="d-flex flex-column align-items-center justify-content-center h-100 w-100"
                        style="pointer-events: none; position: absolute; top: 0; left: 0;">
                        <div class="d-flex flex-column align-items-center justify-content-center w-100 h-100">
                            <span class="heading-color fw-bold small d-block text-center" style="width: 100%;">Drop files here or</span>
                            <span class="text-primary text-decoration-underline small d-block text-center" style="width: 100%;">browse files</span>
                        </div>
                    </div>
                </div>
                <span class="text-danger" id="error-file_url"></span>
 
                <span class="text-danger">Only .jpeg, .jpg, .png files are allowed.</span>
            </div>

            <!-- Right Column: Name, Category, Subcategory, Status -->
            <div class="col-md-6">
                <!-- Name -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ __('service.lbl_name') }} <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        class="form-control" 
                        placeholder="{{ __('service.lbl_name') }}"
                        value="{{ old('name', $service->name ?? '') }}" 
                        id="name-input"
                    >
                    <div class="invalid-feedback d-none" id="error-name"></div>
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ __('service.lbl_category_id') }} <span class="text-danger">*</span>
                    </label>
                    <select name="category_id" class="form-select select2" id="category-select">
                        <option value="">{{ __('messages.select_category') }}</option>
                    </select>
                    <div class="invalid-feedback d-none" id="error-category_id"></div>
                </div>

                <!-- Subcategory -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('messages.subcategory') }}</label>
                    <select name="subcategory_id" class="form-select select2" id="subcategory-select">
                        <option value="">{{ __('messages.select_subcategory') }}</option>
                    </select>
                </div>
            </div>

             <!-- Description -->
            <div class="col-12">
                <label class="form-label fw-semibold">{{ __('service.lbl_description') }}</label>
                <textarea 
                    name="description" 
                    class="form-control" 
                    rows="3" 
                    maxlength="250"
                    placeholder="{{ __('service.lbl_description') }}"
                    id="description-input"
                >{{ old('description', $service->description ?? '') }}</textarea>
                <div class="d-flex justify-content-between mt-1">
                    
                    <small class="text-muted" id="description-counter">0/250</small>
                </div>
                <div class="invalid-feedback d-none" id="error-description"></div>
            </div>
            
                <!-- Type -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ __('service.lbl_type') }}</label>
                    <select name="type" class="form-select select2" id="type-select">
                        <option value="in_clinic" {{ (isset($service) ? $service->type : old('type', 'in_clinic')) == 'in_clinic' ? 'selected' : '' }}>In Clinic</option>
                        <option value="online" {{ (isset($service) ? $service->type : old('type')) == 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>
                <!-- Online Consultancy (beside Type; reserve space when hidden) -->
                <div class="col-md-6 invisible" id="video-consultancy-section">
                    <label class="form-label fw-semibold">{{ __('service.lbl_online_consultancy') }}</label>
                    <select name="is_video_consultancy" class="form-select select2" id="is_video_consultancy">
                        <option value="0" {{ (isset($service) ? $service->is_video_consultancy : old('is_video_consultancy', 0)) == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ (isset($service) ? $service->is_video_consultancy : old('is_video_consultancy')) == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>

                <!-- Set As Featured (fixed position: always below Type) -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold d-block">{{ __('service.lbl_featured') }}</label>
                    <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2 bg-gray-900">
                        <span class="mb-0">Yes</span>
                        <div class="form-check form-switch m-0">
                            <!-- Hidden input ensures a value is sent when checkbox is unchecked -->
                            <input type="hidden" name="featured" value="0">

                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="featured" 
                                name="featured" 
                                value="1"
                                {{ (isset($service) ? $service->featured : old('featured', 0)) ? 'checked' : '' }}
                            >
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold d-block">{{ __('messages.status') }}</label>
                    <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2 bg-gray-900">
                        <span class="mb-0">Active</span>
                        <div class="form-check form-switch m-0">
                            <!-- Hidden input ensures a value is sent when checkbox is unchecked -->
                            <input type="hidden" name="status" value="0">

                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="status" 
                                name="status" 
                                value="1"
                                {{ (isset($service) ? $service->status : old('status', 1)) ? 'checked' : '' }}
                            >
                        </div>
                    </div>
                </div>

            </div>
        </div>

        
    </div>
    <div class="offcanvas-footer border-top pt-4">
        <!-- Buttons -->
        <div class="d-flex justify-content-end gap-2">
            <button 
                type="button" 
                class="btn btn-light border" 
                data-bs-dismiss="offcanvas" 
                id="cancel-system-service-btn"
            >{{ __('messages.cancel') }}</button>
            <button 
                type="submit" 
                class="btn btn-secondary" 
                id="save-system-service-btn"
            >
                <span id="save-system-service-btn-text">{{ __('messages.save') }}</span>
                <span id="save-system-service-btn-loader" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</form>

<script>
// Route keys for AJAX operations
const ROUTES = {
    store: `{{ route('backend.system-service.store') }}`,
    update: `{{ route('backend.system-service.update', ['system_service' => 'SERVICE_ID_PLACEHOLDER']) }}`,
    edit: `{{ route('backend.system-service.edit', ['system_service' => 'SERVICE_ID_PLACEHOLDER']) }}`,
    categoryData: `{{ route('backend.category.index_data') }}`,
    subcategories: `{{ route('backend.system-service.subcategories') }}`
};

let isSubmitting = false;

// Helper: Remove all validation errors
function clearValidation(form) {
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.classList.add('d-none'));
}

// Helper: Show validation errors
function showValidationErrors(form, errors) {
    for (let field in errors) {
        const input = form.querySelector(`[name="${field}"]`);
        const errorDiv = document.getElementById('error-' + field);
        if (input) {
            input.classList.add('is-invalid');
        }
        if (errorDiv) {
            errorDiv.textContent = errors[field][0];
            errorDiv.classList.remove('d-none');
        }
    }
}

// JS validation for required fields
function validateSystemServiceForm(form) {
    let errors = {};
    // Name required
    const name = form.querySelector('[name="name"]').value.trim();
    if (!name) {
        errors.name = ["{{ __('validation.required', ['attribute' => __('service.lbl_name')]) }}"];
    }
    // Category required
    const category = form.querySelector('[name="category_id"]').value;
    if (!category) {
        errors.category_id = ["{{ __('validation.required', ['attribute' => __('service.lbl_category_id')]) }}"];
    }
    // Description character limit validation
    const description = form.querySelector('[name="description"]').value.trim();
    if (description && description.length > 250) {
        errors.description = ["Description must not exceed 250 characters."];
    }
    // Price validation removed since price field is commented out in the form
    // const price = form.querySelector('[name="price"]').value.trim();
    // if (!price) {
    //     errors.price = ["{{ __('validation.required', ['attribute' => __('service.lbl_price')]) }}"];
    // } else if (isNaN(price) || Number(price) < 0) {
    //     errors.price = ["{{ __('validation.numeric', ['attribute' => __('service.lbl_price')]) }}"];
    // }
    return errors;
}

// Image preview
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('preview-image').src = e.target.result;
        document.getElementById('file-preview-card').classList.remove('d-none');
        document.getElementById('upload-placeholder').classList.add('d-none');
        // Reset remove flag when a new image is chosen
        const removeFlag = document.getElementById('remove_image');
        if (removeFlag) removeFlag.value = '0';
    };
    reader.readAsDataURL(file);
}

// Remove image preview
function removePreviewImage() {
    document.getElementById('preview-image').src = '';
    document.getElementById('file_url').value = '';
    document.getElementById('file-preview-card').classList.add('d-none');
    document.getElementById('upload-placeholder').classList.remove('d-none');
    // Mark image for removal on update
    const removeFlag = document.getElementById('remove_image');
    if (removeFlag) removeFlag.value = '1';
}

// Character counter for description
function updateDescriptionCounter() {
    const descriptionInput = document.getElementById('description-input');
    const counter = document.getElementById('description-counter');
    if (descriptionInput && counter) {
        const length = descriptionInput.value.length;
        counter.textContent = `${length}/250`;
        if (length > 250) {
            counter.classList.add('text-danger');
            counter.classList.remove('text-muted');
        } else {
            counter.classList.remove('text-danger');
            counter.classList.add('text-muted');
        }
    }
}

// Toggle video consultancy section based on type selection
function toggleVideoConsultancy() {
    const typeSelect = $('#type-select');
    const videoSection = $('#video-consultancy-section');
    const videoSelect = $('#is_video_consultancy');
    
    if (typeSelect.val() === 'online') {
        videoSection.removeClass('invisible');
        videoSelect.prop('disabled', false);
        // Reinitialize Select2 to ensure proper rendering when shown
        if (window.$ && videoSelect.hasClass('select2-hidden-accessible')) {
            videoSelect.select2('destroy');
        }
        videoSelect.select2({
            placeholder: "{{ __('service.lbl_online_consultancy') }}",
            minimumResultsForSearch: -1,
            dropdownParent: $('#form-offcanvas')
        });
    } else {
        // Reset and make invisible but keep layout space
        videoSelect.val('0').trigger('change');
        videoSelect.prop('disabled', true);
        videoSection.addClass('invisible');
    }
}

// Reset form to default (create) state
function resetSystemServiceForm() {
    const form = document.getElementById('system-service-form');
    form.reset();
    clearValidation(form);
    removePreviewImage();
    form.setAttribute('action', ROUTES.store);
    form.querySelector('input[name="_method"]').value = "POST";
    
    // Reset select2 fields
    if (window.$) {
        $('#category-select').val('').trigger('change');
        $('#subcategory-select').html(`<option value="">{{ __('messages.select_subcategory') }}</option>`).trigger('change');
        $('#type-select').val('in_clinic').trigger('change');
        $('#is_video_consultancy').val('0').trigger('change');
    }
    
    // Reset checkboxes
    document.getElementById('featured').checked = false;
    document.getElementById('status').checked = true;
    
    const removeFlag = document.getElementById('remove_image');
    if (removeFlag) removeFlag.value = '0';
}

// Fetch categories and populate select
// Fetch categories and populate select
// Fetch categories and populate select
function fetchClinicCategories(selectedCategoryId = null) {
    const categorySelect = document.getElementById('category-select');
    categorySelect.innerHTML = `<option value="">{{ __('messages.select_category') }}</option>`;

    return fetch(ROUTES.categoryData)
        .then(res => res.json())
        .then(data => {
            const categories = Array.isArray(data) ? data : data.data;

            function stripHtml(html) {
                let div = document.createElement("div");
                div.innerHTML = html;
                return div.textContent || div.innerText || "";
            }

            categories.forEach(category => {
                const option = document.createElement("option");
                option.value = category.id;
                option.textContent = stripHtml(category.name); // ✅ clean text only
                if (selectedCategoryId && selectedCategoryId == category.id) {
                    option.selected = true;
                }
                categorySelect.appendChild(option);
            });

            if (window.$ && $(categorySelect).hasClass('select2')) {
                $(categorySelect).trigger('change');
            }
        });
}



// Fetch subcategories for a category
function fetchSubcategories(categoryId, selectedSubcategoryId = null) {
    const subcategorySelect = document.getElementById('subcategory-select');
    subcategorySelect.innerHTML = `<option value="">{{ __('messages.select_subcategory') }}</option>`;
    if (!categoryId) return Promise.resolve();
    const fetchUrl = `${ROUTES.subcategories}?category_id=${categoryId}`;
    return fetch(fetchUrl)
        .then(res => { if (!res.ok) throw new Error(res.status); return res.json(); })
        .then(data => {
            const subcategories = Array.isArray(data) ? data : (data.data || []);
            subcategories.forEach(subcat => {
                const option = new Option(subcat.name, subcat.id);
                if (selectedSubcategoryId && String(selectedSubcategoryId) === String(subcat.id)) option.selected = true;
                subcategorySelect.add(option);
            });
            if (window.$ && $(subcategorySelect).hasClass('select2')) $(subcategorySelect).trigger('change');
        })
        .catch(error => { console.error('[Subcategory Fetch] Error:', error); });
}

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('system-service-form');
    if (form.dataset.initialized) return;
    form.dataset.initialized = 'true';

    // Initialize Select2 for all select fields
    if (window.$) {
        $('#category-select').select2({
            placeholder: "{{ __('messages.select_category') }}",
            allowClear: false,
            dropdownParent: $('#form-offcanvas')
        });
        
        $('#subcategory-select').select2({
            placeholder: "{{ __('messages.select_subcategory') }}",
            allowClear: false,
            dropdownParent: $('#form-offcanvas')
        });
        
        $('#type-select').select2({
            placeholder: "{{ __('service.lbl_type') }}",
            minimumResultsForSearch: -1,
            dropdownParent: $('#form-offcanvas')
        });
        
        $('#is_video_consultancy').select2({
            placeholder: "{{ __('service.lbl_online_consultancy') }}",
            minimumResultsForSearch: -1,
            dropdownParent: $('#form-offcanvas')
        });
    }

    // Form submit
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (isSubmitting) return;

        clearValidation(form);

        // JS validation
        const errors = validateSystemServiceForm(form);
        if (Object.keys(errors).length > 0) {
            showValidationErrors(form, errors);
            isSubmitting = false;
            return;
        }

        isSubmitting = true;

        // Always POST; Laravel will read _method for PUT/PATCH
        let method = 'POST';
        let actionUrl = form.getAttribute('action');
        const formData = new FormData(form);

        const btn = document.getElementById('save-system-service-btn');
        const btnText = document.getElementById('save-system-service-btn-text');
        const btnLoader = document.getElementById('save-system-service-btn-loader');
        btn.disabled = true;
        btnText.classList.add('d-none');
        btnLoader.classList.remove('d-none');

        fetch(actionUrl, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        }).then(async res => {
            let data = {};
            try { data = await res.json(); } catch {}
            btn.disabled = false;
            btnText.classList.remove('d-none');
            btnLoader.classList.add('d-none');
            isSubmitting = false;

            if (res.ok && (data.status || data.success)) {
                resetSystemServiceForm();
                if (window.bootstrap?.Offcanvas) {
                    window.bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('form-offcanvas'))?.hide();
                }
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: (method === 'PUT') ? 'Updated' : 'Created',
                        text: data.message || '{{ __("clinic.system_service_saved_successfully") }}',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4F46E5',  
                        customClass: {
                            popup: 'swal-wide',   
                            title: 'fw-bold fs-4'  
                        }
                    });
                }
                // Always refresh datatable after save
                if (typeof window.renderedDataTable?.ajax?.reload === 'function') {
                    window.renderedDataTable.ajax.reload(null, false);
                }
            } else if (res.status === 422 && data.errors) {
                showValidationErrors(form, data.errors);
            } else {
                Swal.fire({ icon: 'error', title: '{{ __("clinic.error") }}', text: data.message || '{{ __("clinic.an_error_occurred_try_again") }}' });
            }
        }).catch(error => {
            isSubmitting = false;
            btn.disabled = false;
            btnText.classList.remove('d-none');
            btnLoader.classList.add('d-none');
            Swal.fire({ icon: 'error', title: '{{ __("clinic.error") }}', text: error.message });
        });
    });

    // Category change: fetch subcategories
    $('#category-select').on('change', function () {
        fetchSubcategories($(this).val());
    });

    // Type change: toggle video consultancy section
    $('#type-select').on('change', function () {
        toggleVideoConsultancy();
    });

    // Cancel button: reset form
    document.getElementById('cancel-system-service-btn').addEventListener('click', resetSystemServiceForm);

    // Edit button: fill form for editing
    $(document).off('click', '.edit-service-btn').on('click', '.edit-service-btn', function () {
        const id = $(this).data('id');
        const url = ROUTES.edit.replace('SERVICE_ID_PLACEHOLDER', id);

        // Show the offcanvas immediately for better UX
        let offcanvasInstance = window.bootstrap?.Offcanvas.getOrCreateInstance(document.getElementById('form-offcanvas'));
        if (offcanvasInstance) {
            offcanvasInstance.show();
        }

        // Set loading state in form fields (optional: you can add a spinner or disable fields here)
        $('#form-offcanvas-label').text("{{ __('clinic.loading') }}");
        $('#system-service-form input, #system-service-form select, #system-service-form textarea, #system-service-form button').prop('disabled', true);

        // Fetch categories first, then fetch the service data and subcategories
        fetchClinicCategories().then(function() {
            $.get(url, function (res) {
                console.log('Edit response:', res);
                if (res.status && res.data) {
                    const data = res.data;
                    $('#form-offcanvas-label').text("{{ __('clinic.edit_service') }}");
                    let updateRoute = ROUTES.update.replace('SERVICE_ID_PLACEHOLDER', id);
                    $('#system-service-form').attr('action', updateRoute);
                    $('#system-service-form input[name="_method"]').val("PUT");
                    $('#system-service-form [name="name"]').val(data.name);
                    $('#system-service-form [name="description"]').val(data.description);
                    // Price field is commented out in the form, so this line is not needed
                    // $('#system-service-form [name="price"]').val(data.price).trigger('input');
                    $('#system-service-form [name="category_id"]').val(data.category_id).trigger('change');
                    fetchSubcategories(data.category_id, data.subcategory_id).then(function() {
                        $('#system-service-form [name="subcategory_id"]').val(data.subcategory_id).trigger('change');
                    });
                    $('#system-service-form [name="type"]').val(data.type || 'in_clinic').trigger('change');
                    $('#system-service-form [name="is_video_consultancy"]').val(data.is_video_consultancy || '0').trigger('change');
                    $('#system-service-form [name="featured"]').prop('checked', data.featured == 1 || data.featured === true);
                    $('#system-service-form [name="status"]').prop('checked', data.status == 1 || data.status === true);
                    if (data.file_url) {
                        $('#preview-image').attr('src', data.file_url);
                        $('#file-preview-card').removeClass('d-none');
                        $('#upload-placeholder').addClass('d-none');
                        $('#remove_image').val('0');
                    } else {
                        removePreviewImage();
                    }
                } else {
                    $('#form-offcanvas-label').text("{{ __('clinic.edit_service') }}");
                    console.error('Edit response error:', res);
                    Swal.fire({ icon: 'error', title: '{{ __("clinic.error") }}', text: res.message || '{{ __("clinic.failed_to_load_service_data") }}' });
                }
            }).fail(function (xhr, status, error) {
                $('#form-offcanvas-label').text("{{ __('clinic.edit_service') }}");
                console.error('Edit request failed:', xhr, status, error);
                Swal.fire({ icon: 'error', title: '{{ __("clinic.error") }}', text: '{{ __("clinic.edit_request_failed") }}: ' + error });
            }).always(function() {
                // Re-enable form fields after loading
                $('#system-service-form input, #system-service-form select, #system-service-form textarea, #system-service-form button').prop('disabled', false);
            });
        });
    });
 
    const formOffcanvas = document.getElementById('form-offcanvas');
    if (formOffcanvas) {
        formOffcanvas.addEventListener('hidden.bs.offcanvas', function () {
            resetSystemServiceForm();
        });
    }

    // Initial fetch
    fetchClinicCategories();
    
    // Initialize video consultancy section based on current type selection
    toggleVideoConsultancy();
    
    // Initialize description character counter
    const descriptionInput = document.getElementById('description-input');
    if (descriptionInput) {
        updateDescriptionCounter(); // Set initial count
        descriptionInput.addEventListener('input', updateDescriptionCounter);
        descriptionInput.addEventListener('keyup', updateDescriptionCounter);
    }
});
</script>
