<?php
include 'includes/session.php';

if ($_POST) {
    $election_id = $_POST['election_id'];

    // Delete Candidate Votes
    $sql_votes = "DELETE FROM votes WHERE election_id = '$election_id'";

    // Delete Action Item Votes
    $sql_action_items = "DELETE FROM action_item_votes WHERE election_id = '$election_id'";

    // Execute the queries
    if (pg_query($conn, $sql_votes) && pg_query($conn, $sql_action_items)) {
        $_SESSION['success'] = 'Votes were successfully reset.';
    } else {
        $_SESSION['error'] = 'Something went wrong while resetting the votes: ' . pg_last_error($conn);
    }
}

header('location: votes.php');
?>

