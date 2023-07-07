<?php
    $servername = 'localhost';
    $username = 'USERNAME';
    $password = 'PASSWORD'; // replace 'your_password' with your actual password
    $dbname = 'votesystem';

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>

