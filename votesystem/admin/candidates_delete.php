<?php
    include 'includes/session.php';

    if(isset($_POST['id'])){
        $id = pg_escape_string($conn, $_POST['id']);
        
        // Check if ID is a valid integer
        if (filter_var($id, FILTER_VALIDATE_INT)) {
            $sql = "DELETE FROM candidates WHERE id = $1";
            $result = pg_query_params($conn, $sql, array($id));
            
            if($result){
                $_SESSION['success'] = 'Candidate deleted successfully';
                echo json_encode(['success' => true]);
            }
            else{
                $_SESSION['error'] = pg_last_error($conn);
                echo json_encode(['error' => pg_last_error($conn)]);
            }
        } else {
            $_SESSION['error'] = 'Invalid candidate ID: ' . $id . '. POST data: ' . var_export($_POST, true);
            echo json_encode(['error' => 'Invalid candidate ID: ' . $id . '. POST data: ' . var_export($_POST, true)]);
        }
    }
    else{
        $_SESSION['error'] = 'Select candidate to delete first';
        echo json_encode(['error' => 'Select candidate to delete first']);
    }
?>

