<div class="d-flex gap-3 align-items-center">
  <img src="{{ optional($data->doctor)->profile_image ?? default_user_avatar() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">
    <h6 class="m-0">
      @if($data->doctor)
        {{-- <a href="{{ route('backend.doctor.appointment.doctor.detail', ['id' => $data->doctor->id]) }}" 
           class="text-decoration-none text-primary" 
           style="cursor: pointer;"
           title="View Doctor Details"> --}}
          {{ $data->doctor->full_name }}
        </a>
      @else
        {{ default_user_name() }}
      @endif
    </h6>
    <span>{{ optional($data->doctor)->email ?? '--' }}</span>
  </div>
</div>
 
