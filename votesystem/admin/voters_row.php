<?php 
    include 'includes/session.php';

    if(isset($_POST['id'])){
        $id = $_POST['id'];
        $sql = "SELECT * FROM voters WHERE id = $1";
        $query = pg_query_params($conn, $sql, array($id));
        $row = pg_fetch_assoc($query);

        echo json_encode($row);
    }
?>

