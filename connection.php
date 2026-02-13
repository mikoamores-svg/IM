<?php 
// Database Connection Configuration
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "database_gerbag";

// Create connection
$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (mysqli_connect_errno()) {
    error_log("Database Connection Failed: " . mysqli_connect_error());
    die("Database connection error. Please contact support.");
}

// Set charset to UTF-8
mysqli_set_charset($connection, "utf8mb4");

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>

