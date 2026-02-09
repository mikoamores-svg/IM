<?php 

$connection = mysqli_connect("localhost", "root", "", "database_gerbag");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>

