<?php
$servername = "localhost";
$username = "adminmwn_login";
$password = "6QFB0Hc1";
$dbname = "adminmwn_login";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
