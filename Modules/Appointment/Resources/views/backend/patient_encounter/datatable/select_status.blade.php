@if($data->status != 'checkout')
<select name="branch_for" class="select2 change-select" data-token="{{csrf_token()}}" data-url="{{route('backend.appointment.updateStatus', ['id' => $data->id, 'action_type' => 'update-status'])}}" style="width: 100%;">
  @foreach ($status as $key => $value )
   
    <option value="{{$key}}" {{$data->status == $key ? 'selected' : ''}} >{{ __('appointment.' . $key) }}</option>
  @endforeach
</select>
@else

<span class="text-capitalize badge bg-soft-success p-3"> {{ __('appointment.' . $data->status) }}</span>
@endif