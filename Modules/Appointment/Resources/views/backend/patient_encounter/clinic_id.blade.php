<div class="d-flex gap-3 align-items-center">
  <img src="{{ optional($data->clinic)->file_url ?? '--' }}" alt="avatar" class="avatar avatar-40 rounded-pill">
  <div class="text-start">
    <h6 class="m-0">{{ optional($data->clinic)->name ?? '--' }}</h6>
    <span>{{ optional($data->clinic)->email ?? '--' }}</span>
  </div>
</div>