<?php
include 'includes/conn-pgsql.php'; // Include the file to establish PostgreSQL database connection
session_start();

if (isset($_SESSION['voter'])) {
    $sql = "SELECT * FROM voters WHERE id = $1";
    $result = pg_query_params($conn, $sql, array($_SESSION['voter']));

    if ($result && pg_num_rows($result) === 1) {
        $voter = pg_fetch_assoc($result);
        $username = $voter['voters_id'];

        // Store the username in the session
        $_SESSION['username'] = $username;
    } else {
        // Voter not found in the database
        header('location: index.php');
        exit();
    }
} else {
    header('location: index.php');
    exit();
}
?>

