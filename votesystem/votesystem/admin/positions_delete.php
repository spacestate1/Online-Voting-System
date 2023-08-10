<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	include 'includes/session.php';

	if(isset($_POST['delete'])){
		if (isset($_POST['id']) && is_numeric($_POST['id'])) {
			$id = $_POST['id'];
			$sql = "DELETE FROM positions WHERE id = $1";
			if(pg_query_params($conn, $sql, array($id))){
				$_SESSION['success'] = 'Position deleted successfully';
			}
			else{
				$_SESSION['error'] = pg_last_error($conn);
			}
		} else {
			$_SESSION['error'] = 'Invalid item to delete';
		}
	}
	else{
		$_SESSION['error'] = 'Select item to delete first';
	}

	header('location: positions.php');
	
?>

