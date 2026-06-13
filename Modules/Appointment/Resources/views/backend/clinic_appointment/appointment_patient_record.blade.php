@extends('backend.layouts.app')

@section('title')  {{ __($module_title) }} @endsection

@section('content')
<div class="card">
    <div class="card-body">
        {{-- <div class="d-flex justify-content-end">
            <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#body-chart-offcanvas" aria-controls="body-chart-offcanvas">{{__('Body Chart')}}</button>
        </div> --}}
        <div data-render="app">

            <appointment_patient_records encounter_id="{{$encounter_id}}" patient_id="{{$patient_id}}" appointment_id="{{$appointment_id}}"></appointment_patient_records>
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
