<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">
    <?php include 'includes/navbar.php'; ?>

    <div class="content-wrapper">
        <div class="container">
            <section class="content">
                <div class="row">
                    <div class="col-sm-6 col-sm-offset-3">
                        <h1 class="text-center">Change Password</h1>

                        <!-- Display messages -->
                        <!-- Your error and success message handling here -->

                        <!-- Change Password Form -->
                        <form method="POST" action="update_password.php">
                            <div class="form-group">
                                <label for="curr_password">Old Password:</label>
                                <input type="password" class="form-control" id="curr_password" name="curr_password" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password:</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                            </div>
                            <div class="form-group">
                                <label for="retype_password">Confirm Password:</label>
                                <input type="password" class="form-control" id="retype_password" name="retype_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </form>

                    </div>
                </div>
            </section>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>
