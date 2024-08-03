<div class="modal fade" id="edit_model" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"  id="edit_heading"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div id="edit-container" class="">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="history" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="varyingModalLabel">History of Map/Unmap</h5>
                <h5 id="att_date" class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive pt-0">
                    <table class="table table-hover" id="history">
                        <thead>
                            <th id="sourcename"></th>
                            <th>Action</th>
                            <th>User</th>
                            <th>date</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
            </div>
        </div>
    </div>
</div>