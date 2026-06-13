<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription List</title>
    <!-- Include Bootstrap 5 CSS -->
</head>
<body>
    <div class="container">
        <h2>Prescription List</h2>

   

        <table style="border:1px solid black;width:100%">
            <thead>
                <tr>
                    <th style="border:1px solid black">#</th>
                    <th style="border:1px solid black">NAME</th>
                    <th style="border:1px solid black">FREQUENCY</th>
                    <th style="border:1px solid black">DAYS</th>
                    <th style="border:1px solid black">Instruction</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptionList as $index => $prescription)
                    <tr>
                        <td style="border:1px solid black">{{ $index + 1 }}</td>
                        <td style="border:1px solid black">{{ $prescription->name }}</td>
                        <td style="border:1px solid black">{{ $prescription->frequency }}</td>
                        <td style="border:1px solid black">{{ $prescription->duration }}</td>
                        <td style="border:1px solid black">{{ $prescription->instruction }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    
    </div>
    <!-- Include Bootstrap 5 JS (optional) -->
  
</body>
</html>

