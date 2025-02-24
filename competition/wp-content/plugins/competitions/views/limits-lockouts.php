<div class="limit_lock_content" id="competitions-plugin-container">
    <div class="header_content">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-md-3 text-white">Limits & Lockouts</h3>
                <div class="col-md-6">

                    <div class="btn-group" role="group" aria-label="Status Filter" id="instant_win_tabs">
                        <a href="#tab-table1" class="btn btn-sm btn-accent" data-bs-target="#tab-table1"
                            data-bs-toggle="tab">Limits</a>
                        <a href="#tab-table2" class="btn btn-sm btn-black" data-bs-target="#tab-table2"
                            data-bs-toggle="tab">Lockouts</a>
                    </div>

                </div>
                <div class="col-md-3 text-end">&nbsp;</div>
            </div>
        </div>
    </div>

    <div class="tab-content pt-2">
        <div class="tab-pane show active" id="tab-table1">
            <div class="table-responsive">
            <table id="myTable1" class="table wp-list-table widefat fixed striped table-view-list limit_lock_table"
                cellspacing="0" width="100%" data-view="limits">
                <thead>
                    <tr>
                        <th width="7%">User ID</th>
                        <th>Name</th>
                        <th width="20%">Email</th>
                        <th>Length</th>
                        <th>Amount</th>
                        <th>Balance Remaining</th>
                        <th width="15%">Limit Created Date</th>
                        <th width="15%">Limit Renewal Date</th>
                    </tr>
                </thead>
            </table>
            </div>
        </div>
        <div class="tab-pane" id="tab-table2">
        <div class="table-responsive">
            <table id="myTable2" class="table wp-list-table widefat fixed striped table-view-list limit_lock_table"
                cellspacing="0" width="100%" data-view="locks">
                <thead>
                    <tr>
                        <th width="7%">User ID</th>
                        <th width="10%">Name</th>
                        <th>Email</th>
                        <th>Lock Created</th>
                        <th>Lock Expires</th>
                    </tr>
                </thead>
            </table>
           </div>
        </div>
    </div>
</div>
<div class="show_loader d-none">
    <div class="modal-backdrop show"></div>
    <div class="d-flex justify-content-center dt-loader">
        <div class="spinner-border" role="status">
            <span class="sr-only"></span>
        </div>
    </div>
</div>