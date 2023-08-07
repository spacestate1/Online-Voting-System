<?php
include 'includes/session.php';
include 'includes/header.php';
include 'includes/conn-pgsql.php'; // Adding database connection include

if (!isset($_SESSION['userid'])) {
    $_SESSION['error'] = 'Invalid voter. Please log in again.';
    header('location: index.php');
}

$voter = '';
if (isset($_SESSION['userid'])) {
    $sql = "SELECT * FROM voters WHERE id = $1";
    $stmtname = uniqid(); // unique statement name
    $result = pg_prepare($conn, $stmtname, $sql);
    $query = pg_execute($conn, $stmtname, array($_SESSION['userid']));
    $voter = pg_fetch_assoc($query);
}
function generateRow($conn)
{
    // Check if user has already voted
    $election_id = $_SESSION['election_id'];
    $sql = "SELECT * FROM votes WHERE voters_id = $1 AND election_id = $2";
    $stmtname_votecheck = uniqid(); // unique statement name
    $result = pg_prepare($conn, $stmtname_votecheck, $sql);
    if (!$result) {
        die('Query failed: ' . pg_last_error());
    }
    $query = pg_execute($conn, $stmtname_votecheck, array($_SESSION['userid'], $election_id));
    if (!$query) {
        die('Query failed: ' . pg_last_error());
    }

    $userHasVoted = pg_num_rows($query) > 0;
    $contents = '';

    if ($userHasVoted) {
        // User has already voted, so show their votes
        $contents .= "<h2>You have already voted in this election. Here are your votes:</h2>";
        while ($vote = pg_fetch_assoc($query)) {
            $sql = "SELECT c.*, p.description as position 
                    FROM candidates c 
                    JOIN positions p ON c.position_id = p.id 
                    WHERE c.id = $1";
            $stmtname_candidate = uniqid(); // unique statement name
            $result = pg_prepare($conn, $stmtname_candidate, $sql);
            if (!$result) {
                die('Query failed: ' . pg_last_error());
            }
            $cquery = pg_execute($conn, $stmtname_candidate, array($vote['candidate_id']));
            if (!$cquery) {
                die('Query failed: ' . pg_last_error());
            }
            $candidate = pg_fetch_assoc($cquery);
            $contents .= "<p>You voted for " . $candidate['firstname'] . " " . $candidate['lastname'] . " for " . $candidate['position'] . ".</p>";
        }
    } else {
        // User has not voted, so show the voting form
        $sql = "SELECT * FROM positions WHERE election_id = $1 ORDER BY priority ASC";
        $stmtname_positions = uniqid(); // unique statement name
        $result = pg_prepare($conn, $stmtname_positions, $sql);
        if (!$result) {
            die('Query failed: ' . pg_last_error());
        }
        $query = pg_execute($conn, $stmtname_positions, array($election_id));
        if (!$query) {
            die('Query failed: ' . pg_last_error());
        }

        // Check if the query returned any rows
        if (pg_num_rows($query) == 0) {
            // No positions found for this election
            $contents .= "<h3>No ballot exists</h3>";
            return [
                'content' => $contents,
                'hasVoted' => true,  // set this to true to hide the submit button
            ];
        }

        while ($row = pg_fetch_assoc($query)) {
            $contents .= "<h3>{$row['description']}</h3>";

            $sql = "SELECT * FROM candidates WHERE position_id = $1";
            $stmtname_candidates = uniqid(); // unique statement name
            $result = pg_prepare($conn, $stmtname_candidates, $sql);
            if (!$result) {
                die('Query failed: ' . pg_last_error());
            }
            $cquery = pg_execute($conn, $stmtname_candidates, array($row['id']));
            if (!$cquery) {
                die('Query failed: ' . pg_last_error());
            }
            while ($candidate = pg_fetch_assoc($cquery)) {
                $contents .= "<input type='radio' name='vote[{$row['id']}]' value='{$candidate['id']}' required> {$candidate['firstname']} {$candidate['lastname']}<br>";
            }
            $contents .= "<hr style='border: 1px solid black;'>"; // Line added between positions
        }
    }

    return [
        'content' => $contents,
        'hasVoted' => $userHasVoted,
    ];
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
                        if (isset($_SESSION['error'])) {
                            echo "
                                <div class='alert alert-danger alert-dismissible'>
                                    <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
                                    <h4><i class='icon fa fa-warning'></i> Error!</h4>
                                    " . $_SESSION['error'] . "
                                </div>
                            ";
                            unset($_SESSION['error']);
                        }
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

                        <?php $row = generateRow($conn); ?>
                        <?php if (!$row['hasVoted']) : ?>
                            <form action="process_vote.php" method="post">
                                <?php echo $row['content']; ?>
                                <input type="submit" value="Submit Vote" class="btn btn-success">
                            </form>
                        <?php else: ?>
                            <?php echo $row['content']; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
        <?php include 'includes/footer.php'; ?>
        <?php include 'includes/profile_modal.php'; ?>
    </div>
    <?php include 'includes/scripts.php'; ?>
</body>

</html>
