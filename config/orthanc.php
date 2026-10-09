<?php

return [
    'url' => rtrim((string) env('ORTHANC_URL', 'http://localhost:8042'), '/'),
    'user' => env('ORTHANC_USER', ''),
    'password' => env('ORTHANC_PASS', ''),
];
