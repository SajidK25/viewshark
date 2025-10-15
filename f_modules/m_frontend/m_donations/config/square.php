<?php
return [
    'square' => [
        'application_id' => 'YOUR_SQUARE_APP_ID',
        'access_token' => 'YOUR_SQUARE_ACCESS_TOKEN',
        'location_id' => 'YOUR_SQUARE_LOCATION_ID',
        'environment' => 'sandbox', // Change to 'production' when going live
        'currency' => 'USD',
        'min_donation' => 1.00,
        'max_donation' => 1000.00,
        'default_amounts' => [5, 10, 25, 50, 100],
        'webhook_secret' => 'YOUR_WEBHOOK_SECRET'
    ],
    'streamer' => [
        'min_balance' => 10.00, // Minimum balance before payout
        'payout_fee' => 2.9,    // Square payout fee percentage
        'payout_fee_fixed' => 0.30 // Fixed fee per payout
    ]
]; 