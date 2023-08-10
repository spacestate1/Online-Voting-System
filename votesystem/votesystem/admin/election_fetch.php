<?php
include 'includes/session.php';

$id = $_POST['id'];
$sql = "SELECT * FROM elections WHERE id = '$id'";
$query = pg_query($conn, $sql);
$row = pg_fetch_assoc($query);

echo json_encode($row);
