<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = pg_escape_string($conn, $_POST['id']);
		$firstname = pg_escape_string($conn, $_POST['firstname']);
		$lastname = pg_escape_string($conn, $_POST['lastname']);
		$position = isset($_POST['position']) && $_POST['position'] !== "" ? pg_escape_string($conn, $_POST['position']) : null;
		$platform = pg_escape_string($conn, $_POST['platform']);

		if($position !== null){
			$sql = "UPDATE candidates SET firstname = $1, lastname = $2, position_id = $3, platform = $4 WHERE id = $5";
			$result = pg_query_params($conn, $sql, array($firstname, $lastname, $position, $platform, $id));

			if($result){
				$_SESSION['success'] = 'Candidate updated successfully';
			}
			else{
				$_SESSION['error'] = pg_last_error($conn);
			}
		}else{
			$_SESSION['error'] = 'Invalid position value';
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: candidates.php');
?>

