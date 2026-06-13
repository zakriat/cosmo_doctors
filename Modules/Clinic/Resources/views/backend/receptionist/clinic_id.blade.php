<div class="d-flex gap-3 align-items-center">
  <img src="{{optional(optional($data->receptionist)->clinics)->file_url ?? '--' }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">
    <h6 class="m-0">{{ optional(optional($data->receptionist)->clinics)->name ?? '--' }}</h6>
    <span>{{ optional(optional($data->receptionist)->clinics)->email ?? '--' }}</span>
  </div>
</div>