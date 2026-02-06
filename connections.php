<?php 
$connections = mysqli_connect("localhost", "root", "", "gerbag_db");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>

<?php
$conn = new mysqli("localhost", "root", "", "gerbag_db");
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}
?>