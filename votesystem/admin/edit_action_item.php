<?php
include 'includes/session.php';
include 'includes/conn-pgsql.php';

// Check if an ID has been provided
if(!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'No action item specified.';
    header('Location: action_items.php');
    exit;
}

$id = intval($_GET['id']);

// Fetch all elections for the dropdown
$elections_sql = "SELECT * FROM elections";
$elections_query = pg_query($conn, $elections_sql);
$elections = [];
while($election = pg_fetch_assoc($elections_query)) {
    $elections[] = $election;
}

$query = "SELECT * FROM action_items WHERE id = $1";
$result = pg_query_params($conn, $query, array($id));

if(pg_num_rows($result) <= 0) {
    $_SESSION['error'] = 'Action item not found.';
    header('Location: action_items.php');
    exit;
}

$action_item = pg_fetch_assoc($result);
?>

<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Edit Action Item
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="action_items.php">Action Items</a></li>
        <li class="active">Edit</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-body">
              <form method="POST" action="action_items_edit.php">
                  <input type="hidden" name="id" value="<?php echo $action_item['id']; ?>">
                  <div class="form-group">
                      <label for="title">Title:</label>
                      <input type="text" name="title" value="<?php echo $action_item['title']; ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="description">Description:</label>
                      <textarea name="description"><?php echo $action_item['description']; ?></textarea>
                  </div>
                  <div class="form-group">
                      <label for="election">Election:</label>
                      <select id="election" name="election_id">
                          <?php foreach($elections as $election): ?>
                              <option value="<?= $election['id'] ?>" <?= ($action_item['election_id'] == $election['id'] ? 'selected' : '') ?>><?= $election['name'] ?></option>
                          <?php endforeach; ?>
                      </select>
                  </div>
                  <div class="form-group">
                      <button type="submit" class="btn btn-primary">Update Action Item</button>
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
