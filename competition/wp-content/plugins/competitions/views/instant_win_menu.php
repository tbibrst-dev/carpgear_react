<?php

if (isset($_REQUEST['paid']) && !empty($_REQUEST['paid'])) {

    global $wpdb;
    $ticket_number = "";
    $comp_name = "";
    $intant_title = "";
    $instant_image = "";
    $user_email = 'cggtest123@yopmail.com';

    if ($_REQUEST['type'] == "instant") {
        $updated = $wpdb->update("{$wpdb->prefix}comp_instant_prizes_tickets", ["is_admin_declare_winner" => "2"], ["id" => $_REQUEST['paid']]);

        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes_tickets WHERE id = %s", $_REQUEST['paid']);
        $instant_wins = $wpdb->get_results($query, ARRAY_A);

        $query2 = $wpdb->prepare("SELECT title FROM " . $wpdb->prefix . "competitions WHERE id = %s", $instant_wins[0]['competition_id']);
        $competition = $wpdb->get_results($query2, ARRAY_A);

        $query3 = $wpdb->prepare("SELECT image , title AS instant_title  FROM " . $wpdb->prefix . "comp_instant_prizes WHERE id = %s", $instant_wins[0]['instant_id']);
        $instant_wins_details = $wpdb->get_results($query3, ARRAY_A);

        $query4 = $wpdb->prepare("SELECT email  FROM " . $wpdb->prefix . "user WHERE id = %s", $instant_wins[0]['user_id']);
        $user = $wpdb->get_results($query3, ARRAY_A);

        $ticket_number = $instant_wins[0]['ticket_number'];
        $comp_name = $competition[0]['title'];
        $intant_title = $instant_wins_details[0]['instant_title'];
        $instant_image = $instant_wins_details[0]['image'];
        $user_email = $user[0]['email'];

    } elseif($_REQUEST['type'] == "reward") {
        $updated = $wpdb->update("{$wpdb->prefix}comp_reward_winner", ["is_admin_declare_winner" => "2"], ["id" => $_REQUEST['paid']]);

    }else{
        $updated =$wpdb->update("{$wpdb->prefix}competition_winners", ["is_admin_declare_winner" => "2"], ["id" => $_REQUEST['paid']]);

    }

    // echo "<pre>";
    // print_r($instant_wins);
    // echo "</pre>";

    // echo "<pre>";
    // print_r($competition);
    // echo "</pre>";

    // echo "<pre>";
    // print_r($instant_wins_details);
    // echo "</pre>";


    if ($updated) {
        // Email subject
        $subject = " Well done Legend! Your prize is on the way";

        // Get WooCommerce mailer instance
        $mailer = WC()->mailer();

        // Prepare email variables based on prize type
        $template = 'emails/win-web-order-email.php';

        // Generate email content
        $content = wc_get_template_html(
            $template,
            array(
                'email_heading' => $subject,
                'sent_to_admin' => false,
                'plain_text' => false,
                'email' => $mailer,
                'title' => $intant_title,
                'value' => "Cash Prize",
                'type' => "Cash Prize",
                'image' => $instant_image,
                'comp_title' => $comp_name,
                'ticket_number' =>  $ticket_number,
                'prize_id' => "Cash Prize",
                'competition_id' => "Cash Prize",
                'order' => "Cash Prize",
            )
        );

        // Email headers
        $headers = "Content-Type: text/html\r\n";

        // Send email
        // $user_email = $competition['winner_email']; // Ensure this variable is defined and holds the winner's email
         // Ensure this variable is defined and holds the winner's email        
        $mailer->send($user_email, $subject, $content, $headers);
    }
}




?>


<div class="limit_lock_content" id="competitions-plugin-container">
    <!-- Full-Screen Overlay -->
    <div id="instant-overlay" style="display: none;"></div>
    <div class="header_content">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-xxl-3 mb-2 text-white">Instant Winners</h3>

                <div class="col-xxl-6 col-md-8 mb-2">

                    <div class="btn-group" role="group" aria-label="Status Filter" id="instant_win_tabs">
                        <a href="#tab-table1" class="btn btn-sm btn-accent" data-bs-target="#tab-table1" data-bs-toggle="tab">Unclaimed</a>
                        <a href="#tab-table4" class="btn btn-sm btn-black" data-bs-target="#tab-table4" data-bs-toggle="tab">Claimed</a>
                        <a href="#tab-table2" class="btn btn-sm btn-black" data-bs-target="#tab-table2" data-bs-toggle="tab">Paid</a>
                        <a href="#tab-table3" class="btn btn-sm btn-black" data-bs-target="#tab-table3" data-bs-toggle="tab">Tickets</a>
                        <a href="#tab-table5" class="btn btn-sm btn-black" data-bs-target="#tab-table5" data-bs-toggle="tab">Points</a>
                    </div>

                </div>

                <div class="col-xxl-3 col-md-4 mb-2 text-end instant_sec">
                       
                            <input type="hidden" name="page" value="instant_win_menu" />
                            <input type="text" name="comp_search" placeholder="Search" id="comp_search" value="<?php echo !empty($_REQUEST['comp_search']) ? $_REQUEST['comp_search'] : ''; ?>" />
                </div>

            </div>
        </div>
    </div>


    <div class="tab-content pt-2">
        <div class="tab-pane show active" id="tab-table1">
            <div class="table-responsive">
            <table id="unpaidInstantWins" class="table wp-list-table widefat fixed striped table-view-list instant_win_table" cellspacing="0" width="100%" data-view="unpaid">
                <thead>
                    <tr>
                        <th width="10%">Ticket Number</th>
                        <th width="15%">Competition Name</th>
                        <th>Order ID</th>
                        <th>Prize</th>
                        <th>Winner</th>
                        <th width="15%">Email</th>
                        <th>Tel</th>
                        <th>Address</th>
                    </tr>
                </thead>
            </table>
            </div>
        </div>
        <div class="tab-pane" id="tab-table4">
            <div>
                <select class="select-claimed-value">
                    <option value="0" selected> All</option>
                    <option value="1">Cash</option>
                    <option value="2">Prize</option>
                </select>

                <select class="mark-claimed-value">
                    <option value="0" selected>Actions</option>
                    <option value="1">Mark as paid</option>
                    <option value="2">Print Shipping Labels and Packing Slips</option>
                </select>

                <!-- <select class="mark-claimed-value-prize hide">
                    <option value="0" selected>Actions</option>
                    <option value="1">Print Shipping Labels and Packing Slips</option>
                </select> -->
            </div>
            <div class="table-responsive">
            <table id="claimedInstantWins" class="table wp-list-table widefat fixed striped table-view-list instant_win_table" cellspacing="0" width="100%" data-view="claimed">
                <thead>
                    <tr>
                        <th width="2%"></th>
                        <th width="10%"> Ticket Number</th>
                        <th width="15%">Competition Name</th>
                        <th>Order ID</th>
                        <th>Prize</th>
                        <th>Type</th>
                        <th>Claimed</th>
                        <th>Winner</th>
                        <th width="15%">Email</th>
                        <th>Tel</th>
                        <th>Address</th>
                    </tr>
                </thead>
            </table>

            </div>
        </div>
        <div class="tab-pane" id="tab-table2">
        <div class="table-responsive">
            
            <table id="paidInstantWins" class="table wp-list-table widefat fixed striped table-view-list instant_win_table" cellspacing="0" width="100%" data-view="paid">
                <thead>
                    <tr>
                        <th width="10%">Ticket Number</th>
                        <th width="15%">Competition Name</th>
                        <th>Order ID</th>
                        <th>Prize</th>
                        <th>Claimed</th>
                        <th>Winner</th>
                        <th width="15%">Email</th>
                        <th>Tel</th>
                        <th>Address</th>
                    </tr>
                </thead>
            </table>
        </div>
        </div>
        <div class="tab-pane" id="tab-table5">
        <div class="table-responsive">

            <table id="pointCredInstantWins" class="table wp-list-table widefat fixed striped table-view-list instant_win_table" cellspacing="0" width="100%" data-view="points-cred">
                <thead>
                    <tr>
                        <!-- <th width="10%">Ticket Number</th>
                        <th width="15%">Competition Name</th> -->
                        <th>Order ID</th>
                        <th>Prize</th>
                        <th>Winner</th>
                        <th width="15%">Email</th>
                        <th>Tel</th>
                        <th>Address</th>
                    </tr>
                </thead>
            </table>
            </div>
        </div>

        <div class="tab-pane" id="tab-table3">
        <div class="table-responsive">

            <table id="ticketCredInstantWins" class="table wp-list-table widefat fixed striped table-view-list instant_win_table" cellspacing="0" width="100%" data-view="ticket-cred">
                <thead>
                    <tr>
                        <th width="10%">Ticket Number</th>
                        <th width="15%">Competition Name</th>
                        <th>Order ID</th>
                        <th>Prize</th>
                        <th>Winner</th>
                        <th width="15%">Email</th>
                        <th>Tel</th>
                        <th>Address</th>
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


<div class="modal" id="editModaltitle" tabindex="-1" role="dialog" style="display:none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Edit Prize Title</h5>
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
            </div>
            <form action="">
                <div class="modal-body">

                    <input type="text" id="editPrizeInput" class="form-control" required />

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelEditBtn">Close</button>
                    <button type="button" class="btn btn-primary" id="updatePrizeBtn">Save changes</button>
                </div>
            </form>

        </div>
    </div>
</div>