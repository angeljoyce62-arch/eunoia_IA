<?php
// Database configuration
$servername = "localhost";
$username   = "root";
$password   = ""; // leave empty if no password is set in XAMPP
$database   = "eunoia_db";

// Create connection
$conn = mysqli_connect("localhost", "root", "", "eunoia_db");


// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
