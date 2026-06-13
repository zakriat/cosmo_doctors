@extends('backend.layouts.app', ['isBanner' => false])

@section('title')
    {{ 'Dashboard' }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>{{ __('dashboard.lbl_performance') }}</h3>
                <div class="d-flex  align-items-center">
                    <form action="{{ route('backend.home') }}" class="d-flex align-items-center gap-2">
                        {{-- <div class="form-group my-0 ms-3">
                            <input type="text" name="date_range" value="{{ $date_range }}"
                                class="form-control dashboard-date-range" placeholder="24 may 2023 to 25 June 2023"
                                readonly="readonly">
                        </div> --}}
                        <button type="submit" name="action" value="filter" class="btn btn-primary"
                            data-bs-toggle="tooltip"
                            data-bs-title="{{ __('messages.submit_date_filter') }}">{{ __('dashboard.lbl_submit') }}</button>
                        {{-- <button type="submit" name="action" value="reset" class="btn btn-secondary btn-icon"
            data-bs-toggle="tooltip" data-bs-title="Reset Filter"><i class="fa-solid fa-clock-rotate-left"></i></button>
          --}}
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-2">
                    <div class="card dashboard-cards appointments"
                        style="background-image: url({{ asset('img/dashboard/appointment.svg') }})">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-end mb-1">
                                <i class="fa-solid fa-circle-info" data-bs-toggle="tooltip"
                                    data-bs-title="{{ __('messages.total_appointment_count') }}"></i>
                            </div>
                            <h3 class="mb-2">{{ $data['total_appointments'] }}</h3>
                            <p class="mb-0">{{ __('dashboard.lbl_appointment') }}</p>
                        </div>
                    </div>
                   
                </div>
                <div class="col-sm-6 col-lg-2">
                    <div class="card dashboard-cards appointments"
                        style="background-image: url({{ asset('img/dashboard/appointment.svg') }})">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-end mb-1">
                                <i class="fa-solid fa-circle-info" data-bs-toggle="tooltip"
                                    data-bs-title="{{ __('messages.total_appointment_count') }}"></i>
                            </div>
                            <h3 class="mb-2">{{ $data['total_service'] }}</h3>
                            <p class="mb-0">{{ __('dashboard.service') }}</p>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
       
    </div>
@endsection

@push('after-styles')
    <style>
        #chart-01 {
            height: 28.5rem;
        }

        #chart-02 {
            height: 22.5rem;
        }

        .list-group {
            --bs-list-group-item-padding-y: 1.5rem;
            --bs-list-group-color: inherit !important;
        }

        .date-calender {
            display: flex;
            justify-content: space-between;
        }

        .date-calender .date {
            width: 12%;
            display: flex;
            align-items: center;
            flex-direction: column
        }

        .upcoming-appointments {
            min-height: 28rem;
            max-height: 28rem;
            overflow-y: scroll;


        }

        .iq-upcomming {
            display: flex !important;
            justify-content: center;
            align-items: center;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.40.0/apexcharts.min.css"
        integrity="sha512-tJYqW5NWrT0JEkWYxrI4IK2jvT7PAiOwElIGTjALSyr8ZrilUQf+gjw2z6woWGSZqeXASyBXUr+WbtqiQgxUYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.40.0/apexcharts.min.js"
        integrity="sha512-Kr1p/vGF2i84dZQTkoYZ2do8xHRaiqIa7ysnDugwoOcG0SbIx98erNekP/qms/hBDiBxj336//77d0dv53Jmew=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>


@endpush
