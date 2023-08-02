<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$userid = $_POST['userid']; // Updated line
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$password = $_POST['password'];

		$sql = "SELECT * FROM voters WHERE id = $1";
		$query = pg_query_params($conn, $sql, array($id));
		$row = pg_fetch_assoc($query);

		// Check if new user_id is unique
		$sql_check = "SELECT * FROM voters WHERE user_id = $1 AND id != $2";
		$query_check = pg_query_params($conn, $sql_check, array($userid, $id));
		if(pg_num_rows($query_check) > 0){
			$_SESSION['error'] = 'User ID already exists';
			header('location: voters.php');
			exit();
		}

		if($password == $row['password']){
			$password = $row['password'];
		}
		else{
			$password = password_hash($password, PASSWORD_DEFAULT);
		}

		$sql = "UPDATE voters SET user_id = $1, firstname = $2, lastname = $3, email = $4, password = $5 WHERE id = $6";
		if(pg_query_params($conn, $sql, array($userid, $firstname, $lastname, $email, $password, $id))){
			$_SESSION['success'] = 'Voter updated successfully';
		}
		else{
			$_SESSION['error'] = pg_last_error($conn);
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location: voters.php');
?>

