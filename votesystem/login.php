<?php
    session_start();
    include 'includes/conn-pgsql.php';

    if(isset($_POST['login'])){
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM voters WHERE user_id = $1";
        $result = pg_query_params($conn, $sql, array($user_id));

        if(pg_num_rows($result) < 1){
            $_SESSION['error'] = 'Cannot find voter with the ID';
            header('location: index.php');
        }
        else{
            $row = pg_fetch_assoc($result);
            if(password_verify($password, $row['password'])){
                // The session variable name is updated to 'userid' here
                $_SESSION['userid'] = $row['id'];
                header('location: home.php');
            }
            else{
                $_SESSION['error'] = 'Incorrect password';
                header('location: index.php');
            }
        }
    }
    else{
        $_SESSION['error'] = 'Input voter credentials first';
        header('location: index.php');
    }
?>

