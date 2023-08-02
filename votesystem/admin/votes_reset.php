<?php
    include 'includes/session.php';

    $sql = "DELETE FROM votes";
    if(pg_query($conn, $sql)){
        $_SESSION['success'] = "Votes reset successfully";
    }
    else{
        $_SESSION['error'] = "Something went wrong in resetting: " . pg_last_error($conn);
    }

    header('location: votes.php');

?>

