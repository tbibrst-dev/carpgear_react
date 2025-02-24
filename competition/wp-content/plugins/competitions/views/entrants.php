<?php

global $wpdb;


if (isset($_GET['download_csv'])) {

    $query = "SELECT {$wpdb->prefix}competitions.title as comp_name, {$wpdb->prefix}competition_tickets.*, {$wpdb->prefix}users.display_name, 
    {$wpdb->prefix}user_quest.answer AS answer_selected, {$wpdb->prefix}global_questions.question, 
    {$wpdb->prefix}global_questions.correct_option FROM {$wpdb->prefix}competition_tickets 
    INNER JOIN {$wpdb->prefix}users ON {$wpdb->prefix}users.ID = {$wpdb->prefix}competition_tickets.user_id 
    INNER JOIN {$wpdb->prefix}competitions ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_tickets.competition_id 
    LEFT JOIN {$wpdb->prefix}user_quest ON {$wpdb->prefix}user_quest.order_id = {$wpdb->prefix}competition_tickets.order_id 
    LEFT JOIN {$wpdb->prefix}global_questions ON {$wpdb->prefix}global_questions.id = {$wpdb->prefix}user_quest.question_id 
    WHERE competition_id = %d ";

    $comp_search = isset($_GET['comp_search']) ? absint($_GET['comp_search']) : '';

    if (!empty($comp_search)) {
        $query .= " AND ticket_number LIKE '%" . $comp_search . "%'";
    }

    $correct_only = isset($_GET['correct_only']) ? absint($_GET['correct_only']) : '';

    if (!empty($correct_only)) {
        $query .= " AND {$wpdb->prefix}user_quest.answer = {$wpdb->prefix}global_questions.correct_option";
    }

    $in_correct_only = isset($_GET['in_correct_only']) ? absint($_GET['in_correct_only']) : '';

    if (!empty($in_correct_only)) {
        $query .= " AND {$wpdb->prefix}user_quest.answer != {$wpdb->prefix}global_questions.correct_option";
    }

    $recordData = $wpdb->get_results($wpdb->prepare($query, [$_REQUEST['id']]), ARRAY_A);

    $comp_name = preg_split('/\W+/', $recordData['0']['comp_name']);

    $comp_name = implode("-", $comp_name);

    header('Content-Type: text/csv');

    header('Content-Disposition: attachment; filename="EntrantExport-' . $comp_name . '.csv"');

    ob_end_clean();

    $fp = fopen('php://output', 'w');

    $header_row = array(
        'Ticket Number',
        'Name',
        'Order ID',
        'User ID',
        'Question Asked',
        'Answer Selected',
        'Answered Correctly',
        'Date of order',
    );

    fputcsv($fp, $header_row);

    if (!empty($recordData)) {
        foreach ($recordData as $record) {
            $OutputRecord = array(
                $record['ticket_number'],
                $record['display_name'],
                $record['order_id'],
                $record['user_id'],
                $record['question'],
                $record['answer_selected'],
                ($record['answer_selected'] == $record['correct_option']) ? "Yes" : "No",
                $record['purchased_on'],
            );
            fputcsv($fp, $OutputRecord);
        }
    }

    fclose($fp);
    exit;
}

$record = $_REQUEST['id'];

$main_table = $wpdb->prefix . 'competitions';

$comp_info = $wpdb->get_row("SELECT * FROM " . $main_table . " WHERE id = '" . $record . "'", ARRAY_A);

$currentPage = $_REQUEST['page'];

$query = "SELECT {$wpdb->prefix}competition_tickets.*, {$wpdb->prefix}users.display_name, {$wpdb->prefix}users.user_email FROM {$wpdb->prefix}competition_tickets 
INNER JOIN {$wpdb->prefix}users on {$wpdb->prefix}users.ID = {$wpdb->prefix}competition_tickets.user_id 
WHERE competition_id = " . $_REQUEST['id'];

$comp_search = isset($_GET['comp_search']) ? $_GET['comp_search'] : '';

if (!empty($comp_search)) {

    if (absint($_GET['comp_search'])) {

        $query .= " AND  {$wpdb->prefix}competition_tickets.ticket_number LIKE '%" . $comp_search . "%'";

    } else {

        $query .= " AND ({$wpdb->prefix}users.display_name LIKE '%" . $comp_search . "%' OR {$wpdb->prefix}users.user_email LIKE '%" . $comp_search . "%')";
    }
}

$pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;

$limit = 100; // number of rows in page
$offset = ($pagenum - 1) * $limit;

$sql = explode("FROM", $query);

$total = $wpdb->get_var("SELECT COUNT(*) as total FROM " . $sql['1']);

$num_of_pages = ceil($total / $limit);

$page_links = paginate_links(
    array(
        'base' => add_query_arg('pagenum', '%#%'),
        'format' => '',
        'prev_text' => __('&laquo;', 'text-domain'),
        'next_text' => __('&raquo;', 'text-domain'),
        'total' => $num_of_pages,
        'current' => $pagenum,
        'type' => 'array'
    )
);

$query .= " ORDER BY {$wpdb->prefix}competition_tickets.user_id  ASC LIMIT $offset, $limit";

$records = $wpdb->get_results($query, ARRAY_A);

$com_winners = $wpdb->get_results("select ticket_id from {$wpdb->prefix}competition_winners where competition_id = " . $_REQUEST['id'], ARRAY_A);

$winners = array();

foreach ($com_winners as $com_winner) {
    $winners[] = $com_winner['ticket_id'];
}

?>
<div id="competitions-plugin-container" class="competition_entries">
    <div class="header_content">
        <div class="container-fluid">

            <div class="row">

                <h3 class="col-xxl-2 col-md-4 mb-2 text-white w-97">Entries</h3>

                <div class="col-xxl-5 col-md-8 mb-2">
                    <div class="btn-group" role="group" aria-label="Status Filter">
                        <a href="<?php echo admin_url('admin.php?page=entrants&id=' . $record); ?>"
                            class="btn btn-sm btn-accent">Entrants</a>
                        <a href="<?php echo admin_url('admin.php?page=leaderboard&id=' . $record); ?>"
                            class="btn btn-sm btn-black">Leaderboard</a>
                        <?php if ($comp_info['enable_instant_wins'] == 1): ?>
                            <a href="<?php echo admin_url('admin.php?page=instant_wins&id=' . $record);
                            ; ?>" class="btn btn-sm btn-black">instant win</a>
                        <?php endif; ?>
                        <?php if ($comp_info['enable_reward_wins'] == 1): ?>
                            <a href="<?php echo admin_url('admin.php?page=reward_prizes&id=' . $record);
                            ; ?>" class="btn btn-sm btn-black">reward prizes</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-xxl-3 col-md-6 mb-2">
                    <form action="" method="get">
                        <input type="hidden" name="page" value="entrants" />
                        <input type="hidden" name="id" value="<?php echo $record; ?>" />
                        <input type="text" name="comp_search" placeholder="Search" id="comp_search"
                            value="<?php echo !empty($_REQUEST['comp_search']) ? $_REQUEST['comp_search'] : ''; ?>" />
                    </form>
                </div>

                <div class="col-xxl-2 col-md-6 mb-2 text-end">
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-accent create_btn dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="6,6" id="dropdownExportBtn">
                            Export
                        </button>
                        <ul class="dropdown-menu  dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-type="all">ALL</a></li>
                            <li><a class="dropdown-item" href="#" data-type="correct_only">CORRECT ONLY</a></li>
                            <li><a class="dropdown-item" href="#" data-type="in_correct_only">INCORRECT ONLY</a></li>
                        </ul>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <div class="row mb-3">
        <h3 class="col-md-10 header-text"><?php echo $comp_info['title']; ?></h3>
        <label class="form-check-label col-md-2 text-secondary text-end custom_label" for="custom">
            Winners &nbsp;<?php echo count($com_winners) . "/" . $comp_info['total_winners']; ?>
        </label>
    </div>
    <div class="table-responsive">
    <table class="table wp-list-table widefat fixed striped table-view-list" id="competitions_table">
        <thead>
            <tr>
                <th width="7%" class="text-start px-3">
                    Ticket</th>
                <th width="40%" class="text-start">Name</th>
                <th width="9%">User ID</th>
                <th width="20%">Email</th>
                <th width="11%">Order ID</th>
            </tr>
        </thead>
        <tbody>

            <?php

            if (!empty($records)) {
                foreach ($records as $record) {
                    ?>
                    <tr>
                        <td class="normal-text normal-weight">
                            <?php echo $record['ticket_number']; ?>
                        </td>
                        <td>
                            <div class="comp-image">
                                <?php
                                echo "<div class='comp_winner'><span class='text-content'>" . $record['display_name'] . "</span>";
                                ?>
                                <?php if (!empty($winners) && in_array($record['id'], $winners)): ?>
                                    <span class="badge">Winner</span>
                                <?php else: ?>
                                    <a class="make_winners" href="#" data-url="#" data-id="<?php echo $record['id']; ?>">MAKE
                                        WINNER</a>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="normal-text normal-weight">
                            <a class="link_text"
                                href="<?php echo admin_url('user-edit.php?user_id=' . $record['user_id']); ?>"><?php echo $record['user_id']; ?></a>
                        </td>
                        <td class="normal-text normal-weight">
                            <?php echo $record['user_email']; ?>
                        </td>
                        <td class="normal-text normal-weight">
                            <a class="link_text"
                                href="<?php echo admin_url('post.php?post=' . $record['order_id'] . '&action=edit'); ?>"><?php echo $record['order_id']; ?></a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='4' class='text-center px-5 py-5' style='text-align: center !important;'><span class='empty_message'>No Record Found</span></td></tr>";
            }
            ?>
        </tbody>
    </table>
    </div>

    <?php

    if ($page_links) { ?>
        <div class="tablenav">
            <div class="tablenav-pages" style="margin: 1em 0">
                <ul class="pagination">
                    <?php foreach ($page_links as $page_link) { ?>
                        <li class="page-item">
                            <?php echo $page_link; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    <?php }
    ?>

</div>

<div class="modal fade" id="makeWinnerModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3 class="winner_heading">Make Winner</h3>
                <p class="winner_content">This will make<span id="winner_name"></span>winner of this competition</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-cancel" id="close-modal"
                    data-bs-dismiss="modal">Cancel</button>
                <a href="#" class="btn make_winner_comp">Award Prize</a>
                <input type="hidden" id="winning_id" name="winning_id" value="" />
                <input type="hidden" id="main_competition_id" name="main_competition_id"
                    value="<?php echo $_REQUEST['id']; ?>" />
            </div>
        </div>
    </div>
</div>
<div class="show_loader d-none">
    <div class="modal-backdrop show"></div>
    <div class="d-flex justify-content-center comp_loader">
        <div class="spinner-border" role="status">
            <span class="sr-only"></span>
        </div>
    </div>
</div>