<?php
    $host = 'localhost';
    $dbname = 'votesystem';
    $username = 'system';
    $password = 'system-pass'; // replace 'your_password' with your actual password
 
    $conn_string = "host={$host} dbname={$dbname} user={$username} password={$password}";
    $conn = pg_connect($conn_string);
    if ($conn === false) {
        die("Connection failed: " . pg_last_error());
    }
?>
