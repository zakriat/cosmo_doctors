<!DOCTYPE html>
<html>
<head>
    <title>{{ __('appointment.appointment_confirmation') }}</title>
</head>
<body>
    <p>{{ __('appointment.title') }}: {{ $meetingDetails['title'] }}</p>
    <p>{{ __('appointment.description') }}: {{ $meetingDetails['description'] }}</p>
    <p>{{ __('appointment.join_link') }}: <a href="{{ $meetingDetails['link'] }}">{{ $meetingDetails['link'] }}</a></p>

</body>
</html>
