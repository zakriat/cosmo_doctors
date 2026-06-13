<div class="d-flex gap-3 align-items-center">
  <img src="{{ optional($doctorData->user)->profile_image ?? default_user_avatar() }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">
    <h6 class="m-0">{{ optional($doctorData->user)->full_name ?? default_user_name() }}</h6>
    <span>{{ optional($doctorData->user)->email ?? '--' }}</span>
  </div>
</div>
