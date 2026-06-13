@if(isset($data->appointmenttransaction))
    @if($data->appointmenttransaction->payment_status == 1)
        <span class="text-capitalize badge bg-success-subtle p-2">{{ getLocalizedPaymentStatus($data->appointmenttransaction->payment_status) }}</span>
    @elseif(optional($data->appointmenttransaction)->payment_status == 0 && optional($data->appointmenttransaction)->advance_payment_status == 1)
        <span class="text-capitalize badge bg-info-subtle p-2">{{ getLocalizedPaymentStatus(5) }}</span>
    @else
        <select name="branch_for" class="select2 change-select" data-token="{{csrf_token()}}"
            data-url="{{route('backend.appointments.updatePaymentStatus', ['id' => $data->id, 'action_type' => 'update-payment-status'])}}"
            style="width: 100%;" {{ $data->status !== 'checkout' ? 'disabled' : '' }}>
            @php
                $localizedPaymentStatuses = getLocalizedPaymentStatuses();
            @endphp
            @foreach ($localizedPaymentStatuses as $status)
                @if($status['value'] != 5) {{-- Exclude advance_paid from dropdown as it's handled separately --}}
                    <option value="{{$status['value']}}" {{optional($data->appointmenttransaction)->payment_status == $status['value'] ? 'selected' : ''}}>
                        {{$status['name']}}
                    </option>
                @endif
            @endforeach
        </select>
    @endif 
@else
<span class="text-capitalize badge bg-danger-subtle p-3">{{ getLocalizedPaymentStatus(2) }}</span>
@endif 