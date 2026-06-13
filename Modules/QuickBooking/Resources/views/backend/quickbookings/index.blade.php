@extends('backend.layouts.quick-booking')

@section('title') Quick Booking @endsection

@push('after-styles')
    <link rel="stylesheet" href='{{ mix("modules/quickbooking/style.css") }}'>
@endpush


@php
    $multiVendorEnabled = multiVendor();
   

@endphp

@section('content')
  <div class="container">
    <div class="row justify-content-center align-items-center vh-100">
      <div class="col">
      <quick-booking :user_id="{{ $id }}"></quick-booking>
      </div>
    </div>
  </div>
@endsection

@push ('after-scripts')
<script>
      window.multiVendorEnabled = @json($multiVendorEnabled);
    </script>

<script src="{{ mix("modules/quickbooking/script.js") }}"></script>
@endpush
