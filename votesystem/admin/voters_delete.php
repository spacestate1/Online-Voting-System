<?php
    include 'includes/session.php';

    if(isset($_POST['delete'])){
        $id = $_POST['id'];
        $sql = "DELETE FROM voters WHERE id = $1";
        if(pg_query_params($conn, $sql, array($id))){
            $_SESSION['success'] = 'Voter deleted successfully';
        }
        else{
            $_SESSION['error'] = pg_last_error($conn);
        }
    }
    else{
        $_SESSION['error'] = 'Select item to delete first';
    }

    header('location: voters.php');
    
?>

