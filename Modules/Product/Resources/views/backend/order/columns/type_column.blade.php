@if ($data->orderGroup->type == 'booking')
    <span class="badge bg-danger-subtle rounded-pill text-capitalize">
         Booking
    </span>
@else
    <span class="badge bg-primary-subtle rounded-pill text-capitalize">
       Online
    </span>
@endif
