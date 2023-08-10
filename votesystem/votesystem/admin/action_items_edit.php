<?php
include 'includes/session.php';
include 'includes/conn-pgsql.php';

try {
    // Check if all required fields are set
    if(isset($_POST['id'], $_POST['title'], $_POST['description'], $_POST['election_id'])) {
        $id = intval($_POST['id']);
        $title = $_POST['title'];
        $description = $_POST['description'];
        $election_id = intval($_POST['election_id']);

        // Check for empty fields
        if(empty($title) || empty($description) || $election_id <= 0) {
            throw new Exception("All fields are required.");
        }

        // Check if the election is ongoing, as it was done in the add script
        $query = "SELECT * FROM elections WHERE id = $1 AND start_date <= NOW() AND end_date >= NOW()";
        $result = pg_query_params($conn, $query, array($election_id));

        if (pg_num_rows($result) > 0) {
            throw new Exception("Cannot edit action items for an ongoing election.");
        }

        // Check if another action item with the same title exists for the selected election
        $query = "SELECT * FROM action_items WHERE title = $1 AND election_id = $2 AND id <> $3";
        $result = pg_query_params($conn, $query, array($title, $election_id, $id));

        if (pg_num_rows($result) > 0) {
            throw new Exception("Another action item with this title already exists for the selected election.");
        }

        // Update the action item
        $query = "UPDATE action_items SET title = $1, description = $2, election_id = $3 WHERE id = $4";
        pg_query_params($conn, $query, array($title, $description, $election_id, $id));

        $_SESSION['success'] = 'Action item updated successfully.';

    } else {
        throw new Exception("Required data not received.");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

// Redirect back to action items page
header('Location: action_items.php');
exit;
?>

