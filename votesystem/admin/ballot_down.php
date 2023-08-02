<?php
	include 'includes/session.php';

	if(isset($_POST['id'])){
		$id = $_POST['id'];

		$sql = "SELECT * FROM positions";
		$pquery = pg_query($conn, $sql);

		$output = array('error'=>false);

		$sql = "SELECT * FROM positions WHERE id=$1";	
		$query = pg_query_params($conn, $sql, array($id));
		$row = pg_fetch_assoc($query);

		$priority = $row['priority'] + 1;

		if($priority > pg_num_rows($pquery)){
			$output['error'] = true;
			$output['message'] = 'This position is already at the bottom';
		}
		else{
			$sql = "UPDATE positions SET priority = priority - 1 WHERE priority = $1";
			pg_query_params($conn, $sql, array($priority));

			$sql = "UPDATE positions SET priority = $1 WHERE id = $2";
			pg_query_params($conn, $sql, array($priority, $id));
		}

		echo json_encode($output);

	}
	
?>

