<?php
include 'includes/session.php';

if(isset($_POST['id'])){
    $voter_id = $_SESSION['userid'];
    $candidate_id = $_POST['id'];
    $position_id = str_replace('position_', '', $_POST['pos_id']);
    $election_id = $_SESSION['election_id'];

    $sql = "SELECT * FROM votes WHERE election_id = '$election_id' AND voters_id = '$voter_id' AND position_id = '$position_id'";
    $query = pg_query($conn, $sql);
    if(pg_num_rows($query) > 0){
        $sql = "UPDATE votes SET candidate_id = '$candidate_id' WHERE election_id = '$election_id' AND voters_id = '$voter_id' AND position_id = '$position_id'";
    }
    else{
        $sql = "INSERT INTO votes(election_id, voters_id, candidate_id, position_id) VALUES ('$election_id', '$voter_id', '$candidate_id', '$position_id')";
    }

    if(pg_query($conn, $sql)){
        $_SESSION['success'] = 'Ballot successfully cast';
    }
    else{
        $_SESSION['error'] = pg_last_error($conn);
    }

}
else{
    $_SESSION['error'] = 'Select candidate to vote first';
}

header('location: vote.php');
?>

