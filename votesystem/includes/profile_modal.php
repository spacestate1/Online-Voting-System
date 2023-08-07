<!-- Profile Modal -->
<div class="modal fade" id="profileModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">User Profile</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 text-center">
                        <p>User ID: <span id="profileUserId"><?php echo $voter['user_id']; ?></span></p>
                        <p>Voter ID: <span id="profileVoterId"><?php echo $voter['voters_id']; ?></span></p>
                        <p>First Name: <span id="profileFirstName"><?php echo $voter['firstname']; ?></span></p>
                        <p>Last Name: <span id="profileLastName"><?php echo $voter['lastname']; ?></span></p>
                        <p>Email: <span id="profileEmail"><?php echo $voter['email']; ?></span></p>
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

