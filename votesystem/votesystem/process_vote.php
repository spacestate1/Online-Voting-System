<?php
// Enabling error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/session.php';
include 'includes/conn-pgsql.php'; // Ensure this file is correctly setting up the connection to your PostgreSQL database

function generateCandidateRow($conn) {
    // ... [Previous content of the function]
}

function generateActionItemRow($conn) {
    // ... [Previous content of the function]
}

function displayResults($conn) {
    // Fetch and display results for candidates
    echo "<h2>Candidate Results</h2>";
    $sql = "SELECT candidate_id, COUNT(candidate_id) as vote_count FROM votes GROUP BY candidate_id ORDER BY vote_count DESC";
    $result = pg_query($conn, $sql);
    while ($row = pg_fetch_assoc($result)) {
        echo "Candidate ID: " . $row['candidate_id'] . " has " . $row['vote_count'] . " votes.<br/>";
    }

    // Fetch and display results for action items
    echo "<h2>Action Item Results</h2>";
    $sql = "SELECT action_item_id, COUNT(action_item_id) as vote_count FROM action_item_votes GROUP BY action_item_id ORDER BY vote_count DESC";
    $result = pg_query($conn, $sql);
    while ($row = pg_fetch_assoc($result)) {
        echo "Action Item ID: " . $row['action_item_id'] . " has " . $row['vote_count'] . " votes.<br/>";
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $election_id = $_SESSION['election_id'];
    $voter_id = $_SESSION['userid'];

    // Begin transaction
    pg_query($conn, 'BEGIN');

    try {
        // Handling votes for positions and candidates
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'vote_') === 0) {
                $position_id = (int) str_replace('vote_', '', $key);
                $candidate_id = (int) $value;

                // Check if the user has already voted for this position
                $check_sql = "SELECT * FROM votes WHERE voters_id = $1 AND position_id = $2";
                $check_result = pg_query_params($conn, $check_sql, array($voter_id, $position_id));
                if (pg_num_rows($check_result) == 0) {
                    $sql = "INSERT INTO votes (election_id, voters_id, candidate_id, position_id) VALUES ($1, $2, $3, $4)";
                    $result = pg_query_params($conn, $sql, array($election_id, $voter_id, $candidate_id, $position_id));
                    if (!$result) {
                        throw new Exception('Votes Execution failed: ' . pg_last_error());
                    }
                }
            }
        }

        // Handling votes for action items
        foreach ($_POST as $key => $vote) {
            if (strpos($key, 'action_vote_') === 0) {
                $action_item_id = (int) str_replace('action_vote_', '', $key);

                $check_sql = "SELECT * FROM action_item_votes WHERE voters_id = $1 AND action_item_id = $2";
                $check_query = pg_query_params($conn, $check_sql, array($voter_id, $action_item_id));

                if (pg_num_rows($check_query) == 0) { 
                    $sql = "INSERT INTO action_item_votes (election_id, voters_id, action_item_id, vote) VALUES ($1, $2, $3, $4)";
                    $query = pg_query_params($conn, $sql, array($election_id, $voter_id, $action_item_id, $vote));
                    if (!$query) {
                        throw new Exception('Action Items Execution failed: ' . pg_last_error());
                    }
                }
            }
        }

        // Commit transaction
        pg_query($conn, 'COMMIT');

        displayResults($conn);

        $_SESSION['success'] = "Your vote has been successfully submitted!";
        header('location: vote.php');
        exit();

    } catch (Exception $e) {
        pg_query($conn, 'ROLLBACK');
        $_SESSION['error'] = $e->getMessage();
        header('location: vote.php');
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request.";
    header('location: vote.php');
    exit();
}
