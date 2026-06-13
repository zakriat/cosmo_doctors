@if ($data->email_verified_at) 

<span class="badge booking-status bg-success-subtle p-2">{{__('employee.msg_verified')}} </span>
                  
@else

 <span href="{{route("backend.doctor.verify-doctor", $data->id)}}" id="delete-{{$module_name}}-{{$data->id}}" class="text-capitalize badge bg-danger-subtle p-2" data-type="ajax" data-method="GET" data-token="{{csrf_token()}}" data-bs-toggle="tooltip" title="{{__('Verify')}}" data-confirm="{{ __('messages.verify_account') }}"> Verify </span>

@endif