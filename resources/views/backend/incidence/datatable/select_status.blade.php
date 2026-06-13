@if($data->status != 'completed' && $data->status != 'check_in' && $data->status != 'checkout' && $data->status != 'cancelled')
<select name="branch_for" class="select2 change-select" data-token="{{csrf_token()}}" data-url="{{route('backend.appointments.updateStatus', ['id' => $data->id, 'action_type' => 'update-status'])}}" style="width: 100%;">
  @foreach ($status as $key => $value )
   
    <option value="{{ $value->name }}" {{$data->status == $value->name ? 'selected' : ''}} >{{$value['value']}}</option>
  @endforeach
</select>

@elseif($data->status == 'cancelled')

<span class="text-capitalize badge bg-danger-subtle p-2"> {{ str_replace('_', ' ', $data->status) }}</span>
@elseif($data->status == 'check_in')

<span class="text-capitalize badge bg-info-subtle p-2"> {{ str_replace('_', ' ', $data->status) }}</span>
@else

<span class="text-capitalize badge bg-success-subtle p-2"> Complete</span>
@endif