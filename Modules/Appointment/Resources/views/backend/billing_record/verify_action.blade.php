@if ($data->payment_status) 

<span class="badge booking-status bg-success-subtle p-2">{{__('appointment.paid')}} </span>

@elseif(optional(optional(optional($data->patientencounter)->appointmentdetail)->appointmenttransaction)->advance_payment_status)
    <span class="badge booking-status bg-success-subtle py-2 px-3">{{__('appointment.advance_paid')}} </span>        

@elseif(optional(optional(optional($data->patientencounter)->appointmentdetail)->appointmenttransaction) == null)
    <span class="badge booking-status bg-danger-subtle py-2 px-3">{{__('appointment.failed')}} </span>
@else

<span class="badge booking-status bg-danger-subtle py-2 px-3">{{__('messages.unpaid')}} </span>

@endif