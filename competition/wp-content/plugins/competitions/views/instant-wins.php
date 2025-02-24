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


if (isset($_GET['download_csv_instant'])) {

    // Base query
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




    $query2 = "SELECT  
    wp_comp_instant_prizes_tickets.*, 
    wp_comp_instant_prizes.title 
    FROM  
    wp_comp_instant_prizes_tickets 
    LEFT JOIN  
    wp_comp_instant_prizes 
    ON  
    wp_comp_instant_prizes.id = wp_comp_instant_prizes_tickets.instant_id 
    WHERE  
    wp_comp_instant_prizes_tickets.user_id IS NOT NULL AND wp_comp_instant_prizes_tickets.competition_id = %d ";


    $recordData2 = $wpdb->get_results($wpdb->prepare($query2, [$_REQUEST['id']]), ARRAY_A);


    $result = [];

    if (!empty($recordData)) {

        $query2Map = [];
        foreach ($recordData2 as $item) {
            $query2Map[$item['ticket_number']] = $item;
        }

        // Merge data for matching ticket_numbers
        foreach ($recordData as $item) {
            $ticketNumber = $item['ticket_number'];
            if (isset($query2Map[$ticketNumber])) {
                // Merge objects
                $merged = array_merge($item, $query2Map[$ticketNumber]);
                $result[] = $merged;
            }
        }
    }
    // Create a map of ticket_number from the second query



    error_log('recordData' . print_r($recordData, true));
    error_log('recordData2' . print_r($recordData2, true));
    error_log('result' . print_r($result, true));

    $comp_name = preg_split('/\W+/', $recordData['0']['comp_name']);

    $comp_name = implode("-", $comp_name);

    header('Content-Type: text/csv');

    header('Content-Disposition: attachment; filename="InstantWinnerExport-' . $comp_name . '.csv"');

    ob_end_clean();

    $fp = fopen('php://output', 'w');

    $header_row = array(
        'Ticket ID',
        'Ticket Number',   
        'User ID',     
       
        'User Name',
        'Order ID',
        'Question',
        'Answer',
        // 'Answered Correctly',
        'Date',
        'Prize Title'
    );

    fputcsv($fp, $header_row);

    if (!empty($result)) {
        $sr_no = 1; // Initialize serial number
        foreach ($result as $record) {
            $OutputRecord = array(
                isset($record['id']) ? $record['id'] : '',
                isset($record['ticket_number']) ? $record['ticket_number'] : '',
                isset($record['user_id']) ? $record['user_id'] : '',
                isset($record['display_name']) ? $record['display_name'] : '',
                isset($record['order_id']) ? $record['order_id'] : '',
                isset($record['question']) ? $record['question'] : '',
                isset($record['answer_selected']) ? $record['answer_selected'] : '',
                // (isset($record['answer_selected']) && isset($record['correct_option']) && $record['answer_selected'] == $record['correct_option']) ? "Yes" : "No",
                isset($record['purchased_on']) ? $record['purchased_on'] : '',
                isset($record['title']) ? $record['title'] : '' // Add Prize Title
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

$query = "SELECT {$wpdb->prefix}comp_instant_prizes.*, {$wpdb->prefix}comp_instant_prizes_tickets.ticket_number, {$wpdb->prefix}comp_instant_prizes_tickets.user_id, {$wpdb->prefix}users.display_name, {$wpdb->prefix}users.user_email FROM `{$wpdb->prefix}comp_instant_prizes` 
INNER JOIN {$wpdb->prefix}comp_instant_prizes_tickets ON {$wpdb->prefix}comp_instant_prizes_tickets.instant_id = {$wpdb->prefix}comp_instant_prizes.id 
INNER JOIN {$wpdb->prefix}users ON {$wpdb->prefix}users.ID = {$wpdb->prefix}comp_instant_prizes_tickets.user_id 
WHERE {$wpdb->prefix}comp_instant_prizes.competition_id = " . $_REQUEST['id'];

$comp_search = isset($_GET['comp_search']) ? $_GET['comp_search'] : '';

if (!empty($comp_search)) {

    if (absint($_GET['comp_search'])) {

        $query .= " AND  {$wpdb->prefix}comp_instant_prizes_tickets.ticket_number LIKE '%" . $comp_search . "%'";
    } else {

        $query .= " AND ({$wpdb->prefix}users.display_name LIKE '%" . $comp_search . "%' OR {$wpdb->prefix}users.user_email LIKE '%" . $comp_search . "%')";
    }
}

$pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;

$limit = 10; // number of rows in page
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

$query .= " ORDER BY {$wpdb->prefix}comp_instant_prizes.id ASC LIMIT $offset, $limit";

$records = $wpdb->get_results($query, ARRAY_A);

?>
<div id="competitions-plugin-container" class="competition_entries">
    <div class="header_content">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-md-1 text-white w-97">Entries</h3>
                <div class="col-md-6">
                    <div class="btn-group" role="group" aria-label="Status Filter">
                        <a href="<?php echo admin_url('admin.php?page=entrants&id=' . $record); ?>"
                            class="btn btn-sm btn-black">Entrants</a>
                        <a href="<?php echo admin_url('admin.php?page=leaderboard&id=' . $record); ?>"
                            class="btn btn-sm btn-black">Leaderboard</a>
                        <?php if ($comp_info['enable_instant_wins'] == 1): ?>
                            <a href="<?php echo admin_url('admin.php?page=instant_wins&id=' . $record);; ?>" class="btn btn-sm btn-accent">instant win</a>
                        <?php endif; ?>
                        <?php if ($comp_info['enable_reward_wins'] == 1): ?>
                            <a href="<?php echo admin_url('admin.php?page=reward_prizes&id=' . $record);; ?>" class="btn btn-sm btn-black">reward prizes</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <form action="" method="get">
                        <input type="hidden" name="page" value="instant_wins" />
                        <input type="hidden" name="id" value="<?php echo $record; ?>" />
                        <input type="text" name="comp_search" placeholder="Search" id="comp_search"
                            value="<?php echo !empty($_REQUEST['comp_search']) ? $_REQUEST['comp_search'] : ''; ?>" />
                    </form>
                </div>
                <div class="col-md-2 text-end">
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


    <div class="main-com-title mb-3">
        <div class="title">
            <h3 class="header-text"><?php echo $comp_info['title']; ?></h3>

        </div>
        <div class="export_button_instant">
            <div class="dropdown">
                <button class="btn btn-secondary btn-accent create_btn dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="6,6" id="dropdownExportBtnInstant">
                    Export
                </button>
                <ul class="dropdown-menu  dropdown-menu-end">
                    <li class="dropdown-item-instant-li"><a class="dropdown-item-instant" href="#" data-type="all">ALL</a></li>
                    <li class="dropdown-item-instant-li"><a class="dropdown-item-instant" href="#" data-type="correct_only">CORRECT ONLY</a></li>
                    <li class="dropdown-item-instant-li"><a class="dropdown-item-instant" href="#" data-type="in_correct_only">INCORRECT ONLY</a></li>
                </ul>
            </div>
        </div>
    </div>


<div class="table-responsive">
    <table class="table wp-list-table widefat fixed striped table-view-list instantwin_table" id="competitions_table">
        <thead>
            <tr>
                <th width="10%" class="text-start px-3">Ticket</th>
                <th width="10%" class="text-start">Type</th>
                <th width="25%" class="text-start" style="text-align: start !important;">Prize Title</th>
                <th width="10%">Cash Alt</th>
                <th width="15%">Name</th>
                <th width="7%">User ID</th>
                <th width="20%">Email</th>

            </tr>
        </thead>
        <tbody>

            <?php

            if (!empty($records)) {
                foreach ($records as $record) {
            ?>
                    <tr>
                        <td class="text-content">
                            <?php echo $record['ticket_number']; ?>
                        </td>
                        <td class="text-content">
                            <?php echo $record['type']; ?>
                        </td>
                        <td>
                            <div class="comp-image">
                                <?php
                                echo "<div class=''><span class='text-content'>" . $record['title'] . "</span>";
                                ?>
                            </div>
                        </td>
                        <td class="text-content">
                            <?php echo $record['value']; ?>
                        </td>
                        <td class="normal-text">
                            <a class="link_text"
                                href="<?php echo admin_url('user-edit.php?user_id=' . $record['user_id']); ?>"><?php echo $record['display_name']; ?></a>
                        </td>
                        <td class="normal-text">
                            <a class="link_text"
                                href="<?php echo admin_url('user-edit.php?user_id=' . $record['user_id']); ?>"><?php echo $record['user_id']; ?></a>
                        </td>
                        <td class="text-content">
                            <?php echo $record['user_email']; ?>
                        </td>
                    </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='6' class='text-center px-5 py-5' style='text-align: center !important;'><span class='empty_message'>No Record Found</span></td></tr>";
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