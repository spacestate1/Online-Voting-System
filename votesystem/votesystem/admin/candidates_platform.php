<?php
include 'includes/conn-pgsql.php';

$id = $_POST['id'];

$sql = "SELECT platform FROM candidates WHERE id = $1";
$res = pg_query_params($conn, $sql, array($id));
$row = pg_fetch_assoc($res);

echo $row['platform'];
?>

