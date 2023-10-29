<!DOCTYPE html>
<html>
<head>
        <link rel="icon" href="/var/www/html/favicon.ico" type="image/x-icon">
  	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<title>Voting System using PHP</title>
  	<!-- Tell the browser to be responsive to screen width -->
  	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  	<!-- Bootstrap 3.3.7 -->
  	<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="plugins/iCheck/all.css">
  	<!-- DataTables -->
    <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  	<!-- Font Awesome -->
  	<link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  	<!-- Theme style -->
  	<link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  	<!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  	<link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

  	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  	<!--[if lt IE 9]>
  	<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  	<![endif]-->

  	<!-- Google Font -->
  	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
         <?php
         ob_start();
         if(session_status() == PHP_SESSION_NONE) {
    session_start();
}


        // Assuming you have the connection to the database established somewhere else
        // and it is available to this script.

        // Check if the user has been inactive for more than 20 minutes
        if (isset($_SESSION['last_activity_time']) && (time() - $_SESSION['last_activity_time'] > 1200)) {
            // Handle logout
            if (isset($_SESSION['userid'])) {
                // Update the is_logged_in status in the database
                $updateSql = "UPDATE voters SET is_logged_in = FALSE WHERE id = $1";
                pg_query_params($conn, $updateSql, array($_SESSION['userid']));

                // Unset and destroy the session
                session_unset();
                session_destroy();
                
                // Redirecting to the index or login page
                echo "<script>window.location.href = 'index.php';</script>";
            }
        }

        // Update last activity time
        $_SESSION['last_activity_time'] = time();
    ob_end_flush();

    ?>



  	<style>
        .mt20 {
            margin-top: 20px;
        }
        .title {
            font-size: 50px;
        }
        #candidate_list {
            margin-top:20px;
        }
        #candidate_list ul {
            list-style-type:none;
        }
        #candidate_list ul li { 
            margin:0 30px 30px 0; 
            vertical-align:top;
        }
        .clist {
            margin-left: 20px;
        }
        .cname {
            font-size: 25px;
        }
        .votelist {
            font-size: 17px;
        }
        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        /* Adjusting the width for the Vote column */
        table th:nth-child(3), table td:nth-child(3) { 
            width: 150px; 
        }

        /* Media query for screens with max-width of 768px */
        @media (max-width: 768px) {
            .title {
                font-size: 30px; /* Adjusted font size for smaller screens */
            }
        }
    </style>		
</head>

