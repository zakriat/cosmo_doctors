<div class="banner-section-wrapper">
    <div class="main-banner">
        @foreach ($sliders as $slider)
            <div class="slick-item">
                <img class="main-banner-image" src="{{ $slider->getFirstMediaUrl('file_url') }}" alt="kivicare">
            </div>
        @endforeach
    </div>
    @if ($sectionData && isset($sectionData['section_1']) && $sectionData['section_1']['section_1'] == 1)
        <div class="banner-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="banner-content-wrapper">
                            <h2 class="banner-content-title mb-5">
                                {{ $sectionData['section_1']['title'] }}
                            </h2>
                            <!-- <h1 class="banner-content-title text-center mb-5">
                            Your Health, Our Priority - <span class="fw-normal">Book An Appointment Today!</span>
                        </h1> -->

                            @if (!empty($sectionData['section_1']['enable_search']) && $sectionData['section_1']['enable_search'] === 'on')
                                <div class="banner-content-search-box">
                                    <form id="search-form" method="GET" action="{{ url('/search') }}">
                                        <div class="d-flex align-items-center p-2 rounded section-bg">
                                            <div class="icon ps-2">
                                                <i class="ph ph-magnifying-glass align-middle"></i>
                                            </div>
                                            <input type="text" id="search-query" name="query"
                                                class="form-control px-2 py-2 h-auto" placeholder="{{ __('messages.search') }}..."
                                                value="{{ request('query') }}">
                                            <button type="submit" class="btn btn-primary"
                                                id="search-button">{{ __('messages.search') }}</button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @php
                                $instantLinkMap = [
                                    'clinics' => ['route' => route('clinics'), 'icon' => 'ph-hospital'],
                                    'services' => ['route' => route('services'), 'icon' => 'ph-first-aid-kit'],
                                    'doctors' => ['route' => route('doctors'), 'icon' => 'ph-stethoscope'],
                                ];
                            @endphp
                            @if (!empty($sectionData['section_1']['instant_link']))
                                <div class="mt-md-5 mt-4">
                                    <div class="d-flex flex-wrap align-items-center gap-3 instant-link">
                                        <h6 class="mb-0 font-size-14 instant-link-title">
                                         {{ __('frontend.instant_link') }}
                                        </h6>
                                        <ul
                                            class="list-inline m-0 p-0 d-flex flex-wrap align-items-center row-gap-md-3 row-gap-2 column-gap-md-3 column-gap-2">
                                            @foreach ($sectionData['section_1']['instant_link'] as $instant_link)
                                                @php
                                                    $linkData = $instantLinkMap[$instant_link] ?? [
                                                        'route' => '#',
                                                        'icon' => 'ph-question',
                                                    ];
                                                @endphp
                                                <li>
                                                    <a href="{{ $linkData['route'] }}"
                                                        class="btn btn-secondary rounded-pill">
                                                        <span class="d-flex align-items-center gap-2">
                                                            <i
                                                                class="ph {{ $linkData['icon'] }}"></i>{{ __('frontend.' . strtolower($instant_link)) }}
                                                        </span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@if (
    $sectionData &&
        isset($sectionData['section_1']) &&
        $sectionData['section_1']['section_1'] == 1 &&
        $sectionData['section_1']['enable_quick_booking'] == 'on')
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="book-appointment-box section-bg rounded px-5 py-4">
                    <h5>{{ __('frontend.book_appointment') }}</h5>

                        <div class="row align-items-center book-appointment-content mt-4">
                            <div class="col-xl-5">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <select id="serviceDropdown" class="form-select select2 bg-body border-0">
                                            <option value="" disabled selected>{{ __('frontend.select_service') }}</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-md-0 mt-4">
                                        <select id="clinicDropdown" class="form-select select2 bg-body border-0" disabled>
                                            <option value="" disabled selected>{{ __('frontend.select_clinic') }}</option>
                                            @foreach ($clinics as $clinic)
                                                <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-7 mt-xl-0 mt-4">
                                <div class="row align-items-center">
                                    <div class="col-md-5 date-filter-input">
                                        <div class="input-group custom-input-group">
                                            <input type="text" id="appointment_date" class="form-control date-picker"
                                            name="appointment_date" placeholder="{{ __('frontend.choose_your_date') }}">
                                            <span class="input-group-text" id="calendar-icon">
                                                <i class="ph ph-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-md-0 mt-4">
                                        <div class="slot-selection input-group">
                                            <select id="time_slots" class="form-select select2 bg-body border-0">
                                                <option value="" disabled selected>{{ __('frontend.select_time') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-md-0 mt-4">
                                        <!-- Book Now Button -->
                                        <button class="btn btn-secondary w-100" id="bookNowButton"
                                            data-bs-toggle="modal" disabled>{{ __('frontend.book_now') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Payment Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="">
                <div class="float-end">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <h5 class="modal-title" id="paymentModalLabel">{{ __('frontend.payment_method') }}</h5>
                <div class="card mb-3">
                    <div class="card-body">
                        @foreach ($paymentMethods as $method)
                            <div class="form-check d-flex align-items-center justify-content-between">
                                <label class="form-check-label" for="method-{{ $method }}">
                                    {{ $method }}
                                </label>
                                <input class="form-check-input" type="radio" name="payment_method"
                                    value="{{ $method }}" id="method-{{ $method }}"
                                    @if ($method === 'cash') checked @endif>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="text-end">
                    <button class="btn btn-secondary px-4" id="submitAppointment"
                        data-bs-dismiss="modal">{{ __('frontend.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('after-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content')
            const user_id = {{ auth()->user()->id ?? 'null' }};
            const user_type = "{{ auth()->user()->user_type ?? '' }}";
            // Initialize Select2
            $('#serviceDropdown').select2({
                width: '100%',
                allowClear: true,
                placeholder: '@lang('frontend.select_service')'
            });

            $('#clinicDropdown').select2({
                width: '100%',
                allowClear: true,
                placeholder: '@lang('frontend.select_clinic')'
            });

            $('#time_slots').select2({
                width: '100%',
                allowClear: true,
                 placeholder: '@lang('frontend.select_time')'
            });

            const serviceDropdown = $('#serviceDropdown');
            const clinicDropdown = $('#clinicDropdown');
            const timeSlotDropdown = $('#time_slots');
            let doctor_id = null;
            const calendarIcon = document.getElementById('calendar-icon');
            const datePicker = document.getElementById('appointment_date');

            // Initialize Flatpickr
            const flatpickrInstance = flatpickr(datePicker, {
                dateFormat: "Y-m-d",
                minDate: "today",
                altInput: true,
                altFormat: "F j, Y",
                allowInput: true,
                onChange: function(selectedDates, dateStr, instance) {
                    console.log("Selected Date: ", dateStr);
                    fetchAvailableSlots();
                },
            });

            calendarIcon.addEventListener('click', function() {
                flatpickrInstance.open();
            });

            datePicker.addEventListener('click', function() {
                flatpickrInstance.open();
            });

            const bookNowButton = document.getElementById('bookNowButton');
            const paymentMethodForm = document.getElementById('payment-method-form');
            const searchInput = document.getElementById('search-query');
            const searchButton = document.getElementById('search-button');
            const form = document.getElementById('search-form');

            const apiEndpoint = "{{ route('getClinicsByService') }}";
            setupServiceClinicDropdown("serviceDropdown", "clinicDropdown", apiEndpoint);

            // Search functionality
            searchButton?.addEventListener('click', function(e) {
                e.preventDefault();
                const query = searchInput.value.trim();
                if (query) form.submit();
            });

            searchInput?.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = searchInput.value.trim();
                    if (query) form.submit();
                }
            });

            async function fetchAvailableSlots() {
                const serviceId = serviceDropdown.val();
                const clinicId = clinicDropdown.val();
                const appointmentDate = datePicker.value;

                if (serviceId && clinicId && appointmentDate) {
                    try {
                        const response = await fetch("{{ route('random_slot') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                service_id: serviceId,
                                clinic_id: clinicId,
                                appointment_date: appointmentDate
                            })
                        });

                        if (response.ok) {
                            const data = await response.json();

                            // Clear existing options
                            timeSlotDropdown.empty();
                            timeSlotDropdown.append(new Option('Select Time', ''));

                            if (data.available_slots.length > 0) {
                                doctor_id = data.doctor_id;
                                let now = new Date();
                                let currentTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                                let currentDate = now.toISOString().split('T')[0];

                                let sortedSlots = data.available_slots.sort((a, b) => a.localeCompare(b));

                                sortedSlots.forEach(slot => {
                                    if (appointmentDate !== currentDate || slot > currentTime) {
                                        timeSlotDropdown.append(new Option(slot, slot));
                                    }
                                });
                            } else {
                                timeSlotDropdown.append(new Option('No available slots', ''));
                            }

                            timeSlotDropdown.trigger('change');
                            updateButtonState();
                        }
                    } catch (error) {
                        console.error("Error:", error);
                    }
                }
            }

            const submitAppointmentButton = document.getElementById('bookNowButton');
            submitAppointmentButton.addEventListener('click', function(event) {
                const clinicId = clinicDropdown.val();
                const selectedDoctor = doctor_id;
                const serviceId = serviceDropdown.val();
                const appointmentDate = datePicker.value;
                const appointmentTime = timeSlotDropdown.val();

                if (!user_id  || user_type != "user") {

                     window.location.href = "{{ route('login-page') }}";
                 } else {
                     submitForm(serviceId, clinicId, selectedDoctor, appointmentDate, appointmentTime);
                 }

            });

            function updateButtonState() {
                const clinicId = clinicDropdown.val();
                const serviceId = serviceDropdown.val();
                const appointmentDate = datePicker.value;
                const appointmentTime = timeSlotDropdown.val();
                const allFieldsFilled = clinicId && serviceId && appointmentDate && appointmentTime && doctor_id;
                bookNowButton.disabled = !allFieldsFilled;
            }

            // Event listeners for Select2 dropdowns
            clinicDropdown.on('change', updateButtonState);
            serviceDropdown.on('change', updateButtonState);
            timeSlotDropdown.on('change', updateButtonState);
            $(datePicker).on('change', updateButtonState);

            function submitForm(serviceId, clinicId, selectedDoctor, appointmentDate, appointmentTime) {
                const selectedPaymentMethod = 'cash';
                const formData = new FormData();

                formData.append('clinic_id', clinicId);
                formData.append('selectedDoctor', selectedDoctor);
                formData.append('service_id', serviceId);
                formData.append('appointment_date', appointmentDate);
                formData.append('appointment_time', appointmentTime);
                formData.append('transaction_type', selectedPaymentMethod);
                formData.append('status', 'pending');

                fetch('{{ route('saveAppointment') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                })
                .then((response) => response.json())
                .then((data) => {
                    console.log('Form submitted successfully:', data);

                    if (selectedPaymentMethod == 'cash') {
                        const paymentDetails = {
                            doctorName: data.data.doctor_name || 'N/A',
                            clinicName: data.data.clinic_name || 'N/A',
                            appointmentDate: data.data.appointment_date || 'N/A',
                            appointmentTime: data.data.appointment_time || 'N/A',
                            bookingId: data.data.id || 'N/A',
                            paymentVia: data.data.transaction_type || 'N/A',
                            currency: data.data.currency_symbol || 'N/A',
                            totalAmount: data.data.total_amount || '0.00'
                        };

                        Swal.fire({
                            title: 'Appointment Submitted!',
                            html: `
                                <p>Your appointment with <strong>Dr. ${paymentDetails.doctorName}</strong> at
                                <strong>${paymentDetails.clinicName}</strong> has been successfully booked on
                                <strong>${new Date(paymentDetails.appointmentDate).toLocaleDateString()}</strong> at
                                <strong>${new Date('1970-01-01T' + paymentDetails.appointmentTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</strong>.</p>
                                <div>
                                    <p><strong>Booking ID:</strong> #${paymentDetails.bookingId}</p>
                                    <p><strong>Payment via:</strong> ${paymentDetails.paymentVia}</p>
                                    <p><strong>Total Payment:</strong> ${paymentDetails.currency} ${parseFloat(paymentDetails.totalAmount).toFixed(2)}</p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#FF6F61',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('appointment-list') }}";
                            }
                        });
                    } else if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch((error) => {
                    console.error('Error submitting form:', error);
                    alert('There was an error submitting the form.');
                });
            }
        });

        function setupServiceClinicDropdown(serviceDropdownId, clinicDropdownId, apiEndpoint) {
            const serviceDropdown = $(`#${serviceDropdownId}`);
            const clinicDropdown = $(`#${clinicDropdownId}`);

            if (serviceDropdown.length && clinicDropdown.length) {
                let previousServiceSelection = null;

                // Add clinic change handler
                clinicDropdown.on('change', async function() {
                    const clinicId = $(this).val();
                    if (clinicId) {
                        try {
                            previousServiceSelection = serviceDropdown.val();
                            serviceDropdown.prop('disabled', true);

                            // New endpoint needed for getting services by clinic
                            const response = await fetch(`{{ route('getServicesByClinic') }}?clinic_id=${clinicId}`);                            if (!response.ok) throw new Error("Failed to fetch services");

                            const services = await response.json();

                            // Keep existing options and update with available services
                            let currentOptions = new Set(serviceDropdown.find('option').map((_, opt) => opt.value));

                            // Clear all options except the default one
                            serviceDropdown.find('option:not([value=""])').remove();

                            services.forEach(service => {
                                serviceDropdown.append(new Option(service.name, service.id));
                            });

                            // Restore previous selection if it exists and is valid
                            if (previousServiceSelection && services.some(s => s.id == previousServiceSelection)) {
                                serviceDropdown.val(previousServiceSelection);
                            }

                            serviceDropdown.prop('disabled', false);
                            serviceDropdown.trigger('change');
                        } catch (error) {
                            console.error("Error fetching services:", error);
                            serviceDropdown.prop('disabled', false);
                        }
                    }
                });

                // Modified service change handler
                serviceDropdown.on('change', async function() {
                    const serviceId = $(this).val();
                    const clinicId = clinicDropdown.val();

                    if (serviceId && !clinicId) {
                        // If service is selected but no clinic, fetch and update clinics
                        try {
                            clinicDropdown.prop('disabled', true);
                            const response = await fetch(`${apiEndpoint}?service_id=${serviceId}`);
                            if (!response.ok) throw new Error("Failed to fetch clinics");

                            const clinics = await response.json();

                            // Update clinic options
                            clinicDropdown.find('option:not([value=""])').remove();
                            clinics.forEach(clinic => {
                                clinicDropdown.append(new Option(clinic.name, clinic.id));
                            });

                            clinicDropdown.prop('disabled', false);
                            clinicDropdown.trigger('change');
                        } catch (error) {
                            console.error("Error fetching clinics:", error);
                            clinicDropdown.prop('disabled', false);
                        }
                    }
                });
            }
        }

        $(document).ready(function () {
            $('#serviceDropdown').on('change', function () {
                let selectedService = $(this).val();

                if (selectedService) {
                    $('#clinicDropdown').prop('disabled', false);
                } else {
                    $('#clinicDropdown').prop('disabled', true).val(null).trigger('change');
                }
            });
        });
    </script>
@endpush