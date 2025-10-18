<?php

// Session Management 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// CORS Preflight (OPTIONS) Request Handling 
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');


    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Max-Age: 86400'); 

    http_response_code(204);
    exit(); 
}


//  Rate Limiting
$maxRequests = 15; // Maximum requests allowed
$timePeriod = 60;  // In seconds (e.g., 15 requests per 60 seconds)

// Initialize session data for the user if it doesn't exist
if (!isset($_SESSION['request_timestamps'])) {
    $_SESSION['request_timestamps'] = [];
}

$currentTime = time();
$requestTimestamps = $_SESSION['request_timestamps'];

// Filter out old timestamps and keep only the timestamps from the last 60 seconds.
$recentRequests = array_filter($requestTimestamps, function($timestamp) use ($currentTime, $timePeriod) {
    return ($currentTime - $timestamp) < $timePeriod;
});

if (count($recentRequests) >= $maxRequests) {
    http_response_code(429); 
    header('Retry-After: ' . $timePeriod);
    echo json_encode([
        "status" => "error",
        "message" => "Too many requests. Please wait a moment and try again."
    ]);
    exit();
}

$recentRequests[] = $currentTime;
$_SESSION['request_timestamps'] = $recentRequests;


// Response Headers 
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

