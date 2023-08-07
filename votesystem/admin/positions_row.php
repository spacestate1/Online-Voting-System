<?php
include 'includes/session.php';

// Set Content-Type to application/json for all responses
header('Content-Type: application/json');

if(isset($_POST['id'])){
    $id = $_POST['id'];

    $sql = "SELECT *, positions.id AS posid FROM positions LEFT JOIN elections ON elections.id=positions.election_id WHERE positions.id = $1";

    // Prepare the SQL statement
    $stmt = pg_prepare($conn, "query", $sql);
    if(!$stmt){
        // Log and return the error if the preparation fails
        $error = pg_last_error($conn);
        echo json_encode(['error' => 'Failed to prepare statement: ' . $error]);
        exit;
    }

    // Execute the prepared statement
    $result = pg_execute($conn, "query", array($id));
    if(!$result){
        // Log and return the error if execution fails
        $error = pg_last_error($conn);
        echo json_encode(['error' => 'Failed to execute statement: ' . $error]);
        exit;
    }

    $row = pg_fetch_assoc($result);
    if($row){
        if(isset($row['posid'], $row['description'], $row['max_vote'], $row['election_id'], $row['priority'])) {
            echo json_encode($row);
        } else {
            echo json_encode(['error' => 'One or more fields are missing in the fetched data.']);
        }
    } else {
        echo json_encode(['error' => 'No data found for the provided id.']);
    }

} else {
    echo json_encode(['error' => 'ID not provided.']);
}
?>

