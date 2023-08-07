<?php
include 'includes/session.php';
include 'includes/header.php';
?>

<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
   
  <div class="content-wrapper" style="background-color: #F1E9D2 ">
    <div class="container" style="background-color: #F1E9D2 ">

    <!-- Main content -->
    <section class="content">
        <h1 class="page-header text-center title"><b>Voting Confirmation</b></h1>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <?php
                    if(isset($_SESSION['success'])){
                        echo "
                            <div class='alert alert-success'>
                                ".$_SESSION['success']."
                            </div>
                        ";
                        unset($_SESSION['success']);
                    }

                    if(isset($_SESSION['error'])){
                        echo "
                            <div class='alert alert-danger'>
                                ".$_SESSION['error']."
                            </div>
                        ";
                        unset($_SESSION['error']);
                    }
                ?>
                <div class="text-center">
                    <p>Your vote has been successfully registered.</p>
                    <p>Thank you for participating in the election.</p>
                </div>
            </div>
        </div>
    </section>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>
