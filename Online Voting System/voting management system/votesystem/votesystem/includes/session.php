<?php
include 'includes/conn.php';
session_start();

if (isset($_SESSION['voter'])) {
    $sql = "SELECT * FROM voters WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['voter']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $voter = $result->fetch_assoc();
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

