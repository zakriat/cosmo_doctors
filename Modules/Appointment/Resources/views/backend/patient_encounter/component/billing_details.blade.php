<div class="modal modal-xl fade" id="generate_invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <form id="billingForm">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ __('clinic.lbl_generate_invoice') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="billing_encounter_id" value="{{ $data['id'] }}" />
                    <input type="hidden" id="final_total_amount" value="">

                    <p class="d-inline-flex gap-1">
                   <div class="d-flex align-items-center justify-content-between gap-3">
                        <h4>
                            {{ __('appointment.add_item_in_billing') }}
                        </h4>
                        <button class="btn btn-primary" type="button" id="toggleButton" data-bs-toggle="collapse"
                            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                            {{ __('appointment.add_item') }}
                        </button>
                    </div>
                    </p>
                    <div class="collapse" id="collapseExample">
                        <div class="card card-body" id="extra-service-list">

                            @include('appointment::backend.patient_encounter.component.add_service', [
                                'encounter_id' => $data['id'],
                                'billing_id' => $data['billingrecord']['id'],
                                'service_id' => $data['billingrecord']['service_id'],
                            ])

                        </div>
                    </div>

                    <div id="Service_list">

                        @include('appointment::backend.patient_encounter.component.service_list', [
                            'data' => $data['billingrecord'],
                            'status' => $data['status'],
                        ])
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center form-control">
                                    <label class="form-label m-0"
                                        for="category-discount">{{ __('service.discount') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="final_discount" id="category-discount"
                                            type="checkbox"
                                            {{ old('final_discount', $data['final_discount'] ?? false) ? 'checked' : '' }}
                                            onchange="toggleDiscountSection()" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row d-none" id="final_discount_section" >
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label m-0"
                                        for="category-discount">{{ __('service.lbl_discount_type') }}
                                        <span class="text-danger">*</span></label>
                                    <select id="final_discount_type" name="final_discount_type"
                                        class="select2 form-select" placeholder="{{ __('service.lbl_discount_type') }}"
                                        data-filter="select" onchange="updateDiscount()">
                                        <option value="percentage"
                                            {{ old('final_discount_type', $data['final_discount_type'] ?? '') === 'percentage' ? 'selected' : '' }}>
                                            {{ __('appointment.percentage') }}
                                        </option>
                                        <option value="fixed"
                                            {{ old('final_discount_type', $data['final_discount_type'] ?? '') === 'fixed' ? 'selected' : '' }}>
                                            {{ __('appointment.fixed') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('service.lbl_discount_value') }} <span
                                            class="text-danger">*</span> </label>
                                            <input type="number" name="final_discount_value" id="final_discount_value"
                            class="form-control" placeholder="{{ __('service.lbl_discount_value') }}" step="1.00"
                            value="{{ old('final_discount_value', $data['final_discount_value'] ?? 1) }}"

                            oninput="validateDiscount(this)"
                             required />

                             <span id="discount_amount_error" class="text-danger" ></span>
                                </div>
                            </div>
                        </div>


                        <div id="tax_list">

                            @include('appointment::backend.patient_encounter.component.tax_list', [
                                'data' => $data['billingrecord'],
                            ])

                        </div>

                        {{-- {{ dd($data['billingrecord'])  }} --}}

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center form-control">
                                        <label class="form-label m-0">{{ __('appointment.service_amount') }}</label>
                                        <div class="form-check" id="service_amount">

                                            <input type="hidden" id="total_service_amount"
                                                value={{ $data['billingrecord']['service_amount'] }}>

                                            {{ Currency::format($data['billingrecord']['service_amount']) ?? Currency::format(0) }}

                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center form-control d-none" id="discount_section">
                                        <label
                                            class="form-label m-0">{{ __('appointment.discount_amount') }}</label>
                                        <div class="form-check" id="discount_amount">

                                            <input type="hidden" id="final_discount_amount" value="">

                                            {{ Currency::format($data['billingrecord']['discount_amount']) ?? Currency::format(0) }}

                                        </div>
                                    </div>



                                    @if ($data['billingrecord']['final_discount'] > 0)
                                        <div class="d-flex justify-content-between align-items-center form-control d-none">
                                            <label
                                                class="form-label m-0">{{ __('appointment.discount_amount') }}</label>
                                            <div class="form-check" id="discount_amount">

                                                <input type="hidden" id="final_discount_amount" value="">

                                                {{ Currency::format($data['billingrecord']['service_amount']) ?? Currency::format(0) }}

                                            </div>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center form-control">
                                        <label class="form-label m-0">{{ __('appointment.tax') }}</label>
                                        <div class="form-check" id="tax_amount">

                                            <input type="hidden" id="total_tax_amount" value="">

                                            {{ Currency::format($data['billingrecord']['final_tax_amount']) ?? Currency::format(0) }}

                                        </div>
                                    </div>
                                    
                                    @if (optional(optional($data['appointmentdetail'])->appointmenttransaction)->advance_payment_status == 1)
                                        <div class="d-flex justify-content-between align-items-center form-control">
                                            <label class="form-label m-0">{{ __('service.advance_payment_amount') }} ({{ optional($data['appointmentdetail'])->advance_payment_amount ?? 0 }}%)</label>
                                            <div class="form-check" id="advance_payment_amount">
                                                <input type="hidden" id="advance_paid_amount" value="{{ optional($data['appointmentdetail'])->advance_paid_amount ?? 0 }}">
                                                {{ Currency::format(optional($data['appointmentdetail'])->advance_paid_amount ?? 0) }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center form-control">
                                        <label
                                            class="form-label m-0">{{ __('appointment.total_payable_amount') }}</label>
                                        <div class="form-check" id="total_payable_amount">

                                            <input type="hidden" id="total_amount" value="">

                                            {{ Currency::format($data['billingrecord']['final_total_amount']) ?? Currency::format(0) }}

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center form-control">
                                        <label class="form-label m-0">{{ __('clinic.lbl_payment_status') }}</label>
                                        <div class="form-check billing-detail-select">

                                            <select id="payment_status" name="payment_status"
                                                class="select2 form-select"
                                                placeholder="{{ __('service.lbl_discount_type') }}"
                                                data-filter="select">
                                                <option value="0"
                                                    {{ old('payment_status', $data['payment_status'] ?? '') === '0' ? 'selected' : '' }}>
                                                    {{ __('appointment.pending') }}
                                                </option>
                                                <option value="1"
                                                    {{ old('payment_status', $data['payment_status'] ?? '') === '1' ? 'selected' : '' }}>
                                                    {{ __('appointment.paid') }}
                                                </option>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('appointment.close') }}</button>
                    <button type="submit" id="save-button" class="btn btn-primary">{{ __('appointment.save') }}</button>
                </div>
            </div>

        </div>

    </form>

</div>

@push('after-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    width: '100%'
                });
            }

            const button = document.getElementById('toggleButton');
            const collapse = document.getElementById('collapseExample');

            collapse.addEventListener('shown.bs.collapse', () => {
                button.textContent = '{{ __('appointment.close') }}';
            });

            collapse.addEventListener('hidden.bs.collapse', () => {
                button.textContent = '{{ __('appointment.add_item') }}';
            });
        });

        function toggleDiscountSection() {
       const isChecked = document.getElementById('category-discount').checked;
       const discountSection = document.getElementById('final_discount_section');

        if (isChecked) {
            discountSection.classList.remove('d-none'); // Correct method
        } else {
            discountSection.classList.add('d-none'); // Correct method

            removeDiscountValue();

        }
     }

        document.addEventListener('DOMContentLoaded', () => {
            toggleDiscountSection();

            const billingId = "{{ $data['billingrecord']['id'] }}";
            getTotalAmount(billingId);


        });

        function removeDiscountValue() {

            $('#final_discount_value').val(0);
            $('#final_discount_type').val('percentage');

            $('#discount_section').addClass('d-none');

            updateDiscount()

        }





        function getTotalAmount(billingId) {
            var baseUrl = '{{ url('/') }}';

            var url = `${baseUrl}/app/billing-record/get-billing-record/${billingId}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {

                    $('#service_amount').text(currencyFormat(data.service_details.service_total));
                    $('#tax_amount').text(currencyFormat(data.service_details.total_tax));
                    $('#total_payable_amount').text(currencyFormat(data.service_details.total_amount));

                    $('#final_total_amount').val(data.service_details.total_amount);
                    $('#total_service_amount').val(data.service_details.service_total);
                    $('#total_tax_amount').val(data.service_details.total_tax);
                    $('#total_amount').val(data.service_details.total_amount);
                    $('#discount_amount').text(currencyFormat(data.service_details.final_discount_amount));

                    if(data.service_details.final_discount_amount >0){

                         $('#discount_section').removeClass('d-none');
                         $('#final_discount_section').removeClass('d-none');
                         document.getElementById('category-discount').checked = true;
                         $('#final_discount_value').val(data.service_details.final_discount_value);
                         $('#final_discount_type').val(data.service_details.final_discount_type);

                         $('#discount_amount').text(currencyFormat(data.service_details.final_discount_amount));

                         $('#final_total_amount').val(data.service_details.total_amount);

                      }

                })
                .catch(error => console.error('Error fetching total amount:', error));
        }


        function validateDiscount(input) {
    let maxAmount = 100;
    const discountType_value = document.querySelector('#final_discount_type').value;
    // Get numeric value from service_amount text content
    const totalServiceAmount = parseFloat(document.querySelector('#service_amount').innerText.replace(/[^0-9.-]+/g, ''));
    const discountValue = parseFloat(input.value);

    if (discountType_value === 'percentage' && input.value > maxAmount) {
        $('#discount_amount_error').text('{{ __('appointment.discount_value_less_than_100') }}');
        input.value = 0;
        updateDiscount();
    } else {
        if (discountValue > totalServiceAmount) {
            $('#discount_amount_error').text('{{ __('appointment.discount_amount_exceed_service_amount') }}');
            input.value = 0;
        } else {
            $('#discount_amount_error').text('');
        }
        updateDiscount();
    }
}


        function updateDiscount() {

            const discountValue = document.querySelector('input[name="final_discount_value"]').value;
            const discountType = document.querySelector('#final_discount_type').value;



            if (discountType && discountValue) {

                calculateFinalAmount(discountValue, discountType);
            }

        }

        function calculateFinalAmount(discountValue, discountType) {
            const billingId = "{{ $data['billingrecord']['id'] }}"; // Replace with dynamic billing ID as needed
            const baseUrl = '{{ url('/') }}'; // Base URL of your application

            // Prepare the data to send in the POST request
            const data = {
                discount_value: discountValue,
                discount_type: discountType,
                billing_id: billingId
            };

            // Make a POST request to the server to calculate the final amount
            fetch(`${baseUrl}/app/billing-record/calculate-discount-record`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Include CSRF token for Laravel
                    },
                    body: JSON.stringify(data), // Send the data as JSON
                })
                .then(response => response.json())
                .then(data => {

                    $('#total_service_amount').val(data.service_details.service_total);
                    $('#total_tax_amount').val(data.service_details.total_tax);
                    $('#total_amount').val(data.service_details.total_amount);
                    $('#final_total_amount').val(data.service_details.total_amount);

                    $('#service_amount').text(currencyFormat(data.service_details.service_total));
                    $('#tax_amount').text(currencyFormat(data.service_details.total_tax));
                    $('#total_payable_amount').text(currencyFormat(data.service_details.total_amount));

                    $('#discount_amount').text(currencyFormat(data.service_details.final_discount_amount));

                    if(data.service_details.final_discount_amount >0){

                        $('#discount_section').removeClass('d-none');


                    }

                })
                .catch(error => {
                    console.error('Error fetching billing data:', error);
                });
        }


        $('#billingForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            const baseUrl = '{{ url('/') }}';
            const formData = {
                encount_id: $('#billing_encounter_id').val(),
                final_discount_value: $('#final_discount_value').val(),
                final_discount_type: $('#final_discount_type').val(),
                payment_status: $('#payment_status').val(),
                final_discount: $('#category-discount').is(':checked') ? 1 : 0,
                final_total_amount: $('#final_total_amount').val(),
                _token: '{{ csrf_token() }}' // Include CSRF token for Laravel
            };

            // Make AJAX request
            $.ajax({
                url: `${baseUrl}/app/billing-record/save-billing-detail-data`,
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#generate_invoice').modal('hide');

                    window.location.reload();

                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseJSON || xhr.responseText || error);
                }
            });
        });
    </script>
@endpush
