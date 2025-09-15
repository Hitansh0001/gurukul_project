<?php
// Database configuration
define('DB_SERVER', '192.168.2.34');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'Ex@mple123');
define('DB_NAME', 'task_management');

// define('DB_SERVER', '13.126.199.186');
// define('DB_USERNAME', 'root');
// define('DB_PASSWORD', 'Digpal@8890');
// define('DB_NAME', 'task_management');


// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
