<?php
include 'includes/session.php';
include 'includes/conn-pgsql.php'; // Adding database connection include

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $election_id = $_SESSION['election_id'];
    $voter_id = $_SESSION['userid'];
    $votes = $_POST['vote']; // This should be an associative array of position_id => candidate_id

    foreach ($votes as $position_id => $candidate_id) {
        $sql = "INSERT INTO votes (election_id, voters_id, candidate_id, position_id) VALUES ($1, $2, $3, $4)";
        $stmtname = uniqid(); // unique statement name
        $result = pg_prepare($conn, $stmtname, $sql);
        if (!$result) {
            die('Query failed: ' . pg_last_error());
        }
        $query = pg_execute($conn, $stmtname, array($election_id, $voter_id, $candidate_id, $position_id));
        if (!$query) {
            die('Query failed: ' . pg_last_error());
        }
    }

    $_SESSION['success'] = "Your vote has been successfully submitted!";
    header('location: vote.php');
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header('location: vote.php');
    exit();
}
?>

