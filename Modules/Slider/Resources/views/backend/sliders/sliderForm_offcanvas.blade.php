<form id="sliderForm" action="{{ isset($slider) ? route('backend.app-banners.update', $slider->id) : route('backend.app-banners.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
        @if(isset($slider))
            @method('PUT')
            <input type="hidden" name="id" value="{{ $slider->id }}" />
        @endif
        
        <div class="offcanvas offcanvas-end" tabindex="-1" id="form-offcanvas" aria-labelledby="form-offcanvasLabel">
            
            {{-- Header --}}
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="form-offcanvasLabel">
                    {{ isset($slider) ? __('slider.edit_title') : __('slider.create_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
    
            <div class="offcanvas-body">
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('slider.lbl_file_url') }}</label>
                    <div class="image-upload-container text-center">
                        <div class="slider-image-preview d-flex justify-content-center align-items-center mb-2 rounded-circle overflow-hidden">
                            <img id="sliderImagePreview"
                                alt="Slider Image Preview"
                                src="{{ isset($slider) && $slider->getFirstMediaUrl('file_url') ? $slider->getFirstMediaUrl('file_url') : (function_exists('default_file_url') ? default_file_url() : asset('img/default.webp')) }}"
                                class="img-fluid object-fit-cover avatar-170 rounded-circle"/>
                        </div>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-light"
                                    onclick="document.getElementById('sliderFileInput').click();">
                                {{ __('clinic.upload') }}
                            </button>
                            <!-- <button type="button"
                                    class="btn btn-danger"
                                    @php $hasImage = isset($slider) && $slider->getFirstMediaUrl('file_url'); @endphp
                                    {{ $hasImage ? '' : 'disabled' }}
                                    onclick="removeSliderImage();">
                                {{ __('messages.remove') }}
                            </button> -->
                        </div>
                        <input type="file" name="file_url" id="sliderFileInput" style="display: none;"
                               accept=".jpeg,.jpg,.png,.gif"
                               onchange="previewSliderImage(this);" />
                        <input type="hidden" name="remove_file" id="remove_slider_file" value="0" />
                        <span class="text-danger">Only .jpeg, .jpg, .png files are allowed.</span>
                    </div>
                    <span class="text-danger">@error('file_url'){{ $message }}@enderror</span>
                </div>
                {{-- Name --}}
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('slider.lbl_name') }} <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $slider->name ?? '') }}" 
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="{{ __('slider.lbl_name') }}">
                    <span class="validation-error text-danger"></span>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
    
                {{-- Link --}}
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('slider.lbl_link') }}</label>
                    <input type="text" 
                           name="link" 
                           value="{{ old('link', $slider->link ?? '') }}" 
                           class="form-control @error('link') is-invalid @enderror"
                           placeholder="{{ __('slider.lbl_link') }}">
                    @error('link')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
    
                {{-- Type --}}
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('slider.lbl_type') }} <span class="text-danger">*</span></label>
                    <select name="type" class="select2 form-select @error('type') is-invalid @enderror">
                        <option value="">{{ __('slider.lbl_type') }}</option>
                        @foreach($types as $key => $value)
                            <option value="{{ $key }}" 
                                {{ old('type', $slider->type ?? '') == $key ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                    <span class="validation-error text-danger"></span>
                    @error('type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
    
                {{-- Link ID --}}
                <div class="form-group mb-3">
                    <label class="form-label">{{ __('slider.lbl_link_id') }}</label>
                    <select name="link_id" class="select2 form-select @error('link_id') is-invalid @enderror">
                        <option value="">{{ __('slider.lbl_link_id') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('link_id', $slider->link_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
    
                {{-- Status --}}
                <div class="form-group mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-label">{{ __('slider.lbl_status') }}</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   name="status" 
                                   value="1" 
                                   {{ old('status', $slider->status ?? 1) ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
    
            </div>
    
            {{-- Footer --}}
            <div class="offcanvas-footer p-3 d-flex justify-content-end gap-2">
                <button type="button" 
                        class="btn btn-white" 
                        data-bs-dismiss="offcanvas">
                    {{ __('messages.cancel') }}
                </button>
                <button type="submit" class="btn btn-secondary">
                    {{ isset($slider) ? __('messages.update') : __('messages.save') }}
                </button>
            </div>
    
        </div>
    </form>
    
    <script>
    window.previewSliderImage = function(input) {
        const file = input?.files && input.files[0] ? input.files[0] : null;
        const preview = document.getElementById('sliderImagePreview');
        const removeBtn = document.querySelector('.image-upload-container .btn.btn-danger');
        const removeFlag = document.getElementById('remove_slider_file');
        if (!file || !preview) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            if (removeBtn) removeBtn.disabled = false;
            if (removeFlag) removeFlag.value = '0';
        };
        reader.readAsDataURL(file);
    }
    
    window.removeSliderImage = function() {
        const preview = document.getElementById('sliderImagePreview');
        const fileInput = document.getElementById('sliderFileInput');
        const removeFlag = document.getElementById('remove_slider_file');
        const removeBtn = document.querySelector('.image-upload-container .btn.btn-danger');
        
        const defaultUrl = "{{ function_exists('default_file_url') ? default_file_url() : asset('img/default.webp') }}";
        
        if (preview) {
            preview.src = defaultUrl;
        }
        if (fileInput) {
            fileInput.value = '';
        }
        if (removeFlag) {
            removeFlag.value = '1';
        }
        if (removeBtn) {
            removeBtn.disabled = true;
        }
    }
    
    // Form validation
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('sliderForm');
        if (!form) {
            return;
        }
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Clear previous validation errors
            form.querySelectorAll('.validation-error').forEach(span => {
                span.textContent = '';
                span.style.display = 'none';
            });
            form.querySelectorAll('.is-invalid').forEach(input => {
                input.classList.remove('is-invalid');
            });
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = '{{ __("messages.processing") }}';
            
            const formData = new FormData(form);
            const url = form.action;
            const method = form.querySelector('input[name="_method"]')?.value || 'POST';
            
            fetch(url, {
                method: method === 'PUT' ? 'POST' : method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    showNotification('success', data.message || '{{ __("messages.saved_successfully") }}');
                    
                    // Close offcanvas
                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('form-offcanvas'));
                    if (offcanvas) {
                        offcanvas.hide();
                    }
                    
                    // Reload datatable if exists
                    if (window.LaravelDataTables && window.LaravelDataTables['sliders-table']) {
                        window.LaravelDataTables['sliders-table'].draw();
                    } else if (typeof reloadDatatable === 'function') {
                        reloadDatatable();
                    } else {
                        location.reload();
                    }
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`);
                            const errorSpan = input?.parentElement.querySelector('.validation-error');
                            
                            if (input) {
                                input.classList.add('is-invalid');
                            }
                            
                            if (errorSpan) {
                                errorSpan.textContent = data.errors[field][0];
                                errorSpan.style.display = 'block';
                            } else {
                                // Fallback: create error span if not found
                                if (input) {
                                    const fallbackSpan = document.createElement('span');
                                    fallbackSpan.className = 'validation-error text-danger';
                                    fallbackSpan.style.display = 'block';
                                    fallbackSpan.textContent = data.errors[field][0];
                                    input.parentElement.appendChild(fallbackSpan);
                                }
                            }
                        });
                        
                        // Show general error notification
                        showNotification('error', 'Please fix the validation errors above');
                    } else {
                        showNotification('error', data.message || '{{ __("messages.error_occurred") }}');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', '{{ __("messages.error_occurred") }}');
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    });
    
    // Notification function (reuse global snackbar like MultiVendor)
    function showNotification(type, message) {
        const isSuccess = (type === 'success' || type === true);
        const useSuccess = typeof window.successSnackbar === 'function';
        const useError = typeof window.errorSnackbar === 'function';
        
        if (isSuccess && useSuccess) {
            window.successSnackbar(message);
            return;
        }
        if (!isSuccess && useError) {
            window.errorSnackbar(message);
            return;
        }
        // Fallback if snackbar lib not loaded
        alert(message);
    }
    </script>
    
    
