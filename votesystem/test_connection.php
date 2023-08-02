<?php
include 'includes/conn.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Execute the SELECT query
$query = "SELECT * FROM voters";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Display the data
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['voters_id'] . "<br>";
        echo "Name: " . $row['name'] . "<br>";
        echo "Email: " . $row['email'] . "<br>";
        echo "<br>";
    }
} else {
    echo "No records found.";
}

// Close the database connection
$conn->close();
?>

