<?php
session_start();
include 'includes/conn-pgsql.php';

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    if (isset($_POST['login'], $_POST['user_id'], $_POST['password'])) {
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM voters WHERE user_id = $1";
        $result = pg_query_params($conn, $sql, array($user_id));

        if ($result === false) {
            die('Database error'); // Ideally log the error and show a user-friendly message
        }

        $row = pg_fetch_assoc($result);
        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['userid'] = $row['id'];
            $_SESSION['last_activity'] = time(); // Set last activity time to current time

            // Update login status and timestamp
            $updateSql = "UPDATE voters SET is_logged_in = TRUE, last_login = NOW() WHERE id = $1";
            pg_query_params($conn, $updateSql, array($row['id']));

            header('location: home.php');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid login credentials';
            header('location: index.php');
            exit;
        }
    }
} else {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a CSRF token
    $_SESSION['error'] = 'Input voter credentials first';
    header('location: index.php');
    exit;
}
?>

