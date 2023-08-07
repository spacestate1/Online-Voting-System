<?php
include 'includes/session.php';
include 'includes/conn-pgsql.php';

if(isset($_POST['add'])){
    $election_title = trim($_POST['election_title']) ?? null;
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;

    if (!empty($election_title) && !empty($start_date) && !empty($end_date)) {
        // Convert date format to timestamp
        $start_date = date('Y-m-d H:i:s', strtotime($start_date));
        $end_date = date('Y-m-d H:i:s', strtotime($end_date));

        // Assuming you have a table `elections` with fields `name`, `start_date`, and `end_date`
        $sql = "INSERT INTO elections (name, start_date, end_date) VALUES ($1, $2, $3)";

        // Use pg_query_params to prevent SQL injection
        $result = pg_query_params($conn, $sql, array($election_title, $start_date, $end_date));
        
        if($result){
            $_SESSION['success'] = 'Election created successfully.';
            header('Location: election_list.php');
exit;
        }
        else{
            $_SESSION['error'] = pg_last_error($conn);
    
        }
    } else {
        $_SESSION['error'] = 'All fields are required.';
    }
} else {
    $_SESSION['error'] = 'Form data not received.';
}

header('location: home.php');
?>

