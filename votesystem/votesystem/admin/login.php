<?php
session_start();
include 'includes/conn-pgsql.php'; // use the connection file for PostgreSQL

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Initialize attempts
    if(!isset($_SESSION['attempts'])) {
        $_SESSION['attempts'] = 0;
    }

    $sql = "SELECT * FROM admin WHERE username = $1"; // use placeholders for parameters
    $result = pg_query_params($conn, $sql, array($username)); // use pg_query_params for parameterized query

    if($result && pg_num_rows($result) < 1){
        $_SESSION['error'] = 'Cannot find account with the username';
    }
    else{
        $row = pg_fetch_assoc($result); // use pg_fetch_assoc for fetching associative array
        if(password_verify($password, $row['password'])){
            $_SESSION['admin'] = $row['id'];
            $_SESSION['attempts'] = 0; // Reset the attempts if the login is successful
        }
        else{
            $_SESSION['error'] = 'Incorrect password';
            $_SESSION['attempts'] += 1; // Increment the attempts count

            // Check if attempts exceeded the limit
            if($_SESSION['attempts'] >= 3) {
                $_SESSION['error'] = 'Maximum login attempts exceeded. Please try again later.';
                exit; // Stop script execution or you can redirect to a specific page
            }
        }
    }
}
else{
    $_SESSION['error'] = 'Input admin credentials first';
}

header('location: index.php');
?>

