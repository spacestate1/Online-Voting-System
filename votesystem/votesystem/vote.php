<?php
include 'includes/session.php';
include 'includes/header.php';
include 'includes/conn-pgsql.php';

if (!isset($_SESSION['userid'])) {
    $_SESSION['error'] = 'Invalid voter. Please log in again.';
    header('location: index.php');
    exit();
}


$voter = '';
if (isset($_SESSION['userid'])) {
    $sql = "SELECT * FROM voters WHERE id = $1";
    $stmtname = uniqid();
    $result = pg_prepare($conn, $stmtname, $sql);
    $query = pg_execute($conn, $stmtname, array($_SESSION['userid']));

    $voter = pg_fetch_assoc($query);
}


function getCurrentElection($conn) {
    $electionId = $_SESSION['election_id'];
    $sql = "SELECT * FROM elections WHERE id = $1";
    $stmtname = "fetch_election";
    $result = pg_prepare($conn, $stmtname, $sql);
    $query = pg_execute($conn, $stmtname, array($electionId));
    $election = pg_fetch_assoc($query);
    return $election;
}

function userHasVoted($conn) {
    $electionId = $_SESSION['election_id'];

    $sql1 = "SELECT * FROM votes WHERE voters_id = $1 AND election_id = $2";
    $stmt1 = pg_prepare($conn, "check_candidate_vote", $sql1);
    $query1 = pg_execute($conn, "check_candidate_vote", array($_SESSION['userid'], $electionId));
    $userHasVotedForCandidates = pg_num_rows($query1) > 0;

    $sql2 = "SELECT * FROM action_item_votes WHERE voters_id = $1 AND election_id = $2";
    $stmtname2 = uniqid("check_action_vote_");
    $stmt2 = pg_prepare($conn, $stmtname2, $sql2);
    $query2 = pg_execute($conn, "check_action_vote", array($_SESSION['userid'], $electionId));
    $userHasVotedForActionItems = pg_num_rows($query2) > 0;

    return $userHasVotedForCandidates && $userHasVotedForActionItems;
}

function generateCandidateRow($conn) {
     $electionId = $_SESSION['election_id'];
    $contents = '';

    // Check if there are any candidates for the current election
    $sqlCheck = "SELECT * FROM candidates WHERE election_id = $1";
    $stmtname_check = uniqid(); // unique statement name
    $resultCheck = pg_prepare($conn, $stmtname_check, $sqlCheck);
    $queryCheck = pg_execute($conn, $stmtname_check, array($electionId));

    if (pg_num_rows($queryCheck) == 0) {
        return ''; // Return empty string if no candidates
    }

    // Check if the user has voted for candidates
    $sql = "SELECT * FROM votes WHERE voters_id = $1 AND election_id = $2";
    $stmtname_votecheck = uniqid(); // unique statement name
    $result = pg_prepare($conn, $stmtname_votecheck, $sql);
    $query = pg_execute($conn, $stmtname_votecheck, array($_SESSION['userid'], $electionId));
    $userHasVotedForCandidates = pg_num_rows($query) > 0;

    if ($userHasVotedForCandidates) {
        $contents .= "<h2>You have already voted for the candidates. Here are your votes:</h2>";
        
        // Display the candidate votes
        $contents .= "<table>";
        $contents .= "<tr><th>Candidate</th><th>Position</th></tr>";

        $sql = "SELECT c.*, p.description as position 
                FROM candidates c 
                JOIN positions p ON c.position_id = p.id 
                WHERE c.id = $1";
        $stmtname_candidate = uniqid(); // unique statement name
        $result = pg_prepare($conn, $stmtname_candidate, $sql);
        
        while ($vote = pg_fetch_assoc($query)) {
            $candidateQuery = pg_execute($conn, $stmtname_candidate, array($vote['candidate_id']));
            $candidate = pg_fetch_assoc($candidateQuery);
            $contents .= "<tr><td>{$candidate['firstname']} {$candidate['lastname']}</td><td>{$candidate['position']}</td></tr>";
        }
        $contents .= "</table>";
    } else {

$contents .= "<h2>Vote for the candidates:</h2>";
        $contents .= "<table>";
        $contents .= "<tr><th>Candidate</th><th>Position</th><th>Vote</th></tr>";

        $sql = "SELECT * FROM positions WHERE election_id = $1 ORDER BY priority ASC";
        $stmtname_positions = uniqid(); // unique statement name
        $result = pg_prepare($conn, $stmtname_positions, $sql);
        $positionQuery = pg_execute($conn, $stmtname_positions, array($electionId));

        while ($position = pg_fetch_assoc($positionQuery)) {

            // Fetch candidates for the current position
            $sqlCandidates = "SELECT * FROM candidates WHERE position_id = $1";
            $stmtname_candidates = uniqid();
            $resultCandidates = pg_prepare($conn, $stmtname_candidates, $sqlCandidates);
            $candidatesQuery = pg_execute($conn, $stmtname_candidates, array($position['id']));
            
            while ($candidate = pg_fetch_assoc($candidatesQuery)) {
                $contents .= "<tr><td>{$candidate['firstname']} {$candidate['lastname']}</td><td>{$position['description']}</td><td><input type='radio' name='vote_{$position['id']}' value='{$candidate['id']}'> Vote</td></tr>";
            }
        }
        $contents .= "</table>";
    }

    return $contents;
}


function generateActionItemRow($conn) {
    $electionId = $_SESSION['election_id'];
    $contents = '';

    // Check if there are any action items for the current election
    $sqlCheck = "SELECT * FROM action_items WHERE election_id = $1";
    $stmtCheck = pg_prepare($conn, "check_action_items_exist", $sqlCheck);
    if (!$stmtCheck) {
        die("Preparation failed: " . pg_last_error());
    }
    $queryCheck = pg_execute($conn, "check_action_items_exist", array($electionId));
    if (pg_num_rows($queryCheck) == 0) {
        return ''; // Return empty string if no action items
    }


    // Check if the user has voted for action items
    $sql = "SELECT * FROM action_item_votes WHERE voters_id = $1 AND election_id = $2";
    $stmt = pg_prepare($conn, "check_action_vote", $sql);
    if (!$stmt) {
        die("Preparation failed: " . pg_last_error());
    }

    $query = pg_execute($conn, "check_action_vote", array($_SESSION['userid'], $electionId));
    if (!$query) {
        die("Execution failed: " . pg_last_error());
    }

    $userHasVotedForActionItems = pg_num_rows($query) > 0;

    if ($userHasVotedForActionItems) {
        $contents .= "<h2>You have already voted for the action items. Here are your votes:</h2>";

        // Display the action items votes
        $contents .= "<table>";
        $contents .= "<tr><th>Action Item Title</th><th>Description</th><th>Vote</th></tr>";

        while ($action_vote = pg_fetch_assoc($query)) {
            $sql = "SELECT ai.*, aiv.vote 
                    FROM action_items ai 
                    LEFT JOIN action_item_votes aiv ON ai.id = aiv.action_item_id 
                    WHERE ai.id = $1";
            $stmtname = uniqid("check_action_vote_");
            $stmt = pg_prepare($conn, $stmtname, $sql);
            if (!$stmt) {
                die("Preparation failed: " . pg_last_error());
            }

            if(!isset($action_vote['action_item_id'])) {
                die("action_item_id not set in action_vote array");
            }

            $actionItemQuery = pg_execute($conn, $stmtname, array($action_vote['action_item_id']));
            if (!$actionItemQuery) {
                die("Execution failed: " . pg_last_error());
            }

            if (pg_num_rows($actionItemQuery) == 0) {
                die("No action items found for the given ID.");
            }

            $action_item = pg_fetch_assoc($actionItemQuery);
            $contents .= "<tr><td>" . htmlspecialchars($action_item['title']) . "</td><td>" . htmlspecialchars($action_item['description']) . "</td><td>" . htmlspecialchars($action_vote['vote']) . "</td></tr>";
        }
        $contents .= "</table>";
    } else {
        $contents .= "<h2>Vote for the action items:</h2>";
        $contents .= "<table>";
        $contents .= "<tr><th>Action Item Title</th><th>Description</th><th>Vote</th></tr>";

        // Display voting options for action items
        $sql = "SELECT * FROM action_items WHERE election_id = $1";
        $stmt = pg_prepare($conn, "fetch_action_to_vote", $sql);
        if (!$stmt) {
            die("Preparation failed: " . pg_last_error());
        }

        $actionQuery = pg_execute($conn, "fetch_action_to_vote", array($electionId));
        if (!$actionQuery) {
            die("Execution failed: " . pg_last_error());
        }

       # while ($action_item = pg_fetch_assoc($actionQuery)) {
       #     $contents .= "<tr><td>" . htmlspecialchars($action_item['title']) . "</td><td>" . htmlspecialchars($action_item['description']) . "</td><td><input type='radio' name='action_vote_" . $action_item['id'] . "' value='Approve'> Approve <input type='radio' name='action_vote_" . $action_item['id'] . "' value='Deny'> Deny</td></tr>";
       while ($action_item = pg_fetch_assoc($actionQuery)) {
    $contents .= "<tr><td>" . htmlspecialchars($action_item['title']) . "</td><td>" . htmlspecialchars($action_item['description']) . "</td><td>";
    $contents .= "<input type='radio' name='action_vote_" . $action_item['id'] . "' value='Approve'> Approve<br>";
    $contents .= "<input type='radio' name='action_vote_" . $action_item['id'] . "' value='Deny'> Deny</td></tr>";
        }
        $contents .= "</table>";
    }

    return $contents;
}
?>

<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <?php
        include 'includes/navbar.php';
        include 'includes/sidebar.php';
        ?>
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-sm-9">
                        <?php
                        $election = getCurrentElection($conn);
                        echo "<h1>" . htmlspecialchars($election['name']) . "</h1>";

                        if (isset($_SESSION['success'])) {
                            echo "
                                <div class='alert alert-success alert-dismissible'>
                                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                                    <h4><i class='icon fa fa-check'></i> Success!</h4>
                                    " . $_SESSION['success'] . "
                                </div>
                            ";
                            unset($_SESSION['success']);
                        }
                        ?>

                        <!-- Call and display results from the candidate and action item functions -->
                        <form action="process_vote.php" method="post" id="votingForm">
                       
                            <?php echo generateCandidateRow($conn); ?>
                            <?php echo generateActionItemRow($conn); ?>
                          <?php 
$userVotedForCandidates = userHasVoted($conn);
$userVotedForActionItems = pg_num_rows(pg_execute($conn, "check_action_vote", array($_SESSION['userid'], $_SESSION['election_id']))) > 0;

if (!$userVotedForCandidates && !$userVotedForActionItems): ?> <!-- Only display the button if the user hasn't voted for either candidates or action items -->
    <input type="submit" value="Submit Vote" class="btn btn-success">
<?php endif; ?>

</form>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'includes/footer.php'; ?>
        <?php include 'includes/profile_modal.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var votingForm = document.getElementById('votingForm');
    
    if (votingForm) {
        votingForm.addEventListener('submit', function(e) {
            var radios = document.querySelectorAll('input[type="radio"]');
            var radioNames = {};

            radios.forEach(function(radio) {
                radioNames[radio.name] = true;
            });

            for (var name in radioNames) {
                var selected = document.querySelector('input[name="' + name + '"]:checked');
                if (!selected) {
                    e.preventDefault(); // prevent form submission
                    alert('Please answer all the questions before submitting.');
                    return false;
                }
            }
        });
    }
});
</script>
</body>
</html>
