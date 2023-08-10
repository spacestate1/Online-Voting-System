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
        // Debug start date
        var_dump($row['start_date']);

        $now = new DateTime();
        $start_date = new DateTime($row['start_date']);

        // Check if current date is within the election period
        if ($now >= $start_date) {
            // Election has started, editing is not allowed
            echo json_encode(['error' => 'Election has started, editing is not allowed.']);
        } else {
            // Election has not started, send the position data
            echo json_encode($row);
        }
            
    } else {
        echo json_encode(['error' => 'No data found for the provided id.']);
    }

} else {
    echo json_encode(['error' => 'ID not provided.']);
}
?>

