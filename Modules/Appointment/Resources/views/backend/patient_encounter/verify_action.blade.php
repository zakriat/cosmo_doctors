@if ($data->status) 

<span class="badge booking-status bg-success-subtle p-2">{{__('appointment.open')}} </span>
                  
@else

<span class="badge booking-status bg-danger-subtle p-2">{{__('appointment.close')}} </span>

@endif