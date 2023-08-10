<!-- Ballot Modal -->
<div class="modal fade" id="ballotModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Vote for the Ballot</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 text-center">
                        <p>Description: <span id="ballotDescription">N/A</span></p>
                    </div>
                    <div class="col-12 text-center">
                        <form action="vote.php" method="post">
                            <input type="hidden" name="ballot_id" id="ballotId" value="">
                            <button type="submit" class="btn btn-primary">Vote</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

