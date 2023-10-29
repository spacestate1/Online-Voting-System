<!-- statistics.php -->

<?php
    // Connect to the database

    if (!$conn) {
        die("Connection failed: " . pg_last_error());
    }

    // Fetch current and future elections, their start and end dates, and the number of ballots cast for each election
    $query = "
        SELECT e.id, e.name, e.start_date, e.end_date,
               COALESCE(candidate_votes.count, 0) as candidate_ballot_count,
               COALESCE(action_item_votes.count, 0) as action_item_ballot_count
        FROM elections e
        LEFT JOIN (
            SELECT election_id, COUNT(*) as count
            FROM votes
            GROUP BY election_id
        ) candidate_votes ON e.id = candidate_votes.election_id
        LEFT JOIN (
            SELECT election_id, COUNT(DISTINCT voters_id) as count
            FROM action_item_votes
            GROUP BY election_id
        ) action_item_votes ON e.id = action_item_votes.election_id
        WHERE e.end_date >= NOW()
        ORDER BY e.start_date;
    ";
 
    $result = pg_query($conn, $query);
    if (!$result) {
        die("Query failed: " . pg_last_error($conn));
    }
    $data = pg_fetch_all($result);

    // Fetch total number of voters
    $votersCountQuery = "SELECT COUNT(*) as total_voters FROM voters";
    $votersCountResult = pg_query($conn, $votersCountQuery);
    if (!$votersCountResult) {
        die("Voters count query failed: " . pg_last_error($conn));
    }
    $votersCount = pg_fetch_result($votersCountResult, 0, 'total_voters');
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Current and Future Elections</h3>
    </div>

    <div class="box-body">
        <table id="electionsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Election Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Candidate Ballots Cast</th>
                    <th>Action Item Ballots Cast</th>
                    <th>Total Ballots Cast</th>
                    <th>Out of (Total Voters)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (is_array($data) && !empty($data)) {
                    foreach($data as $row): 
                ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['start_date']; ?></td>
                        <td><?php echo $row['end_date']; ?></td>
                        <td><?php echo $row['candidate_ballot_count']; ?></td>
                        <td><?php echo $row['action_item_ballot_count']; ?></td>
                        <td><?php echo ($row['candidate_ballot_count'] + $row['action_item_ballot_count']); ?></td>
                        <td><?php echo $votersCount; ?></td>
                    </tr>
                <?php 
                    endforeach;
                } else {
                    echo "<tr><td colspan='7'>No data available.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables jQuery plugin for table sorting functionality -->
<script>
    $(function () {
        $('#electionsTable').DataTable({
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
    /* Modified header color to a shade of blue */
    #electionsHistoryTable thead th {
        background-color: #007BFF;
        color: white;
    }
</style>


</style>
