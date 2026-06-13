<div id="app">
    <div data-render="app">
        <vue-custom-tabs :tabs="{{ json_encode($data) }}" :appointment-id="{{ $appointment_id }}"></vue-custom-tabs>
    </div>
</div>

@push('after-styles')
    <link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush
