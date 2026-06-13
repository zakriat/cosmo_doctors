@if ($data->payment_status == 'unpaid')
    <span class="badge bg-danger-subtle rounded-pill text-capitalize">
        {{ $data->payment_status }}
    </span>
@else
    <span class="badge bg-primary-subtle rounded-pill text-capitalize">
        {{ $data->payment_status }}
    </span>
@endif
