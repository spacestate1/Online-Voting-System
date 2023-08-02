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
     </b> </h1>
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
                  <th>Photo</th>
                  <th>Firstname</th>
                  <th>Lastname</th>
                  <th>Platform</th>
                  <th>Tools</th>
                </thead>
                <tbody>
                  <?php
                  try {
                    $sql = "SELECT *, candidates.id AS canid FROM candidates LEFT JOIN positions ON positions.id=candidates.position_id ORDER BY positions.priority ASC";
                    $result = pg_query($conn, $sql);
          
                    if (!$result) {
                      throw new Exception("An error occurred.\n");
                    }
          
                    while($row = pg_fetch_assoc($result)){
                      $image = (!empty($row['photo'])) ? '../images/'.$row['photo'] : '../images/profile.jpg';
                      echo "
                        <tr style='color:black ; font-size: 15px; font-family:Times'>
                          <td class='hidden'></td>
                          <td>".$row['description']."</td>
                          <td>
                            <img src='".$image."' width='30px' height='30px'>
                            <a href='#edit_photo' data-toggle='modal' class='pull-right photo' data-id='".$row['canid']."'><span class='fa fa-edit'></span></a>
                          </td>
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
<script>


	$(function(){
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id, function() {
      $('#edit').modal('show');
    });
  });
  
 $(document).on('click', '.delete', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    console.log("ID to delete: ", id); // Log the ID to the console
    $('#delete').modal({
        backdrop: 'static',
        keyboard: false
    }).one('click', '#confirm_delete', function(e) {
        $.ajax({
            type: 'POST',
            url: 'candidates_delete.php',
            data: {id:id},
            dataType: 'json',
            success: function(response){
                if(response.error) {
                    console.error("Error deleting candidate: ", response.error);
                } else {
                    location.reload();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: ", textStatus, ", ", errorThrown);
            }
        });
    });
});
 
  $(document).on('click', '.photo', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.platform', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    getRow(id);
  });
});

function getRow(id, callback){
  $.ajax({
    type: 'POST',
    url: 'candidates_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      if(response.error){
        alert(response.error);
      } else {
        callback(response);
      }
    }
  });
}
</script>
</body>
</html>

