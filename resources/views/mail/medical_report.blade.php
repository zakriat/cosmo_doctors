<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Report</title>
</head>
<body>
    <h2>Medical Report</h2>
    
    <p>User: {{ $medicalReport->user->first_name }} {{ $medicalReport->user->last_name }}</p>
    <p>Date: {{ $medicalReport->created_at }}</p> 

    <p> Click here to View Medical Report <a href="{{ $medicalReport->file_url }}">Medical Report</a> </p>

</body>
</html>