<?php
// Only apply CORS in development
if (getenv('NODE_ENV') === 'development') {
    // Allow from local dev server and browser extension
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('HTTP/1.1 204 No Content');
        exit();
    }
}
?> 