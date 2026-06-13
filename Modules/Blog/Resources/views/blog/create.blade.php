@extends('backend.layouts.app')

@section('title', $pageTitle)

@section('content')

    <x-backend.section-header>
        <x-slot name="toolbar">
            <div class="d-flex justify-content-end">
                <a href="{{ route('backend.blog.index') }}" class="btn btn-primary" data-type="ajax"
                    data-bs-toggle="tooltip">
                    {{ __('appointment.back') }}
                </a>
            </div>
        </x-slot>
    </x-backend.section-header>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        {{ html()->form('POST', route('backend.blog.store'))->attributes(['enctype' => 'multipart/form-data', 'data-toggle' => 'validator', 'id' => 'blog'])->open() }}
                        {{ html()->hidden('id', $blogdata->id ?? null) }}

                        <div class="row  align-items-center">
                            <!-- Title -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{ html()->label(trans('messages.title') . ' <span class="text-danger">*</span>', 'title')->class('form-label') }}
                                    {{ html()->text('title', $blogdata->title)->placeholder(__('messages.enter_blog_title'))->class('form-control') }}
                                </div>
                            </div>

                            <!-- Author (Vendor List) -->
                            @if(auth()->user()->hasAnyRole(['admin','demo_admin']))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="author_id">{{ __('messages.author') }}</label>
                                    <div class="author-custom-dropdown" style="position: relative;">
                                        <input type="text" id="author_search_input" class="form-control" placeholder="{{ __('messages.select_author') }}" readonly>
                                        <select name="author_id" id="author_id" class="form-control" style="display: none;">
                                            <option value="">{{ __('messages.select_author') }}</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}" {{ ($blogdata->author_id ?? null) == $vendor->id ? 'selected' : '' }}>{{ $vendor->full_name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="author-dropdown-list" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ced4da; border-radius: 0.375rem; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                                            <!-- Options will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Tags -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="tags">{{ __('messages.tags') }}</label>
                                <select class="form-select select2-tag" name="tags[]" multiple id="tags">
                                    @if(isset($blogdata) && $blogdata->tags != null)
                                        @foreach(json_decode($blogdata->tags) as $tags)
                                            <option value="{{ $tags }}" selected>{{ $tags }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>


                        <!-- Image -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label" for="blog_attachment">{{ __('messages.image') }}</label>
                                <input type="file" name="blog_attachment[]" class="form-control" id="blog_attachment" multiple accept="image/*">
                            </div>
                        </div>

                         <!-- Status -->
                         <div class="col-md-4">
                             <div class="form-group">
                                <label class="form-label fw-semibold d-block">{{ __('messages.status') }}</label>
                                <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2 bg-white">
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
                                            {{ (isset($blogdata) ? $blogdata->status : old('status', 1)) ? 'checked' : '' }}
                                        >
                                    </div>
                                </div>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Image Preview Row (separate row to prevent layout shifts) -->
                        <div class="col-md-12" id="image-preview-row" @if(!isset($blogdata) || $blogdata->getMedia('blog_attachment')->isEmpty()) style="display: none;" @endif>
                            <div class="form-group">
                                <label class="form-label">{{ __('messages.image_preview') }}</label>
                                <div id="image-preview-container">
                                    @if(isset($blogdata) && $blogdata->getMedia('blog_attachment')->isNotEmpty())
                                        <ul class="list-unstyled d-flex flex-wrap">
                                            @foreach($blogdata->getMedia('blog_attachment') as $media)
                                                <li style="position: relative; margin-right: 10px; margin-bottom: 10px;">
                                                    <!-- Image display -->
                                                    <img src="{{ $media->getUrl() }}" alt="Blog Image" 
                                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;" />
                        
                                                    <a href="{{ route('backend.blog.remove-media', ['id' => $blogdata->id, 'media_id' => $media->id]) }}" 
                                                        class="text-danger" 
                                                        >
                                                        <i class="fas fa-times"></i>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>

                            <!-- Description -->
                        <div class="col-md-12">
                            <div class="form-group col-md-12">
                                {{ html()->label(__('messages.description'), 'description')->class('form-control-label') }}
                                {{ html()->textarea('description',$blogdata->description)->class('form-control tinymce-template')->placeholder(__('messages.description')) }}
                            </div>
                        </div>
                        </div>

                        {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-end') }}
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('bottom_script')
    @push('after-scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>

<style>
.tinymce-template {
    height: 300px !important;
}
.tinymce-template:empty::before {
    content: "Description";
    color: #999;
    position: absolute;
    pointer-events: none;
    z-index: 1;
    padding: 10px;
}

/* Fix form labels - remove blue highlighting */
.form-label {
    background: none !important;
    color: #333 !important;
    padding: 0 !important;
    margin-bottom: 0.5rem !important;
    font-weight: 500 !important;
}

/* Ensure labels don't have blue background */
label.form-label {
    background: transparent !important;
    color: #333 !important;
}

/* Remove any blue highlighting from labels */
.form-group label {
    background: none !important;
    color: #333 !important;
    padding: 0 !important;
}
</style>

<script>
    $(document).ready(function() {
            $('#tags').select2({
                tags: true,
                tokenSeparators: [',', ' '],
                placeholder: '{{ __("messages.type_to_add_tags") }}',
                allowClear: true,
                width: '100%',
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });
        });
    $(document).ready(function() {
        tinymce.init({
            selector: '.tinymce-template',
            height: 300,
            menubar: false,
            plugins: 'link image code',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image',
            content_style: 'body { padding: 10px; margin: 0; }',
            setup: function(editor) {
                editor.on('change', function () {
                    editor.save();
                });
                editor.on('init', function() {
                    // Check if editing existing blog with content
                    var body = editor.getBody();
                    var hasContent = body.innerHTML.trim() !== '' && body.innerHTML.trim() !== '<p><br></p>' && body.innerHTML.trim() !== '<p></p>';
                    
                    if (!hasContent) {
                        // Only set empty content for new blogs
                        body.innerHTML = '<p><br></p>';
                        // Set cursor to the beginning of the first paragraph
                        editor.selection.setCursorLocation(body.firstChild, 0);
                    }
                });
                editor.on('focus', function() {
                    // Clear placeholder when focused
                    var body = editor.getBody();
                    if (body.innerHTML.trim() === '<p><br></p>' || body.innerHTML.trim() === '<p></p>') {
                        body.innerHTML = '<p><br></p>';
                    }
                });
            }
        });
    });
    
    // Simple client-side validation for required fields
    $(document).ready(function() {
        var $form = $('#blog');
        var $titleInput = $form.find('input[name="title"]');

        function validateTitle() {
            var value = $.trim($titleInput.val());
            if (value === '') {
                $titleInput.addClass('is-invalid');
                if ($titleInput.next('.invalid-feedback').length === 0) {
                    $titleInput.after('<div class="invalid-feedback">{{ __("validation.required", ["attribute" => __("messages.title")]) }}</div>');
                }
                return false;
            }   
            $titleInput.removeClass('is-invalid');
            $titleInput.next('.invalid-feedback').remove();
            return true;
        }

        $titleInput.on('input blur', validateTitle);

        $form.on('submit', function(e) {
            var ok = validateTitle();
            if (!ok) {
                e.preventDefault();
                $('html, body').animate({ scrollTop: ($titleInput.offset().top - 120) }, 300);
            }
        });

        // Status toggle functionality
        $('#status').on('change', function() {
            var $statusText = $(this).closest('.d-flex').find('span.mb-0');
            if ($(this).is(':checked')) {
                $statusText.text('Active');
            } else {
                $statusText.text('Inactive');
            }
        });

        // Initialize status text on page load
        var $statusCheckbox = $('#status');
        var $statusText = $statusCheckbox.closest('.d-flex').find('span.mb-0');
        if ($statusCheckbox.is(':checked')) {
            $statusText.text('Active');
        } else {
            $statusText.text('Inactive');
        }

        // Initialize image preview row visibility on page load
        var $previewRow = $('#image-preview-row');
        var $previewContainer = $('#image-preview-container');
        
        // Show preview row if there are existing images
        if ($previewContainer.find('ul li').length > 0) {
            $previewRow.show();
        }

        // Initialize Author dropdown functionality
        function initAuthorDropdown() {
            const authorSelect = document.getElementById('author_id');
            const searchInput = document.getElementById('author_search_input');
            const dropdownList = document.querySelector('.author-dropdown-list');
            
            if (!authorSelect || !searchInput || !dropdownList) return;

            // Populate dropdown with options
            function populateDropdown() {
                dropdownList.innerHTML = '';
                const options = authorSelect.querySelectorAll('option');
                
                options.forEach((option, index) => {
                    if (option.value === '') return; // Skip placeholder
                    
                    const item = document.createElement('div');
                    item.className = 'author-dropdown-item';
                    item.style.padding = '8px 12px';
                    item.style.cursor = 'pointer';
                    item.style.borderBottom = '1px solid #f8f9fa';
                    item.textContent = option.textContent;
                    item.dataset.value = option.value;
                    
                    // Hover effects
                    item.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#f8f9fa';
                    });
                    item.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = '#fff';
                    });
                    
                    // Click handler
                    item.addEventListener('click', function() {
                        authorSelect.value = this.dataset.value;
                        searchInput.value = this.textContent;
                        dropdownList.style.display = 'none';
                        
                        // Trigger change event
                        const changeEvent = new Event('change', { bubbles: true });
                        authorSelect.dispatchEvent(changeEvent);
                    });
                    
                    dropdownList.appendChild(item);
                });
            }

            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const items = dropdownList.querySelectorAll('.author-dropdown-item');
                
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Show/hide dropdown
            searchInput.addEventListener('focus', function() {
                dropdownList.style.display = 'block';
                populateDropdown();
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.closest('.author-custom-dropdown').contains(e.target)) {
                    dropdownList.style.display = 'none';
                }
            });

            // Initialize with current value
            const selectedOption = authorSelect.querySelector('option:checked');
            if (selectedOption && selectedOption.value !== '') {
                searchInput.value = selectedOption.textContent;
            }

            // Update search input when select value changes
            authorSelect.addEventListener('change', function() {
                const selectedOption = this.querySelector('option:checked');
                if (selectedOption) {
                    searchInput.value = selectedOption.textContent;
                }
            });
        }

        // Initialize author dropdown
        initAuthorDropdown();


        // Image preview functionality
        $('#blog_attachment').on('change', function(e) {
            var files = e.target.files;
            var previewContainer = $('#image-preview-container');
            var previewRow = $('#image-preview-row');
            
            // Clear ALL existing previews (both new and existing uploaded images)
            previewContainer.find('.new-image-preview').remove();
            previewContainer.find('ul').remove();
            
            if (files.length > 0) {
                // Show the preview row
                previewRow.show();
                
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    if (file.type.startsWith('image/')) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            var previewHtml = '<div class="new-image-preview" style="position: relative; margin-right: 10px; margin-bottom: 10px; display: inline-block;">' +
                                '<img src="' + e.target.result + '" alt="Image Preview" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">' +
                                '<button type="button" class="btn btn-sm btn-danger remove-preview" style="position: absolute; top: -5px; right: -5px; width: 20px; height: 20px; border-radius: 50%; padding: 0; font-size: 12px;">×</button>' +
                                '</div>';
                            previewContainer.append(previewHtml);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            } else {
                // Hide the preview row if no files selected
                previewRow.hide();
            }
        });

        // Remove preview image functionality
        $(document).on('click', '.remove-preview', function() {
            $(this).closest('.new-image-preview').remove();
            
            // Clear the file input
            $('#blog_attachment').val('');
            
            // Hide preview row if no more previews
            if ($('#image-preview-container').find('.new-image-preview').length === 0) {
                $('#image-preview-row').hide();
            }
        });
    });
</script>
@endpush
@endsection
