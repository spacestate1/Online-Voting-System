<!-- voters-status.php -->

<?php 
    // Fetch the voters, their login status, and last login date
    $votersQuery = "SELECT firstname, lastname, is_logged_in, last_login FROM voters";
    $votersResult = pg_query($conn, $votersQuery);
    $votersData = pg_fetch_all($votersResult);
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Voters' Status</h3>
    </div>

    <div class="box-body">
        <table id="votersTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Last Login</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($votersData as $voter): ?>
                    <tr>
                        <td><?php echo $voter['firstname'] . ' ' . $voter['lastname']; ?></td>
                        <td>
                            <span class="status-dot <?php echo $voter['is_logged_in'] === 't' ? 'online' : 'offline'; ?>"></span>
                            <?php echo $voter['is_logged_in'] === 't' ? 'Online' : 'Offline'; ?>
                        </td>
                        <td><?php echo $voter['last_login'] !== null ? date("Y-m-d H:i:s", strtotime($voter['last_login'])) : 'Never'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables jQuery plugin for table sorting functionality -->
<script>
    $(function () {
        $('#votersTable').DataTable({
            'paging'      : true,
            'lengthChange': true,
            'searching'   : true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false
        });
    });
</script>

<style>
    .status-dot.online {
        background-color: #4CAF50; /* green */
        border-radius: 50%;
        display: inline-block;
        width: 10px;
        height: 10px;
    }

    .status-dot.offline {
        background-color: #f44336; /* red */
        border-radius: 50%;
        display: inline-block;
        width: 10px;
        height: 10px;
    }

    /* Modified header color to a shade of blue */
    #votersTable thead th {
        background-color: #007BFF;
        color: white;
    }
</style>

