<?php
// Security Headers
function cors() {
  if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
  }

  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
      header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
    exit(0);
  }
}

cors();

require 'db.php';
require 'router.php';
require 'controller/v1.php';

// Initialize Router
$router = new Router();

// Post Requests
$router->post('/api/v1/data', 'ExpController@data');


// Get Requests
$router->get('/api/v1/data', 'ExpController@data');




// Put Requests
$router->put('/api/v1/data', 'ExpController@data');

// Delete Requests
$router->delete('/api/v1/data', 'ExpController@data');

// Dispatch the request
$router->dispatch();
?>
