<?php
	include 'includes/session.php';
	include 'includes/slugify.php';

	if(isset($_POST['vote'])){
		if(count($_POST) == 1){
			$_SESSION['error'][] = 'Please vote at least one candidate';
		}
		else{
			$_SESSION['post'] = $_POST;
			$sql = "SELECT * FROM positions";
			$query = pg_query($conn, $sql);
			$error = false;
			$sql_array = array();
			while($row = pg_fetch_assoc($query)){
				$position = slugify($row['description']);
				$pos_id = $row['id'];
				if(isset($_POST[$position])){
					if($row['max_vote'] > 1){
						if(count($_POST[$position]) > $row['max_vote']){
							$error = true;
							$_SESSION['error'][] = 'You can only choose '.$row['max_vote'].' candidates for '.$row['description'];
						}
						else{
							foreach($_POST[$position] as $key => $values){
								$sql_array[] = array("INSERT INTO votes (voters_id, candidate_id, position_id) VALUES ($1, $2, $3)", array($voter['id'], $values, $pos_id));
							}
						}
					}
					else{
						$candidate = $_POST[$position];
						$sql_array[] = array("INSERT INTO votes (voters_id, candidate_id, position_id) VALUES ($1, $2, $3)", array($voter['id'], $candidate, $pos_id));
					}
				}
			}

			if(!$error){
				foreach($sql_array as $sql_row){
					pg_query_params($conn, $sql_row[0], $sql_row[1]);
				}

				unset($_SESSION['post']);
				$_SESSION['success'] = 'Ballot Submitted';
			}
		}
	}
	else{
		$_SESSION['error'][] = 'Select candidates to vote first';
	}

	header('location: home.php');
?>

