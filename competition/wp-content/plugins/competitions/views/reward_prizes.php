<?php

global $wpdb;

if (isset($_GET['download_csv'])) {

    if (isset($_GET['reward']) && !empty($_GET['reward'])) {
        define( 'WP_DEBUG', true );
        @ini_set( 'display_errors', 1 );
        $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : "";

        $total_tickets = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT total_sell_tickets FROM {$wpdb->prefix}competitions WHERE id = %d",
                $_REQUEST['id']
            )
        );
    
        $milestone_percentage = round(($limit / $total_tickets) * 100); // Convert limit to percentage


        $query = $wpdb->prepare("
        SELECT 
            {$wpdb->prefix}competitions.title AS comp_name,
            {$wpdb->prefix}competition_tickets.*,
            {$wpdb->prefix}users.display_name,
            GROUP_CONCAT({$wpdb->prefix}user_quest.answer ORDER BY {$wpdb->prefix}competition_tickets.order_id ASC SEPARATOR ', ') AS answer_selected,
            GROUP_CONCAT({$wpdb->prefix}global_questions.question ORDER BY {$wpdb->prefix}competition_tickets.order_id ASC SEPARATOR ', ') AS question,
            GROUP_CONCAT({$wpdb->prefix}global_questions.correct_option ORDER BY {$wpdb->prefix}competition_tickets.order_id ASC SEPARATOR ', ') AS correct_option
        FROM {$wpdb->prefix}competition_tickets
        INNER JOIN {$wpdb->prefix}comp_reward 
            ON {$wpdb->prefix}comp_reward.competition_id = {$wpdb->prefix}competition_tickets.competition_id 
        INNER JOIN {$wpdb->prefix}users 
            ON {$wpdb->prefix}users.ID = {$wpdb->prefix}competition_tickets.user_id 
        LEFT JOIN {$wpdb->prefix}user_quest 
            ON {$wpdb->prefix}user_quest.order_id = {$wpdb->prefix}competition_tickets.order_id 
        LEFT JOIN {$wpdb->prefix}global_questions 
            ON {$wpdb->prefix}global_questions.id = {$wpdb->prefix}user_quest.question_id 
        INNER JOIN {$wpdb->prefix}competitions 
            ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_tickets.competition_id
        WHERE 
            {$wpdb->prefix}competition_tickets.reward_milestone <= $milestone_percentage
            AND
            {$wpdb->prefix}competition_tickets.user_id > 0 
            AND {$wpdb->prefix}competition_tickets.is_purchased = 1 
            AND {$wpdb->prefix}competition_tickets.competition_id = %d 
            AND {$wpdb->prefix}comp_reward.id = %d
        GROUP BY {$wpdb->prefix}competition_tickets.id
        ORDER BY {$wpdb->prefix}competition_tickets.order_id ASC
    ", $_REQUEST['id'], $_GET['reward']);
    
    
       
        if (!empty($limit)) {
            $query .= " LIMIT $limit";
        }

        $recordData = $wpdb->get_results( $query, ARRAY_A);
    
        $comp_name = preg_split('/\W+/', $recordData['0']['title']);

        $comp_name = implode("-", $comp_name);

        header('Content-Type: text/csv');

        header('Content-Disposition: attachment; filename="RewardPrizeEntrants' . $comp_name . '.csv"');

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

    } else {
        $query = $wpdb->prepare("
        SELECT 
            {$wpdb->prefix}competitions.title as comp_name, 
            {$wpdb->prefix}competition_tickets.*, 
            {$wpdb->prefix}users.display_name, 
            {$wpdb->prefix}user_quest.answer AS answer_selected, 
            {$wpdb->prefix}global_questions.question, 
            {$wpdb->prefix}global_questions.correct_option 
            FROM {$wpdb->prefix}competition_tickets 
            INNER JOIN {$wpdb->prefix}users 
                ON {$wpdb->prefix}users.ID = {$wpdb->prefix}competition_tickets.user_id 
            INNER JOIN {$wpdb->prefix}competitions 
                ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_tickets.competition_id 
            LEFT JOIN {$wpdb->prefix}user_quest 
                ON {$wpdb->prefix}user_quest.order_id = {$wpdb->prefix}competition_tickets.order_id 
            LEFT JOIN {$wpdb->prefix}global_questions 
                ON {$wpdb->prefix}global_questions.id = {$wpdb->prefix}user_quest.question_id 
            WHERE 
                competition_id = %d 
                ORDER BY {$wpdb->prefix}competition_tickets.order_id ASC
            ", $_REQUEST['id']);

         
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
}

$record = $_REQUEST['id'];

$main_table = $wpdb->prefix . 'competitions';

$comp_info = $wpdb->get_row("SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->prefix}competitions comp 
LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
WHERE comp.id = '" . $record . "'", ARRAY_A);

$competition_sold_prcnt = ($comp_info['total_ticket_sold'] / $comp_info['total_sell_tickets']) * 100;




$currentPage = $_REQUEST['page'];

$query = "SELECT {$wpdb->prefix}comp_reward.* FROM {$wpdb->prefix}comp_reward 
WHERE competition_id = " . $_REQUEST['id'];

$pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;

$limit = 20; // number of rows in page
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

$query .= " ORDER BY {$wpdb->prefix}comp_reward.id ASC LIMIT $offset, $limit";

$records = $wpdb->get_results($query, ARRAY_A);



// echo "<pre>";
// print_r($records);
// echo "</pre>";

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
                            <a href="<?php echo admin_url('admin.php?page=instant_wins&id=' . $record);
                            ; ?>" class="btn btn-sm btn-black">instant win</a>
                        <?php endif; ?>
                        <?php if ($comp_info['enable_reward_wins'] == 1): ?>
                            <a href="<?php echo admin_url('admin.php?page=reward_prizes&id=' . $record);
                            ; ?>" class="btn btn-sm btn-accent">reward prizes</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <form action="" method="get">
                        <input type="hidden" name="page" value="entrants" />
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
    <h3 class="header-text"><?php echo $comp_info['title']; ?></h3>
    <table class="table wp-list-table widefat fixed striped table-view-list" id="competitions_table">
        <thead>
            <tr>
                <th width="10%" class="text-start px-3">
                    Type</th>
                <th width="40%" class="text-start">Prize Title</th>
                <th width="10%">Available</th>
                <th width="10%">Cash Alt</th>
                <th width="15%">Entrants</th>
                <th width="15%">Download</th>
            </tr>
        </thead>
        <tbody>

            <?php

            if (!empty($records)) {

                foreach ($records as $index => $reward_record) {

                    if ($index === array_key_last($records)) {
                        // echo "+++++++++++++++++++++++1";
                        // $limit = "";
                        $limit = round($comp_info['total_sell_tickets'] * ($reward_record['prcnt_available'] / 100.0));

                    } else {
                        // echo "+++++++++++++++++++++++2";

                        $limit = round($comp_info['total_sell_tickets'] * ($reward_record['prcnt_available'] / 100.0));
                    }

                    // echo "<pre>";
                    // echo "+++++++++++++++++++++++";
                    // print_r($limit);
                    ?>
                    <tr>
                        <td class="text-content">
                            <?php echo $reward_record['type']; ?>
                        </td>
                        <td>
                            <div class="comp-image">
                                <?php
                                echo "<div class=''><span class='text-content'>" . $reward_record['title'] . "</span>";
                                ?>
                            </div>
                        </td>
                        <td class="text-content">
                            <?php echo round($reward_record['prcnt_available']); ?>%
                        </td>
                        <td class="text-content">
                            <?php echo $reward_record['value']; ?>
                        </td>
                        <td class="normal-text">
                            <?php if ($reward_record['prcnt_available'] <= $competition_sold_prcnt): ?>
                                <a class="link_text"
                                    href="<?php echo admin_url('admin.php?page=reward_prizes_entrants&id=' . $record . '&reward=' . $reward_record['id'] . '&limit=' . $limit); ?>">View
                                    Entrants</a>
                            <?php endif; ?>
                        </td>
                        <td class="normal-text">
                            <?php if ($reward_record['prcnt_available'] <= $competition_sold_prcnt): ?>
                                <a class="link_text"
                                    href="<?php echo admin_url('admin.php?page=reward_prizes&id=' . $record . '&download_csv=1&reward=' . $reward_record['id'] . '&limit=' . $limit); ?>">Export
                                    CSV</a>
                            <?php endif; ?>
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