<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<?php
$query = "SELECT id, name FROM elections";
$elections = pg_query($conn, $query);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title> Vote Records</title>
</head>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper" style="background-color:#F1E9D2; color:black; font-size: 17px; font-family:Times;">
            <section class="content-header" style="color:black; font-size: 17px; font-family:Times;">
                <h1>
                    VOTES
                </h1>
                <ol class="breadcrumb" style="color:black; font-size: 17px; font-family:Times;">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active" style="color:black; font-size: 17px; font-family:Times">Dashboard</li>
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                <?php
                if (isset($_GET['delete'])) {
                    $fileToDelete = urldecode($_GET['delete']);
                    $pathToFile = "/var/www/html/vote-records/" . $fileToDelete;

                    if (file_exists($pathToFile)) {
                        unlink($pathToFile);
                        $_SESSION['message'] = "File deleted successfully!";
                        $_SESSION['msg_type'] = "success";
                    } else {
                        $_SESSION['message'] = "File not found!";
                        $_SESSION['msg_type'] = "danger";
                    }

                    // Redirect to the same page to prevent resubmission
                    header("Location: votes.php");
                    exit;
                }

                if (isset($_SESSION['message'])) {
                    echo "<div class='alert alert-" . htmlspecialchars($_SESSION['msg_type'], ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "</div>";
                    unset($_SESSION['message']);
                }
                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box" style="background-color: #d8d1bd">
                            <div class="box-header with-border" style="background-color: #d8d1bd">
                                <a href="#reset" data-toggle="modal" class="btn btn-danger btn-sm btn-curve" style="background-color: #ff8e88; color:black; font-size: 12px; font-family:Times">
                                    <i class="fa fa-refresh"></i> Reset
                                </a>
                                <a href="#generatePDF" data-toggle="modal" class="btn btn-success btn-sm btn-curve" style="margin-left: 10px;">
                                    <i class="fa fa-file-pdf-o"></i> Generate PDF
                                </a>

<a href="#generateResults" data-toggle="modal" class="btn btn-primary btn-sm btn-curve" style="margin-left: 10px;">
    <i class="fa fa-bar-chart"></i> Generate Results
</a>
                            </div>

                            <!-- File Listing -->
                            <div class="box-body">
                                <?php
                                $dir = "/var/www/html/vote-records";
                                $base_url = "download.php?file=";
                                $files = scandir($dir);
                                if ($files) {
                                    echo "<div class='panel panel-default'>";
                                    echo "<div class='panel-heading'>Vote Records Files</div>";
                                    echo "<div class='panel-body'>";
                                    echo "<ul class='file-list'>";
                                    foreach ($files as $file) {
                                        if ($file !== "." && $file !== "..") {
                                            $encodedFile = urlencode($file);
                                            echo "<li>";
                                            echo "<a href='" . htmlspecialchars($base_url . $encodedFile, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . "</a>";
                                            echo " <a href='votes.php?delete=" . $encodedFile . "' class='btn btn-danger btn-xs'>Delete</a>"; // Add this line
                                            echo "</li>";
                                        }
                                    }
                                    echo "</ul>";
                                    echo "</div>";
                                    echo "</div>";
                                }
                                ?>
                                <table id="example1" class="table ">
                                    <!-- Table header and body code -->
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include 'includes/footer.php'; ?>

        <!-- Modals -->
        <!-- Reset Modal -->
        <div class="modal fade" id="reset">
            <div class="modal-dialog">
                <div class="modal-content" style="background-color: #d8d1bd; color:black; font-size: 15px; font-family:Times">
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
                                            <option value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-curve pull-left" style='background-color: #FFDEAD;color:black; font-size: 12px; font-family:Times' data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                                <button type="submit" class="btn btn-danger btn-curve" style='background-color: #ff8e88;color:black; font-size: 12px; font-family:Times'><i class="fa fa-refresh"></i> Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

 <!-- PDF Generation Modal -->
        <div class="modal fade" id="generatePDF">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><b>Generate PDF for Election</b></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" method="POST" action="generate_pdf.php">
                            <div class="form-group">
                                <label for="election_pdf_id" class="col-sm-3 control-label">Election</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="election_pdf_id" id="election_pdf_id" required>
                                        <option value="" disabled selected>Select Election</option>
                                        <?php 
                                        // Fetch elections again for this dropdown
                                        $elections_pdf = pg_query($conn, $query); 
                                        while($row_pdf = pg_fetch_assoc($elections_pdf)): ?>
                                            <option value="<?php echo htmlspecialchars($row_pdf['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row_pdf['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Generate PDF</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<!-- END PDF -->

<div class="modal fade" id="generateResults">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>Generate Election Results</b></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="election-results.php">
                    <div class="form-group">
                        <label for="election_results_id" class="col-sm-3 control-label">Election</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="selectedElection" id="election_results_id" required>
                                <option value="" disabled selected>Select Election</option>
                                <?php 
                                // Fetch elections again for this dropdown
                                $elections_results = pg_query($conn, $query); 
                                while($row_results = pg_fetch_assoc($elections_results)): ?>
                                    <option value="<?php echo htmlspecialchars($row_results['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row_results['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Generate Results</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

        <!-- Include Scripts -->
        <?php include 'includes/scripts.php'; ?>


    </div>
</body>

</html>
