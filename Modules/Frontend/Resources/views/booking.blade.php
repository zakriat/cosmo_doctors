@extends('frontend::layouts.patient_layout')
@section('title', __('frontend.booking'))

@section('content')
    <div class="list-page section-spacing px-0">
        <div class="container">

            {{--<div class="widget-tabs px-3 mb-5 pb-3 ">
                <div class="row tab-list">
                    @foreach ($tabs as $index => $tab)
                        <div class="col-4 tab-item" style="--before-content: '{{ $index + 1 }}'"
                            @class(['active' => $index === $currentStep]) data-check="{{ $index < $currentStep ? 'true' : 'false' }}"
                            data-label="{{ $tab['label'] }}">
                            <a href="#" class="nav-link tab-index" data-index="{{ $index }}">
                                <h6
                                    class="mb-0 ms-3 {{ $index === $currentStep ? 'text-primary' : ($index < $currentStep ? 'text-black' : 'text-body') }}">
                                    {{ $tab['label'] }}
                                </h6>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>--}}

            <div class="mb-5 pb-3">
                <ul class="appointments-steps-list">
                    @foreach ($tabs as $index => $tab)
                        <li
                            class="appointments-steps-item
                            {{ $index < $currentStep ? 'complete' : '' }}
                            {{ $index === $currentStep ? 'active' : '' }}"
                            data-check="{{ $index < $currentStep ? 'true' : 'false' }}">
                            <div class="appointments-step d-flex align-items-center gap-3">
                                <span class="flex-shrink-0">
                                    <a href="#" class="appointments-step-inner tab-index" data-index="{{ $index }}">
                                        <span class="d-flex align-items-center gap-2">
                                            <span class="step-counter">{{ $index + 1 }}</span>
                                            <span
                                                class="step-text mb-0 ms-3 {{ $index === $currentStep ? 'text-primary' : ($index < $currentStep ? 'text-black' : 'text-body') }}">
                                                {{ $tab['label'] }}
                                            </span>
                                        </span>
                                    </a>
                                </span>
                                @if($index < count($tabs) - 1)
                                    <span class="flex-grow-1 separator">
                                        <span class="line"></span>
                                    </span>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="row">
                <div class="col-lg-8">

                    <div>
                        <div class="mb-3">
                            <i class="ph ph-caret-left align-middle"></i>
                            <a id="prev-step-btn" href="javascript:void(0);" class="text-body font-size-14 fw-semibold">
                                {{ __('frontend.previous_step') }} </a>
                        </div>

                        <div class="row gy-3">
                            @if (isset($selectedService))
                                <div class="col-lg-4 ">
                                    <div class="bg-primary-subtle service-box-wizard rounded p-3 position-relative">
                                        <div class="position-absolute top-0 end-0 m-2 ">
                                            <a href="{{ $previousUrl ?? '#' }}" class="text-muted" id="service-edit-button"
                                                data-step="0">
                                                <i class="ph ph-pencil-simple heading-color"></i>
                                            </a>
                                        </div>
                                        <div>
                                            <p class="font-size-14 heading-color mb-2">{{ __('frontend.service') }}</p>
                                            <h6 class="font-size-14 text-heading fw-semibold mb-0">
                                                {{ $selectedService->name }}</h6>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (isset($selectedCategory))
                                <div class="col-lg-4">
                                    <div class="bg-success-subtle category-box-wizard rounded p-3 position-relative">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <a href="#" class="text-muted" id="category-edit-button" data-step="0">
                                                <i class="ph ph-pencil-simple heading-color"></i>
                                            </a>
                                        </div>
                                        <div>
                                            <p class="font-size-14 heading-color mb-2">{{ __('frontend.category') }}</p>
                                            <h6 class="font-size-14 text-heading fw-semibold mb-0">
                                                {{ $selectedCategory->name }}</h6>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @php
                                $clinicTab = collect($tabs)->firstWhere('value', 'Choose Clinics');
                                $doctorTab = collect($tabs)->firstWhere('value', 'Choose Doctors');
                            @endphp

                            @if ($clinicTab && $doctorTab)
                                @if ($clinicTab['index'] < $doctorTab['index'])
                                    <div id="selected-clinic-container" class="col-lg-4">
                                        @if (isset($selectedClinic))
                                            <div
                                                class="bg-primary-subtle rounded p-3  clinic-box-wizard p-3 position-relative">
                                                <div class="position-absolute top-0 end-0 m-2" id="clinic-edit-button"
                                                    data-step="{{ $clinicTab['index'] }}">
                                                    <a href="#" class="text-muted">
                                                        <i class="ph ph-pencil-simple heading-color"></i>
                                                    </a>
                                                </div>
                                                <div>
                                                    <p class="font-size-14 heading-color mb-2">{{ __('frontend.clinic') }}</p>
                                                    <h6 class="font-size-14 text-heading fw-semibold mb-0">
                                                        {{ $selectedClinic->name }}</h6>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div id="selected-doctor-container" class="col-lg-4">
                                        @if (isset($selectedDoctor))
                                            <div class="card shadow-sm small-card doctor-card p-3 position-relative">
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <a href="#" class="text-muted" id="doctor-edit-button"
                                                        data-step="{{ $doctorTab['index'] }}">
                                                        <i class="ph ph-pencil-simple"></i>
                                                    </a>
                                                </div>

                                                <div>
                                                    <p class="mb-1">{{ __('frontend.doctor') }}</p>
                                                    <h6 class="card-title mb-1">
                                                        {{ $selectedDoctor->user->first_name }}
                                                        {{ $selectedDoctor->user->last_name }}
                                                    </h6>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div id="selected-doctor-container" class="col-lg-4">
                                        @if (isset($selectedDoctor))
                                            <div class="bg-primary-subtle doctor-box-wizard rounded p-3 position-relative">
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <a href="#" class="text-muted" id="doctor-edit-button"
                                                        data-step="{{ $doctorTab['index'] }}">
                                                        <i class="ph ph-pencil-simple"></i>
                                                    </a>
                                                </div>
                                                <div>
                                                    <p class="font-size-14 text-body mb-2">{{ __('frontend.doctor') }}
                                                    </p>
                                                    <h6 class="font-size-14 text-heading fw-semibold mb-0">
                                                        {{ $selectedDoctor->user->first_name }}
                                                        {{ $selectedDoctor->user->last_name }}
                                                    </h6>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div id="selected-clinic-container" class="col-lg-4">
                                        @if (isset($selectedClinic))
                                            <div class="bg-primary-subtle clinic-box-wizard rounded p-3 position-relative">
                                                <div class="position-absolute top-0 end-0 m-2" id="clinic-edit-button"
                                                    data-step="{{ $clinicTab['index'] }}">
                                                    <a href="#" class="text-muted">
                                                        <i class="ph ph-pencil-simple"></i>
                                                    </a>
                                                </div>
                                                <div>
                                                    <p class="font-size-14 text-body mb-2">{{ __('frontend.clinic') }}</p>
                                                    <h6 class="font-size-14 text-heading fw-semibold mb-0">{{ $selectedClinic->name }}</h6>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endif

                            @if (isset($appointmentDate) || isset($selectedSlot))
                                <div class="col-3">
                                    <div class="card shadow-sm small-card appointment-card p-3">
                                        <div>
                                            <p class="mb-1">{{ __('frontend.appointment_details') }}</p>
                                            <h6 class="card-title mb-1">
                                                {{ $appointmentDate ?? '' }}{{ isset($appointmentDate) && isset($selectedSlot) ? ' , ' : '' }}{{ $selectedSlot ?? '' }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div id="step-content">
                            <div id="step-content-3" class="step-content d-none">

                                <!-- Choose Date and Time Section -->
                                <div class="mb-50 mt-5">
                                    <h6 class="mb-2">{{ __('frontend.choose_date') }}
                                    </h6>
                                    <div class="form-group position-relative">
                                        <div class="input-group">

                                            <input type="text" id="appointment_date" class="form-control"
                                                name="appointment_date" placeholder="Select appointment date" value="{{ date('Y-m-d') }}">
                                            <span class="input-group-text" id="calendar-icon">
                                                <i class="ph ph-calendar"></i>
                                                <!-- Replace with your preferred icon library -->
                                            </span>
                                        </div>
                                    </div>

                                    <div class="section-bg rounded p-3 mt-3" id="time-slot-card">
                                        <div class="booked-time">
                                            <span class="font-size-14 mb-2">{{ __('frontend.choose_time') }}                                            </span>
                                            <div class="d-flex flex-wrap justify-content-start" id="time-slots-container">
                                                <!-- Available time slots will be dynamically inserted here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="mb-0">{{ __('messages.booking_for') }}</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="bookForOthers">
                                                                          </div>
                                </div>

                                <!-- Add this new section for other patients -->
                                <div id="otherPatientsSection" class="my-3 d-none">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ __('clinic.lbl_select_patient') }}</h6>
                                        <button type="button" class="btn btn-link p-0 fw-semibold text-secondary" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                                            {{ __('clinic.add_other_patient') }}
                                        </button>
                                    </div>

                                    <div class="other-patients-list d-flex flex-wrap gap-3 mt-3">
                                        <!-- Other patients will be loaded here dynamically -->
                                    </div>
                                </div>
                                <!-- Enhanced Medical History Section -->
                                <div class="mb-50">
                                    <h6 class="font-size-18 fw-semibold">{{ __('frontend.medical_history') }}</h6>
                                    
                                    <!-- Speech-to-Text Controls -->
                                    <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
                                        <button type="button" id="record-audio-btn" class="btn btn-outline-primary btn-sm">
                                            <i class="ph ph-microphone"></i> {{ __('frontend.record_audio') }}
                                        </button>
                                        <button type="button" id="upload-audio-btn" class="btn btn-outline-secondary btn-sm">
                                            <i class="ph ph-upload"></i> {{ __('frontend.upload_audio') }}
                                        </button>
                                        <input type="file" id="audio-file-input" accept="audio/*" multiple class="d-none">
                                        <button type="button" id="stop-recording-btn" class="btn btn-danger btn-sm d-none">
                                            <i class="ph ph-stop"></i> {{ __('frontend.stop_recording') }}
                                        </button>
                                        <button type="button" id="cancel-recording-btn" class="btn btn-outline-secondary btn-sm d-none">
                                            {{ __('frontend.cancel_recording') }}
                                        </button>
                                        <span id="recording-timer" class="text-muted d-none fw-bold">00:00</span>
                                    </div>

                                    <!-- Audio Player (hidden until recording is made) -->
                                    <div id="audio-player-container" class="mb-3 d-none">
                                        <div class="audio-player-wrapper p-3 border rounded bg-light">
                                            <div class="mb-2">
                                                <small class="text-muted">{{ __('frontend.current_audio') }}:</small>
                                                <strong id="audio-name-display" class="d-block">Recording 1</strong>
                                            </div>
                                            <audio id="audio-player" controls class="w-100 mb-2"></audio>
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button type="button" id="transcribe-btn" class="btn btn-primary btn-sm">
                                                    <i class="ph ph-plus-circle"></i> {{ __('frontend.add_to_queue') }}
                                                </button>
                                                <button type="button" id="delete-recording-btn" class="btn btn-outline-danger btn-sm">
                                                    <i class="ph ph-trash"></i> {{ __('frontend.delete_recording') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Transcription Status -->
                                    <div id="transcription-status" class="alert alert-info d-none mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="ph ph-spinner ph-spin me-2"></i>
                                            <span>{{ __('frontend.transcribing') }}...</span>
                                        </div>
                                    </div>

                                    <!-- Audio Queue Container -->
                                    <div id="audio-queue-container" class="mb-4 d-none">
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="ph ph-queue me-2"></i>
                                                    <strong>{{ __('frontend.audio_queue') }}</strong>
                                                    <span class="badge bg-white text-primary ms-2" id="queue-count">0</span>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" id="transcribe-all-btn" class="btn btn-sm btn-light">
                                                        <i class="ph ph-text-aa"></i> {{ __('frontend.transcribe_all') }}
                                                    </button>
                                                    <button type="button" id="clear-queue-btn" class="btn btn-sm btn-outline-light">
                                                        <i class="ph ph-trash"></i> {{ __('frontend.clear') }}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-3">
                                                <div id="audio-queue-list">
                                                    <!-- Queue items will be dynamically inserted here -->
                                                </div>
                                                
                                                <!-- Add All to Notes Button -->
                                                <div class="mt-3">
                                                    <button type="button" id="add-all-to-notes-btn" class="btn btn-success w-100 d-none">
                                                        <i class="ph ph-check-circle"></i> {{ __('frontend.add_all_to_notes') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Enhanced Dual Transcription Display -->
                                    <div id="transcription-cards" class="mb-3 d-none">
                                        <!-- Original Text Card -->
                                        <div class="transcription-card mb-3 border rounded overflow-hidden">
                                            <div class="card-header d-flex justify-content-between align-items-center bg-light p-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ph ph-microphone text-primary me-2"></i>
                                                    <strong class="mb-0">Original Speech</strong>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="copy-original-btn" title="Copy to main textarea">
                                                        <i class="ph ph-copy"></i> Copy
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-3">
                                                <div id="original-text" class="transcription-text" contenteditable="true" 
                                                     style="min-height: 60px; padding: 12px; background: #fff; border: 1px solid #e9ecef; border-radius: 6px; line-height: 1.5;"
                                                     placeholder="Your original speech will appear here..."></div>
                                            </div>
                                        </div>

                                        <!-- Medical Version Card -->
                                        <div class="transcription-card mb-3 border rounded overflow-hidden">
                                            <div class="card-header d-flex justify-content-between align-items-center bg-success-subtle p-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ph ph-hospital text-success me-2"></i>
                                                    <strong class="mb-0">Medical Enhancement</strong>
                                                    <span id="gemini-status" class="badge bg-success ms-2 d-none">AI Enhanced</span>
                                                    <span id="fallback-status" class="badge bg-warning ms-2 d-none">Fallback Mode</span>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-success" id="copy-medical-btn" title="Copy to main textarea">
                                                        <i class="ph ph-copy"></i> Copy
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success" id="use-medical-btn" title="Use this version">
                                                        <i class="ph ph-check"></i> Use This
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-3">
                                                <div id="medical-text" class="transcription-text" contenteditable="true" 
                                                     style="min-height: 60px; padding: 12px; background: #fff; border: 1px solid #e9ecef; border-radius: 6px; line-height: 1.5;"
                                                     placeholder="AI-enhanced medical terminology will appear here..."></div>
                                            </div>
                                        </div>

                                        <!-- Combined Action Buttons -->
                                        <div class="d-flex gap-2 mb-3 flex-wrap">
                                            <button type="button" class="btn btn-primary btn-sm" id="use-combined-btn">
                                                <i class="ph ph-arrows-merge"></i> Use Both Versions
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="collapse-cards-btn">
                                                <i class="ph ph-caret-up"></i> Hide Details
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Color Legend -->
                                    <div id="color-legend" class="mb-3 d-none">
                                        <div class="p-3 bg-light border rounded">
                                            <small class="text-muted fw-semibold d-block mb-2">Medical Categories:</small>
                                            <div class="d-flex flex-wrap gap-2">
                                                <span class="badge medical-category-badge" style="background: #ff6b6b; color: white;">
                                                    <i class="ph ph-warning-circle me-1"></i>Symptoms
                                                </span>
                                                <span class="badge medical-category-badge" style="background: #51cf66; color: white;">
                                                    <i class="ph ph-clock-clockwise me-1"></i>Medical History
                                                </span>
                                                <span class="badge medical-category-badge" style="background: #ffd43b; color: #000;">
                                                    <i class="ph ph-pill me-1"></i>Medications
                                                </span>
                                                <span class="badge medical-category-badge" style="background: #339af0; color: white;">
                                                    <i class="ph ph-user me-1"></i>Personal Info
                                                </span>
                                                <span class="badge medical-category-badge" style="background: #9775fa; color: white;">
                                                    <i class="ph ph-test-tube me-1"></i>Tests & Treatments
                                                </span>
                                                <span class="badge medical-category-badge" style="background: #ff922b; color: white;">
                                                    <i class="ph ph-warning me-1"></i>Allergies
                                                </span>
                                                <span class="badge medical-category-badge" style="background: #c92a2a; color: white;">
                                                    <i class="ph ph-warning-diamond me-1"></i>Urgent
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Main Textarea (Final Version) -->
                                    <div class="position-relative">
                                        <label for="appointment_extra_info" class="form-label fw-semibold">
                                            Medical History Details
                                            <small class="text-muted">(You can edit this text directly)</small>
                                        </label>
                                        <textarea class="form-control" id="appointment_extra_info" name="appointment_extra_info" 
                                                  placeholder="{{ __('frontend.enter_medical_history') }}" rows="6"
                                                  style="transition: all 0.3s ease;"></textarea>
                                        
                                        <!-- Hidden field for audio transcription IDs -->
                                        <input type="hidden" id="audio_transcription_ids" name="audio_transcription_ids" value="">
                                        
                                        <small class="text-muted mt-1 d-block">
                                            <i class="ph ph-info me-1"></i>
                                            Record your voice above or type directly. AI will enhance medical terminology automatically.
                                        </small>
                                    </div>
                                </div>

                                <!-- Upload Medical Report Section -->

                                <div class="mb-50">
                                    <h6 class ="font-size-18 fw-semibold">{{ __('frontend.upload_medical_report') }}
                                        </h6>
                                        <span> Please upload medical records, scans, any test results or medical reports</span>

                                    <!-- Uppy Dashboard Container -->
                                    <div id="uppy-dashboard"></div>
                                    <small class="text-muted d-block mt-2">
                                        Allowed files: PDF, PNG, JPG, JPEG. Maximum size: 20MB.
                                    </small>
                                    <div id="medical-report-error" class="text-danger mt-2 d-none"></div>


                                    <!-- Display the selected file name -->
                                    <div id="file-info"></div>
                                </div>

                                <!-- Available Coupons Section -->
                                {{--<div class="mb-40">
                                    <div class="d-flex justify-content-between gap-3 flex-warp mb-3">
                                        <h6 class ="font-size-18 fw-semibold m-0">Available Coupons</h6>
                                        <a data-bs-toggle="modal" data-bs-target="#all-coupons"
                                            class="font-size-14 fw-bold text-secondary">View All</a>
                                    </div>
                                    <!-- <div class="section-bg p-3 rounded">
                                                <p class="m-0">Coupon not Available</p>
                                            </div> -->
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div
                                                class="d-flex justify-content-between gap-3 p-3 section-bg rounded coupons-code active">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="flexRadioDefault" id="flexRadioDefault2" checked>
                                                    <label class="form-check-label" for="flexRadioDefault2">Get extra 10%
                                                        off on first appointment
                                                    </label>
                                                </div>
                                                <span class="fw-bold font-size-12 coupons-status">Applied</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 mt-lg-0 mt-3">
                                            <div
                                                class="d-flex justify-content-between gap-3 p-3 section-bg rounded coupons-code">
                                                <div class="form-check">
                                                    <label class="form-check-label" for="flexRadioDefault2">
                                                        Get extra 10% off on first appointment
                                                    </label>
                                                    <input class="form-check-input" type="radio"
                                                        name="flexRadioDefault" id="flexRadioDefault2">
                                                </div>
                                                <a href="#" class="font-size-12 fw-bold coupons-status">Apply</a>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}

                                <!-- Choose Payment Method Section -->
                                <div>
                                    <h6 class="mb-3">{{ __('frontend.payment_method') }}
                                    </h6>
                                    <div class="payments-container section-bg rounded mt-3">
                                        <a class="d-flex justify-content-between align-items-center gap-3 payments-show-list"
                                            href="#booking-payments-method" data-bs-toggle="collapse" aria-expanded="true">
                                            <p class="mb-0 h6" id="selected-payment-method">Select Payment Method</p>
                                            <i class="ph ph-caret-down"></i>
                                        </a>
                                    </div>
                                    <div id="booking-payments-method"
                                        class="section-bg rounded booking-payment-method collapse show mt-3">
                                        @foreach ($enabledPaymentMethods as $method)
                                            <div
                                                class="form-check payment-method-items ps-0 d-flex justify-content-between align-items-center gap-3">
                                                <label class="form-check-label d-flex gap-2 align-items-center"
                                                    for="method-{{ $method }}">
                                                    <img src="{{ asset('dummy-images/payment_icons/' . strtolower($method) . '.svg') }}"
                                                        alt="{{ $method }}" style="width: 20px; height: 20px;">
                                                        <span class="h6 fw-semibold m-0">{{ ucwords($method) }}</span>
                                                </label>
                                                <input class="form-check-input payment-radio" type="radio" name="payment_method"
                                                    value="{{ $method }}" id="method-{{ $method }}"
                                                    @if ($loop->first) checked @endif>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-lg-3 mt-lg-0 mt-5">
                    <div class="payment-container" id="payment-container">

                    </div>
                </div>

                <div class="mt-5">
                    <div id="step-content">

                        <div id="step-content-0" class="step-content">
                            @if($hasCategories ?? false)
                                {{-- Category Selection Step - Server-Side Rendered --}}
                                @php
                                    $categories = \Modules\Clinic\Models\ClinicsCategory::where('parent_id', $serviceId)
                                                 ->where('status', 1)
                                                 ->get();
                                @endphp
                                
                                <div class="category-selection-container">
                                    <h5 class="mb-4">{{ __('frontend.select_category') }}</h5>
                                    
                                    @if($categories->count() > 0)
                                        <div class="row g-3" id="categories-container">
                                            @foreach($categories as $category)
                                                <div class="col-lg-6 col-md-6">
                                                    <div class="category-card card h-100 border-0 shadow-sm" 
                                                         data-category-id="{{ $category->id }}"
                                                         data-requires-doctor="{{ $category->service_classification === 'doctor_required' ? 'true' : 'false' }}"
                                                         style="cursor: pointer; transition: all 0.3s ease;">
                                                        <div class="card-body p-4">
                                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                                <h6 class="card-title mb-0 fw-semibold">{{ $category->name }}</h6>
                                                                <span class="badge bg-primary-subtle text-primary">£{{ $category->price ?? '0' }}</span>
                                                            </div>
                                                            
                                                            @if($category->description)
                                                                <p class="card-text text-muted small mb-3">{{ $category->description }}</p>
                                                            @endif
                                                            
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small class="text-muted">
                                                                    <i class="ph ph-{{ $category->service_classification === 'doctor_required' ? 'user-md' : 'test-tube' }}"></i>
                                                                    {{ $category->service_classification === 'doctor_required' ? 'Doctor consultation' : 'No doctor required' }}
                                                                </small>
                                                                <button class="btn btn-outline-primary btn-sm select-category-btn">
                                                                    Select
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            console.log('✅ Server-side categories loaded:', {{ $categories->count() }});
                                            
                                            // Add click handlers to category cards
                                            document.querySelectorAll('.category-card').forEach(card => {
                                                card.addEventListener('click', function() {
                                                    console.log('Category card clicked:', this.dataset.categoryId);
                                                    selectCategoryServerSide(this);
                                                });
                                            });
                                        });
                                        
                                        function selectCategoryServerSide(cardElement) {
                                            // Remove previous selections
                                            document.querySelectorAll('.category-card').forEach(card => {
                                                card.classList.remove('border-primary', 'bg-primary-subtle');
                                            });
                                            
                                            // Highlight selected card
                                            cardElement.classList.add('border-primary', 'bg-primary-subtle');
                                            
                                            const categoryId = cardElement.dataset.categoryId;
                                            const requiresDoctor = cardElement.dataset.requiresDoctor === 'true';
                                            
                                            // Store selection
                                            sessionStorage.setItem('selectedCategoryId', categoryId);
                                            sessionStorage.setItem('categoryRequiresDoctor', requiresDoctor);
                                            
                                            // Update next button
                                            const nextButton = document.getElementById('nextButton');
                                            if (nextButton) {
                                                nextButton.disabled = false;
                                                nextButton.textContent = 'Continue';
                                                
                                                // Add click handler for next button
                                                nextButton.onclick = function() {
                                                    console.log('🚀 Next button clicked - category selected:', categoryId);
                                                    
                                                    // Trigger category selection event for enhanced-booking.js
                                                    const event = new CustomEvent('categorySelected', {
                                                        detail: { 
                                                            categoryId: categoryId, 
                                                            requiresDoctor: requiresDoctor 
                                                        }
                                                    });
                                                    document.dispatchEvent(event);
                                                };
                                            }
                                            
                                            console.log('✅ Category selected:', categoryId, 'Requires doctor:', requiresDoctor);
                                        }
                                        </script>
                                        
                                        <style>
                                        .category-card:hover {
                                            transform: translateY(-2px);
                                            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
                                        }
                                        
                                        .category-card.border-primary {
                                            border-width: 2px !important;
                                        }
                                        
                                        .category-selection-container {
                                            padding: 20px 0;
                                        }
                                        </style>
                                    @else
                                        <div class="alert alert-warning">
                                            <p class="mb-0">No categories available for this service.</p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                {{-- Original Clinic Selection Step --}}
                                <div class="alert alert-info">
                                    <p><strong>Info:</strong> No categories found for this service, proceeding with standard booking flow.</p>
                                </div>
                                <!-- Content for Step 1 (e.g. Select Clinic) -->
                            @endif
                        </div>

                        <div id="service-shimmer-loader" class="d-flex gap-3 flex-wrap p-4 d-none">
                             @for ($i = 0; $i < 4; $i++)
                                 @include('frontend::components.card.shimmer_service_card')
                             @endfor
                         </div>


                        <div id="step-content-1" class="step-content">

                        <!-- Content for Step 2 (e.g. Select Clinic) -->
                        </div>

                     


                        <div id="step-content-2" class="step-content">

                        </div>

                        <div id="doctor-shimmer-loader" class="d-flex gap-3 flex-wrap p-4 d-none">
                           @for ($i = 0; $i < 4; $i++)
                               @include('frontend::components.card.shimmer_doctor_card')
                           @endfor
                       </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-5 pt-2">
                <button class="btn btn-secondary" id="nextButton">{{ __('frontend.next') }}
                </button>
            </div>
        </div>
    </div>

    @if (!empty($paymentDetails))
        <!-- Payment Success Modal -->
        <div class="modal fade" id="paymentSuccessModal" tabindex="-1" aria-labelledby="paymentSuccessModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3 shadow">
                    <!-- Header with Success Icon -->
                    <div class="modal-header justify-content-center bg-light">
                        <div class="text-center">
                            <img src="/path/to/success-icon.svg" alt="Success Icon" class="img-fluid mb-2"
                                style="width: 50px;" />
                            <h5 class="modal-title text-success fw-bold" id="paymentSuccessModalLabel">
                                {{ $paymentDetails['message'] }}
                            </h5>
                        </div>
                    </div>
                    <!-- Modal Body -->
                    <div class="modal-body heading-color">
                        <!-- {{ dd($paymentDetails) }} -->
                          <h1 style="color:red">TEST123</h1>
                        @php
                            $isGP = stripos($paymentDetails['serviceName'] ?? '', 'GP') !== false;
                            $expert = trim($paymentDetails['doctorExpert'] ?? '');
                            $doctorDisplay = 'Dr. ' . ($paymentDetails['doctorName'] ?? '');
                            if (!$isGP && $expert) {
                                $doctorDisplay .= ' ' . $expert;
                            }
                            $formattedDate = isset($paymentDetails['appointmentDate'])
                                ? date('d F Y', strtotime($paymentDetails['appointmentDate']))
                                : '';
                            $formattedTime = isset($paymentDetails['appointmentTime'])
                                ? date('h:i a', strtotime($paymentDetails['appointmentTime']))
                                : '';
                        @endphp
                        <p>
                            @if($isGP)
                                Your private GP appointment with <strong>{{ $doctorDisplay }}</strong> at
                                <strong>Cosmo Doctors</strong> has been confirmed for
                                <strong>{{ $formattedDate }}</strong> at <strong>{{ $formattedTime }}</strong>.
                            @else
                                Your private Specialist appointment with <strong>{{ $doctorDisplay }}</strong> at
                                <strong>Cosmo Doctors</strong> has been confirmed for
                                <strong>{{ $formattedDate }}</strong> at <strong>{{ $formattedTime }}</strong>.
                            @endif
                        </p>
                        <div class="mt-3 pt-3 border-top text-start">
                            <p><strong>Booking ID:</strong> <span
                                    class="heading-color">#{{ $paymentDetails['bookingId'] }}</span></p>
                            <p><strong>Payment via:</strong> <span
                                    class="heading-color">{{ $paymentDetails['paymentVia'] }}</span></p>
                            <p><strong>Total Payment:</strong> <span class="heading-color">{{ $paymentDetails['currency'] }}
                                    {{ $paymentDetails['totalAmount'] }}</span></p>
                        </div>
                    </div>

                    <!-- Footer with Button -->
                    <div class="modal-footer justify-content-center bg-light">
                        <button type="button" class="btn btn-danger px-4" id="back-to-appointments">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- id="all-coupons" -->
    <div class="modal fade" id="all-coupons">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content section-bg">
                <div class="modal-body modal-body-inner">
                    <div class="close-modal-btn" data-bs-dismiss="modal">
                        <i class="ph ph-x align-middle"></i>
                    </div>
                    <div>
                        <h6 class="font-size-18 mb-3">Available coupons</h6>
                        <form>
                            <ul class="list-inline m-0 coupons-inner">
                                <li>
                                    <div class="d-flex justify-content-between gap-3 p-3 rounded coupons-code active">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                id="flexRadioDefault2" checked>
                                            <label class="form-check-label" for="flexRadioDefault2">Get extra 10% off on
                                                first appointment
                                            </label>
                                        </div>
                                        <span class="fw-bold font-size-12 coupons-status">Applied</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between gap-3 p-3 rounded coupons-code">
                                        <div class="form-check">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                                Get extra 10% off on first appointment
                                            </label>
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                id="flexRadioDefault2">
                                        </div>
                                        <a href="#" class="font-size-12 fw-bold coupons-status">Apply</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between gap-3 p-3 rounded coupons-code">
                                        <div class="form-check">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                                Get extra 10% off on first appointment
                                            </label>
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                id="flexRadioDefault2">
                                        </div>
                                        <a href="#" class="font-size-12 fw-bold coupons-status">Apply</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between gap-3 p-3 rounded coupons-code">
                                        <div class="form-check">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                                Get extra 10% off on first appointment
                                            </label>
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                id="flexRadioDefault2">
                                        </div>
                                        <a href="#" class="font-size-12 fw-bold coupons-status">Apply</a>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between gap-3 p-3 rounded coupons-code">
                                        <div class="form-check">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                                Get extra 10% off on first appointment
                                            </label>
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                id="flexRadioDefault2">
                                        </div>
                                        <a href="#" class="font-size-12 fw-bold coupons-status">Apply</a>
                                    </div>
                                </li>
                            </ul>
                            <div class="d-flex justify-content-end mt-5">
                                <button type="button" class="btn btn-secondary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- modal end -->

    <!-- Add Patient Modal -->
    <div class="modal fade add-patient-modal" id="addPatientModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('customer.add_new_patient') }}</h5>
                    <div class="close-modal-btn" data-bs-dismiss="modal"><i class="ph ph-x align-middle"></i></div>
                </div>
                <div class="modal-body">
                    <form id="addPatientForm">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="form-group mb-3">

                                    <div class="col-md-12">
                                        <div class="d-flex align-items-center justify-content-center p-3">
                                            <img id="miniLogoViewer"  src={{ $data['profile_image'] ?? asset('img/avatar/avatar.webp')  }} class="img-fluid avatar-130 rounded-pill" alt="mini_logo" />
                                        </div>

                                        <div class="d-flex align-items-center gap-3 justify-content-center mt-5">
                                            <input type="file" class="form-control d-none" id="mini_logo" name="profile_image" accept=".jpeg, .jpg, .png, .gif">
                                            <button type="button" class="btn btn-info" onclick="document.getElementById('mini_logo').click();">{{ __('messages.upload') }}</button>
                                            <button type="button" class="btn btn-danger" id="removeMiniLogoButton">{{ __('messages.remove') }}</button>
                                        </div>
                                        <span class="text-danger" id="error_mini_logo"></span>
                                    </div>

                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="row g-3">
                                    <!-- Name Fields -->

                                    <div class="col-xl-6 col-lg-12">
                                        <label class="form-label">{{ __('clinic.lbl_first_name') }} <span class="text-danger">*</span></label>
                                        <div class="input-group custom-input-group mb-1">
                                            <input type="text" class="form-control" name="first_name" placeholder="First Name" required>
                                            <span class="input-group-text"><i class="ph ph-user"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-12">
                                        <label class="form-label">{{ __('clinic.lbl_last_name') }} <span class="text-danger">*</span></label>
                                        <div class="input-group custom-input-group mb-1">
                                            <input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
                                            <span class="input-group-text"><i class="ph ph-user"></i></span>
                                        </div>
                                    </div>

                                    <!-- Contact Information -->

                                    <div class="col-xl-6 col-lg-12">
                                        <label class="form-label">{{ __('clinic.lbl_phone_number') }} <span class="text-danger">*</span></label>
                                        <div class="input-group custom-input-group mb-1">
                                            <input type="tel" class="form-control" name="contactNumber_display" placeholder="Phone Number" id="mobile" required>
                                            <input type="hidden" name="contactNumber" id="fullPhoneNumber"> <!-- This will store full number -->
                                            <span class="input-group-text"><i class="ph ph-phone"></i></span>
                                        </div>
                                    </div>

                                    <!-- Date of Birth -->
                                    <div class="col-xl-6 col-lg-12">
                                        <label class="form-label">{{ __('clinic.date_of_birth') }} <span class="text-danger">*</span></label>
                                        <div class="input-group custom-input-group mb-1">
                                            <input type="date" class="form-control" name="dob" id="dob" placeholder="DOB" required>
                                            <span class="input-group-text"><i class="ph ph-cake"></i></span>
                                        </div>
                                    </div>

                                    <!-- Gender Selection -->
                                    <div class="col-lg-12">
                                        <label class="form-label">{{ __('clinic.lbl_gender') }} <span class="text-danger">*</span></label>
                                        <div class=" input-group d-flex flex-wrap align-items-center gap-2">
                                            <div class="form-check custom-radio-btn">
                                                <input class="form-check-input" type="radio" name="gender" value="male" id="genderMale" required>
                                                <label class="form-check-label rounded-pill" for="genderMale">{{ __('messages.male') }}</label>
                                            </div>
                                            <div class="form-check custom-radio-btn">
                                                <input class="form-check-input" type="radio" name="gender" value="female" id="genderFemale" required>
                                                <label class="form-check-label rounded-pill" for="genderFemale">{{ __('messages.female') }}</label>
                                            </div>
                                            <div class="form-check custom-radio-btn">
                                                <input class="form-check-input" type="radio" name="gender" value="other" id="genderOther" required>
                                                <label class="form-check-label rounded-pill" for="genderOther">{{ __('messages.lbl_other') }}</label>
                                            </div>
                                            <div class="form-check custom-radio-btn">
                                                <input class="form-check-input" type="radio" name="gender" value="prefer_not_to_say" id="genderPreferNotToSay" required>
                                                <label class="form-check-label rounded-pill" for="genderPreferNotToSay">{{ __('messages.prefer_not_to_say') }}</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Relationship Selection -->
                                    <!-- Replace the relationship select field with radio buttons -->
                                    <div class="col-lg-12">
                                        <label class="form-label mb-3">{{ __('clinic.relation') }} <span class="text-danger">*</span></label>
                                        <div class="input-group d-flex flex-wrap align-items-center gap-2">
                                            <div class="form-check custom-radio-btn">
                                                <input class="form-check-input" type="radio" name="relation"
                                                    id="relationParent" value="Parents" required>
                                                <label class="form-check-label rounded-pill" for="relationParent">
                                                    {{ __('clinic.parents') }}
                                                </label>
                                            </div>
                                            <div class="form-check custom-radio-btn">
                                                <input class="form-check-input" type="radio" name="relation"
                                                    id="relationSibling" value="Siblings" required>
                                                <label class="form-check-label rounded-pill" for="relationSibling">
                                                    {{ __('clinic.sibling') }}
                                                </label>
                                            </div>
                                            <div class="form-check custom-radio-btn">
                                                <input class="form-check-input" type="radio" name="relation"
                                                    id="relationSpouse" value="Spouse" required>
                                                <label class="form-check-label rounded-pill" for="relationSpouse">
                                                    {{ __('clinic.spouse') }}
                                                </label>
                                            </div>
                                            <div class="form-check custom-radio-btn">
                                                <input class="form-check-input" type="radio" name="relation"
                                                    id="relationOther" value="Other" required>
                                                <label class="form-check-label rounded-pill" for="relationOther">
                                                    {{ __('messages.lbl_other') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end gap-2 flex-wrap mt-5">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                            <button type="button" class="btn btn-primary" id="savePatient">{{ __('messages.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script type="text/javascript" defer>
        // Pass PHP variables to JavaScript
        window.bookingConfig = {
            currentStep: {{ $currentStep ?? 0 }},
            hasCategories: {{ isset($hasCategories) && $hasCategories ? 'true' : 'false' }},
            selectedCategoryId: {{ $categoryId ?? 'null' }},
            serviceId: {{ $serviceId ?? 'null' }}
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Set protection flag for services with categories
            @if($hasCategories ?? false)
                window.skipOriginalStep0 = true;
                console.log('🛡️ PROTECTION ACTIVATED: skipOriginalStep0 = true for service with categories');
            @endif

            var input = document.querySelector("#mobile");
            var hiddenInput = document.querySelector("#fullPhoneNumber");

            // Debug logging
            console.log('Enhanced Booking Debug:', {
                currentStep: currentStep,
                hasCategories: hasCategories,
                serviceId: {{ $serviceId ?? 'null' }},
                categorySelectionExists: !!document.querySelector('.category-selection-container'),
                stepContent0Exists: !!document.getElementById('step-content-0'),
                stepContent0Visible: document.getElementById('step-content-0') ? !document.getElementById('step-content-0').classList.contains('d-none') : false
            });

            // Only initialize if elements exist
            if (input && hiddenInput) {
                initializePhoneInput(input, hiddenInput);
            }

            // Ensure step 0 is visible for categories
            if (hasCategories && currentStep === 0) {
                const stepContent0 = document.getElementById('step-content-0');
                if (stepContent0) {
                    stepContent0.classList.remove('d-none');
                    console.log('Ensured step-content-0 is visible for categories');
                }
            }

            // Initialize booking flow
            initializeBookingFlow();
            
            // Listen for category selection
            document.addEventListener('categorySelected', function(event) {
                selectedCategoryId = event.detail.categoryId;
                categoryRequiresDoctor = event.detail.requiresDoctor;
                
                // Update step navigation based on category requirements
                updateStepNavigation();
            });
        });

        function initializePhoneInput(input, hiddenInput) {
            var iti = window.intlTelInput(input, {
                initialCountry: "gb",
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
            });

            function updateFullPhoneNumber() {
                var fullNumber = iti.getNumber();
                var localNumber = input.value;

                // Save full number in hidden input
                hiddenInput.value = fullNumber;

                // Keep displaying only local number
                input.value = localNumber;
            }

            input.addEventListener("countrychange", updateFullPhoneNumber);
            input.addEventListener("blur", updateFullPhoneNumber);
        }

        function initializeBookingFlow() {
            // Show appropriate step content
            showStep(currentStep);
            
            // Update next button
            updateNextButton();
            
            // Handle step navigation
            setupStepNavigation();
        }

        function showStep(stepIndex) {
            // Hide all step contents
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.add('d-none');
            });
            
            // Show current step content
            const currentStepContent = document.getElementById(`step-content-${stepIndex}`);
            if (currentStepContent) {
                currentStepContent.classList.remove('d-none');
            }
            
            // Update step indicators
            updateStepIndicators(stepIndex);
        }

        function updateStepIndicators(activeStep) {
            document.querySelectorAll('.appointments-steps-item').forEach((item, index) => {
                item.classList.remove('active', 'complete');
                
                if (index < activeStep) {
                    item.classList.add('complete');
                } else if (index === activeStep) {
                    item.classList.add('active');
                }
            });
        }

        function updateStepNavigation() {
            // If category doesn't require doctor, skip doctor step
            if (!categoryRequiresDoctor && hasCategories) {
                // Adjust step indices to skip doctor step
                console.log('Category does not require doctor - will skip doctor step');
            }
        }

        function setupStepNavigation() {
            // Next button handler
            const nextButton = document.getElementById('nextButton');
            if (nextButton) {
                nextButton.addEventListener('click', function() {
                    handleNextStep();
                });
            }
            
            // Previous step button handler
            const prevButton = document.getElementById('prev-step-btn');
            if (prevButton) {
                prevButton.addEventListener('click', function() {
                    handlePreviousStep();
                });
            }
            
            // Tab click handlers
            document.querySelectorAll('.tab-index').forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetStep = parseInt(this.dataset.index);
                    if (targetStep <= currentStep) {
                        goToStep(targetStep);
                    }
                });
            });
        }

        function handleNextStep() {
            if (validateCurrentStep()) {
                const nextStep = getNextStep();
                if (nextStep !== null) {
                    goToStep(nextStep);
                }
            }
        }

        function handlePreviousStep() {
            const prevStep = getPreviousStep();
            if (prevStep !== null) {
                goToStep(prevStep);
            }
        }

        // function validateCurrentStep() {
        //     if (hasCategories && currentStep === 0 && !selectedCategoryId) {
        //         alert('Please select a category to continue.');
        //         return false;
        //     }
            
        //     // Add other step validations here
        //     return true;
        // }

        function getNextStep() {
            let nextStep = currentStep + 1;
            
            // Skip doctor step if category doesn't require doctor
            if (hasCategories && !categoryRequiresDoctor && nextStep === 2) {
                nextStep = 3; // Skip to date/time/payment
            }
            
            // Check if next step exists
            const maxSteps = document.querySelectorAll('.appointments-steps-item').length;
            if (nextStep >= maxSteps) {
                return null;
            }
            
            return nextStep;
        }

        function getPreviousStep() {
            let prevStep = currentStep - 1;
            
            // Skip doctor step if category doesn't require doctor (going backwards)
            if (hasCategories && !categoryRequiresDoctor && prevStep === 2) {
                prevStep = 1; // Skip back to clinic selection
            }
            
            if (prevStep < 0) {
                return null;
            }
            
            return prevStep;
        }

        function goToStep(stepIndex) {
            currentStep = stepIndex;
            showStep(stepIndex);
            updateNextButton();
            
            // Update URL if needed
            updateURL();
        }

        function updateNextButton() {
            const nextButton = document.getElementById('nextButton');
            if (!nextButton) return;
            
            const maxSteps = document.querySelectorAll('.appointments-steps-item').length;
            
            if (currentStep >= maxSteps - 1) {
                nextButton.textContent = 'Book Appointment';
                nextButton.classList.remove('btn-secondary');
                nextButton.classList.add('btn-primary');
            } else {
                nextButton.textContent = 'Next';
                nextButton.classList.remove('btn-primary');
                nextButton.classList.add('btn-secondary');
            }
            
            // Disable next button if current step is not valid
            if (hasCategories && currentStep === 0 && !selectedCategoryId) {
                nextButton.disabled = true;
            } else {
                nextButton.disabled = false;
            }
        }

        function updateURL() {
            // Update browser URL to reflect current step and selections
            try {
                const url = new URL(window.location);
                
                if (selectedCategoryId) {
                    url.searchParams.set('category_id', selectedCategoryId);
                }
                
                // Update without page reload
                window.history.replaceState({}, '', url);
            } catch (e) {
                console.log('URL update failed:', e);
            }
        }

        // Enhanced category selection handling
        function selectCategory(categoryId, requiresDoctor) {
            selectedCategoryId = categoryId;
            categoryRequiresDoctor = requiresDoctor;
            
            // Store in session storage
            sessionStorage.setItem('selectedCategoryId', categoryId);
            sessionStorage.setItem('categoryRequiresDoctor', requiresDoctor);
            
            // Update UI
            updateNextButton();
            
            // Trigger custom event
            const event = new CustomEvent('categorySelected', {
                detail: { categoryId, requiresDoctor }
            });
            document.dispatchEvent(event);
        }

        // Initialize date picker if element exists
        const dobElement = document.getElementById('dob');
        if (dobElement && typeof flatpickr !== 'undefined') {
            flatpickr('#dob', {
                dateFormat: 'Y-m-d',
                maxDate: 'today'
            });
        }

        // Handle existing tab navigation (legacy code)
        document.querySelectorAll('.tab-index').forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                const index = parseInt(this.getAttribute('data-index'));
                const steps = document.querySelectorAll('.appointments-steps-item');

                // Update current step and modify classes
                steps.forEach((step, idx) => {
                    const stepText = step.querySelector('.step-text');
                    if (stepText) {
                        if (idx === index) {
                            step.classList.add('active');
                            step.classList.remove('complete');
                            step.setAttribute('data-check', 'false');
                            stepText.className = 'step-text';
                        } else if (idx < index) {
                            step.classList.add('complete');
                            step.classList.remove('active');
                            step.setAttribute('data-check', 'true');
                            stepText.className = 'step-text';
                        } else {
                            step.classList.remove('complete', 'active');
                            step.setAttribute('data-check', 'false');
                            stepText.className = 'step-text';
                        }
                    }
                });
            });
        });

        // Initialize state (legacy code)
        const initialState = {
            selectedService: {{ $serviceId ?? 'null' }},
            selectedServiceName: @json($selectedService->name ?? null),
            selectedClinic: {{ $clinicId ?? 'null' }},
            selectedDoctor: {{ $doctorId ?? 'null' }},
            selectedClinicName: @json($selectedClinic->name ?? null),
            selectedDate: null,
            selectedTime: null,
            selectedDoctorName: @json($selectedDoctor ? $selectedDoctor->user->first_name . ' ' . $selectedDoctor->user->last_name : null),
            uploadedFiles: [],
            selectedPaymentMethod: @json($selectedService && $selectedService->is_enable_advance_payment == 1 ? 'Wallet' : 'cash'),
            status: "pending",
            user_id: @json(optional(auth()->user())->id) ?? '',
            previousUrl: @json($previousUrl ?? ''),
            totalAmount: 0,
            payment: {
                totalAmount: @json($totalAmount ?? 0),
                advance_payment_status: @json($advancePaymentStatus ?? 0),
                remaining_payment_amount: @json($remainingPaymentAmount ?? 0),
                payment_status: @json($paymentStatus ?? 1),
                transaction_type: @json($transactionType ?? 'cash'),
                advance_payment_amount: @json($advancePaymentAmount ?? 0),
                is_enable_advance_payment: 0,
            }
        };

        const checkWalletBalanceUrl = "{{ route('check.wallet.balance') }}";
        const initialStep = {{ $currentStep ?? 0 }};
        const tabs = @json($tabs);
        const routes = {
            clinicIndex: '{{ route('clinic.index_data') }}',
            doctorIndex: '{{ route('doctor.index_data') }}',
            paymentData: '{{ route('payment.data') }}',
            slotTimeList: '{{ route('slot_time_list') }}',
            saveAppointment: '{{ route('saveAppointment') }}',
            appointmentList: '{{ route('appointment-list') }}',
            appointmentDetails: '{{ route('appointment-details', '') }}',
            otherPatient:'{{ route("other-patients.store") }}',
            otherPatientList:'{{ route("other-patients.list") }}?patient_id={{ auth()->id() }}'
        };
        
        const paymentDetails = @json($paymentDetails ?? '');
        const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '';
        const paymentDetail = "{{ __('frontend.payment_details') }}";
        const price = "{{ __('frontend.price') }}";
        const Discount = "{{ __('frontend.discount') }}";
        const Subtotal = "{{ __('frontend.subtotal') }}";
        const Tax = "{{ __('frontend.tax') }}";
        const InclusiveTax = "{{ __('service.inclusive_tax') }}";
        const Total = "{{ __('frontend.total') }}";
        const ChooseClinic = "{{ __('frontend.choose_clinic') }}";
        const ChooseDoctor = "{{ __('frontend.choose_doctors') }}";
        const AdvancePayableAmount = "{{ __('frontend.advance_payable_amount') }}";
        const Submit = "{{ __('frontend.submit') }}";
        const clinicTitle = "{{ __('frontend.clinic') }}";
        const doctorTitle = "{{ __('frontend.doctor') }}";
        const withInclusivetax = "{{ __('messages.lbl_with_inclusive_tax') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Consolidated DOMContentLoaded event listener
        document.addEventListener('DOMContentLoaded', () => {
            // Payment method functionality
            const paymentRadios = document.querySelectorAll('.payment-radio');
            const selectedPaymentMethod = document.getElementById('selected-payment-method');

            if (selectedPaymentMethod) {
                const initialChecked = document.querySelector('.payment-radio:checked');
                if (initialChecked) {
                    selectedPaymentMethod.textContent = initialChecked.value.charAt(0).toUpperCase() + initialChecked.value.slice(1);
                }

                paymentRadios.forEach((radio) => {
                    radio.addEventListener('change', (event) => {
                        selectedPaymentMethod.textContent = event.target.value.charAt(0).toUpperCase() + event.target.value.slice(1);
                    });
                });
            }

            // Date picker functionality
            const appointmentDateInput = document.getElementById('appointment_date');
            const calendarIcon = document.getElementById('calendar-icon');

            if (appointmentDateInput && typeof flatpickr !== 'undefined') {
                const picker = flatpickr(appointmentDateInput, {
                    dateFormat: "Y-m-d",
                    placeholder: "Select appointment date",
                    clickOpens: false,
                    minDate: "today",
                    defaultDate: "today"
                });

                appointmentDateInput.addEventListener('click', function() {
                    picker.open();
                });

                if (calendarIcon) {
                    calendarIcon.addEventListener('click', function() {
                        picker.open();
                    });
                }
            }

            // File input functionality for mini logo
            const minilogoInput = document.getElementById('mini_logo');
            const miniLogoViewer = document.getElementById('miniLogoViewer');

            if (minilogoInput && miniLogoViewer) {
                minilogoInput.addEventListener('change', function() {
                    const minilogofile = this.files[0];
                    if (minilogofile) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            miniLogoViewer.src = e.target.result;
                        }
                        reader.readAsDataURL(minilogofile);
                    }
                });
            }
        });

    </script>
    <script>
        window.patientFormMessages = {
            first_name_required: "{{ __('messages.first_name_required') }}",
            last_name_required: "{{ __('messages.last_name_required') }}",
            contact_number_required: "{{ __('messages.contact_number_required') }}",
            dob_required: "{{ __('messages.dob_required') }}",
            dob_past: "{{ __('messages.dob_past') }}",
            gender_required: "{{ __('messages.gender_required') }}",
            relation_required: "{{ __('messages.relation_required') }}"
        };
    </script>
    {{-- Conditional script loading based on service type --}}
    @if($hasCategories)
        {{-- Enhanced Flow: Load enhanced-booking.js as coordinator --}}
        <script type="text/javascript" src="{{ asset('js/appointment.min.js') }}" defer></script>
        <script type="text/javascript" src="{{ asset('js/enhanced-booking.js') }}" defer></script>
    @else
        {{-- Original Flow: Load only appointment.js --}}
        <script type="text/javascript" src="{{ asset('js/appointment.min.js') }}" defer></script>
    @endif
    <script type="text/javascript" src="{{ asset('js/enhanced-medical-transcription.js') }}" defer></script>
    
    {{-- Initialize Medical Transcription as Global --}}
    <script>
        // Initialize the medical transcription component and make it globally accessible
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof EnhancedMedicalTranscription !== 'undefined') {
                window.medicalTranscription = new EnhancedMedicalTranscription();
                console.log('✅ Medical Transcription initialized globally');
            }
        });
    </script>
    
    {{-- Draft Appointment Auto-Save --}}
    <script type="text/javascript" src="{{ asset('js/draft-appointment.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/draft-appointment-integration.js') }}" defer></script>

    <!-- Speech-to-Text JavaScript -->
    <script>
    (function() {
        let mediaRecorder;
        let audioChunks = [];
        let recordingStartTime;
        let timerInterval;
        let audioBlob;

        const recordBtn = document.getElementById('record-audio-btn');
        const stopBtn = document.getElementById('stop-recording-btn');
        const cancelBtn = document.getElementById('cancel-recording-btn');
        const transcribeBtn = document.getElementById('transcribe-btn');
        const deleteRecordingBtn = document.getElementById('delete-recording-btn');
        const timerDisplay = document.getElementById('recording-timer');
        const audioPlayerContainer = document.getElementById('audio-player-container');
        const audioPlayer = document.getElementById('audio-player');
        const transcriptionStatus = document.getElementById('transcription-status');
        const textarea = document.getElementById('appointment_extra_info');

        // Start Recording
        recordBtn.addEventListener('click', async function() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                
                mediaRecorder.ondataavailable = (event) => {
                    audioChunks.push(event.data);
                };
                
                mediaRecorder.onstop = () => {
                    audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    audioPlayer.src = audioUrl;
                    audioPlayerContainer.classList.remove('d-none');
                    
                    // Stop all tracks
                    stream.getTracks().forEach(track => track.stop());
                };
                
                mediaRecorder.start();
                recordingStartTime = Date.now();
                
                // Update UI
                recordBtn.classList.add('d-none');
                stopBtn.classList.remove('d-none');
                cancelBtn.classList.remove('d-none');
                timerDisplay.classList.remove('d-none');
                audioPlayerContainer.classList.add('d-none');
                
                // Start timer
                timerInterval = setInterval(updateTimer, 1000);
                
            } catch (error) {
                console.error('Error accessing microphone:', error);
                alert('{{ __("frontend.microphone_permission_denied") }}');
            }
        });

        // Stop Recording
        stopBtn.addEventListener('click', function() {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
                clearInterval(timerInterval);
                
                // Update UI
                recordBtn.classList.remove('d-none');
                stopBtn.classList.add('d-none');
                cancelBtn.classList.add('d-none');
                timerDisplay.classList.add('d-none');
                
                // Check duration
                const duration = (Date.now() - recordingStartTime) / 1000;
                if (duration < 1) {
                    alert('{{ __("frontend.recording_too_short") }}');
                    audioPlayerContainer.classList.add('d-none');
                } else if (duration > 300) {
                    alert('{{ __("frontend.recording_too_long") }}');
                    audioPlayerContainer.classList.add('d-none');
                }
            }
        });

        // Cancel Recording
        cancelBtn.addEventListener('click', function() {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
                clearInterval(timerInterval);
                audioChunks = [];
                
                // Update UI
                recordBtn.classList.remove('d-none');
                stopBtn.classList.add('d-none');
                cancelBtn.classList.add('d-none');
                timerDisplay.classList.add('d-none');
                timerDisplay.textContent = '00:00';
                audioPlayerContainer.classList.add('d-none');
            }
        });

        // Delete Recording
        deleteRecordingBtn.addEventListener('click', function() {
            audioBlob = null;
            audioPlayer.src = '';
            audioPlayerContainer.classList.add('d-none');
            timerDisplay.textContent = '00:00';
        });

        // Transcribe Audio
        transcribeBtn.addEventListener('click', async function() {
            if (!audioBlob) {
                alert('No recording found');
                return;
            }

            const formData = new FormData();
            formData.append('audio', audioBlob, 'recording.wav');
            formData.append('_token', '{{ csrf_token() }}');

            // Show loading
            transcribeBtn.disabled = true;
            transcribeBtn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> {{ __("frontend.transcribing") }}';
            transcriptionStatus.classList.remove('d-none');

            try {
                const response = await fetch('{{ route("transcribe-audio") }}', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // ⭐ POPULATE TEXTAREA - MAIN FEATURE
                    const existingText = textarea.value.trim();
                    
                    if (existingText) {
                        // Ask user: append or replace
                        if (confirm('{{ __("frontend.append_or_replace") }}\n\n{{ __("frontend.append") }} = OK\n{{ __("frontend.replace") }} = Cancel')) {
                            textarea.value = existingText + '\n\n' + data.transcription;
                        } else {
                            textarea.value = data.transcription;
                        }
                    } else {
                        textarea.value = data.transcription;
                    }

                    // Visual feedback
                    textarea.classList.add('highlight-success');
                    setTimeout(() => textarea.classList.remove('highlight-success'), 2000);
                    
                    // Scroll to show content
                    textarea.focus();
                    textarea.scrollTop = textarea.scrollHeight;

                    // Success message
                    transcriptionStatus.classList.remove('alert-info');
                    transcriptionStatus.classList.add('alert-success');
                    transcriptionStatus.innerHTML = '<i class="ph ph-check-circle"></i> {{ __("frontend.transcription_complete") }}';
                    
                    setTimeout(() => {
                        transcriptionStatus.classList.add('d-none');
                        transcriptionStatus.classList.remove('alert-success');
                        transcriptionStatus.classList.add('alert-info');
                    }, 3000);

                } else {
                    throw new Error(data.message || 'Transcription failed');
                }

            } catch (error) {
                console.error('Transcription error:', error);
                transcriptionStatus.classList.remove('alert-info');
                transcriptionStatus.classList.add('alert-danger');
                transcriptionStatus.innerHTML = '<i class="ph ph-x-circle"></i> {{ __("frontend.transcription_failed") }}';
                
                setTimeout(() => {
                    transcriptionStatus.classList.add('d-none');
                    transcriptionStatus.classList.remove('alert-danger');
                    transcriptionStatus.classList.add('alert-info');
                }, 5000);

            } finally {
                transcribeBtn.disabled = false;
                transcribeBtn.innerHTML = '<i class="ph ph-text-aa"></i> {{ __("frontend.transcribe_audio") }}';
            }
        });

        // Update Timer Display
        function updateTimer() {
            const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
            const seconds = (elapsed % 60).toString().padStart(2, '0');
            timerDisplay.textContent = `${minutes}:${seconds}`;
            
            // Auto-stop at 5 minutes
            if (elapsed >= 300) {
                stopBtn.click();
            }
        }
    })();
    </script>

    <style>
    /* Highlight textarea when transcription populates */
    .highlight-success {
        border: 2px solid #28a745 !important;
        box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
        transition: all 0.3s ease;
    }

    #appointment_extra_info {
        transition: border 0.3s ease, box-shadow 0.3s ease;
    }

    #recording-timer {
        font-family: monospace;
        font-size: 1.1rem;
        font-weight: bold;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    #stop-recording-btn:not(.d-none) {
        animation: pulse 1.5s infinite;
    }

    /* Audio Queue Styles */
    .audio-queue-item {
        transition: all 0.3s ease;
    }

    .audio-queue-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .medical-category-symptoms { background-color: rgba(255, 107, 107, 0.2); }
    .medical-category-medical_history { background-color: rgba(81, 207, 102, 0.2); }
    .medical-category-medications { background-color: rgba(255, 212, 59, 0.2); }
    .medical-category-personal_info { background-color: rgba(51, 154, 240, 0.2); }
    .medical-category-tests_treatments { background-color: rgba(151, 117, 250, 0.2); }
    .medical-category-allergies { background-color: rgba(255, 146, 43, 0.2); }
    .medical-category-urgent { background-color: rgba(201, 42, 42, 0.2); }

    .medical-term-tooltip {
        cursor: help;
        border-bottom: 2px dotted currentColor;
        padding: 2px 4px;
        border-radius: 3px;
    }

    .fade-in {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes highlightPulse {
        0%, 100% { background-color: transparent; }
        50% { background-color: rgba(81, 207, 102, 0.2); }
    }
    </style>

@endpush
