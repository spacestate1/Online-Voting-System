<?php
include 'includes/conn.php'; // Include the file to establish database connection

// Check if the user is logged in
include 'includes/session.php';

// Retrieve form inputs
$username = $_SESSION['username'];
$curr_password = $_POST['curr_password'];
$new_password = $_POST['new_password'];
$retype_password = $_POST['retype_password'];

// Sanitize and validate user inputs
$curr_password = mysqli_real_escape_string($conn, $curr_password);
$new_password = mysqli_real_escape_string($conn, $new_password);
$retype_password = mysqli_real_escape_string($conn, $retype_password);

// Validate form inputs
if ($new_password !== $retype_password) {
    // Passwords do not match
    exit("New password and confirm password do not match.");
}

// Fetch the user's current hashed password from the database
$query_check = "SELECT password FROM voters WHERE voters_id = ?";
$stmt = $conn->prepare($query_check);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User not found in the database
    exit("User not found.");
}

$row = $result->fetch_assoc();
$hashed_password = $row['password'];

// Verify the current password with the hashed password stored in the database
if (!password_verify($curr_password, $hashed_password)) {
    // Current password is incorrect
    exit("Incorrect current password.");
}

// Hash the new password
$hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update the user's password in the database
$query_update = "UPDATE voters SET password = ? WHERE voters_id = ?";
$stmt = $conn->prepare($query_update);
$stmt->bind_param("ss", $hashed_new_password, $username);
if (!$stmt->execute()) {
    // Update query execution error
    exit("Error updating password: " . $stmt->error);
}

// Password updated successfully
echo "Password updated successfully.";

// Close the database connection
$stmt->close();
$conn->close();
?>

