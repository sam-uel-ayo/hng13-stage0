<?php

/**
 * Main application entry point and router.
 */
require_once __DIR__ . '/assets/Profile.php';

$endpoint = $_GET['endpoint'] ?? ''; // e.g., "me"


switch ($endpoint) {
    case 'me':
        require_once __DIR__ . '/assets/header.php';
        
        // If header security checks pass, load the endpoint logic.
        require_once __DIR__ . '/me.php';
        break;
    default:
        // Handle 404 Not Found for any other endpoint
        header('Content-Type: application/json'); // Set header for the error message
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "Endpoint not found."
        ]);
        break;
}
