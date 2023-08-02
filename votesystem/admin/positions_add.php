<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$description = $_POST['description'];
		$max_vote = $_POST['max_vote'];

		$sql = "SELECT * FROM positions ORDER BY priority DESC LIMIT 1";
		$query = pg_query($conn, $sql);
		$row = pg_fetch_assoc($query);

		$priority = $row['priority'] + 1;
		
		$sql = "INSERT INTO positions (description, max_vote, priority) VALUES ($1, $2, $3)";
		$result = pg_query_params($conn, $sql, array($description, $max_vote, $priority));
		if($result){
			$_SESSION['success'] = 'Position added successfully';
		}
		else{
			$_SESSION['error'] = pg_last_error($conn);
		}

	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: positions.php');
?>

