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

                        <button type="submit" name="action" value="filter" class="btn btn-primary"
                            data-bs-toggle="tooltip"
                            data-bs-title="{{ __('messages.submit_date_filter') }}">{{ __('dashboard.lbl_submit') }}</button>

                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="card dashboard-cards appointments"
                        style="background-image: url({{ asset('img/dashboard/appointment.svg') }})">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-end mb-1">
                                <i class="fa-solid fa-circle-info" data-bs-toggle="tooltip"
                                    data-bs-title="Total Appointment"></i>
                            </div>
                            <h3 class="mb-2">5</h3>
                            <p class="mb-0">Total Appointment</p>
                        </div>
                    </div>

                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card dashboard-cards appointments"
                        style="background-image: url({{ asset('img/dashboard/appointment.svg') }})">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-end mb-1">
                                <i class="fa-solid fa-circle-info" data-bs-toggle="tooltip"
                                    data-bs-title="Total Service"></i>
                            </div>
                            <h3 class="mb-2">5</h3>
                            <p class="mb-0">Total Service</p>
                        </div>
                    </div>

                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card dashboard-cards appointments"
                        style="background-image: url({{ asset('img/dashboard/appointment.svg') }})">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-end mb-1">
                                <i class="fa-solid fa-circle-info" data-bs-toggle="tooltip"
                                    data-bs-title="Total Patients"></i>
                            </div>
                            <h3 class="mb-2">5</h3>
                            <p class="mb-0">Total Patients</p>
                        </div>
                    </div>

                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card dashboard-cards appointments"
                        style="background-image: url({{ asset('img/dashboard/appointment.svg') }})">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-end mb-1">
                                <i class="fa-solid fa-circle-info" data-bs-toggle="tooltip"
                                    data-bs-title="Total Revenue"></i>
                            </div>
                            <h3 class="mb-2">5</h3>
                            <p class="mb-0">Total Revenue</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class=" d-flex justify-content-between  flex-wrap">
                        <h4 class="card-title">Total Appointment </h4>
                    </div>
                    <div id="chart-02"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <h4 class="card-title">Upcomming Booking </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive border rounded">
                        <table class="table table-lg m-0">
                            <thead>
                                <tr class="text-white bg-primary">
                                    <th scope="col">{{ __('messages.service') }}</th>
                                    <th scope="col">{{ __('messages.total_count') }}</th>
                                    <th scope="col">{{ __('messages.total_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td scope="col">{{ __('messages.service') }}</td>
                                        <td scope="col">{{ __('messages.total_count') }}</td>
                                        <td scope="col">{{ __('messages.total_amount') }}</td>
                                    </tr>
                            </tbody>
                        </table>
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
