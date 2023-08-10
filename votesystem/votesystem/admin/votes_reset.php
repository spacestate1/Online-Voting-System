<?php
include 'includes/session.php';

if ($_POST) {
    $election_id = $_POST['election_id'];
    $reset_type = $_POST['reset_type'];

    // Initialize the query
    $sql = "";

    if ($reset_type == "votes") {
        $sql = "DELETE FROM votes WHERE election_id = '$election_id'";
    } elseif ($reset_type == "action_items") {
        $sql = "DELETE FROM action_item_votes WHERE election_id = '$election_id'";
    }

    // Execute the query
    if ($sql && pg_query($conn, $sql)) {
        $_SESSION['success'] = 'Votes were successfully reset.';
    } else {
        $_SESSION['error'] = 'Something went wrong while resetting the votes: ' . pg_last_error($conn);
    }
}

header('location: votes.php');
?>

