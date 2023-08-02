<?php 
    include 'includes/session.php';

    if(isset($_POST['id'])){
        $id = $_POST['id'];
        $sql = "SELECT * FROM positions WHERE id = $1";
        $result = pg_query_params($conn, $sql, array($id));
        $row = pg_fetch_assoc($result);

        echo json_encode($row);
    }
?>

