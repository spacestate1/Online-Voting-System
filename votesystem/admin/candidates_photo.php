<?php
    include 'includes/session.php';

    if(isset($_POST['upload'])){
        $id = pg_escape_string($conn, $_POST['id']);
        $filename = $_FILES['photo']['name'];
        if(!empty($filename)){
            move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);    
        }

        // Using PostgreSQL syntax here
        $sql = "UPDATE candidates SET photo = $1 WHERE id = $2";
        $result = pg_query_params($conn, $sql, array($filename, $id));

        if($result){
            $_SESSION['success'] = 'Photo updated successfully';
        }
        else{
            $_SESSION['error'] = pg_last_error($conn);
        }
    }
    else{
        $_SESSION['error'] = 'Select candidate to update photo first';
    }

    header('location: candidates.php');
?>

