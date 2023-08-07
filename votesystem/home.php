<?php 
include 'includes/session.php'; 
include 'includes/header.php'; 
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/sidebar.php'; ?> 

  <div class="content-wrapper" style="background-color: #F1E9D2 ">
    <div class="container" style="background-color: #F1E9D2 ">

    <!-- Main content -->
    <section class="content">
        <h1 class="page-header text-center title"><b>Select Election</b></h1>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <form method="POST" id="electionForm" action="submit_election.php">
                    <div class="form-group">
                        <label for="election">Election:</label>
                        <select class="form-control" name="election_id" id="election" required>
                            <?php
                                $sql = "SELECT * FROM elections";
                                $query = pg_query($conn, $sql);

                                // Check if the query was successful
                                if($query === false) {
                                    echo "Error: Failed to execute query. Please check your SQL syntax and database connection.";
                                } else {
                                    while($row = pg_fetch_assoc($query)){
                                        echo "<option value='".$row['id']."'>".$row['name']."</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Go</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/profile_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>
