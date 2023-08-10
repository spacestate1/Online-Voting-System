<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		try {
			$firstname = pg_escape_string($conn, $_POST['firstname']);
			$lastname = pg_escape_string($conn, $_POST['lastname']);
			$position = pg_escape_string($conn, $_POST['position']);
			$platform = pg_escape_string($conn, $_POST['platform']);

			// Getting the election_id from positions table
			$sql = "SELECT election_id FROM positions WHERE id = '$position'";
			$query = pg_query($conn, $sql);

			if(!$query){
				throw new Exception('Failed to retrieve election id. ' . pg_last_error($conn));
			}
			$row = pg_fetch_assoc($query);
			$election = $row['election_id'];

			if(!$election){
				throw new Exception('No election associated with this position.');
			}

			$sql = "INSERT INTO candidates (election_id, position_id, firstname, lastname, platform) VALUES ('$election', '$position', '$firstname', '$lastname', '$platform')";
			$result = pg_query($conn, $sql);

			if(!$result){
				throw new Exception(pg_last_error($conn));
			}
			$_SESSION['success'] = 'Candidate added successfully';
		} catch (Exception $e) {
			$_SESSION['error'] = $e->getMessage();
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: candidates.php');
?>

