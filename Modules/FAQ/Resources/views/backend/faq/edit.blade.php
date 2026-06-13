@extends('backend.layouts.app')

@section('content')

<x-backend.section-header>
    <x-slot name="toolbar">
        <div class="d-flex justify-content-end">
            <a href="{{ route('backend.faqs.index') }}" class="btn btn-primary" data-type="ajax"
                data-bs-toggle="tooltip">
                {{ __('appointment.back') }}
            </a>
        </div>
    </x-slot>
</x-backend.section-header>

{{ html()->form('POST', route('backend.faqs.update', $data->id))
        ->attribute('enctype', 'multipart/form-data')
        ->attribute('data-toggle', 'validator')
        ->attribute('id', 'form-submit')  // Add the id attribute here
        ->class('requires-validation')  // Add the requires-validation class
        ->attribute('novalidate', 'novalidate')  // Disable default browser validation
        ->open() }}
@csrf
@method('PUT')

<div class="card">
    <div class="card-body">
        <div class="row gy-3">
            <!-- Question Field -->
            <div class="col-md-6">
                <div class="form-group">
                    {{ html()->label(__('messages.lbl_question') . ' <span class="text-danger">*</span>', 'question')->class('form-label') }}
                    {{ html()->text('question')
                            ->attribute('value', $data->question)
                            ->placeholder(__('messages.enter_question'))
                            ->class('form-control')
                            ->required() }}
                    @error('question')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div class="invalid-feedback" id="question-error">Question field is required</div>
                </div>
            </div>

            <!-- Answer Field -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="answer" class="form-label">{{ __('messages.lbl_answer') }} <span class="text-danger">*</span></label>
                    <textarea name="answer" id="answer" class="form-control tinymce-template" placeholder="{{ __('messages.enter_answer') }}" required>{{ old('answer', $data->answer) }}</textarea>
                    @error('answer')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    <div class="invalid-feedback" id="answer-error">Answer field is required</div>
                </div>
            </div>

            <!-- Status Field -->
            <div class="col-md-6">
                <div class="form-group">
                    <div class="d-flex align-items-center gap-3">
                        {{ html()->label(__('messages.lbl_status'), 'status')->class('form-label') }}
                        <!-- {{ html()->label(__('messages.active'), 'status')->class('form-label mb-0 text-body') }} -->
                        <div class="form-check form-switch">
                            {{ html()->hidden('status', 0) }}
                            {{ html()->checkbox('status', $data->status)
                                    ->class('form-check-input')
                                    ->id('status') }}
                        </div>
                    </div>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-grid d-sm-flex justify-content-sm-end gap-3 mb-5">
    {{ html()->submit(trans('messages.save'))->class('btn btn-md btn-primary float-right')->id('submit-button') }}
</div>

{{ html()->form()->close() }}
@endsection

@push('after-scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Initialize TinyMCE editor for answer field
        tinymce.init({
            selector: '.tinymce-template',
            height: 300,
            menubar: false,
            plugins: 'autoresize link image code',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | link image',
            setup: function(editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });

        // Initialize Select2 for any select fields if needed
        $('.select2').select2();
    });
</script>
@endpush
