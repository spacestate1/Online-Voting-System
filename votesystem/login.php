<?php
	session_start();
	include 'includes/conn-pgsql.php';

	if(isset($_POST['login'])){
		$voter = $_POST['voter'];
		$password = $_POST['password'];

		$sql = "SELECT * FROM voters WHERE voters_id = $1";
		$result = pg_query_params($conn, $sql, array($voter));

		if(pg_num_rows($result) < 1){
			$_SESSION['error'] = 'Cannot find voter with the ID';
		}
		else{
			$row = pg_fetch_assoc($result);
			if(password_verify($password, $row['password'])){
				$_SESSION['voter'] = $row['id'];
			}
			else{
				$_SESSION['error'] = 'Incorrect password';
			}
		}
		
	}
	else{
		$_SESSION['error'] = 'Input voter credentials first';
	}

	header('location: index.php');

?>

