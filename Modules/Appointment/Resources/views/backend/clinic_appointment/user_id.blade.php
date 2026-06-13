@if(isset($data->user) || $data->user || $data->user !== null)
<a href="{{ route('backend.customers.patient_detail', optional($data->user)->id) }}" class="text-reset">
<div class="d-flex gap-3 align-items-center">
  <img src="{{ optional($data->user)->profile_image ?? default_user_avatar() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">
    <h6 class="m-0">{{ optional($data->user)->full_name ?? default_user_name() }}</h6>
    <span>{{ optional($data->user)->email ?? '--' }}</span>
  </div>
</div>
</a>
@else
<p>-</p>
@endif
