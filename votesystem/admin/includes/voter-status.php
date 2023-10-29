<!-- voters-status.php -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/dt-1.10.24/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.3.1/dt-1.10.24/datatables.min.j

<div class="voters-section">
    <div class="voters-count">
        <?php
            $sql = "SELECT * FROM voters";
            $query = pg_query($conn, $sql);
            echo "<h3>".pg_num_rows($query)."</h3>";
        ?>
        <p><b>Total Voters</b></p>
    </div>

    <div class="icon">
        <i class="fa fa-users"></i>
    </div>

    <!-- Start of Voters' Online Status Section -->
    <div class="row">
        <div class="col-xs-12">
