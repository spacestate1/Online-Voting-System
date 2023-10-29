<?php
// User data related logic
$userImage = (!empty($user['photo'])) ? '../images/'.$user['photo'] : '../images/profile.jpg';
$userName = $user['firstname'].' '.$user['lastname'];
$memberSince = date('M. Y', strtotime($user['created_on']));
?>

<header class="main-header" style="background-color:#717A83">

    <!-- Logo -->
    <a href="#" class="logo" style="background-color: #717A83">
        <span class="logo-mini" style="background-color: #717A83"><b>O</b>VS</span>
        <span class="logo-lg" style="background-color:#717A83;color:white;font-size:22px;font-family:Times"><marquee behavior="scroll" direction="left">Wyoming Energy Co-Op Voting System</marquee></span>
    </a>
  
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" style="background-color: #717A83">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu" style="color:black; font-size:17px; font-family:Times">
            <ul class="nav navbar-nav" style="background-color: #717A83;color:black;font-size:17px;font-family:Times">
                <!-- User Account -->
                <li class="dropdown user user-menu" style="color:black">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="background-color: #717A83;color:white;font-size:17px;font-family:Times">
                        <img src="<?= $userImage ?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?= $userName ?></span>
                    </a>
                    <ul class="dropdown-menu" style="background-color: #4d5863;color:black;font-size:17px;font-family:Times">
                        <!-- User image -->
                        <li class="user-header" style="background-color:#4d5863;color:black;font-size:17px;font-family:Times">
                            <img src="<?= $userImage ?>" class="img-circle" alt="User Image">
                            <p>
                                <?= $userName ?>
                                <small>Member since <?= $memberSince ?></small>
                            </p>
                        </li>
                        <!-- Footer -->
                        <li class="user-footer" style="background-color: #90979e;color:white;font-size:17px;font-family:Times">
                            <div class="pull-left" style="background-color: #90979e;color:white;font-size:17px;font-family:Times">
                                <a href="#profile" data-toggle="modal" class="btn btn-default btn-curve" style="background-color: #d2d5d8" id="admin_profile">Update</a>
                            </div>
                            <div class="pull-right" style="background-color:#90979e;color:black;font-size:17px;font-family:Times">
                                <a href="logout.php" class="btn btn-default btn-curve" style="background-color: #d2d5d8">Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<?php include 'includes/profile_modal.php'; ?>
