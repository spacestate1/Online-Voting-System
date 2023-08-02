<?php
include 'includes/session.php';

$response = array();

if(isset($_POST['edit'])){
    $id = pg_escape_string($conn, $_POST['id']);
    $firstname = pg_escape_string($conn, $_POST['firstname']);
    $lastname = pg_escape_string($conn, $_POST['lastname']);
    $platform = pg_escape_string($conn, $_POST['platform']);

    if(isset($_POST['position']) && $_POST['position'] !== "") {
        $position = pg_escape_string($conn, $_POST['position']);

        $sql = "UPDATE candidates SET firstname = $1, lastname = $2, position_id = $3, platform = $4 WHERE id = $5";
        $result = pg_query_params($conn, $sql, array($firstname, $lastname, $position, $platform, $id));

        if($result) {
            $response['status'] = 'success';
            $response['message'] = 'Candidate successfully updated.';
        } else {
            $response['status'] = 'error';
            $response['message'] = pg_last_error($conn);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid position value';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Fill up edit form first';
}

header('Content-type: application/json');
echo json_encode($response);
?>

