<?php
// Database configuration
define('DB_HOST', 'sql110.infinityfree.com');
define('DB_USER', 'if0_37163279');
define('DB_PASSWORD', 'ViO83vawqZKRiQo');
define('DB_NAME', 'if0_37163279_expense_tracker');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
  $response = array(
    'success' => false,
    'message' => 'Database connection failed: ' . $conn->connect_error
  );
  echo json_encode($response);
  exit();
}
?>
