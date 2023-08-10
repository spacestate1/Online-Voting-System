<?php 
  include 'includes/session.php';

  if(!isset($_SESSION['admin'])){
    header('location: index.php');
  }

  $sql = "SELECT *, positions.id AS posid FROM positions LEFT JOIN elections ON elections.id=positions.election_id";
  $query = pg_query($conn, $sql);
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Positions
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Positions</li>
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
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th>Election</th>
                  <th>Description</th>
                  <th>Max Vote</th>
                  <th>Tools</th>
                </thead>
                <tbody>
                  <?php
                    while($row = pg_fetch_assoc($query)){
                      echo "
                        <tr>
                          <td>".$row['name']."</td>
                          <td>".$row['description']."</td>
                          <td>".$row['max_vote']."</td>
                          <td>
                           <a href='edit_position.php?id=".$row['posid']."' class='btn btn-success btn-sm btn-flat'><i class='fa fa-edit'></i> Edit</a> 

<a href='delete_position.php?id=".$row['posid']."' onclick=\"return confirm('Are you sure you want to delete this position? This will also delete all associated candidates and votes.')\" class='btn btn-danger btn-sm btn-flat'><i class='fa fa-trash'></i> Delete</a>                          

</td>
                        </tr>
                      ";
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
  <?php include 'includes/positions_modal.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script>

$(document).ready(function() {

    // When the 'Edit' button is clicked
$(document).on('click', '.edit', function(e) {
    e.preventDefault();
    var id = $(this).data('id');

    $.ajax({
        url: 'positions_row.php',
        method: 'POST',
        data: {
            id: id
        },
        dataType: 'json',
        success: function(response) {
            var expectedKeys = ["posid", "description", "max_vote", "election_id", "priority", "start_date"];
            var hasAllKeys = expectedKeys.every(key => response.hasOwnProperty(key));

            if (hasAllKeys) {
                // Get the current date and time
                var now = new Date();

                // Parse the start date from the response
                var startDate = new Date(response.start_date);

                // If the start date is greater than the current date and time, the election hasn't started yet
                if (startDate > now) {
                    // Fill the form fields
                    $('.id').val(response.posid);
                    $('#edit_description').val(response.description);
                    $('#edit_max_vote').val(response.max_vote);
                    $('#edit_election').val(response.election_id);
                    $('#edit_priority').val(response.priority);

                    // Show the modal
                    $('#edit').modal('show');
                } else {
                    alert("This election has already started and cannot be edited.");
                }
            } else {
                console.error("Unexpected response structure from positions_row.php:", response);
                alert('Data fetched has unexpected structure. Please contact system administrator.');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX request failed:", textStatus, errorThrown);
            alert('Could not fetch data. Please try again.');
        }
    });
});


    // When the 'Delete' button is clicked
    $(document).on('click', '.delete', function(e) {
        e.preventDefault();
        $('#delete').modal('show');
        var id = $(this).data('id');
        getRow(id);
    });

    // Function to fetch a row's details
    function getRow(id) {
        $.ajax({
            type: 'POST',
            url: 'positions_row.php',
            data: {
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response && response.posid) {
                    $('.id').val(response.posid);
                } else {
                    console.error("Unexpected response structure from positions_row.php during deletion:", response);
                    alert('Data fetched for deletion has unexpected structure. Please contact system administrator.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX request failed for getRow:", textStatus, errorThrown);
                alert('Could not fetch data for deletion. Please try again.');
            }
        });
    }
});

</script>

</body>
</html>
