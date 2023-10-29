<!-- election-history.php -->

<?php
    // Connect to the database

    if (!$conn) {
        die("Connection failed: " . pg_last_error());
    }

    // Fetch past elections, their start and end dates, and the number of ballots cast for each election
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
        WHERE e.end_date < NOW()
        ORDER BY e.end_date DESC;
    ";

    $result = pg_query($conn, $query);
    
    if (!$result) {
        die("Query failed: " . pg_last_error($conn));
    }

    $data = pg_fetch_all($result);
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Election History</h3>
    </div>

    <div class="box-body">
        <table id="electionsHistoryTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Election Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Candidate Ballots Cast</th>
                    <th>Action Item Ballots Cast</th>
                    <th>Total Ballots Cast</th>
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
                    </tr>
                <?php 
                    endforeach;
                } else {
                    echo "<tr><td colspan='6'>No data available.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- DataTables jQuery plugin for table sorting functionality -->
<script>
    $(function () {
        $('#electionsHistoryTable').DataTable({
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
    #electionsTable thead th {
        background-color: #007BFF;
        color: white;
    }
</style>
