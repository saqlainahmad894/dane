<?php
// signup.php
$servername = "localhost";
$username = "867630";
$password = "123456";
$dbname = "867630";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>