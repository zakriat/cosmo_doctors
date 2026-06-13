<div class="card-body m-2">
    <div class="table-responsive rounded">
        <table class="table table-lg m-0" id="service_list_table">
            <thead>

                <tr class="text-white">
                    <th>{{ __('appointment.sr_no') }}</th>
                    <th>{{ __('appointment.lbl_services') }}</th>
                    <th>{{ __('service.discount') }}</th>
                    <th>{{ __('product.quantity') }}</th>
                    <th>{{ __('appointment.price') }}</th>
                    <th>{{ __('service.inclusive_tax') }}</th>
                    <th>{{ __('appointment.total') }}</th>
                    @if ($status == 1)
                        <th>{{ __('appointment.lbl_action') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>

                @foreach ($data['billingItem'] as $index => $iteam)
                <tr data-service-id="{{ $iteam['id'] }}">

                        <td>
                            <h6 class="text-primary">
                                {{ $index + 1 }}
                            </h6>

                        </td>
                        <td>
                            <h6 class="text-primary">
                                {{ $iteam['item_name'] }}
                            </h6>

                        </td>
                        <td>
                            <p class="m-0">
                                @if ($iteam['discount_value'] == null)
                                    -
                                @else
                                    @if ($iteam['discount_type'] == 'fixed')
                                        <span>{{ Currency::format($iteam['discount_value']) }}</span>
                                    @else
                                        <span>({{ $iteam['discount_value'] }}%) </span>
                                    @endif
                                @endif
                            </p>
                        </td>
                        <td>
                            {{ $iteam['quantity'] }}
                        </td>
                        <td>
                            {{ Currency::format($iteam['service_amount']) }}
                        </td>
                        <td>
                            {{ Currency::format($iteam['inclusive_tax_amount']) }}
                        </td>
                        <td>
                            {{ Currency::format($iteam['total_amount']) }}
                        </td>
                        @if ($status == 1 )



                            <td class="action">
                                <div class="d-flex align-items-center gap-3">

                                    @if( $index  !==0)

                                    <button type="button" class="btn text-danger p-0 fs-5"
                                        onclick="destroyServiceData({{ $iteam['id'] }}, 'Are you sure you want to delete it?')"
                                        data-bs-toggle="tooltip">
                                        <i class="ph ph-trash"></i>
                                    </button>

                                    @endif
                                </div>
                            </td>

                        @endif
                    </tr>
                @endforeach


                @if (count($data['billingItem']) <= 0)
                    <tr>
                        <td colspan="7">
                            <div class="my-1 text-danger text-center">{{ __('appointment.no_service_found') }}
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>

        </table>
        <div id="service-error-message" class="alert alert-danger mt-2 d-none"></div>

    </div>
</div>

@push('after-scripts')
    <script>


   // Add this function to check service count
   function checkServiceCount() {
            // Get all billing items that have actual services (those with an ID)
            const serviceRows = document.querySelectorAll('#service_list_table tbody tr[data-service-id]');
            return serviceRows.length;
        }

        function showError(message) {
        const errorDiv = document.getElementById('service-error-message');
        errorDiv.textContent = message;
        errorDiv.classList.remove('d-none');
        setTimeout(() => {
            errorDiv.classList.add('d-none');
        }, 3000);


    }
        function destroyServiceData(id) {
            const serviceCount = checkServiceCount();
            console.log(serviceCount)
            if (serviceCount <= 1) {
            showError('At least one service is required. Cannot delete the All service.');
                    return;
                }


            var baseUrl = '{{ url('/') }}';

            $.ajax({
                url: baseUrl + '/app/billing-record/delete-billing-item/' + id,
                method: 'GET',

                success: function(response) {

                    if (response) {


                        document.getElementById('Service_list').innerHTML = ''

                        document.getElementById('Service_list').innerHTML = response.html;

                        $('#service_id').val(null).trigger('change');
                        $('#charges').val('');
                        $('#quantity').val('');
                        $('#total').val('');
                        $('#discount_value').val('');
                        $('#discount_type').val('');
                        $('#service_amount').text(currencyFormat(response.service_details.service_total));
                        $('#tax_amount').text(currencyFormat(response.service_details.total_tax));
                        $('#total_payable_amount').text(currencyFormat(response.service_details.total_amount));
                        $('#total_service_amount').val(response.service_details.service_total);
                        $('#total_tax_amount').val(response.service_details.total_tax);
                        $('#total_amount').val(response.service_details.total_amount);
                        $('#final_total_amount').val(response.service_details.total_amount);
                        
                        // Check if discount is enabled and recalculate based on new service total
                        const isDiscountEnabled = document.getElementById('category-discount').checked;
                        if (isDiscountEnabled) {
                            // Recalculate discount with current form values
                            updateDiscount();
                        } else {
                            // No discount enabled, just update the display
                            $('#discount_amount').text(currencyFormat(0));
                        }
                        
                        // After updating the service list, recheck the count
                        const updatedCount = checkServiceCount();


                        deleteButtons.forEach(btn => {
                            if (updatedCount <= 1) {
                                btn.setAttribute('disabled', 'disabled');
                                btn.setAttribute('title', '{{ __("appointment.cannot_delete_last_service") }}');
                            } else {
                                btn.removeAttribute('disabled');
                                btn.removeAttribute('title');
                            }
                        });

                    } else {

                        console.error('Failed to fetch services.');
                    }
                },
                error: function(error) {
                    console.error('Error fetching services:', error);
                }
            });

        }
    </script>
@endpush
