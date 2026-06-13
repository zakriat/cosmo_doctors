@extends('backend.layouts.app', ['isBanner' => false])

@section('title')
{{ 'Dashboard' }}
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-50">
    <div>
        <div class="d-flex align-items-center pb-3 pt-3">
            <span class="head-title fw-500">{{__('menu.main')}}</span>
            <svg class="mx-2" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                <g clip-path="url(#clip0_2007_2051)">
                    <path d="M2.625 2.25L6.375 6L2.625 9.75" stroke="#828A90" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M6.375 2.25L10.125 6L6.375 9.75" stroke="#828A90" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </g>
                <defs>
                    <clipPath id="clip0_2007_2051">
                        <rect width="12" height="12" fill="white" />
                    </clipPath>
                </defs>
            </svg>
            <span class="head-title fw-500 h6 mb-0">{{__('dashboard.title')}}</span>
        </div>
        <div class="user-info">
            <h1 class="fs-37">
                <span class="left-text text-capitalize fw-light">{{greeting()}}</span>
                <span class="right-text text-capitalize">{{$current_user}}</span>
            </h1>

        </div>
    </div>
    <div>

    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">{{ __('dashboard.lbl_performance') }}</h3>
            <!-- <div class="d-flex  align-items-center">
                    <form action="{{ route('backend.home') }}" class="d-flex align-items-center gap-2">

                        <button type="submit" name="action" value="filter" class="btn btn-primary"
                            data-bs-toggle="tooltip"
                            data-bs-title="{{ __('messages.submit_date_filter') }}">{{ __('dashboard.lbl_submit') }}</button>

                    </form>
                </div> -->
        </div>
        <div class="row">
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('backend.appointments.index') }}" class="text-secondary">
                    <div class="card dashboard-cards appointments">
                        <div class="card-body">
                            <p class="mb-0">{{__('appointment.total_number_appointment')}}  </p>
                            <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                                <h2 class="mb-0">{{ $data['total_appointments']}}</h2>
                                <img src="{{ asset('img/dashboard/appointment.png') }}" alt="image">
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('backend.customers.index') }}" class="text-secondary">
                    <div class="card dashboard-cards appointments">
                        <div class="card-body">
                            <p class="mb-0">{{__('appointment.total_number_of_patients')}} </p>
                            <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                                <h2 class="mb-0">{{ $data['total_patient']}}</h2>
                                <img src="{{ asset('img/dashboard/users.png') }}" alt="image">
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('backend.services.index') }}" class="text-secondary">
                    <div class="card dashboard-cards appointments">
                        <div class="card-body">
                            <p class="mb-0">{{__('appointment.total_number_of_services')}}</p>
                            <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                                <h2 class="mb-0">{{$data['total_service_count']}}</h2>
                                <img src="{{ asset('img/dashboard/services.png') }}" alt="image">
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card dashboard-cards appointments">
                    <div class="card-body">
                        <p class="mb-0">{{__('appointment.total_earning')}} </p>
                        <div class="d-flex align-items-center justify-content-between gap-3 mt-5">
                            <h2 class="mb-0">{{ Currency::format($data['total_earning']) }}</h2>
                            <img src="{{ asset('img/dashboard/revenue.png') }}" alt="image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div id='calendar'></div>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.40.0/apexcharts.min.css" integrity="sha512-tJYqW5NWrT0JEkWYxrI4IK2jvT7PAiOwElIGTjALSyr8ZrilUQf+gjw2z6woWGSZqeXASyBXUr+WbtqiQgxUYg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
@push('after-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.40.0/apexcharts.min.js" integrity="sha512-Kr1p/vGF2i84dZQTkoYZ2do8xHRaiqIa7ysnDugwoOcG0SbIx98erNekP/qms/hBDiBxj336//77d0dv53Jmew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap',
            events: function(info, successCallback, failureCallback) {
                $.ajax({
                    url: "{{ route('backend.get-appointments') }}",
                    dataType: 'json',
                    success: function(response) {
                        if (response.status && response.data && Array.isArray(response.data)) {
                            var events = response.data.map(function(appointment) {
                                return {
                                    id: appointment.id,
                                    name: appointment.user.first_name,
                                    title: appointment.service_name,
                                    start: appointment.start_date_time,
                                };
                            });
                            successCallback(events);
                        } else {
                            failureCallback("Invalid data format.");
                        }
                    },
                    error: function(xhr, status, error) {
                        failureCallback(error);
                    }
                });
            },
            eventColor: 'rgb(19, 193, 240)',
            textColor: '#fff',
            timeFormat: {
                agenda: 'H(:mm)' //h:mm{ - h:mm}'
            },
            eventDidMount: function(info) {
                $(info.el).tooltip({
                    title: info.event.extendedProps.name + ' - ' + info.event.title,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            },
            timeZone: 'UTC',
            eventClick: function(info) {
                var id = info.event.id;
                var url = "{{ URL::to('app/appointments/clinicAppointmentDetail') }}/" + id;
                window.location.replace(url);
            },
            timeFormat: 'H(:mm)',
            eventTimeFormat: { // Set the format for event times
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short' // Optionally, you can set meridiem to false to display 24-hour format
            },
            buttonText: {
                today: 'Today' // Capitalize "Today"
            },
        });
        calendar.render();
    });
</script>


@endpush