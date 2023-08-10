<?php
include 'includes/session.php';
include 'includes/conn-pgsql.php';

try {
    // Get the id from the POST request
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Make sure the id is valid
    if($id <= 0){
        throw new Exception("Invalid ID.");
    }

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("SELECT * FROM action_items WHERE id = :id");
    $stmt->execute(['id'=>$id]);

    // Fetch result
    $row = $stmt->fetch();

    // Check if a row was returned
    if(!$row){
        throw new Exception("No action item found with that ID.");
    }

    // Sanitize output before sending it back
    $output = array();
    foreach($row as $key => $value){
        $output[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    // Send back data as JSON
    echo json_encode($output);
    
} catch (PDOException $e) {
    // If any error occurs, return the error message
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    // If any error occurs, return the error message
    echo json_encode(['error' => $e->getMessage()]);
}
?>

