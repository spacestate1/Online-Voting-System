<?php
include 'includes/session.php';
include 'includes/conn-pgsql.php';

try {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $election_id = intval($_POST['election_id']);

    if(empty($title) || empty($description) || $election_id <= 0) {
        throw new Exception("All fields are required.");
    }

    $query = "SELECT * FROM elections WHERE id = $1 AND start_date <= NOW() AND end_date >= NOW()";
    $result = pg_query_params($conn, $query, array($election_id));

    if (pg_num_rows($result) > 0) {
        throw new Exception("Cannot add action items for an ongoing election.");
    }

    $query = "SELECT * FROM action_items WHERE title = $1 AND election_id = $2";
    $result = pg_query_params($conn, $query, array($title, $election_id));

    if (pg_num_rows($result) > 0) {
        throw new Exception("This action item already exists for the selected election.");
    }

    $query = "INSERT INTO action_items (title, description, election_id) VALUES ($1, $2, $3)";
    pg_query_params($conn, $query, array($title, $description, $election_id));

    $_SESSION['success'] = 'Action item added successfully.';

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: action_items.php');
exit;
?>

