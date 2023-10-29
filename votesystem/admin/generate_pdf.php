<?php
include 'includes/conn-pgsql.php';
if (isset($_POST['election_pdf_id'])) {
    $election_id = $_POST['election_pdf_id'];

    // Establishing connection to the database
    $conn = pg_connect("host=localhost dbname=votesystem user=system password=system-pass");
    if (!$conn) {
        die("Error connecting to the database.");
    }

    // Fetch the election name from the database for the given ID
    $query = "SELECT name FROM elections WHERE id = $election_id";
    $result = pg_query($conn, $query);
    $election = pg_fetch_assoc($result);
    $election_name = $election['name'];

    // Run the Python script
    $command = "python3 vote-record-gen.py " . escapeshellarg($election_name); 
    $output = shell_exec($command);

    // Redirect back with a success message
    $_SESSION['message'] = "PDF generated successfully!";
    $_SESSION['msg_type'] = "success";
    header('Location: votes.php');

}
?>

