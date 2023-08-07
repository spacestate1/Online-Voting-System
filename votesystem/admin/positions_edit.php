<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    try {
        $id = $_POST['id'];
        $description = $_POST['description'];
        $max_vote = $_POST['max_vote'];
        $election_id = $_POST['election_id']; // capture the election_id

        if (!is_numeric($id)) {
            throw new Exception('ID must be a number');
        } elseif (!is_numeric($max_vote)) {
            throw new Exception('Max Vote must be a number');
        } elseif (!is_numeric($election_id)) {
            throw new Exception('Election ID must be a number');
        }

        // check if the position already exists
        $check_sql = "SELECT * FROM positions WHERE election_id = $1 AND description = $2 AND max_vote = $3 AND id != $4";
        $check_result = pg_query_params($conn, $check_sql, array($election_id, $description, $max_vote, $id));
        if (pg_num_rows($check_result) > 0) {
            throw new Exception('The position already exists');
        }

        $sql = "UPDATE positions SET description = $1, max_vote = $2, election_id = $3 WHERE id = $4";
        $result = pg_query_params($conn, $sql, array($description, $max_vote, $election_id, $id));
        if(!$result){
            throw new Exception(pg_last_error($conn));
        }
        $_SESSION['success'] = 'Position updated successfully';
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}
else{
    $_SESSION['error'] = 'Fill up edit form first';
}

header('location: positions.php');
?>

