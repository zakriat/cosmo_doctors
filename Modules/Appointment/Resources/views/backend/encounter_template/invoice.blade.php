<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Certificate</title>
    <style>
        body {
            height: 98vh;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p {
            margin: 0;
        }

        .c-row {
            display: flex;
            flex-wrap: wrap;
        }

        .c-col-7 {
            flex: 0 0 auto;
            width: 58.33333333%;
        }

        .c-col-5 {
            flex: 0 0 auto;
            width: 41.66666667%;
        }

        ul {
            margin: 0;
            padding: 0;
            list-style-type: none;
        }

        ul li:not(:last-child) {
            margin-bottom: 5px;
        }

        .border {
            border: 1px solid #000;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
        }

        table {
            width: 100%;
            vertical-align: middle;
        }

        table th,
        table td {
            padding: 5px 10px;
            text-align: start;
        }

        .table-border {
            border: 1px solid #000;
            border-collapse: collapse;
        }

        .table-border td,
        .table-border th {
            border: 1px solid #000;
        }

        .table-flex-element {
            display: flex;
            gap: 15px;
        }

        .table-flex-element .title {
            margin-bottom: 5px;
        }

        .m-0 {
            margin: 0;
        }

        .text-center {
            text-align: center;
        }

        .main-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .d-flex {
            display: flex;
        }

        .signature {
            width: 130px;
        }

        .gap-15 {
            gap: 15px;
        }
    </style>
</head>

<body style="font-size: 16px; color: #000;">

    <div class="main-content">
        <div class="content-top">

            <div class="header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
                <div class="main-logo" style="align-items: center;gap: 15px;">
                    <h2>{{ __('appointment.lbl_encounter_details') }}</h2>
                </div>

            </div>
            <div class="c-row">
                @php
                $setting = App\Models\Setting::where('name', 'date_formate')->first();
                        $dateformate = $setting ? $setting->val : 'Y-m-d';
                        $setting = App\Models\Setting::where('name', 'time_formate')->first();
                        $timeformate = $setting ? $setting->val : 'h:i A';
                $encounter_date = isset($data['encounter_date']) ? date($dateformate, strtotime($data['encounter_date'] ?? '--' )) : '--';
                @endphp
                <div class="c-col-7">
                    <ul>
                        <li><b>{{ __('appointment.lbl_name') }}:</b>
                            <span>{{ $data['user']['first_name'] . ' ' . $data['user']['last_name'] ?? '--' }}</span>
                        </li>
                        <li><b>{{ __('appointment.lbl_email') }}:</b>{{ $data['user']['email'] ?? '--' }} </li>
                        <li><b>{{ __('appointment.lbl_encounter_date') }}:</b>{{ $encounter_date ?? '--' }}</li>
                        <li><b>{{ __('appointment.address') }}:</b>{{ $data['user']['address'] ?? '--' }}</li>
                    </ul>
                </div>
                <div class="c-col-12">
                    <ul>
                        <li><b>{{ __('appointment.lbl_clinic_name') }}:</b> {{ $data['clinic']['name'] ?? '--' }}</li>
                        <li><b>{{ __('appointment.lbl_doctor_name') }}:</b> {{ $data['doctor']['first_name'] . ' ' . $data['user']['last_name'] ?? '--' }}</li>
                        <li><b>{{ __('appointment.lbl_description') }}:</b> {{ $data['description'] ?? '--' }}</li>
                    </ul>
                </div>
            </div>
            <div class="header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; margin-top: 30px;">
                <div class="main-logo" style="align-items: center;gap: 15px;">
                    <h2>{{ __('appointment.lbl_clinic_details') }}</h2>
                </div>

            </div>
            <table class="table-striped table-border" style="margin: 30px 0 20px;">
                <thead>
                    <tr>
                        <th>
                            <p class="m-0 text-center">{{ __('appointment.lbl_no') }}</p>
                        </th>
                        <th>
                            <p class="m-0 text-center">{{ __('appointment.lbl_problem') }}</p>
                        </th>
                        <th>
                            <p class="m-0 text-center">{{ __('appointment.lbl_observation') }}</p>
                        </th>
                        <th>
                            <p class="m-0 text-center">{{ __('appointment.lbl_notes') }}</p>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @php
                    $selectedProblemList = $data->selectedProblemList;
                    $selectedObservationList = $data->selectedObservationList;
                    $notesList = $data->notesList;
                    @endphp

                    @if(!empty($selectedProblemList) && !empty($selectedObservationList) && !empty($notesList))
                    @foreach($selectedProblemList as $index => $problem)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $problem->title ?? '--' }}</td>
                        <td>{{ isset($selectedObservationList[$index]) ? $selectedObservationList[$index]->title ?? '--' : '--' }}</td>
                        <td>{{ isset($notesList[$index]) ? $notesList[$index]->title ?? '--' : '--' }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>

            </table>

            <div class="header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px;">
                <div class="main-logo" style="align-items: center;gap: 15px;">
                    <h2>{{ __('appointment.lbl_prescription') }}</h2>
                </div>
            </div>
            <table class="table-striped table-border" style="margin: 30px 0 20px;">
                <thead>
                    <tr>
                        <th>
                            <p class="m-0 text-center">{{ __('appointment.lbl_name') }}</p>
                        </th>
                        <th>
                            <p class="m-0 text-center">{{ __('appointment.lbl_frequency') }}</p>
                        </th>
                        <th>
                            <p class="m-0 text-center">{{ __('appointment.lbl_duration') }}</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $prescriptionsList = $data->prescriptions;
                    @endphp
                    @if(!empty($prescriptionsList))
                    @foreach($prescriptionsList as $index => $prescription)
                    <tr>
                        <!-- <td>{{ $index + 1 }}</td> -->
                        <td>
                            <h5 class="text-primary">{{ $prescription->name ?? '--' }}</h5>
                            <p class="m-0">{{ $prescription->instruction ?? '--' }}</p>
                        </td>
                        <td>{{ $prescription->frequency ?? '--' }}</td>
                        <td>{{ $prescription->duration ?? '--' }}</td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>

            <table class="table-border" style="margin: 30px 0 20px;">
                <tr>
                    <th>{{ __('appointment.lbl_other_detail') }}</th>
                    <td>{{ $data->other_details ?? '--' }}</td>
                </tr>
            </table>
            @if($data->signature !== null)
            <div class="d-flex gap-15">
                <h5 class="m-0">{{ __('appointment.lbl_doctor_sign') }} : </h5>
                <div>
                <img src="{{ $data->signature }}" class="signature">
                </div>

            </div>
            @endif
        </div>
    </div>
</body>

</html>
