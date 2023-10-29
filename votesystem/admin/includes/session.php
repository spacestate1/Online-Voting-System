<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    include 'includes/conn-pgsql.php'; // use the connection file for PostgreSQL

    if(!isset($_SESSION['admin']) || trim($_SESSION['admin']) == ''){
        header('location: index.php');
        exit;
    }

    $sql = "SELECT * FROM admin WHERE id = $1"; // use placeholders for parameters
    $result = pg_query_params($conn, $sql, array($_SESSION['admin'])); // use pg_query_params for parameterized query

    if ($result !== false) {
        $user = pg_fetch_assoc($result); // use pg_fetch_assoc for fetching associative array
    } else {
        // Optionally, handle the database error here.
        echo "An error occurred.\n";
        exit;
    }
?>

