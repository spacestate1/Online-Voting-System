<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
$query = "SELECT id, name FROM elections";
$elections = pg_query($conn, $query);
?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" style="background-color:#F1E9D2 ;color:black ; font-size: 17px; font-family:Times ">
    
    <!-- Content Header (Page header) -->
    <section class="content-header" style= "color:black ; font-size: 17px; font-family:Times">
      <h1>
        VOTES
      </h1>
      <ol class="breadcrumb" style="color:black ; font-size: 17px; font-family:Times">
        <li><a href="#"><i class="fa fa-dashboard" ></i> Home</a></li>
        <li class="active" style="color:black ; font-size: 17px; font-family:Times" >Dashboard</li>
      </ol>
    </section>
    
    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['message'])){
          echo "<div class='alert alert-".$_SESSION['msg_type']."'>".$_SESSION['message']."</div>";
          unset($_SESSION['message']);
        }
      ?>
      
      <div class="row">
        <div class="col-xs-12">
          <div class="box" style="background-color: #d8d1bd">
            <div class="box-header with-border" style="background-color: #d8d1bd">
              <a href="#reset" data-toggle="modal" class="btn btn-danger btn-sm btn-curve"  style="background-color: #ff8e88;color:black ; font-size: 12px; font-family:Times"><i class="fa fa-refresh"></i> Reset</a>
            </div>
            <div class="box-body">
              <table id="example1" class="table ">
                <!-- Table header and body code... (this section remains unchanged) -->
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
    
  <?php include 'includes/footer.php'; ?>

  <!-- Reset Modal -->
  <div class="modal fade" id="reset">
    <div class="modal-dialog">
      <div class="modal-content" style="background-color: #d8d1bd ;color:black ; font-size: 15px; font-family:Times ">
        <div class="modal-header">
          <button type="button" class="btn btn-close btn-curve pull-right" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b>Reseting...</b></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="votes_reset.php">
            <div class="form-group">
              <label for="election_id" class="col-sm-3 control-label">Election</label>
              <div class="col-sm-9">
                <select class="form-control" name="election_id" id="election_id" required>
                  <option value="" disabled selected>Select Election</option>
                  <?php while($row = pg_fetch_assoc($elections)): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
            </div>





            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-curve pull-left" style='background-color: #FFDEAD;color:black; font-size: 12px; font-family:Times' data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-danger btn-curve" style='background-color: #ff8e88;color:black ; font-size: 12px; font-family:Times'><i class="fa fa-refresh"></i> Reset</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- End Reset Modal -->

  <?php include 'includes/scripts.php'; ?>
</body>
</html>
