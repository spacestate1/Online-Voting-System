<!-- Create Election -->
<div class="modal fade" id="createElection">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #d8d1bd ;color:black ; font-size: 15px; font-family:Times ">
            <div class="modal-header">
                <button type="button" class="btn btn-close btn-curve pull-right" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>Create New Election</b></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="election_add.php">
                    <div class="form-group">
                        <label for="election_title" class="col-sm-3 control-label">Election Title</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="election_title" name="election_title" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="start_date" class="col-sm-3 control-label">Start Date</label>
                        <div class="col-sm-9">
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="end_date" class="col-sm-3 control-label">End Date</label>
                        <div class="col-sm-9">
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-curve pull-left" style='background-color: #FFDEAD  ;color:black ; font-size: 12px; font-family:Times' data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
                <a href="election_list.php" class="btn btn-info btn-curve" style='background-color: #4682B4  ;color:black ; font-size: 12px; font-family:Times'><i class="fa fa-list"></i> Election List</a>
                <button type="submit" class="btn btn-primary btn-curve" style='background-color: #9CD095  ;color:black ; font-size: 12px; font-family:Times' name="add"><i class="fa fa-save"></i> Create</button>
            </div>
                </form>
        </div>
    </div>
</div>

<!-- Edit Election -->
<div class="modal fade" id="editElection">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #d8d1bd ;color:black ; font-size: 15px; font-family:Times ">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><b>Edit Election</b></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="election_edit.php">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group">
                        <label for="edit_election_title" class="col-sm-3 control-label">Election Title</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="edit_election_title" name="election_title" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_start_date" class="col-sm-3 control-label">Start Date</label>
                        <div class="col-sm-9">
                            <input type="datetime-local" class="form-control" id="edit_start_date" name="start_date" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_end_date" class="col-sm-3 control-label">End Date</label>
                        <div class="col-sm-9">
                            <input type="datetime-local" class="form-control" id="edit_end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" name="edit">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


