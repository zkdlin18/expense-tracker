<?php
function cors() {
    // Always allow requests from the specific origin
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400'); // Cache the CORS preflight response for 24 hours

    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(204); // No Content
        exit();
    }
}

cors();

// Include your necessary files
require 'db.php';
require 'router.php';
require 'controller/v1.php';

// Initialize Router
$router = new Router();

// Define Routes
$router->post('/api/v1/data', 'ExpController@data');
$router->get('/api/v1/data', 'ExpController@data');
$router->put('/api/v1/data', 'ExpController@data');
$router->delete('/api/v1/data', 'ExpController@data');

// Dispatch the request
$router->dispatch();

?>
