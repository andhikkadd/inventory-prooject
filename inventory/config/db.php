<?php
$host = 'localhost';
$db = 'inventory';
$user = 'root';
$pass = 'Pamekasan2005,';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>