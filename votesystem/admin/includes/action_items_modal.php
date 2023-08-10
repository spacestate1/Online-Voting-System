<!-- Add -->
<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #d8d1bd ;color:black ; font-size: 15px; font-family:Times ">
            <div class="modal-header">
              <button type="button" class=" btn btn-close btn-curve pull-right"  data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Add New Action Item</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="action_items_add.php">
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">Title</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description" class="col-sm-3 control-label">Description</label>

                    <div class="col-sm-9">
                      <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="election" class="col-sm-3 control-label">Election</label>

                    <div class="col-sm-9">
                      <select class="form-control" id="election" name="election_id" required>
                        <?php
                        $sql = "SELECT id, name FROM elections";
                        $result = pg_query($conn, $sql);
                        if (!$result) {
                          echo "An error occurred.\n";
                          exit;
                        }
                        while ($row = pg_fetch_row($result)) {
                          echo "<option value=".$row[0].">".$row[1]."</option>";
                        }
                        ?>
                      </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="date_created" class="col-sm-3 control-label">Date Created</label>

                    <div class="col-sm-9">
                      <input type="date" class="form-control" id="date_created" name="date_created" required>
                    </div>
                </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-curve pull-left"style='background-color:  #FFDEAD  ;color:black ; font-size: 12px; font-family:Times'  data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-primary btn-curve"style='background-color: #9CD095 ;color:black ; font-size: 12px; font-family:Times'  name="add"><i class="fa fa-save"></i> Save</button>
              </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit -->
<div class="modal fade" id="edit">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #d8d1bd ;color:black ; font-size: 15px; font-family:Times ">
            <div class="modal-header">
              <button type="button" class=" btn btn-close btn-curve pull-right"  data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Edit Action Item</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="action_items_edit.php">
                <input type="hidden" class="id" name="id">
                <div class="form-group">
                    <label for="edit_title" class="col-sm-3 control-label">Title</label>

                    <div class="col-sm-9">
                      <input type="text" class="form-control" id="edit_title" name="title">
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_description" class="col-sm-3 control-label">Description</label>

                    <div class="col-sm-9">
                      <textarea class="form-control" id="edit_description" name="description"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_election" class="col-sm-3 control-label">Election</label>

                    <div class="col-sm-9">
                      <select class="form-control" id="edit_election" name="election_id">
                        <?php
                        $sql = "SELECT id, name FROM elections";
                        $result = pg_query($conn, $sql);
                        if (!$result) {
                          echo "An error occurred.\n";
                          exit;
                        }
                        while ($row = pg_fetch_row($result)) {
                          echo "<option value=".$row[0].">".$row[1]."</option>";
                        }
                        ?>
                      </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_date_created" class="col-sm-3 control-label">Date Created</label>

                    <div class="col-sm-9">
                      <input type="date" class="form-control" id="edit_date_created" name="date_created">
                    </div>
                </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-curve pull-left"style='background-color: #FFDEAD ;color:black ; font-size: 12px; font-family:Times'  data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-success btn-curve" style='background-color: #9CD095 ;color:black ; font-size: 12px; font-family:Times'  name="edit"><i class="fa fa-check-square-o"></i> Update</button>
</form>
</div>
</div>
</div>
</div>

<!-- Delete -->
<div class="modal fade" id="delete">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #d8d1bd ;color:black ; font-size: 15px; font-family:Times ">
            <div class="modal-header">
              <button type="button"class=" btn btn-close btn-curve pull-right"  data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><b>Deleting...</b></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" method="POST" action="action_items_delete.php">
                <input type="hidden" class="id" name="id">
                <div class="text-center">
                    <p>DELETE ACTION ITEM</p>
                    <h2 class="bold del-action-title"></h2>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default btn-curve pull-left" style='background-color: #FFDEAD ;color:black ; font-size: 12px; font-family:Times'  data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
              <button type="submit" class="btn btn-danger btn-curve" style='background-color: #F75D59 ;color:black ; font-size: 12px; font-family:Times' name="delete"><i class="fa fa-trash"></i> Delete</button>
              </form>
            </div>
        </div>
    </div>
</div>

