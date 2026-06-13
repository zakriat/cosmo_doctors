@if($data->status != 'completed')
<select name="branch_for" class="select2 change-select" data-token="{{csrf_token()}}" data-url="{{route('backend.appointments.updateStatus', ['id' => $data->id, 'action_type' => 'update-status'])}}" style="width: 100%;">
  @foreach ($appointment_status as $key => $value )

    <option value="{{$value->name}}" {{$data->status == $value->name ? 'selected' : ''}} >{{ __('appointment.' . $value->name) }}</option>
  @endforeach
</select>
@else

<span class="text-capitalize badge bg-soft-success p-3"> {{ __('appointment.' . $data->status) }}</span>
@endif
