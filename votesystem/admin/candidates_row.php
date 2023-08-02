<?php 
	include 'includes/session.php';

	if(isset($_POST['id'])){
		try {
			$id = pg_escape_string($conn, $_POST['id']);
			$sql = "SELECT *, candidates.id AS canid FROM candidates LEFT JOIN positions ON positions.id=candidates.position_id WHERE candidates.id = $1";
			$result = pg_query_params($conn, $sql, array($id));

			if(!$result){
				throw new Exception("An error occurred.\n");
			}

			$row = pg_fetch_assoc($result);
			echo json_encode(array('error' => false, 'result' => $row));

		} catch (Exception $e) {
			echo json_encode(array('error' => true, 'message' => $e->getMessage()));
		}
	}
?>

