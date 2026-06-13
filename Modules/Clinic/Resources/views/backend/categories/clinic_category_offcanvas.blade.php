<form id="clinic-category-form" enctype="multipart/form-data" onsubmit="return false;">
    @csrf
    @if(isset($category))
        @method('PUT')
    @endif
    <input type="hidden" name="id" id="category-id" value="">

    <div class="offcanvas offcanvas-end offcanvas-w-40" tabindex="-1" id="clinic-category-offcanvas">
        {{-- Header --}}
        <div class="offcanvas-header border-bottom">
            <h4 class="offcanvas-title">
                @if(isset($category))
                    {{ $category->parent_id ? __('Edit Nested Category') : __('Edit Category') }}
                @else
                    {{ isset($categoryId) && $categoryId ? __('Create Nested Category') : __('Create New Category') }}
                @endif
            </h4>
            <button type="button" class="btn-close-offcanvas" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="ph ph-x-circle"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="offcanvas-body">
            <div class="row">
                {{-- Image --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('clinic.clinic_image') }}</label>
                    <div class="image-upload-container text-center">
                        <div class="avatar-preview mx-auto rounded-circle overflow-hidden mb-2">
                            <img src="{{ asset('img/default.webp') }}" alt="Category Image" id="preview-image" class="img-fluid object-fit-cover avatar-170 rounded-circle">
                        </div>
                        <input type="file" name="file_url" class="d-none" id="fileInput" accept=".jpeg,.jpg,.png,.gif" onchange="validateAndPreviewImage(event)">
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <button type="button" class="btn btn-light" onclick="$('#fileInput').click()">{{ __('clinic.upload') }}</button>
                        </div>
                        <span class="text-danger small">Only .jpeg, .jpg, .png, .gif files are allowed (max 2MB)</span>
                        <div id="image-error" class="text-danger small mt-1 d-none"></div>
                        <input type="hidden" name="remove_image" id="removeImageInput" value="0">
                    </div>
                </div>

                {{-- Name & Description --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('category.lbl_name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="{{ __('category.lbl_name') }}" required>
                    <div id="name-error" class="text-danger small mt-1" style="display:none;"></div>

                    <div class="form-group mt-3">
                        <label class="form-label">{{ __('category.lbl_description') }}</label>
                        <textarea name="description" class="form-control" maxlength="250" id="description-textarea" placeholder="{{ __('category.lbl_description') }}"></textarea>
                        <div class="text-end small text-muted mt-1"><span id="char-count">0</span>/250</div>
                    </div>
                </div>

                {{-- Service Selection (Parent) --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('category.lbl_service') }} <span class="text-danger">*</span></label>
                    <select name="parent_id" class="form-select select2" id="parent-service-select" required>
                        <option value="">{{ __('category.select_service') }}</option>
                    </select>
                    <div id="parent-error" class="text-danger small mt-1" style="display:none;"></div>
                </div>

                {{-- Price --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('category.lbl_price') }} <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" 
                           step="0.01" min="0" placeholder="80.00" required>
                    <div id="price-error" class="text-danger small mt-1" style="display:none;"></div>
                </div>

                {{-- Service Classification --}}
                <div class="col-md-12">
                    <label class="form-label">{{ __('category.lbl_requires_doctor') }} <span class="text-danger">*</span></label>
                    <select name="service_classification" class="form-select" required>
                        <option value="doctor_required">{{ __('category.doctor_required') }}</option>
                        <option value="no_doctor_required">{{ __('category.no_doctor_required') }}</option>
                        <option value="doctor_optional">{{ __('category.doctor_optional') }}</option>
                    </select>
                </div>

                {{-- Doctor Assignment (Optional) --}}
                <div class="col-md-12">
                    <label class="form-label">{{ __('category.lbl_assign_doctors') }}</label>
                    <div class="border rounded p-3" id="doctors-container" style="max-height: 200px; overflow-y: auto;">
                        <!-- Doctors will be loaded here via JavaScript -->
                    </div>
                    <small class="text-muted">{{ __('category.assign_doctors_note') }}</small>
                </div>

                {{-- Featured --}}
               <!-- Set As Featured -->
            <div class="col-md-6">
                <label class="form-label">{{ __('category.lbl_featured') }}</label>
                <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2 bg-white">
                    <span>{{ __('category.lbl_featured') }}</span>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="featured" id="category-featured" value="1">
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="col-md-6">
                <label class="form-label">{{ __('category.lbl_status') }} <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2 bg-white">
                    <span>{{ __('category.lbl_status') }}</span>
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" name="status" id="form-status-toggle" value="1" checked required>
                    </div>
                </div>
            </div>


                {{-- Custom Fields --}}
                <div id="custom-fields-container" class="row"></div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="offcanvas-footer border-top p-3">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light text-muted border" data-bs-dismiss="offcanvas">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="save-category-btn">
                    <span class="btn-text">{{ isset($category) ? __('messages.update') : __('messages.save') }}</span>
                    <span class="spinner-border spinner-border-sm d-none"></span>
                    <span class="loading-text d-none">Loading...</span>
                </button>
            </div>
        </div>
    </div>
</form>

{{-- ========================== SCRIPT ========================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const offcanvas = $('#clinic-category-offcanvas');
    const form = $('#clinic-category-form');
    const saveBtn = $('#save-category-btn');
    const spinner = saveBtn.find('.spinner-border');
    const btnText = saveBtn.find('.btn-text');
    const loadingText = saveBtn.find('.loading-text');
    const descriptionTextarea = $('#description-textarea');
    const charCount = $('#char-count');

    const routes = {
        store: '{{ route("backend.category.store") }}',
        update: '{{ route("backend.category.update", ":id") }}',
        edit: '{{ route("backend.category.edit", ":id") }}',
        parentServices: '{{ route("backend.category.parent_services") }}',
        availableDoctors: '{{ route("backend.category.available_doctors") }}',
        customFields: '{{ route("backend.category.custom_fields") }}'
    };


    let categoryId = null;

    // Select2 Init - Only initialize select2 elements INSIDE the offcanvas
    offcanvas.find('.select2').select2({ 
        width: '100%', 
        dropdownParent: offcanvas 
    });

    // Char counter
    descriptionTextarea.on('input', function() {
        const len = $(this).val().length;
        charCount.text(len);
    });

    // Load parent services (not categories!)
    function loadParentServices(selectedId = null) {
        $.get(routes.parentServices, function (res) {
            if (res.status) {
                const select = $('#parent-service-select');
                select.empty().append(`<option value="">Select Service...</option>`);
                res.data.forEach(s => select.append(`<option value="${s.id}">${s.name}</option>`));
                if (selectedId) select.val(selectedId).trigger('change');
            }
        });
    }

    // Load available doctors for assignment
    function loadAvailableDoctors(selectedDoctorIds = []) {
        $.get(routes.availableDoctors, function (res) {
            if (res.status) {
                const container = $('#doctors-container');
                container.empty();
                if (res.data.length === 0) {
                    container.append('<p class="text-muted small mb-0">No doctors available</p>');
                    return;
                }
                res.data.forEach(doctor => {
                    const checked = selectedDoctorIds.includes(doctor.id) ? 'checked' : '';
                    container.append(`
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="doctor_ids[]" value="${doctor.id}" 
                                   id="doctor-${doctor.id}" ${checked}>
                            <label class="form-check-label" for="doctor-${doctor.id}">
                                ${doctor.name} - ${doctor.specialization}
                            </label>
                        </div>
                    `);
                });
            }
        });
    }

    // Load custom fields
    function loadCustomFields() {
        $.get(routes.customFields, function (res) {
            if (res.status) {
                const container = $('#custom-fields-container');
                container.empty();
                res.data.forEach(field => {
                    const req = field.required ? 'required' : '';
                    let html = `<div class="col-md-6"><label>${field.label}${field.required ? ' *' : ''}</label>`;
                    if (field.type === 'text')
                        html += `<input type="text" name="custom_fields_data[${field.name}]" class="form-control" ${req}>`;
                    if (field.type === 'select') {
                        html += `<select name="custom_fields_data[${field.name}]" class="form-select select2" ${req}>`;
                        field.value.forEach(v => html += `<option value="${v}">${v}</option>`);
                        html += `</select>`;
                    }
                    html += '</div>';
                    container.append(html);
                });
                // Only initialize select2 on newly added custom fields within the offcanvas
                container.find('.select2').select2({ width: '100%', dropdownParent: offcanvas });
            }
        });
    }

    // Load category data for edit - Make it globally accessible
    window.loadCategoryData = function(id) {
        categoryId = id; // Set the category ID for update
        $('#category-id').val(id); // Set the hidden ID field
        
        console.log('Loading category data for edit:', { id, categoryId });
        
        const editUrl = routes.edit.replace(':id', id);
        $.get(editUrl, function (res) {
            if (res.status) {
                const d = res.data;
                $('input[name="name"]').val(d.name);
                $('textarea[name="description"]').val(d.description);
                $('input[name="price"]').val(d.price || '');
                $('select[name="service_classification"]').val(d.service_classification || 'doctor_required');
                $('#category-featured').prop('checked', d.featured == 1);
                $('#form-status-toggle').prop('checked', d.status == 1);
                
                // Load parent services first, then set the selected value
                loadParentServices(d.parent_id);
                
                // Load doctors and set assigned ones
                if (d.assigned_doctors && d.assigned_doctors.length > 0) {
                    loadAvailableDoctors(d.assigned_doctors);
                } else {
                    loadAvailableDoctors([]);
                }
                
                if (d.file_url) $('#preview-image').attr('src', d.file_url);
                if (d.custom_field_data) {
                    Object.entries(d.custom_field_data).forEach(([k, v]) => {
                        $(`[name="custom_fields_data[${k}]"]`).val(v).trigger('change');
                    });
                }
                charCount.text(d.description?.length || 0);
                
                console.log('Category data loaded successfully for edit:', d);
            } else {
                console.error('Failed to load category data:', res);
            }
        }).fail(function(xhr, status, error) {
            console.error('Error loading category data:', { xhr, status, error });
        });
    }

    // Reset form function
    function resetForm() {
        form[0].reset();
        $('#preview-image').attr('src', '{{ asset("img/default.webp") }}');
        categoryId = null; // Reset category ID
        $('#category-id').val(''); // Clear hidden ID field
        charCount.text('0');
        // Ensure status is checked by default
        $('#form-status-toggle').prop('checked', true);
        // Ensure featured is unchecked by default
        $('#category-featured').prop('checked', false);
        // Clear image error messages
        $('#image-error').addClass('d-none').text('');
        // Reset select2 elements within the offcanvas
        offcanvas.find('.select2').val(null).trigger('change');
        // Reload parent services to reset the dropdown
        loadParentServices();
        // Reload doctors
        loadAvailableDoctors([]);
    }

    // Save (Create or Update) - Following working patterns from other forms
    saveBtn.on('click', function () {
        // Client-side validation
        let hasError = false;
        const nameInput = $('input[name="name"]');
        const nameError = $('#name-error');
        nameError.hide().text('');
        if (!nameInput.val() || nameInput.val().trim() === '') {
            nameError.text('{{ __('messages.name_this_field_is_required') }}');
            nameError.show();
            hasError = true;
        }
        if (hasError) {
            return; // stop submit
        }

        const formData = new FormData(form[0]);
        
        // Ensure boolean values are properly set
        formData.set('featured', $('#category-featured').is(':checked') ? '1' : '0');
        formData.set('status', $('#form-status-toggle').is(':checked') ? '1' : '0');
        
        // Show loading state
        btnText.addClass('d-none'); 
        spinner.removeClass('d-none');
        loadingText.removeClass('d-none');
        saveBtn.prop('disabled', true);
        saveBtn.css('opacity', '0.7');

        // Determine if this is create or update based on categoryId and form data
        const isEdit = categoryId && categoryId > 0;
        let url = '';
        
        console.log('Form submission mode:', { isEdit, categoryId, hiddenId: $('#category-id').val() });
        
        if (isEdit) {
            // Update existing category
            url = routes.update.replace(':id', categoryId);
            formData.append('_method', 'PUT');
            formData.append('id', categoryId); // Ensure ID is included in form data
            console.log('Updating category:', { url, categoryId, formData: Object.fromEntries(formData) });
        } else {
            // Create new category
            url = routes.store;
            formData.delete('_method'); // Ensure no PUT method for create
            formData.delete('id'); // Remove any ID for create operation
            console.log('Creating new category:', { url, formData: Object.fromEntries(formData) });
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                console.log('Success response:', res);
                if (res.status) {
                    // Show success toast message
                    if (typeof window.successSnackbar === 'function') {
                        window.successSnackbar(res.message || (isEdit ? 'Category updated successfully!' : 'Category created successfully!'));
                    }
                    offcanvas.offcanvas('hide');
                    $(document).trigger('categoryUpdated', [res.data]);
                    if (window.renderedDataTable) window.renderedDataTable.ajax.reload(null, false);
                    resetForm();
                } else {
                    // Show error toast for operation failure
                    if (typeof window.errorSnackbar === 'function') {
                        window.errorSnackbar(res.message || 'Operation failed.');
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Operation failed.' });
                    }
                }
            },
            error: function (xhr) {
                console.error(isEdit ? 'Update Error:' : 'Create Error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    responseJSON: xhr.responseJSON
                });
                
                // Display inline validation errors
                const resp = xhr.responseJSON || {};
                if (resp.errors && resp.errors.name) {
                    nameError.text(resp.errors.name.join(', ')).show();
                }
                
                // Show error toast message
                if (typeof window.errorSnackbar === 'function') {
                    window.errorSnackbar(errorMessage);
                } else {
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Error', 
                        text: errorMessage
                    });
                }
            },
            complete: function () {
                // Hide loading state
                btnText.removeClass('d-none'); 
                spinner.addClass('d-none');
                loadingText.addClass('d-none');
                saveBtn.prop('disabled', false);
                saveBtn.css('opacity', '1');
            }
        });
    });

    // Image validation and preview
    window.validateAndPreviewImage = function (e) {
        const file = e.target.files[0];
        const errorDiv = $('#image-error');
        
        // Clear previous errors
        errorDiv.addClass('d-none').text('');
        
        if (!file) return;
        
        // Check file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            errorDiv.text('Unsupported file format. Please upload only JPEG, JPG, PNG, or GIF files.').removeClass('d-none');
            e.target.value = ''; // Clear the input
            return;
        }
        
        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
            errorDiv.text('File size too large. Please upload files smaller than 2MB.').removeClass('d-none');
            e.target.value = ''; // Clear the input
            return;
        }
        
        // If validation passes, preview the image
        const reader = new FileReader();
        reader.onload = () => $('#preview-image').attr('src', reader.result);
        reader.readAsDataURL(file);
        $('#removeImageInput').val('0');
    }

    // Legacy function for backward compatibility
    window.previewImage = function (e) {
        validateAndPreviewImage(e);
    }

    window.removeImage = function () {
        $('#preview-image').attr('src', '{{ asset("img/default.webp") }}');
        $('#fileInput').val('');
        $('#removeImageInput').val('1');
        $('#image-error').addClass('d-none').text(''); // Clear any error messages
    }

    // Global function for creating new category
    window.createNewCategory = function() {
        resetForm();
        const offcanvas = document.getElementById('clinic-category-offcanvas');
        const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
        bsOffcanvas.show();
    };

    // Reset form when offcanvas is hidden
    document.getElementById('clinic-category-offcanvas')?.addEventListener('hidden.bs.offcanvas', function() {
        resetForm();
    });

    // Initial Load
    loadParentServices();
    loadAvailableDoctors([]);
    loadCustomFields();
});
</script>