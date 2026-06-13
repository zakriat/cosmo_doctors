@php
$clinicName = optional(optional($data->nurse)->clinics)->name ?? '--';
@endphp

<span class="badge bg-primary-subtle text-primary">{{ $clinicName }}</span>