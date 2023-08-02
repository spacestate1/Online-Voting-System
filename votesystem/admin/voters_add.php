<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $userid = $_POST['userid']; // updated line
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email']; // capture email

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header('location: voters.php');
        exit;
    }

    // Check if user_id is unique
    $sql_check = "SELECT * FROM voters WHERE user_id = $1";
    $query_check = pg_query_params($conn, $sql_check, array($userid));
    if(pg_num_rows($query_check) > 0){
        $_SESSION['error'] = 'User ID already exists';
        header('location: voters.php');
        exit();
    }

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $filename = $_FILES['photo']['name'];

    // If no photo was uploaded, use the default image
    if(empty($filename)){
        $filename = "icons8-user-64.png";
    } else {
        move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);
    }
    
    //generate voters id
    $set = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $voter = substr(str_shuffle($set), 0, 15);

    // Updated SQL query
    $sql = "INSERT INTO voters (user_id, voters_id, password, firstname, lastname, photo, email) VALUES ($1, $2, $3, $4, $5, $6, $7)";

    // Replace pg_query with pg_query_params and pass the variables in an array
    if(pg_query_params($conn, $sql, array($userid, $voter, $password, $firstname, $lastname, $filename, $email))){
        $_SESSION['success'] = 'Voter added successfully';
    }
    else{
        $_SESSION['error'] = pg_last_error($conn);
    }
}
else{
    $_SESSION['error'] = 'Fill up add form first';
}

header('location: voters.php');
?>

