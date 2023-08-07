<?php 
include 'includes/session.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    $_SESSION['error'] = 'Invalid position ID.';
    header('location: positions.php');
    exit;
}

$id = $_GET['id'];

$sql = "DELETE FROM positions WHERE id = $1";
$stmt = pg_prepare($conn, "delete_query", $sql);

if($stmt){
    $result = pg_execute($conn, "delete_query", array($id));
    if($result){
        $_SESSION['success'] = 'Position deleted successfully.';
    } else {
        $_SESSION['error'] = 'Something went wrong while deleting.';
    }
} else {
    $_SESSION['error'] = 'Error preparing statement.';
}

header('location: positions.php');
exit;
?>


