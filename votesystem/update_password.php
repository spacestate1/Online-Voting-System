<?php
include 'includes/conn-pgsql.php'; // Include the file to establish database connection

// Check if the user is logged in
include 'includes/session.php';

// Retrieve form inputs
$username = $_SESSION['username'];
$curr_password = $_POST['curr_password'];
$new_password = $_POST['new_password'];
$retype_password = $_POST['retype_password'];

// Sanitize and validate user inputs
$curr_password = pg_escape_string($conn, $curr_password);
$new_password = pg_escape_string($conn, $new_password);
$retype_password = pg_escape_string($conn, $retype_password);

// Validate form inputs
if ($new_password !== $retype_password) {
    // Passwords do not match
    exit("New password and confirm password do not match.");
}

// Fetch the user's current hashed password from the database
$query_check = "SELECT password FROM voters WHERE voters_id = $1";
$result = pg_query_params($conn, $query_check, array($username));

if (pg_num_rows($result) === 0) {
    // User not found in the database
    exit("User not found.");
}

$row = pg_fetch_assoc($result);
$hashed_password = $row['password'];

// Verify the current password with the hashed password stored in the database
if (!password_verify($curr_password, $hashed_password)) {
    // Current password is incorrect
    exit("Incorrect current password.");
}

// Hash the new password
$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the user's password in the database
$query_update = "UPDATE voters SET password = $1 WHERE voters_id = $2";
$result = pg_query_params($conn, $query_update, array($hashed_new_password, $username));

if (!$result) {
    // Update query execution error
    exit("Error updating password: " . pg_last_error($conn));
}

// Password updated successfully
echo "Password updated successfully.";

// Close the database connection
pg_close($conn);
?>

