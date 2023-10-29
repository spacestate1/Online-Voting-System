<?php
include 'includes/conn-pgsql.php'; // Include the file to establish PostgreSQL database connection
session_start();

// Check for inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1200)) {
    // Update the is_logged_in status in the database
    $updateSql = "UPDATE voters SET is_logged_in = FALSE WHERE id = $1";
    pg_query_params($conn, $updateSql, array($_SESSION['userid']));
    
    // Unset and destroy the session
    session_unset();
    session_destroy();
    
    header('location: index.php');
    exit();
}

$_SESSION['last_activity'] = time(); // Update last activity time

if (isset($_SESSION['userid'])) {
    $sql = "SELECT * FROM voters WHERE id = $1";
    $result = pg_query_params($conn, $sql, array($_SESSION['userid']));

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

