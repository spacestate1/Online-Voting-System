<?php 
include 'includes/session.php';

// Check if position ID is set in the URL
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header('location: positions.php');
    exit;
}

// Fetch all elections for the dropdown
$elections_sql = "SELECT * FROM elections";
$elections_query = pg_query($conn, $elections_sql);
$elections = [];
while($election = pg_fetch_assoc($elections_query)) {
    $elections[] = $election;
}

// Fetch the position you want to edit
$position_id = $_GET['id'];
$position_sql = "SELECT * FROM positions WHERE id = $1";
$position_stmt = pg_prepare($conn, "fetch_position", $position_sql);
$position_result = pg_execute($conn, "fetch_position", array($position_id));
$position = pg_fetch_assoc($position_result);

if(!$position) {
    $_SESSION['error'] = 'Position not found!';
    header('location: positions.php');
    exit;
}

// When the form is submitted to update the position
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = $_POST['description'];
    $max_vote = $_POST['max_vote'];
    $priority = $_POST['priority'];
    $election_id = $_POST['election_id'];

    // Server-side validation
    // Check if election_id exists in the elections table
    $validation_sql = "SELECT * FROM elections WHERE id = $1";
    $validation_stmt = pg_prepare($conn, "validate_election", $validation_sql);
    $validation_result = pg_execute($conn, "validate_election", array($election_id));
    if(!pg_fetch_assoc($validation_result)) {
        $_SESSION['error'] = 'Invalid election selected!';
        header('location: edit_position.php?id='.$position_id);
        exit;
    }
 // Check for duplicates
    $check_sql = "SELECT * FROM positions WHERE description = $1 AND max_vote = $2 AND priority = $3 AND election_id = $4 AND id != $5";
    $check_stmt = pg_prepare($conn, "check_duplicate", $check_sql);
    $check_result = pg_execute($conn, "check_duplicate", array($description, $max_vote, $priority, $election_id, $position_id));
    if(pg_num_rows($check_result) > 0) {
        $_SESSION['error'] = 'A position with similar details already exists.';
        header('location: edit_position.php?id='.$position_id);
        exit;
    }
    // Update the position details
    $update_sql = "UPDATE positions SET description = $1, max_vote = $2, priority = $3, election_id = $4 WHERE id = $5";
    $update_stmt = pg_prepare($conn, "update_position", $update_sql);
    $updated = pg_execute($conn, "update_position", array($description, $max_vote, $priority, $election_id, $position_id));

    if($updated) {
        $_SESSION['success'] = 'Position updated successfully!';
    } else {
        $_SESSION['error'] = 'Failed to update position!';
    }
    header('location: positions.php');
    exit;
}

?>

<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Edit Position
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="positions.php">Positions</a></li>
        <li class="active">Edit</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
              <form method="POST" action="edit_position.php?id=<?= $position_id ?>">
                  <div class="form-group">
                      <label for="description">Description:</label>
                      <input type="text" name="description" id="description" value="<?= $position['description'] ?>" required>
                  </div>

                  <div class="form-group">
                      <label for="max_vote">Max Vote:</label>
                      <input type="number" name="max_vote" id="max_vote" value="<?= $position['max_vote'] ?>" required>
                  </div>

                  <div class="form-group">
                      <label for="priority">Priority:</label>
                      <input type="number" name="priority" id="priority" value="<?= $position['priority'] ?>" required>
                  </div>

                  <div class="form-group">
                      <label for="edit_election">Election:</label>
                      <select id="edit_election" name="election_id">
                          <?php foreach($elections as $election): ?>
                              <option value="<?= $election['id'] ?>" <?= ($position['election_id'] == $election['id'] ? 'selected' : '') ?>><?= $election['name'] ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>

                  <div class="form-group">
                      <button type="submit" class="btn btn-primary">Update Position</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
  
  <?php include 'includes/footer.php'; ?>

</div>
<?php include 'includes/scripts.php'; ?>

</body>
</html>
