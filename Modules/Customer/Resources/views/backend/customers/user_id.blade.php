<div class="d-flex gap-3 align-items-center">
  <img src="{{ $data->profile_image ?? default_user_avatar() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">
    <a href="{{ route('backend.customers.patient_detail', ['id' => $data->id]) }}" 
       >
       <h6 class="m-0">{{ $data->full_name ?? default_user_name() }}</h6>
      </a> 
   
    <span>{{ $data->email ?? '--' }}</span>
  </div>
</div>
