@extends('frontend::layouts.patient_layout')

@section('title', __('frontend.my_appointments'))

@section('content')
@include('frontend::components.section.breadcrumb')
    <div class="list-page section-spacing px-0">
        <div class="page-title" id="page_title">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-7 col-md-8">
                        <ul class="nav nav-pills row-gap-2 column-gap-3 clinic-tab-content mb-0">
                            <li class="nav-item">
                                <a class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="pill"
                                    data-tab="all-appointments" href="#all-appointments">
                                    <i class="ph ph-squares-four"></i>
                                    <span>{{ __('frontend.all') }}<span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill"
                                    data-tab="upcoming-appointments" href="#all-appointments">
                                    <i class="ph ph-calendar-plus"></i><span>{{ __('frontend.upcomming') }}  </span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill"
                                    data-tab="completed-appointments" href="#all-appointments"><i
                                        class="ph ph-check-circle"></i></i><span>{{ __('frontend.completed') }}
                                        </span></a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-xl-1 d-xl-block d-none"></div>
                    <div class="col-xl-3 col-lg-5 col-md-6 mt-3">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                                <h6 class="m-0 flex-shrink-0">{{ __('frontend.filter_by') }}</h6>

                            <!-- Filter Type Dropdown -->
                            <div class="form-group flex-grow-1 datatable-filter">
                                <select id="filter_type" class="form-select select2" data-filter="select">
                                    <option value="">Select Filter Type</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="clinic">Clinic</option>
                                    <option value="service">Service</option>
                                    <option value="category">Category</option>
                                    <option value="consultation_type">Consultation Type</option>
                                    <option value="payment_status">Payment Status</option>
                                    <option value="date">Date</option>
                                </select>
                            </div>

                            <!-- Dynamic Filter Options -->
                            <div class="form-group flex-grow-1" id="filter_options_container">
                                <!-- Options will load here dynamically -->
                            </div>
                        <div class="form-group flex-shrink-0" id="reset_filter_container" >
                            <button type="button" id="reset_filter" class="btn btn-sm btn-outline-danger d-none">
                                Reset
                            </button>
                        </div>

                        </div>
                    </div>
                </div>

                <div class="tab-content mt-5">
                    <div class="tab-pane active p-0 all-appointments" id="all-appointments">
                        <ul class="list-inline m-0">
                            <!-- Draft Appointments Section -->
                            @if(isset($drafts) && $drafts->count() > 0)
                                <div class="mb-4">
                                    <h6 class="text-muted mb-3">
                                        <i class="ph ph-clock me-2"></i>
                                        {{ __('frontend.incomplete_bookings') }} ({{ $drafts->count() }})
                                    </h6>
                                    @foreach($drafts as $draft)
                                        @include('frontend::components.card.draft_appointment_card', ['draft' => $draft])
                                    @endforeach
                                </div>
                                <hr class="my-4">
                                <h6 class="text-muted mb-3">
                                    <i class="ph ph-calendar-check me-2"></i>
                                    {{ __('frontend.confirmed_appointments') }}
                                </h6>
                            @endif
                            
                            <div id="shimmer-loader" class="d-flex gap-3 flex-wrap p-4 shimmer-loader">
                                @for ($i = 0; $i < 8; $i++)
                                    @include('frontend::components.card.shimmer_appointment_card')
                                @endfor
                            </div>
                            <table id="datatable" class="table table-responsive custom-card-table">
                            </table>
                        </ul>
                    </div>
                    <div class="tab-pane active p-0 upcoming-appointments" id="upcoming-appointments">
                        <ul class="list-inline m-0">

                            <table id="datatable" class="table table-responsive custom-card-table">
                            </table>
                        </ul>
                    </div>
                    <div class="tab-pane active p-0 upcoming-appointments" id="completed-appointments">
                        <ul class="list-inline m-0">

                            <table id="datatable" class="table table-responsive custom-card-table">
                            </table>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- cancel-appointment -->
    <div class="modal" id="cancel-appointment">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content section-bg rounded">
                <div class="modal-body modal-body-inner">
                    <div class="cancel-appointment text-center">
                        <div class="cancel-logo mx-auto text-center mb-3">
                            <i class="ph ph-x h4 align-middle text-primary"></i>
                        </div>
                        <h5 class="fw-semibold mb-2">{{ __('messages.cancel_appointment') }}</h5>

                            <p class="text-muted small">
                                {{ __('messages.do_you_want_to_cancel_this_appointment') }}
                            </p>

                            <div id="cancel_charge_info" class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <!-- JS will populate content here -->
                            </div>

                            <div class="text-start mb-3">
                                <label for="cancel_reason" class="form-label">{{ __('messages.lbl_reason') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cancel_reason" placeholder='{{ __('messages.lbl_emergency') }}' />
                                <div id="cancel_reason_error" class="invalid-feedback d-none">
                                    {{ __('messages.please_enter_reason') }}
                                </div>
                            </div>

                        <div class="mt-5 pt-3 text-center">
                            <div class="d-flex justify-content-center flex-wrap align-items-center gap-3">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.go_back') }}</button>
                                <button id="confirm_btn" onclick="cancelAppointment()" class="btn btn-secondary cancel-appointment-btn">
                                    {{ __('messages.cancel_appointment') }}
                                    </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  <!-- success-confirmation-modal -->
<div class="modal" id="success-confirmation-modal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4" style="border-radius: 15px; border: none;">
            <div class="modal-body text-center">
                <div class="booking-sucssufully text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="228" height="150" viewBox="0 0 228 150" fill="none">
                        <circle cx="111.266" cy="62.542" r="37.0947" fill="#5670CC"/>
                        <path d="M126.111 52.2447C126.54 52.6524 126.791 53.2141 126.806 53.8062C126.822 54.3984 126.602 54.9725 126.194 55.4024L111.562 70.836C111.039 71.3859 110.411 71.8239 109.714 72.1234C109.017 72.4229 108.267 72.5778 107.508 72.5785H107.374C106.593 72.5585 105.825 72.3752 105.119 72.0405C104.413 71.7058 103.785 71.227 103.276 70.6349L96.9692 63.3108C96.5825 62.8618 96.3899 62.2776 96.4339 61.6867C96.4779 61.0957 96.7548 60.5465 97.2038 60.1597C97.6528 59.773 98.237 59.5804 98.8279 59.6244C99.4188 59.6684 99.9681 59.9453 100.355 60.3943L106.661 67.7207C106.763 67.8394 106.889 67.9353 107.03 68.0021C107.171 68.0689 107.325 68.1051 107.481 68.1083C107.638 68.117 107.795 68.0903 107.94 68.0301C108.085 67.97 108.214 67.878 108.319 67.7609L122.952 52.3274C123.154 52.1145 123.396 51.9434 123.664 51.8241C123.932 51.7047 124.221 51.6393 124.514 51.6316C124.807 51.6239 125.099 51.6741 125.373 51.7793C125.647 51.8845 125.898 52.0427 126.111 52.2447Z" fill="white"/>
                        <path d="M173.061 122.724C171.465 117.137 164.282 117.735 164.282 117.735L164.481 121.527C168.671 120.728 171.265 125.717 171.265 125.717L173.061 122.724Z" fill="#5670CC"/>
                        <path d="M32.8532 133.452C31.2569 127.865 24.0737 128.464 24.0737 128.464L24.2733 132.255C28.4635 131.457 31.0574 136.445 31.0574 136.445L32.8532 133.452Z" fill="#E0E0E0"/>
                        <path d="M55.1137 89.1406C51.0423 89.5692 50.3994 94.7121 50.3994 94.7121L53.1851 95.1406C53.1851 92.1406 57.0423 90.8549 57.0423 90.8549L55.1137 89.1406Z" fill="#5670CC"/>
                        <path d="M96.5987 141.82C91.1702 142.392 90.313 149.249 90.313 149.249L94.0273 149.82C94.0273 145.82 99.1701 144.106 99.1701 144.106L96.5987 141.82Z" fill="#5670CC"/>
                        <path d="M225.296 26.3295C226.002 21.8589 220.59 19.9766 220.59 19.9766L219.414 22.8001C222.708 23.7413 222.943 27.9766 222.943 27.9766L225.296 26.3295Z" fill="#5670CC"/>
                        <path d="M81.093 11.3549C81.7989 6.88433 76.3872 5.00195 76.3872 5.00195L75.2107 7.8255C78.5048 8.76668 78.7401 13.002 78.7401 13.002L81.093 11.3549Z" fill="#5670CC"/>
                        <path d="M0.29049 83.5883C-0.30951 89.5883 7.4905 90.9883 7.4905 90.9883L8.8905 86.9883C4.0905 86.5883 3.4905 80.9883 3.4905 80.9883L0.29049 83.5883Z" fill="#5670CC"/>
                        <path d="M197.344 77.9939C200.994 78.186 202.338 73.7683 202.338 73.7683L200.033 73C199.649 75.689 196 76.2653 196 76.2653L197.344 77.9939Z" fill="#E0E0E0"/>
                        <path d="M213.702 131.926C213.702 132.136 212.237 132.345 210.353 132.345C208.469 132.345 207.004 132.136 207.004 131.926C207.004 131.717 208.469 131.508 210.353 131.508C212.027 131.508 213.702 131.717 213.702 131.926Z" fill="#E0E0E0"/>
                        <path d="M217.278 122.715C217.488 122.715 217.697 124.18 217.697 126.064C217.697 127.948 217.488 129.413 217.278 129.413C217.069 129.413 216.86 127.948 216.86 126.064C216.86 124.18 217.069 122.715 217.278 122.715Z" fill="#E0E0E0"/>
                        <path d="M221.044 131.716C221.044 131.506 222.51 131.297 224.393 131.297C226.277 131.297 227.742 131.506 227.742 131.716C227.742 131.925 226.277 132.134 224.393 132.134C222.51 131.925 221.044 131.925 221.044 131.716Z" fill="#E0E0E0"/>
                        <path d="M217.484 140.716C217.274 140.716 217.065 139.25 217.065 137.367C217.065 135.483 217.274 134.018 217.484 134.018C217.693 134.018 217.902 135.483 217.902 137.367C217.693 139.25 217.693 140.716 217.484 140.716Z" fill="#E0E0E0"/>
                        <path d="M222.91 136.739C222.701 136.948 222.073 136.32 221.445 135.483C220.817 134.645 220.398 134.017 220.607 133.808C220.817 133.599 221.445 134.227 222.073 135.064C222.491 135.901 222.91 136.739 222.91 136.739Z" fill="#E0E0E0"/>
                        <path d="M222.503 126.48C222.712 126.69 222.084 127.318 221.456 127.946C220.828 128.574 220.2 128.992 219.991 128.992C219.781 128.783 220.409 128.155 221.037 127.527C221.665 126.899 222.293 126.48 222.503 126.48Z" fill="#E0E0E0"/>
                        <path d="M214.764 129.203C214.555 129.413 213.927 128.994 213.299 128.575C212.671 127.947 212.252 127.529 212.252 127.319C212.252 127.11 213.09 127.529 213.718 127.947C214.555 128.366 214.974 128.994 214.764 129.203Z" fill="#E0E0E0"/>
                        <path d="M214.546 133.598C214.756 133.807 214.337 134.435 213.709 135.063C213.081 135.691 212.453 136.109 212.244 136.109C212.035 135.9 212.453 135.272 213.081 134.644C213.709 134.016 214.337 133.598 214.546 133.598Z" fill="#E0E0E0"/>
                        <path d="M22.8263 29.4733C22.8263 29.6826 21.3612 29.8919 19.4775 29.8919C17.5938 29.8919 16.1287 29.6826 16.1287 29.4733C16.1287 29.264 17.5938 29.0547 19.4775 29.0547C21.3612 29.0547 22.8263 29.264 22.8263 29.4733Z" fill="#5670CC"/>
                        <path d="M26.3742 20.2637C26.5835 20.2637 26.7928 21.7288 26.7928 23.6125C26.7928 25.4962 26.5835 26.9613 26.3742 26.9613C26.1649 26.9613 25.9556 25.4962 25.9556 23.6125C26.1649 21.7288 26.3742 20.2637 26.3742 20.2637Z" fill="#5670CC"/>
                        <path d="M30.1458 29.2643C30.1458 29.055 31.6109 28.8457 33.4946 28.8457C35.3783 28.8457 36.8435 29.055 36.8435 29.2643C36.8435 29.4736 35.3783 29.6829 33.4946 29.6829C31.8202 29.4736 30.1458 29.2643 30.1458 29.2643Z" fill="#5670CC"/>
                        <path d="M26.7924 38.2641C26.5831 38.2641 26.3738 36.799 26.3738 34.9153C26.3738 33.0315 26.5831 31.5664 26.7924 31.5664C27.0017 31.5664 27.211 33.0315 27.211 34.9153C27.0017 36.799 26.7924 38.2641 26.7924 38.2641Z" fill="#5670CC"/>
                        <path d="M32.0348 34.2872C31.8255 34.4965 31.1976 33.8686 30.5697 33.0314C29.9417 32.1942 29.5231 31.5663 29.7324 31.357C29.9417 31.1477 30.5696 31.7756 31.1975 32.6128C31.8254 33.45 32.2441 34.2872 32.0348 34.2872Z" fill="#5670CC"/>
                        <path d="M31.8232 24.0313C32.0325 24.2406 31.4046 24.8685 30.7767 25.4964C30.1488 26.1243 29.5209 26.5429 29.3116 26.5429C29.1023 26.3336 29.7302 25.7057 30.3581 25.0778C30.986 24.4499 31.6139 23.822 31.8232 24.0313Z" fill="#5670CC"/>
                        <path d="M24.081 26.7519C23.8717 26.9612 23.2438 26.5426 22.6158 26.124C21.9879 25.4961 21.5693 25.0775 21.5693 24.8682C21.7786 24.6589 22.4065 25.0775 23.0344 25.4961C23.8717 25.9147 24.2903 26.5426 24.081 26.7519Z" fill="#5670CC"/>
                        <path d="M23.8709 31.1484C24.0802 31.3577 23.6616 31.9856 23.0337 32.6135C22.4058 33.2415 21.7779 33.6601 21.5686 33.6601C21.3593 33.4508 21.7779 32.8229 22.4058 32.1949C23.0337 31.567 23.6616 31.1484 23.8709 31.1484Z" fill="#5670CC"/>
                        <path d="M156.207 11.4769C165.81 14.5218 171.9 3.27911 171.9 3.27911L166.044 0C163.234 6.79248 153.396 6.08978 153.396 6.08978L156.207 11.4769Z" fill="#E0E0E0"/>
                        </svg>
                </div>

                <h5 class="mt-3 fw-bold heading-color">Your Appointment has been Cancelled</h5>
                <p class="text-muted mb-3" style="font-size: 14px;">
                    Your booking has successfully been cancelled. Applicable refund will be processed within 24 hours.
                </p>

                <div class="alert alert-danger bg-opacity-10 border border-danger text-danger fw-normal fst-italic py-2 px-3 rounded">
                    *Note: Check your booking history for refund details
                </div>

                <button type="button" class="btn btn-secondary px-4 py-2" id="ok-cancel-success" data-bs-dismiss="modal" style="border-radius: 10px;">Ok</button>
            </div>
        </div>
    </div>
</div>


    <!-- rate us modal -->
    <x-frontend::section.review />
@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('after-scripts')
    <!-- DataTables Core and Extensions -->
    <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Add Axios CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script type="text/javascript" defer>
        let finalColumns = [{
            data: 'card',
            name: 'card',
            orderable: false,
            searchable: true
        }];
        let appointmentIdToCancel = null;
        let cancelledAppointmentId = null;
        let charge = null;
        let cancellation_charge = @json(setting('cancellation_charge'));
        let cancelltion_Type = @json(setting('cancellation_type'));
        let datatable = null;

        document.addEventListener('DOMContentLoaded', (event) => {
            const doctorFilter = $('#doctor');

            // Initialize Select2
            doctorFilter.select2({
                placeholder: 'Doctor',
                allowClear: true,
                width: '100%'
            });

            doctorFilter.on('change', function() {
                window.renderedDataTable.draw();
            });

            let activeTab = "all-appointments"; // Default active tab
            dataTableReload(activeTab); // Load for the default tab

            $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                activeTab = $(e.target).attr('data-tab'); // Get active tab ID
                console.log(activeTab);

                dataTableReload(activeTab);
            });

            $('#cancel-appointment').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                const appointmentId = button.data('appointment-id');
                const cancelCharge = parseFloat(button.data('charge')) || 0;
                appointmentIdToCancel = appointmentId;
                charge = cancelCharge;


                const chargeWithFormat = `<span class="text-primary fw-bold">${currencyFormat(cancelCharge)}</span>`;
                $('#cancel_charge_info').html(`
                    <span class="text-muted">{{ __('messages.cancellation_fee') }}</span>
                    ${chargeWithFormat}
                `);
            });
        });

        function dataTableReload(activeTab) {

            if ($.fn.dataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable().clear().destroy();
            }
            const shimmerLoader = document.querySelector('.shimmer-loader');
            const dataTableElement = document.getElementById('datatable');
            frontInitDatatable({
                url: '{{ route('appointment.index_data') }}',
                finalColumns,
                cardColumnClass: 'row-cols-1',
                  advanceFilter: () => {
                    const filterType = $('#filter_type').val();
                    let filterValue = null;

                    if(filterType) {
                        // If the dynamic filter is a select
                        const dynamicSelect = $('#filter_options_container select');
                        if(dynamicSelect.length) {
                            filterValue = dynamicSelect.val();
                        }

                        // If the dynamic filter is a date input
                        const dynamicDate = $('#filter_options_container input[type="text"]');
                        if(dynamicDate.length) {
                            filterValue = dynamicDate.val();
                        }
                    }

                    return {
                        activeTab: activeTab,
                        filter_type: filterType,
                        filter_value: filterValue
                    }
                },
                onLoadStart: () => {
                    // Show shimmer loader before loading data
                    shimmerLoader.classList.remove('d-none');
                    dataTableElement.classList.add('d-none');
                },
                onLoadComplete: () => {
                    shimmerLoader.classList.add('d-none');
                    dataTableElement.classList.remove('d-none');
                },
            })
        }

        // Initialize Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        function showCancelModal(appointmentId) {
            $('.cancel-appointment-btn').attr('data-appointment-id', appointmentId);
            $('#cancel-appointment').modal('show');
        }

        function cancelAppointment(element) {
            const reason = $('#cancel_reason').val().trim();
                if (!reason) {
                    $('#cancel_reason').addClass('is-invalid');
                    $('#cancel_reason_error').removeClass('d-none');
                    return;
                } else {
                    $('#cancel_reason').removeClass('is-invalid');
                    $('#cancel_reason_error').addClass('d-none');
                }
                const confirm_btn_text = 'Cancel Appointment'
             $('#confirm_btn').html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...');
            $.ajax({
                url: "{{ route('cancel-appointment', '') }}/" + appointmentIdToCancel,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data : {
                    status : 'cancelled',
                    reason: reason,
                    cancellation_charge_amount : charge,
                    cancellation_type : cancelltion_Type,
                    cancellation_charge :cancellation_charge,
                },
                success: function(response) {
                    if (response.status) {
                        successSnackbar(response.message);
                        cancelledAppointmentId = appointmentIdToCancel; // Store the cancelled appointment ID
                        $('#cancel-appointment').modal('hide');
                        $('#confirm_btn').html(confirm_btn_text);
                        $('#cancel_reason').val('');
                        $('#datatable').DataTable().ajax.reload();
                        $('#success-confirmation-modal').modal('show');
                        successModal.show();
                    } else {
                        console.error("Error Message: ", response.message);
                    }
                },
            });
        }

        // Handle redirect to appointment details when Ok button is clicked
        $(document).on('click', '#ok-cancel-success', function() {
            if (cancelledAppointmentId) {
                window.location.href = "{{ route('appointment-details', '') }}/" + cancelledAppointmentId;
            }
        });

    </script>
<script>
    const doctors = @json($doctorOptions ?? []);
    const clinics = @json($clinicOptions ?? []);
    const categories = @json($categoryOptions ?? []);
    const services = @json($serviceOptions ?? []);

    // Debug: Check if data is loaded
    console.log('Doctors:', doctors);
    console.log('Clinics:', clinics);
    console.log('Categories:', categories);
    console.log('Services:', services);
</script>

<script>
    $(document).ready(function() {

        // Filter type change handler
        $('#filter_type').on('change', function() {
            const type = $(this).val();
            const container = $('#filter_options_container');
            const resetBtn = $('#reset_filter');

            console.log('Filter type selected:', type);

            container.html(''); // clear previous options
            resetBtn.addClass('d-none');

            let select;

            if(type === 'doctor') {
                select = $('<select name="doctor" class="form-select select2"></select>');
                select.append('<option value="">Select Doctor</option>');
                if(doctors && doctors.length > 0) {
                    doctors.forEach(d => {
                        select.append(`<option value="${d.id}">${d.name}</option>`);
                    });
                }
            }
            else if(type === 'clinic') {
                select = $('<select name="clinic" class="form-select select2"></select>');
                select.append('<option value="">Select Clinic</option>');
                if(clinics && clinics.length > 0) {
                    clinics.forEach(c => {
                        select.append(`<option value="${c.id}">${c.name}</option>`);
                    });
                }
            }
            else if(type === 'category') {
                select = $('<select name="category" class="form-select select2"></select>');
                select.append('<option value="">Select Category</option>');
                if(categories && categories.length > 0) {
                    categories.forEach(c => {
                        select.append(`<option value="${c.id}">${c.name}</option>`);
                    });
                }
            }
            else if(type === 'service') {
                select = $('<select name="service" class="form-select select2"></select>');
                select.append('<option value="">Select Service</option>');
                if(services && services.length > 0) {
                    services.forEach(s => {
                        select.append(`<option value="${s.id}">${s.name}</option>`);
                    });
                }
            }
            else if(type === 'consultation_type') {
                select = $('<select name="consultation_type" class="form-select select2"></select>');
                select.append(`
                    <option value="">Select Consultation Type</option>
                    <option value="online">Online</option>
                    <option value="inclinic">In-Clinic</option>
                `);
            }
            else if(type === 'payment_status') {
                select = $('<select name="payment_status" class="form-select select2"></select>');
                select.append(`
                    <option value="">Select Payment Status</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="advance_paid">Advance Paid</option>
                    <option value="advance_refunded">Advance Refunded</option>
                    <option value="payment_refunded">Payment Refunded</option>
                `);
            }
            else if(type === 'date') {
                const inputGroup = $(`
                    <div class="input-group">
                        <input type="text" id="date" name="date"
                            class="form-control" placeholder="Select date" />
                        <span class="input-group-text" id="calendar-icon">
                            <i class="ph ph-calendar"></i>
                        </span>
                    </div>
                `);

                container.append(inputGroup);

                const appointmentDateInput = document.getElementById('date');
                const calendarIcon = document.getElementById('calendar-icon');

                const picker = flatpickr(appointmentDateInput, {
                    mode: "range",
                    dateFormat: "d-m-Y",
                    clickOpens: false,
                    allowInput: true,
                    onClose: function(selectedDates, dateStr) {
                        if (dateStr) {
                            resetBtn.removeClass('d-none');
                            window.renderedDataTable.draw();
                        }
                    }
                });

                appointmentDateInput.addEventListener('click', () => picker.open());
                calendarIcon.addEventListener('click', () => picker.open());

                return;
            }

            if(select) {
                container.append(select);
                console.log('Select appended to container');
                
                // Initialize Select2 for dynamically created select
                select.select2({
                    width: '100%',
                    placeholder: 'Select an option',
                    allowClear: true
                });
                
                $(select).on('change', function() {
                    if ($(this).val()) {
                        resetBtn.removeClass('d-none');
                        window.renderedDataTable.draw();
                    }
                });
            }
        });

        // Reset button logic
        $('#reset_filter').on('click', function() {
            $('#filter_type').val('').trigger('change');
            $('#filter_options_container').html('');
            $(this).addClass('d-none');
            window.renderedDataTable.draw();
        });

    });
</script>
@endpush
