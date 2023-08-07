<?php
    include 'includes/session.php';

    if(isset($_POST['election_id']) && is_numeric($_POST['election_id'])){
        // Get selected election id from form
        $election_id = $_POST['election_id'];

        // Save it to session
        $_SESSION['election_id'] = $election_id;

        // Redirect user to the voting page
        header('location: vote.php');
    }
    else {
        // If no election_id is provided, redirect back to selection
        $_SESSION['error'] = 'Please select a valid election.';
        header('location: home.php');
    }
?>

