@if(isset($data->appointmenttransaction))
    @if($data->appointmenttransaction->payment_status != 1)
    <select name="branch_for" class="select2 change-select" data-token="{{csrf_token()}}"
        data-url="{{route('backend.appointment.updatePaymentStatus', ['id' => $data->id, 'action_type' => 'update-payment-status'])}}"
        style="width: 100%;">
        @php
            $localizedPaymentStatuses = getLocalizedPaymentStatuses();
        @endphp
        @foreach ($localizedPaymentStatuses as $status)
            @if($status['value'] != 5) {{-- Exclude advance_paid from dropdown as it's handled separately --}}
                <option value="{{$status['value']}}" {{$data->appointmenttransaction->payment_status == $status['value'] ? 'selected' : ''}}>
                    {{$status['name']}}
                </option>
            @endif
        @endforeach
    </select>
    @else
        <span class="text-capitalize badge bg-soft-info p-3">{{ getLocalizedPaymentStatus($data->appointmenttransaction->payment_status) }}</span>
    @endif 
@else
<span class="text-capitalize badge bg-soft-danger p-3">{{ getLocalizedPaymentStatus(2) }}</span>
@endif 