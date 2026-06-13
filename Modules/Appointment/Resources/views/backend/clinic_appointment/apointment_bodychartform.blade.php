@extends('backend.layouts.app')

@section('title') {{ __($module_title) }} @endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div data-render="app">
            @if(isset($bodychart_id))

            <body-chart-offcanvas appointment_id="{{$appointment_id}}" encounter_id="{{$encounter_id}}" patient_id="{{$patient_id}}" bodychart_id="{{$bodychart_id}}"></body-chart-offcanvas>
            @else
            <body-chart-offcanvas appointment_id="{{$appointment_id}}" encounter_id="{{$encounter_id}}" patient_id="{{$patient_id}}"></body-chart-offcanvas>

            @endif
        </div>
    </div>
</div>

@endsection

@push ('after-styles')
<link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">
<!-- DataTables Core and Extensions -->
<link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
@endpush

@push ('after-scripts')
<script src="{{ mix('modules/appointment/script.js') }}"></script>
<script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
<script src="{{ asset('js/form-modal/index.js') }}" defer></script>

@endpush
