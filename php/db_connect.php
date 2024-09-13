<?php
$servername = "localhost";
$username = "airforcerp_medicine_schedule";
$password = "EKYT07aTAIhOEpN";
$dbname = "airforcerp_medicine_schedule";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>