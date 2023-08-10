<!DOCTYPE html>
<html>
<head>
    <?php include 'includes/header.php'; ?>
    <link rel="stylesheet" href="css/style.css"> <!-- Your CSS Stylesheet -->
</head>
<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <b>WECO Voting System</b>
        </div>
      
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your voting session</p>

            <form action="login.php" method="POST">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" name="user_id" placeholder="User ID" required>
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-curve" name="login">
                            <i class="fa fa-sign-in"></i> Sign In
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
            if(isset($_SESSION['error'])){
                echo "
                    <div class='callout callout-danger text-center mt20'>
                        <p>".$_SESSION['error']."</p> 
                    </div>
                ";
                unset($_SESSION['error']);
            }
        ?>
    </div>
        
    <?php include 'includes/scripts.php' ?>
</body>
</html>

