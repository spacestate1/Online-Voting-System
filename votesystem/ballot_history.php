<?php 
include 'includes/session.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <?php include 'includes/header.php'; ?>
    <link rel="stylesheet" href="css/style.css"> <!-- Your CSS Stylesheet -->
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="content-wrapper">
        <section class="content">
            <h1 class="page-header text-center title"><b>Ballot History</b></h1>
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                    <?php
                    $dir = "/var/www/html/vote-records";
                    $files = scandir($dir);
                    if($files) {
                        echo "<div class='panel panel-default'>";
                        echo "<div class='panel-heading'>Vote Records Files</div>";
                        echo "<div class='panel-body'>";
                        echo "<ul class='file-list'>";
                        foreach ($files as $file) {
                            if($file !== "." && $file !== "..") { 
                                echo "<li><a href='/vote-records/" . $file . "' download>" . $file . "</a></li>";                            
}
                        }
                        echo "</ul>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>
