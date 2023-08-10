<?php 
// Include the database connection file
include 'includes/session.php';

// Get the ID from the URL parameters
$id = $_GET['id'];

// Create a SQL query to delete the row with the ID
$sql = "DELETE FROM elections WHERE id = $id";

// Run the SQL query
pg_query($conn, $sql);

// Redirect back to the election list
header('Location: election_list.php');
exit;
?>


