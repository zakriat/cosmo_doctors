<?php

return [
    'STATUS' => [
        'pending' => ['title' => 'Pending'],
        'confirmed' => ['title' => 'Confirmed'],
        'check_in' => ['title' => 'Check In'],
        'checkout' => ['title' => 'CheckOut'],
        // 'completed' => ['title' => 'Completed'],
        'cancelled' => ['title' => 'Cancelled'],
        
    ],
    'PAYMENT_STATUS' => [
        '0' => ['title' => 'Pending'],
        '1' => ['title' => 'Paid'],
        '2' => ['title' => 'Failed'],
        '3' => ['title' => 'Refunded'],
        '4' => ['title' => 'Partially Paid'],
        '5' => ['title' => 'Advance Paid'],
    ],
    'DEFAULT_STATUS' => 'pending',
];
