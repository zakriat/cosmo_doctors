@extends('frontend::layouts.master')

@section('title', __('frontend.other_patient'))

@section('content')
@include('frontend::components.section.breadcrumb')

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">

        <div class="page-title" id="page_title">
            <h6>{{__('messages.all_patients')}}</h6>
        </div>

        <button id="addPatientBtn" class="btn btn-link p-0 text-secondary fw-bold" data-bs-toggle="modal" data-bs-target="#addPatientModal">{{__('messages.add_patient')}}</button>
    </div>


    <div id="patientsContainer" class="row">

        <div class="tab-content mt-5">
            <div class="tab-pane active p-0 all-appointments" id="all-appointments">
                <ul class="list-inline m-0">
                    <div id="shimmer-loader" class="d-flex gap-3 flex-wrap p-4 shimmer-loader">
                        @for ($i = 0; $i < 8; $i++)
                            @include('frontend::components.card.shimmer_other_patient_card')
                        @endfor
                    </div>
                    <table id="datatable" class="table table-responsive custom-card-table">
                    </table>
                </ul>
            </div>

        </div>

    </div>
    <div id="emptyState" class="col-12 text-center d-none">
        <div class="py-5">
            <h3>No Patients Found</h3>
            <p class="text-muted">You haven't added any patients yet.</p>
        </div>
    </div>
    <div id="pagination" class="d-flex justify-content-center mt-4"></div>
</div>

<!-- Add Patient Modal -->
<div class="modal fade add-patient-modal" id="addPatientModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">{{ __('customer.add_new_patient') }}</h5>
                <div class="close-modal-btn" data-bs-dismiss="modal"><i class="ph ph-x align-middle"></i></div>
            </div>
            <div class="modal-body">
                <form id="patientForm" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="patient_id" id="patientId">
                    <div class="row">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center justify-content-center p-3">
                                        <img id="miniLogoViewer" src="{{ asset('img/avatar/avatar.webp') }}" class="img-fluid avatar-130 rounded-pill" alt="Profile Picture" />
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
                                <div class="form-group col-xl-6 col-lg-12">
                                    <label class="form-label">{{ __('clinic.lbl_first_name') }} <span class="text-danger">*</span></label>
                                    <div class="input-group custom-input-group mb-1">
                                        <input type="text" class="form-control" name="first_name" placeholder="First Name" id="firstName" required>
                                        <span class="input-group-text"><i class="ph ph-user"></i></span>
                                    </div>
                                </div>
                                <div class="form-group col-xl-6 col-lg-12">
                                    <label class="form-label">{{ __('clinic.lbl_last_name') }} <span class="text-danger">*</span></label>
                                    <div class="input-group custom-input-group mb-1">
                                        <input type="text" class="form-control" name="last_name" id="lastName" placeholder="Last Name" required>
                                        <span class="input-group-text"><i class="ph ph-user"></i></span>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="form-group col-xl-6 col-lg-12">
                                    <label class="form-label">{{ __('clinic.lbl_phone_number') }} <span class="text-danger">*</span></label>
                                    <div class="input-group custom-input-group mb-1">
                                        <input type="tel" class="form-control" name="contactNumber" id="contactNumber" placeholder="Phone Number" id="mobile" required>
                                        <span class="input-group-text"><i class="ph ph-phone"></i></span>
                                    </div>
                                </div>

                                <!-- Date of Birth -->
                                <div class="form-group col-xl-6 col-lg-12">
                                    <label class="form-label">{{ __('clinic.date_of_birth') }} <span class="text-danger">*</span></label>
                                    <div class="input-group custom-input-group mb-1">
                                            <input type="date" class="form-control" name="dob" id="dob" placeholder="DOB" required>
                                            <span class="input-group-text"><i class="ph ph-cake"></i></span>
                                    </div>
                                </div>

                                <!-- Gender Selection -->
                                <div class="form-group col-lg-12">
                                    <label class="form-label">{{ __('clinic.lbl_gender') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <div class="form-check custom-radio-btn">
                                            <input class="form-check-input" type="radio" name="gender" value="Male" id="genderMale" required>
                                            <label class="form-check-label rounded-pill" for="genderMale">{{ __('messages.male') }}</label>
                                        </div>
                                        <div class="form-check custom-radio-btn">
                                            <input class="form-check-input" type="radio" name="gender" value="Female" id="genderFemale" required>
                                            <label class="form-check-label rounded-pill" for="genderFemale">{{ __('messages.female') }}</label>
                                        </div>
                                        <div class="form-check custom-radio-btn">
                                            <input class="form-check-input" type="radio" name="gender" value="Other" id="genderOther" required>
                                            <label class="form-check-label rounded-pill" for="genderOther">{{ __('messages.lbl_other') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Relationship Selection -->
                                <!-- Replace the relationship select field with radio buttons -->
                                <div class=" form-group col-lg-12">
                                    <label class="form-label mb-3">{{ __('clinic.relation') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
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
                </form>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('frontend.close') }}</button> -->
                <button type="button" class="btn btn-secondary" id="savePatient">{{ __('messages.save') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body modal-body-inner">
                <div class="close-modal-btn" data-bs-dismiss="modal">
                        <i class="ph ph-x align-middle"></i>
                </div>
                <!-- Trash Icon -->
                <div class="text-center">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="81" height="86" viewBox="0 0 81 86" fill="none">
                            <path d="M71.5464 15.1624V80.7053C71.5464 81.6202 71.183 82.4976 70.536 83.1445C69.8891 83.7915 69.0117 84.1549 68.0968 84.1549H12.9028C11.9879 84.1549 11.1104 83.7915 10.4635 83.1445C9.81657 82.4976 9.45312 81.6202 9.45312 80.7053V15.1624" fill="#FEF1F1"/>
                            <path d="M71.5464 15.1624V80.7053C71.5464 81.6202 71.183 82.4976 70.536 83.1445C69.8891 83.7915 69.0117 84.1549 68.0968 84.1549H12.9028C11.9879 84.1549 11.1104 83.7915 10.4635 83.1445C9.81657 82.4976 9.45312 81.6202 9.45312 80.7053V15.1624" stroke="#F54438" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M78.4445 15.1624H2.55273" stroke="#F54438" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M30.1504 35.8601V63.4571" stroke="#F54438" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M50.8477 35.8601V63.4571" stroke="#F54438" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M57.7482 15.1623V8.26302C57.7482 6.43323 57.0213 4.67837 55.7275 3.38451C54.4336 2.09065 52.6788 1.36377 50.849 1.36377H30.1512C28.3214 1.36377 26.5666 2.09065 25.2727 3.38451C23.9788 4.67837 23.252 6.43323 23.252 8.26302V15.1623" stroke="#F54438" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h5 class="font-size-18">{{ __('messages.are_you_sure_want_to_delete') }}</h5>
                    <p class="font-size-14 mb-0">{{ __('messages.deleting_patient_will_remove_permanently') }}</p>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="button" class="btn btn-secondary" id="confirmDeleteBtn">{{ __('messages.delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
    <style>
        .btn-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.8;
        }
        .btn-loading::after {
            content: '';
            width: 1rem;
            height: 1rem;
            position: absolute;
            top: calc(50% - 0.5rem);
            right: 0.5rem;
            border: 2px solid transparent;
            border-top-color: currentColor;
            border-radius: 50%;
            animation: button-loading-spinner 0.6s linear infinite;
        }
        @keyframes button-loading-spinner {
            from {
                transform: rotate(0turn);
            }
            to {
                transform: rotate(1turn);
            }
        }
    </style>
@endpush

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

    <script type="text/javascript" defer>
        let finalColumns = [{
            data: 'card',
            name: 'card',
            orderable: false,
            searchable: true
        }]

        document.addEventListener('DOMContentLoaded', (event) => {
            const searchInput = document.getElementById('datatable-search');
            // Trigger search when input changes
            // searchInput.addEventListener('input', function() {
            //     window.renderedDataTable.search(this.value).draw();
            // });
            const shimmerLoader = document.getElementById('shimmer-loader');
            const dataTableElement = document.getElementById('datatable');
            frontInitDatatable({
                url: '{{ route('manage-profile-data') }}',
                finalColumns,
                cardColumnClass: 'row-cols-1',
                onLoadStart: () => {
                    // Show shimmer loader before loading data
                    shimmerLoader.classList.remove('d-none');
                    dataTableElement.classList.add('d-none');
                },
                onLoadComplete: () => {
                    shimmerLoader.classList.add('d-none');
                    dataTableElement.classList.remove('d-none');
                },
            });
        })
    </script>
@endpush
@push('after-scripts')
    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
    <script>
            $(document).ready(function() {
                const input = document.querySelector("#contactNumber");
                    const iti = window.intlTelInput(input, {
                        initialCountry: "gb",
                        separateDialCode: true,
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
                    });
                    window.manageProfileIti = iti; // expose for edit/use later


                    flatpickr('#dob', {
                        dateFormat: 'Y-m-d',
                        maxDate: 'today',
                        disable: [
                        new Date().toISOString().split('T')[0] //
                        ]
                    });

            // Set the CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#patientsContainer').on('click', '.deleteBtn', function() {
                var patientId = $(this).data('id');
                $('#confirmDeleteBtn').data('id', patientId); // Store the patient ID in the confirm button
                $('#deleteConfirmationModal').modal('show');
            });

            $('#confirmDeleteBtn').click(function() {
                var patientId = $(this).data('id');
                $.ajax({
                    url: '{{ route('other-patients.destroy', ['id' => '__patientId__']) }}'.replace('__patientId__', patientId),
                    type: 'DELETE',
                    success: function(response) {
                        window.successSnackbar("{{ __('messages.patient_deleted_success') }}");
                        $('#deleteConfirmationModal').modal('hide');

                        if (window.renderedDataTable) {
                            window.renderedDataTable.ajax.reload(null, false);
                            }
                    },
                    error: function(error) {
                        console.error('Error deleting patient:', error);
                        window.errorSnackbar('Something went wrong. Please try again.');
                    }
                });
            })

            $('#addPatientBtn').click(function() {
                $('#addPatientModal').modal('show');
                $('#patientForm').trigger('reset');
                $('#patientId').val('');
                $('#modalTitle').text('{{ __('customer.add_new_patient') }}');
                $('#miniLogoViewer').attr('src', '{{ asset('img/avatar/avatar.webp') }}');
                // Reset intl-tel-input to default country
                if (window.manageProfileIti) {
                    window.manageProfileIti.setNumber('');
                }
            });

            // Handle edit and delete actions
            $('#patientsContainer').on('click', '.editBtn', function() {
                var patientId = $(this).data('id');
                $.ajax({
                    url: '{{ route('other-patients.edit', ['id' => '__patientId__']) }}'.replace('__patientId__', patientId),
                    type: 'GET',
                    success: function(response) {
                        if (response.status) {
                            const patient = response.data;
                            $('#patientId').val(patient.id);
                            $('#firstName').val(patient.first_name);
                            $('#lastName').val(patient.last_name);
                            if (window.manageProfileIti && patient.contactNumber) {
                                // sets both country and formatted number
                                window.manageProfileIti.setNumber(patient.contactNumber);
                            } else {
                                $('#contactNumber').val(patient.contactNumber || '');
                            }
                            $('#dob').val(patient.dob);
                            $(`input[name="gender"][value="${patient.gender}"]`).prop('checked', true);
                            $(`input[name="relation"][value="${patient.relation}"]`).prop('checked', true);
                            const profileImage = patient.profile_image || defaultImagePath;
                            $('#miniLogoViewer').attr('src', profileImage);
                            toggleRemoveButton(profileImage); // Toggle remove button visibility
                            $('#addPatientModal').modal('show');
                            $('#modalTitle').text('{{ __("frontend.edit_patient") }}');
                        } else {
                            window.errorSnackbar(response.message);
                        }
                    },
                    error: function(error) {
                        console.error('Error fetching patient:', error);
                    }
                });
            });

            $('#patientsContainer').on('click', '.deleteBtn', function() {
                var patientId = $(this).data('id');
                // Handle delete logic
            });

            // Show/hide remove button based on default image
            const defaultImagePath = '{{ asset('img/avatar/avatar.webp') }}';
            function toggleRemoveButton(imageSrc) {
                if (imageSrc === defaultImagePath) {
                    $('#removeMiniLogoButton').addClass('d-none');
                } else {
                    $('#removeMiniLogoButton').removeClass('d-none');
                }
            }

            // Update image upload handler
            $('#mini_logo').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#miniLogoViewer').attr('src', e.target.result);
                        $('#removeMiniLogoButton').removeClass('d-none'); // Show remove button
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Update remove image handler
            $('#removeMiniLogoButton').click(function() {
                $('#miniLogoViewer').attr('src', defaultImagePath);
                $('#mini_logo').val('');
                $(this).addClass('d-none'); // Hide remove button
            });

            const today = new Date().toISOString().split('T')[0];
            $('input[name="dob"]').attr('max', today);

            // Allow only digits in the phone number input
            $('input[name="contactNumber"]').on('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $('#savePatient').click(function () {
                const $saveButton = $(this);
                const originalText = $saveButton.html();
                $('.text-danger').text(''); // Clear old errors

                const form = $('#patientForm')[0];
                const formData = new FormData(form);
                let hasError = false;

                const firstName = formData.get('first_name');
                const lastName = formData.get('last_name');
                // Get full international number with country code
                const contactNumber = window.manageProfileIti ? window.manageProfileIti.getNumber() : formData.get('contactNumber');
                const dob = formData.get('dob');
                const gender = formData.get('gender');
                const relation = formData.get('relation');

                if (!firstName) {
                    showError('first_name', 'First name is required');
                    hasError = true;
                }
                if (!lastName) {
                    showError('last_name', 'Last name is required');
                    hasError = true;
                }
                if (!contactNumber) {
                    showError('contactNumber', 'Contact number is required');
                    hasError = true;
                }
                if (!dob) {
                    showError('dob', 'Date of birth is required');
                    hasError = true;
                } else if (new Date(dob) >= new Date(today)) {
                    showError('dob', 'Date of birth must be a past date');
                    hasError = true;
                }
                if (!gender) {
                    showError('gender', 'Gender is required');
                    hasError = true;
                }
                if (!relation) {
                    showError('relation', 'Relation is required');
                    hasError = true;
                }

                if (!hasError) {
                    // Show loading state
                    $saveButton.addClass('btn-loading').html('Saving...').prop('disabled', true);

                    // Update formData with full international number
                    formData.set('contactNumber', contactNumber);

                    const patientId = $('#patientId').val();
                    const url = patientId ? '{{ route('other-patients.update', ['id' => '__patientId__']) }}'.replace('__patientId__', patientId) : '{{ route('other-patients.store') }}';
                    const method = 'POST';

                    $.ajax({
                        url: url,
                        type: method,
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            window.successSnackbar("{{ __('messages.patient_saved_successfully') }}");
                            $('#addPatientModal').modal('hide');
                            $('#patientForm')[0].reset();
                            // Reset intl-tel-input
                            if (window.manageProfileIti) {
                                window.manageProfileIti.setNumber('');
                            }

                            if (window.renderedDataTable) {
                            window.renderedDataTable.ajax.reload(null, false);
                            }
                        },
                        error: function (xhr) {
                            window.errorSnackbar('Something went wrong. Please try again.');
                        },
                        complete: function() {
                            // Reset button state
                            $saveButton.removeClass('btn-loading').html(originalText).prop('disabled', false);
                        }
                    });
                }

                function showError(fieldName, message) {
                    const input = $(`[name="${fieldName}"]`);
                    if (input.length > 0) {
                        input.closest('.form-group').append(`<div class="text-danger">${message}</div>`);
                    } else {
                        $(`#${fieldName}`).closest('.form-group').append(`<div class="text-danger">${message}</div>`);
                    }
                }
            });
        });
    </script>
@endpush
