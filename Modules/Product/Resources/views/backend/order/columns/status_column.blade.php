@if ($data->delivery_status == 'delivered')
    <span class="badge bg-primary-subtle rounded-pill text-capitalize">
        {{ $data->delivery_status }}
    </span>
@elseif($data->delivery_status == 'cancelled')
    <span class="badge bg-danger-subtle rounded-pill text-capitalize">
        {{ Str::title(Str::replace('_', ' ', $data->delivery_status)) }}
    </span>
@else
    <span class="badge bg-info-subtle rounded-pill text-capitalize">
        {{ Str::title(Str::replace('_', ' ', $data->delivery_status))  }}
    </span>
@endif
