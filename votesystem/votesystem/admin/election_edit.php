<?php
  include 'includes/session.php';

  // Initialize variables
  $id = $election_title = $start_date = $end_date = "";

  // Fetch the current data of the election from the database using the id from the query parameters
  if (isset($_GET['id'])) {
      $id = $_GET['id'];
      // Fetch the current data of the election from the database
      $sql = "SELECT * FROM elections WHERE id = '$id'";
      $query = pg_query($conn, $sql);
      $row = pg_fetch_assoc($query);
      $election_title = $row['name'];
      $start_date = $row['start_date'];
      $end_date = $row['end_date'];
  }

  if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $title = $_POST['election_title'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $sql = "UPDATE elections SET name = '$title', start_date = '$start_date', end_date = '$end_date' WHERE id = '$id'";
   
    if(pg_query($conn, $sql)){
      $_SESSION['success'] = 'Election updated successfully';
      header('Location: election_list.php');
exit;
    }
    else{
      $_SESSION['error'] = 'Something went wrong while updating!';
    }
  }
?>
