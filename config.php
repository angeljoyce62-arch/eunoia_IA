<?php
// Database configuration
$servername = "localhost";
$username   = "root";
$password   = ""; // leave empty if no password is set in XAMPP
$database   = "eunoia_db";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
