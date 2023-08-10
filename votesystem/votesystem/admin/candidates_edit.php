<?php
include 'includes/session.php';

$response = array();

if(isset($_POST['edit'])){
    $id = $_POST['id'];

    // Check if ID is a valid integer
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid candidate ID: ' . $id;
        echo json_encode($response);
        exit();
    }

// Check if ID is a valid integer
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid candidate ID: ' . $id;
        echo json_encode($response);
        exit();
    }

    $firstname = pg_escape_string($conn, $_POST['firstname']);
    $lastname = pg_escape_string($conn, $_POST['lastname']);
    $platform = pg_escape_string($conn, $_POST['platform']);

    if(isset($_POST['position']) && $_POST['position'] !== "") {
        $position = pg_escape_string($conn, $_POST['position']);

        // Fetch the 'election_id' associated with the new 'position'
        $electionSql = "SELECT election_id FROM positions WHERE id = $1";
        $electionResult = pg_query_params($conn, $electionSql, array($position));
        
        if(!$electionResult){
            $response['status'] = 'error';
            $response['message'] = 'Could not fetch election for selected position.';
        } else {
            $electionRow = pg_fetch_assoc($electionResult);
            $election_id = $electionRow['election_id'];

            // Update the candidate details
            $sql = "UPDATE candidates SET firstname = $1, lastname = $2, position_id = $3, platform = $4, election_id = $5 WHERE id = $6";
            $result = pg_query_params($conn, $sql, array($firstname, $lastname, $position, $platform, $election_id, $id));

            if($result) {
                $response['status'] = 'success';
                $response['message'] = 'Candidate successfully updated.';
            } else {
                $response['status'] = 'error';
                $response['message'] = pg_last_error($conn);
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid position value';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Fill up edit form first';
}

//header('Content-type: application/json');
header('Location: candidates.php');
echo json_encode($response);
?>

