<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="background-color:#F1E9D2 " >
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1><b>
        Candidates List
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
        <div class="col-xs-12">
          <div class="box" style="background-color: #d8d1bd">
            <div class="box-header with-border" style="background-color: #d8d1bd">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-curve" style="background-color: #4682B4 ;color:black ; font-size: 12px; font-family:Times"><i class="fa fa-plus"></i> New</a>
            </div>
            <div class="box-body">
              <table id="example1" class="table">
                <thead>
                  <th class="hidden"></th>
                  <th>Position</th>
                  <th>Election</th>
                  <th>Firstname</th>
                  <th>Lastname</th>
                  <th>Platform</th>
                  <th>Tools</th>
                </thead>
                <tbody>
                  <?php
                  try {
                    $sql = "SELECT *, candidates.id AS canid, positions.id as position_id, elections.name as election_name, positions.description as pos_desc FROM candidates LEFT JOIN positions ON positions.id=candidates.position_id LEFT JOIN elections ON elections.id=positions.election_id ORDER BY positions.priority ASC";
                    $result = pg_query($conn, $sql);
                    if (!$result) {
                      throw new Exception("An error occurred.\n");
                    }
                    while($row = pg_fetch_assoc($result)){
                      echo "
                        <tr style='color:black ; font-size: 15px; font-family:Times'>
                          <td class='hidden'></td>
                          <td>".$row['pos_desc']."</td>
                          <td>".$row['election_name']."</td>
                          <td>".$row['firstname']."</td>
                          <td>".$row['lastname']."</td>
                          <td><a href='#platform' data-toggle='modal' class='btn btn-info btn-sm btn-curve platform'  style='background-color: #00BFFF ;color:black ; font-size: 12px; font-family:Times' data-id='".$row['canid']."'><i class='fa fa-search'></i> View</a></td>
                          <td>
                            <button class='btn btn-success btn-sm edit btn-curve' style='background-color: #9CD095 ;color:black ; font-size: 12px; font-family:Times'  data-id='".$row['canid']."' ><i class='fa fa-edit'></i> Edit</button>
                            <button class='btn btn-danger btn-sm delete btn-curve' style='background-color:#ff8e88 ;color:black ; font-size: 12px; font-family:Times' data-id='".$row['canid']."'><i class='fa fa-trash'></i> Delete</button>                          
                          </td>
                        </tr>
                      ";
                    }
                  } catch (Exception $e) {
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
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
  <?php include 'includes/candidates_modal.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script src="candidates.js"></script>

<script>
$(function(){
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
$("#edit_form").submit(function(e){
    e.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        type: "POST",
        url: "candidates_edit.php",
        data: formData,
        dataType: "json",
        success: function(response)
        {

$('#edit').modal('hide');
         if(response.status){
            alert(response.message);
            location.reload();
         }
         else{
            alert(response.message);
         }
        },
        error: function()
        {
            alert('Error');
      }
    });
  });
});

$(document).on('click', '.delete', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    if(confirm('Are you sure you want to delete this candidate?')){
        $.ajax({
            type: "POST",
            url: "candidates_delete.php",
            data: {id:id},
            dataType: "json",
            success: function(response)
            {
                if(response.success){
                    alert('Candidate deleted successfully');
                    location.reload();
                }
                else{
                    alert(response.error);
                }
            },
            error: function()
            {
                alert('An error occurred. Please try again later.');
            }
        });
    }
});

$(document).on('click', '[data-target="#platformModal"]', function() {
  var id = $(this).data('id');
  $.ajax({
    type: 'POST',
    url: 'candidates_platform.php',
    data: {id:id},
    success: function(response){
      $('#platformBody').html(response);
    }
  });
});
function getPlatform(id){
  $.ajax({
    type: 'POST',
    url: 'candidates_platform.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('#platform_view').html(response.platform);
      $('#platform').modal('show');
    },
    error: function(){
      alert('An error occurred. Please try again later.');
    }
  });
}

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'candidates_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.id').val(response.result.canid);  // This line sets the ID
      $('#edit_firstname').val(response.result.firstname);
      $('#edit_lastname').val(response.result.lastname);
      $('#edit_position').val(response.result.position_id);
      $('#edit_platform').val(response.result.platform);
      $('#edit_photo').val(response.result.photo);
      $('#platform_view').html(response.result.platform);
    }
  });
}
</script>
</body>
</html>

