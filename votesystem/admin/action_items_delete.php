<?php
include 'includes/session.php';
include 'includes/conn-pgsql.php';

try {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if($id <= 0) {
        throw new Exception("Invalid action item.");
    }

    // Check if the action item exists
    $query = "SELECT * FROM action_items WHERE id = $1";
    $result = pg_query_params($conn, $query, array($id));

    if (pg_num_rows($result) <= 0) {
        throw new Exception("Action item not found.");
    }

    // Check if the related election is ongoing
    $query = "SELECT * FROM elections WHERE id = (SELECT election_id FROM action_items WHERE id = $1) AND start_date <= NOW() AND end_date >= NOW()";
    $result = pg_query_params($conn, $query, array($id));

    if (pg_num_rows($result) > 0) {
        throw new Exception("Cannot delete action items for an ongoing election.");
    }

    // Delete the action item
    $query = "DELETE FROM action_items WHERE id = $1";
    if(pg_query_params($conn, $query, array($id))) {
        echo json_encode(['success' => true, 'message' => 'Action item deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete the action item.']);
    }
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}

// We will not redirect if the above code runs successfully, because it's AJAX. Only use redirect for non-AJAX operations.
// header('Location: action_items.php');
// exit;
?>

