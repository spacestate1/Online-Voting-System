<?php 
include 'includes/session.php';
include 'includes/slugify.php';
include 'includes/header.php'; 
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <div class="content-wrapper bg-light">
            <section class="content-header text-black text-lg">
                <h1 class="font-weight-bold">
                    Dashboard
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#" class="text-black text-lg"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active text-black text-lg">Dashboard</li>
                </ol>
            </section>

            <section class="content">
                <?php include 'includes/statistics.php'; ?>
                <?php include 'includes/election-history.php'; ?>
                <?php include 'includes/voters-status.php'; ?>
            </section>
        </div>

        <?php include 'includes/footer.php'; ?>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>
