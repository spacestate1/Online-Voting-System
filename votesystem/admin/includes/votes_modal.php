<?php
$query = "SELECT id, name FROM elections";
$elections = pg_query($conn, $query);
?>

<!-- Reset Modal -->
<div class="modal fade" id="reset">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #d8d1bd ;color:black ; font-size: 15px; font-family:Times ">
            <div class="modal-header">
                <button type="button" class="btn btn-close btn-curve pull-right" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><b>Reset Votes...</b></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="votes_reset.php">
                    <div class="form-group">
                        <label for="election_id" class="col-sm-3 control-label">Election</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="election_id" id="election_id" required>
                                <option value="" disabled selected>Select Election</option>
                                <?php while($row = pg_fetch_assoc($elections)): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-curve pull-left" style='background-color: #FFDEAD;color:black ; font-size: 12px; font-family:Times' data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                        <button type="submit" class="btn btn-danger btn-curve" style='background-color: #ff8e88;color:black ; font-size: 12px; font-family:Times'><i class="fa fa-refresh"></i> Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Reset Modal -->
