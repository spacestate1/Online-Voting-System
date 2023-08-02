<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		try {
			$firstname = pg_escape_string($conn, $_POST['firstname']);
			$lastname = pg_escape_string($conn, $_POST['lastname']);
			$position = pg_escape_string($conn, $_POST['position']);
			$platform = pg_escape_string($conn, $_POST['platform']);
			$filename = $_FILES['photo']['name'];
			if(!empty($filename)){
				move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
			}

			$sql = "INSERT INTO candidates (position_id, firstname, lastname, photo, platform) VALUES ('$position', '$firstname', '$lastname', '$filename', '$platform')";
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

