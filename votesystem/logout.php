<?php
    session_start();
    include 'includes/conn-pgsql.php';

    if(isset($_SESSION['userid'])){
        // Update the is_logged_in status in the database
        $updateSql = "UPDATE voters SET is_logged_in = FALSE WHERE id = $1";
        pg_query_params($conn, $updateSql, array($_SESSION['userid']));

        // Unset and destroy the session
        session_unset();
        session_destroy();
    }

    header('location: index.php');
?>

