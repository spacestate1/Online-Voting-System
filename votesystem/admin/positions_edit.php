<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$description = $_POST['description'];
		$max_vote = $_POST['max_vote'];

		$sql = "UPDATE positions SET description = $1, max_vote = $2 WHERE id = $3";
		$result = pg_query_params($conn, $sql, array($description, $max_vote, $id));
		if($result){
			$_SESSION['success'] = 'Position updated successfully';
		}
		else{
			$_SESSION['error'] = pg_last_error($conn);
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: positions.php');

?>

