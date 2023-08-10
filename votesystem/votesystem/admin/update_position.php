<?php 
include 'includes/session.php';

if(isset($_POST)) {
    $id = $_POST['id'];
    $description = $_POST['description'];
    $max_vote = $_POST['max_vote'];
    $election_id = $_POST['election_id'];
    $priority = $_POST['priority'];

    // Check for duplicates
    $check_sql = "SELECT * FROM positions WHERE description = $1 AND max_vote = $2 AND election_id = $3 AND priority = $4 AND id != $5";
    $check_stmt = pg_prepare($conn, "check_duplicate", $check_sql);
    $check_result = pg_execute($conn, "check_duplicate", array($description, $max_vote, $election_id, $priority, $id));
    if(pg_num_rows($check_result) > 0) {
        $_SESSION['error'] = 'A position with similar details already exists.';
        header('location: edit_position.php?id=' . $id);
        exit;
    }

    // SQL Update Statement
    $sql = "UPDATE positions SET description = $1, max_vote = $2, election_id = $3, priority = $4 WHERE id = $5";
    $stmt = pg_prepare($conn, "update_query", $sql);
    if(!$stmt) {
        $_SESSION['error'] = 'Failed to prepare statement.';
        header('location: edit_position.php?id=' . $id);
        exit;
    }

    // Execute the prepared statement
    $result = pg_execute($conn, "update_query", array($description, $max_vote, $election_id, $priority, $id));
    if($result) {
        $_SESSION['success'] = 'Position updated successfully.';
    } else {
        $_SESSION['error'] = 'Something went wrong while updating.';
    }
}

header('location: positions.php');
?>

