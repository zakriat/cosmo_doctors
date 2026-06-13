@extends('backend.layouts.app')



@push('after-styles')
<link rel="stylesheet" href="{{ mix('modules/constant/style.css') }}">
@endpush
@section('content')
<div class="card">
    <div class="card-body">

    </div>
    <x-backend.section-header>
        <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">Vital</x-buttons.offcanvas>
    </x-backend.section-header>
</div>
<div data-render="app">
  <vitalform-offcanvas></vitalform-offcanvas>
</div>
<div class="card">

</div>
@endsection

@push('after-styles')
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push('after-scripts')
<script src="{{ mix('modules/customer/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>


<!-- DataTables Core and Extensions -->
<script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>

@endpush
