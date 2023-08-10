<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    try {
        $election_id = pg_escape_string($conn, $_POST['election_id']);
        $description = pg_escape_string($conn, $_POST['description']);
        $max_vote = pg_escape_string($conn, $_POST['max_vote']);

        if (!isset($_POST['priority']) || !is_numeric($_POST['priority'])) {
            throw new Exception('Priority must be a number');
        }
        $priority = pg_escape_string($conn, $_POST['priority']);

        // Check if the election has already started
        $election_check_sql = "SELECT start_date FROM elections WHERE id = '$election_id'";
        $election_check_result = pg_query($conn, $election_check_sql);
        if (pg_num_rows($election_check_result) > 0) {
            $row = pg_fetch_assoc($election_check_result);
            $start_date = new DateTime($row['start_date']);
            if (new DateTime() > $start_date) {
                throw new Exception('Cannot add a position after the election has started');
            }
        }

        // check if the position already exists
        $check_sql = "SELECT * FROM positions WHERE election_id = '$election_id' AND description = '$description' AND max_vote = '$max_vote' AND priority = '$priority'";
        $check_result = pg_query($conn, $check_sql);
        if (pg_num_rows($check_result) > 0) {
            throw new Exception('The position already exists');
        }

        $sql = "INSERT INTO positions (election_id, description, max_vote, priority) VALUES ('$election_id', '$description', '$max_vote', '$priority')"; // add priority here
        $result = pg_query($conn, $sql);

        if(!$result){
            throw new Exception(pg_last_error($conn));
        }
        $_SESSION['success'] = 'Position added successfully';
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
else{
    $_SESSION['error'] = 'Fill up add form first';
}

header('location: positions.php');
?>

