<!-- Modal -->
<div class="modal hide fade" id="block_user" role="dialog" >
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>

            </div>
            <div class="modal-body">
                <p id="blockingText"></p>
            </div>
            <div class="modal-footer">
                <form method="post" id="blockUser">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="status" name="status">

                    <input type="hidden" id="user_id" name="user_id">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
               
                    <button type="submit"  class="btn btn-success">Submit</button>
                </form>
            </div>
        </div>

    </div>
</div>
