<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    try {
        $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
        $max_vote = filter_var($_POST['max_vote'], FILTER_SANITIZE_NUMBER_INT);
        $election_id = filter_var($_POST['election_id'], FILTER_SANITIZE_NUMBER_INT);

        if (!is_numeric($id)) {
            throw new Exception('ID must be a number');
        } elseif (!is_numeric($max_vote)) {
            throw new Exception('Max Vote must be a number');
        } elseif (!is_numeric($election_id)) {
            throw new Exception('Election ID must be a number');
        }

        $election_check_sql = "SELECT * FROM elections WHERE id = $1 AND 
                                (current_date >= start_date OR current_date > end_date) LIMIT 1";
        $election_check_result = pg_query_params($conn, $election_check_sql, array($election_id));
        if (!$election_check_result) {
            throw new Exception('Query error: ' . pg_last_error($conn));
        }
        if (pg_num_rows($election_check_result) > 0) {
            throw new Exception('Cannot edit a position after the election has started or ended');
        }

        $check_sql = "SELECT * FROM positions WHERE election_id = $1 AND description = $2 AND max_vote = $3 AND id != $4";
        $check_result = pg_query_params($conn, $check_sql, array($election_id, $description, $max_vote, $id));
        if (!$check_result) {
            throw new Exception('Query error: ' . pg_last_error($conn));
        }
        if (pg_num_rows($check_result) > 0) {
            throw new Exception('The position already exists');
        }

        $sql = "UPDATE positions SET description = $1, max_vote = $2, election_id = $3 WHERE id = $4";
        $result = pg_query_params($conn, $sql, array($description, $max_vote, $election_id, $id));
        if(!$result){
            throw new Exception('Query error: ' . pg_last_error($conn));
        }

        $_SESSION['success'] = 'Position updated successfully';
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    } finally {
        pg_close($conn);
    }
}
else{
    $_SESSION['error'] = 'Fill up edit form first';
}

header('location: positions.php');
?>

