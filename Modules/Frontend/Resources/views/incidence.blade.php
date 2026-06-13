@extends('frontend::layouts.master')

@section('title', 'Incidence Report')

@section('content')
@include('frontend::components.section.breadcrumb')
    <div class="list-page section-spacing px-0">
        <div class="page-title" id="page_title">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Bootstrap Nav Pills for All, Open, Close -->
                        <ul class="nav nav-pills row-gap-2 column-gap-3 clinic-tab-content mb-0">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="pill" data-tab="all" href="#all-incidences">
                                    <i class="ph ph-squares-four"></i>
                                    <span>{{__('frontend.all')}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" data-tab="open" href="#all-incidences">
                                    <i class="ph ph-calendar-plus"></i>
                                    <span>{{__('messages.lbl_open')}}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" data-tab="close" href="#all-incidences">
                                    <i class="ph ph-check-circle"></i>
                                    <span>{{__('messages.closed')}}</span>
                                </a>
                            </li>

                              <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="pill" data-tab="reject" href="#all-incidences">
                                    <i class="ph ph-check-circle"></i>
                                    <span>{{__('messages.lbl_rejected')}}</span>
                                </a>
                            </li>
                        </ul>
                        <!-- End Nav Pills -->
                        <div class="tab-content">
                            <div class="tab-pane active p-0 all-incidences" id="all-incidences">
                                <ul class="list-inline m-0">
                                    <div id="shimmer-loader" class="d-flex gap-3 flex-wrap p-4 shimmer-loader">
                                        @for ($i = 0; $i < 8; $i++)
                                            @include('frontend::components.card.shimmer_incidence_card')
                                        @endfor
                                    </div>
                                    <table id="datatable" class="table table-responsive custom-card-table">
                                    </table>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mt-lg-5 mt-5">
                        <div class="contact-card p-4 section-bg rounded">
                            <h5>{{ __('messages.we_are_here_to_support')}}</h5>
                            <p class="m-0 font-size-14">{{__('messages.our_passion_is_to_provide')}}</p>

                            <form id="incidence-form" class="mt-5" method="POST" action="{{ route('incidence.store') }}">
                                @csrf
                                <div class="form">
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <div class="input-group custom-input-group mb-1">
                                                <input type="text" name="title" class="form-control" placeholder="{{__('messages.lbl_title')}}" value="" >
                                                <span class="input-group-text"><i class="ph ph-text-t"></i></span>
                                            </div>
                                            <small class="text-danger error-message" id="error-title"></small>
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <div class="input-group custom-input-group mb-1">
                                                <input type="email" name="email" class="form-control" placeholder="{{__('messages.lbl_emailid')}}" value="">
                                                <span class="input-group-text"><i class="ph ph-envelope-simple"></i></span>
                                            </div>
                                            <small class="text-danger error-message" id="error-email"></small>
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <div class="input-group custom-input-group mb-1">
                                                <input type="text" id="phone" name="phone" class="form-control" placeholder="{{__('messages.lbl_phone_number')}}" value="" >
                                                <span class="input-group-text"><i class="ph ph-phone"></i></span>
                                            </div>
                                            <small class="text-danger error-message" id="error-phone"></small>
                                        </div>
                                        <div class="col-lg-12 mb-3">
                                            <textarea type="text" name="description" id="description" class="form-control bg-body" placeholder="{{__('messages.lbl_description')}}" maxlength="250"></textarea>
                                            <small class="text-danger error-message" id="error-description"></small>
                                            <div class="text-end mt-1">
                                                <small id="description-count">0 / 250</small>
                                            </div>
                                        </div>

                                         <div class="col-lg-12 mb-3">
                                      <div class="input-group custom-input-group mb-1">
                                          <input type="file" name="file_url" class="form-control" accept="image/*" id="image-input">
                                          <span class="input-group-text"><i class="ph ph-image"></i></span>
                                      </div>
                                      <div id="image-preview-container" class="mt-2" style="display:none;">
                                        <div class="position-relative d-inline-block">
                                            <img id="image-preview" src="" class="avatar-100" alt="Image Preview"/>
                                            <button type="button" class="btn btn-sm btn-danger p-0 avatar-30 rounded-pills position-absolute top-0 end-0" id="remove-image-preview"><i class="ph ph-x"></i></button>
                                        </div>
                                      </div>
                                      <small class="text-danger error-message" id="error-image"></small>
                                  </div>

                                        <div class="d-flex justify-content-md-end">
                                            <!-- <button type="submit" class="btn btn-secondary">Submit</button> -->
                                            <button type="submit" id="submit-btn" class="btn btn-secondary">
                                                <span id="submit-text">{{__('messages.lbl_submit')}}</span>
                                                <span id="submit-spinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                        <div id="snackbar" class="snackbar">{{__('messages.created_successfully')}}</div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection

@push('after-styles')
    <!-- DataTables Core and Extensions -->
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('after-scripts')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>

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
    let datatable = null;

    document.addEventListener('DOMContentLoaded', (event) => {
        let activeTab = "all"; // Default
        dataTableReload(activeTab);

        $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
            activeTab = $(e.target).attr('data-tab');
            dataTableReload(activeTab);
        });
    });

    function dataTableReload(status) {
        if ($.fn.dataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().clear().destroy();
        }
        const shimmerLoader = document.querySelector('.shimmer-loader');
        const dataTableElement = document.getElementById('datatable');
        frontInitDatatable({
            url: '{{ route('incidence.index_data') }}',
            finalColumns,
            cardColumnClass: 'row-cols-1',
            advanceFilter: () => {
                return {
                    status: status,
                    doctor_id: $('#doctor').val(),
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

     const imageInput = document.getElementById('image-input');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImagedata = document.getElementById('image-preview');
    const removeBtn = document.getElementById('remove-image-preview');
    document.addEventListener('DOMContentLoaded', function() {


    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImagedata.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewImagedata.src = '';
            previewContainer.style.display = 'none';
        }
    });

    removeBtn.addEventListener('click', function() {
        imageInput.value = '';
        previewImagedata.src = '';
        previewContainer.style.display = 'none';
    });
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.querySelector("#phone");
        var iti = window.intlTelInput(input, {
            initialCountry: "gb",
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
        });

        function updateFullNumber() {
            const countryData = iti.getSelectedCountryData();
            const dialCode = countryData.dialCode;

            // Remove any existing +countrycode and spaces
            let number = input.value.replace(/^\+\d+\s*/, '').trim();

            // Now format with the new one
            const formattedNumber = `+${dialCode} ${number}`;
            input.value = formattedNumber;
        }

        input.addEventListener("countrychange", updateFullNumber);
        input.addEventListener("blur", updateFullNumber);
    });
</script>
<script>
    document.getElementById('phone').addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    </script>
<script>
    const validationMessages = {
        required: "{{ __('messages.validation_field_required') }}",
        submitting: "{{ __('messages.submitting') }}",
        submitmessage: "{{ __('messages.lbl_submit') }}",
        incidence_created_success: "{{ __('messages.created_successfully') }}",
        fields: {
            title: "{{ __('messages.field_title') }}",
            emailid: "{{ __('messages.field_emailid') }}",
            phone_number: "{{ __('messages.field_phone_number') }}",
            description: "{{ __('messages.field_description') }}",
            // Add more if needed
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const description = document.getElementById('description');
        const count = document.getElementById('description-count');
        const maxLength = 250;

        description.addEventListener('input', function() {
            let currentLength = description.value.length;
            if (currentLength > maxLength) {
                description.value = description.value.substring(0, maxLength);
                currentLength = maxLength;
            }
            count.textContent = `${currentLength} / ${maxLength}`;
        });
    });

    document.getElementById('incidence-form').addEventListener('submit', function(e)
    {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        let isValid = true;

        const submitBtn = document.getElementById('submit-btn');
        const spinner = document.getElementById('submit-spinner');
        const text = document.getElementById('submit-text');

        // Disable button and show spinner
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        text.textContent = validationMessages.submitting;

        // Clear previous errors
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

        // Function to set error message
        function setError(field, message) {
            // alert("message" + message);
            // alert("field" + field);
            document.getElementById(`error-${field}`).textContent = message;
            isValid = false;
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            text.textContent = validationMessages.submitmessage;
        }

        // Required Fields Validation
        const requiredFields = ['title', 'email', 'phone', 'description'];

       requiredFields.forEach(field => {
            const value = formData.get(field);
            if (!value || value.trim() === '') {
                // Get the translated field label, fallback to formatted key if not found
                const formattedField = validationMessages.fields[field] ||
                                    field.replace(/_/g, ' ')
                                            .replace(/\b\w/g, c => c.toUpperCase());

                // Replace :field in the message template
                const message = validationMessages.required.replace(':field', formattedField);

                setError(field, message);
            }
        });
        // Email Validation
        const email = formData.get('email');
        if (email && !/^[\w.-]+@[a-zA-Z\d.-]+\.[a-zA-Z]{2,}$/.test(email)) {
            setError('email', "Please enter a valid email address.");
        }
        // Phone Number Validation
        const phone = formData.get('phone');
        if (phone && !/^\+?[0-9\s-]+$/.test(phone)) {
            setError('phone', "Please enter a valid phone number.");
        }

        // Description Validation (enforce limit)
        const description = formData.get('description');
        if (description && description.length > 250) {
            setError('description', "Description cannot exceed 250 characters.");
        }

        // Stop submission if there are errors
        if (!isValid) return;

        // Submit form via AJAX
        // Ensure image file is included in formData if selected
        const imageInput = document.getElementById('image-input');
        // If a file is selected, append it to formData
        if (imageInput && imageInput.files.length > 0) {
            formData.set('file_url', imageInput.files[0]);
        }

        console.log("Form Data:", Array.from(formData.entries()));

        fetch(form.action, {
            method: 'POST',
            headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData,
        })
        .then(async response => {
            let data;
            try {
                data = await response.json();
            } catch (e) {
                throw new Error('Invalid JSON response');
            }
            if (response.ok && data && data.success) {
                window.successSnackbar(validationMessages.incidence_created_success);
                let activeTab = "all-incidences"; // Default active tab
                dataTableReload(activeTab);
                document.getElementById('incidence-form').reset();
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
                previewImagedata.src = '';
                previewContainer.style.display = 'none';
                removeBtn.style.display = 'none';
                text.textContent = validationMessages.submitmessage;
            } else {
                window.errorSnackbar(data && data.message ? data.message : "An error occurred while creating the incidence.");
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
                text.textContent = validationMessages.submitmessage;
            }
        })
        .catch(error => {
            console.error("Error:", error);
            window.errorSnackbar("Something went wrong!");
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            text.textContent = validationMessages.submitmessage;
        });
    });

    function previewImage(event, id, value = null) {
        const input = value ;

        fetch("{{ route('changestatus') }}",
            {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    'id': id,
                    'incident_type': input
                }),
            })
            .then(response => response.json())
            .then(data => {
                // window.successSnackbar(data.message);
                if (data.message) {
                    window.successSnackbar(data.message);
                    // Reload the page to reflect the status change
                    location.reload();
                } else {
                    console.log("Error :", data.message);
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                window.errorSnackbar("Something went wrong!");
            });
    }
</script>
@endpush
