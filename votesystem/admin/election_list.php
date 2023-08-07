<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="background-color:#F1E9D2 ">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><b>
        Elections List
      </b></h1>
      <ol class="breadcrumb" style="color:black ; font-size: 17px; font-family:Times">
        <li><a href="#"><i class="fa fa-dashboard" ></i> Home</a></li>
        <li class="active" style="color:black ; font-size: 17px; font-family:Times" >Dashboard</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12" >
          <div class="box" style="background-color: #d8d1bd">
            <div class="box-header with-border" style="background-color: #d8d1bd">
            <a href="#createElection" data-toggle="modal" class="btn btn-primary btn-sm btn-curve " style="background-color: #4682B4 ;color:black ; font-size: 12px; font-family:Times"><i class="fa fa-plus"></i> New</a>
            </div>
            <div class="box-body">
              <table id="example1" class="table ">
                <thead>
                  <th>Election Title</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM elections";
                    $query = pg_query($conn, $sql);
                    while($row = pg_fetch_assoc($query)){
                  ?>
                  <tr style='color:black ; font-size: 15px; font-family:Times'>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['start_date']; ?></td>
                    <td><?php echo $row['end_date']; ?></td>
                    <td>
                      <a href="#editElection" data-toggle="modal" data-id="<?php echo $row['id']; ?>" class="btn btn-success btn-sm edit-btn" style="background-color: #4682B4 ;color:black ; font-size: 12px; font-family:Times">
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a href="election_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" style="background-color: #DC143C ;color:black ; font-size: 12px; font-family:Times" onclick="return confirm('Are you sure you want to delete this record?')">
                        <i class="fa fa-trash"></i> Delete
                      </a>
                    </td>
                  </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
    
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/elections_modal.php'; ?>
  <!-- include or place the "editElection" modal here -->
</div>
<?php include 'includes/scripts.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');

        // Fetch the data for this election from the server
        $.ajax({
            type: 'POST',
            url: 'election_fetch.php',  // This script should return the election data as JSON
            data: {id: id},
            dataType: 'json',
            success: function(response) {
                $('#edit_id').val(response.id);
                $('#edit_election_title').val(response.name);
                $('#edit_start_date').val(response.start_date);
                $('#edit_end_date').val(response.end_date);
            }
        });
    });
});
</script>
</body>
</html>
