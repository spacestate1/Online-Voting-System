<!-- election-result.php -->

<?php 
    include 'includes/session.php';
    include 'includes/header.php';
    include 'includes/slugify.php';

?>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php include 'includes/navbar.php'; ?>
        <?php include 'includes/menubar.php'; ?>

        <div class="content-wrapper bg-light">
            <section class="content-header text-black text-lg">
                <h1 class="font-weight-bold">
                    Election Results
                </h1>
                <ol class="breadcrumb">
                    <li><a href="#" class="text-black text-lg"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active text-black text-lg">Election Results</li>
                </ol>
            </section>

            <section class="content">

                <?php 

    $electionsQuery = "SELECT id, name FROM elections ORDER BY start_date DESC";
    $elections = pg_query($conn, $electionsQuery);

    $selectedElection = $_POST['selectedElection'] ?? null;
    $results = [
        'candidates' => [],
        'action_items' => []
    ];

    if ($selectedElection) {
        // Fetch candidate votes for the selected election
        $candidateVoteQuery = "SELECT c.firstname, c.lastname, p.description AS position, COUNT(v.id) AS vote_count 
                               FROM candidates c 
                               JOIN positions p ON c.position_id = p.id 
                               LEFT JOIN votes v ON c.id = v.candidate_id 
                               WHERE c.election_id = {$selectedElection}
                               GROUP BY c.id, p.id 
                               ORDER BY p.priority, vote_count DESC";

    $electionDetails = null;
if ($selectedElection) {
    $electionDetailQuery = "SELECT name, start_date, end_date FROM elections WHERE id = {$selectedElection}";
    $electionDetails = pg_fetch_assoc(pg_query($conn, $electionDetailQuery));
}
        $results['candidates'] = pg_fetch_all(pg_query($conn, $candidateVoteQuery)) ?: [];

        // Fetch action item votes for the selected election
        $actionVoteQuery = "SELECT ai.title, COUNT(CASE WHEN aiv.vote = 'Approved' THEN 1 END) AS approved_count,
                            COUNT(CASE WHEN aiv.vote = 'Denied' THEN 1 END) AS denied_count 
                            FROM action_items ai 
                            LEFT JOIN action_item_votes aiv ON ai.id = aiv.action_item_id 
                            WHERE ai.election_id = {$selectedElection} 
                            GROUP BY ai.id";
        $results['action_items'] = pg_fetch_all(pg_query($conn, $actionVoteQuery)) ?: [];

        // Fetch the votes of each voter for candidates for the selected election
        $candidateVotesByVoterQuery = "
            SELECT v.voters_id, vo.firstname AS voter_first, vo.lastname AS voter_last, 
                   c.firstname AS candidate_first, c.lastname AS candidate_last, 
                   p.description AS position 
            FROM votes v 
            JOIN voters vo ON v.voters_id = vo.id 
            JOIN candidates c ON v.candidate_id = c.id 
            JOIN positions p ON v.position_id = p.id 
            WHERE v.election_id = {$selectedElection}";

        $candidateVotesByVoter = pg_fetch_all(pg_query($conn, $candidateVotesByVoterQuery)) ?: [];

        // Fetch the votes of each voter for action items for the selected election
$actionItemVotesByVoterQuery = "
            SELECT aiv.voters_id, vo.firstname, vo.lastname, 
                   ai.title, ai.description, aiv.vote, aiv.vote_timestamp 
            FROM action_item_votes aiv 
            JOIN voters vo ON aiv.voters_id = vo.id 
            JOIN action_items ai ON aiv.action_item_id = ai.id 
            WHERE aiv.election_id = {$selectedElection}";
        $actionItemVotesByVoter = pg_fetch_all(pg_query($conn, $actionItemVotesByVoterQuery)) ?: [];
    }
?>

<form method="post" action="election-results.php">
    <select name="selectedElection">
        <?php while ($row = pg_fetch_assoc($elections)): ?>
            <option value="<?php echo $row['id']; ?>" <?php echo $selectedElection == $row['id'] ? 'selected' : ''; ?>>
                <?php echo $row['name']; ?>
            </option>
        <?php endwhile; ?>
    </select>
    <input type="submit" value="Get Results">
</form>
<?php if ($electionDetails): ?>
    <h1><?php echo $electionDetails['name']; ?></h1>
    <p>
        <b>Start Date:</b> <?php echo date('F j, Y, g:i a', strtotime($electionDetails['start_date'])); ?>
    
       <b> End Date:</b> <?php echo date('F j, Y, g:i a', strtotime($electionDetails['end_date'])); ?>
    </p>
<?php endif; ?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Candidate Results</h3>
    </div>
    <div class="box-body">
        <table id="candidatesTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Candidate Name</th>
                    <th>Position</th>
                    <th>Vote Count</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($results['candidates']) && is_array($results['candidates']) && !empty($results['candidates'])): ?>
                    <?php foreach ($results['candidates'] as $res): ?>
                        <tr>
                            <td><?php echo $res['firstname'] . ' ' . $res['lastname']; ?></td>
                            <td><?php echo $res['position']; ?></td>
                            <td><?php echo $res['vote_count']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Action Item Results</h3>
    </div>
    <div class="box-body">
        <table id="actionItemsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Action Item</th>
                    <th>Approved Count</th>
                    <th>Denied Count</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($results['action_items']) && is_array($results['action_items']) && !empty($results['action_items'])): ?>
                    <?php foreach ($results['action_items'] as $res): ?>
                        <tr>
                            <td><?php echo $res['title']; ?></td>
                            <td><?php echo $res['approved_count']; ?></td>
                            <td><?php echo $res['denied_count']; ?></td>
                            <td><?php echo $res['approved_count'] > $res['denied_count'] ? 'Approved' : 'Denied'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4">No data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Voter Candidate Votes</h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Voter Name</th>
                    <th>Candidate Name</th>
                    <th>Position</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidateVotesByVoter as $vote): ?>
                    <tr>
                        <td><?php echo $vote['voter_first'] . ' ' . $vote['voter_last']; ?></td>
                        <td><?php echo $vote['candidate_first'] . ' ' . $vote['candidate_last']; ?></td>
                        <td><?php echo $vote['position']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
 
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Action Item Votes</h3>
    </div>
    <div class="box-body">
        <?php
        $groupedVotes = [];
        foreach ($actionItemVotesByVoter as $vote) {
            $groupedVotes[$vote['title']][$vote['firstname'] . ' ' . $vote['lastname']] = [
                'vote' => $vote['vote'],
                'vote_timestamp' => $vote['vote_timestamp']
            ];
        }

        foreach ($groupedVotes as $title => $voters): ?>
            <table id="voterActionItemVotesTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th colspan="3"><?php echo $title; ?></th> <!-- colspan changed to 3 -->
                    </tr>
                    <tr>
                        <td>Description</td>
                        <td colspan="2"><?php echo $actionItemVotesByVoter[0]['description']; ?></td> <!-- colspan changed to 2 -->
                    </tr>
                    <tr>
                        <td>Voter Name</td>
                        <td>Vote</td>
                        <td>Vote Timestamp UTC</td> <!-- New Column Added -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($voters as $voterName => $voteData): ?>
                        <tr>
                            <td><?php echo $voterName; ?></td>
                            <td><?php echo strtolower($voteData['vote']); ?></td>
                            <td><?php echo date('F j, Y, g:i a', strtotime($voteData['vote_timestamp'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>
<!-- DataTables jQuery plugin for table sorting functionality -->
<script>
    $(function () {
        $('#candidatesTable, #actionItemsTable, #voterActionItemVotesTable').DataTable({
            'paging'      : true,
            'lengthChange': true,
            'searching'   : true,
            'ordering'    : true,
            'info'        : true,
            'autoWidth'   : false
        });
    });
</script>
<!-- Styles to make it look similar -->
<style>
    #candidatesTable thead th, #actionItemsTable thead th, #voterActionItemVotesTable thead th {
        background-color: #007BFF;
        color: white;
    }
</style>

