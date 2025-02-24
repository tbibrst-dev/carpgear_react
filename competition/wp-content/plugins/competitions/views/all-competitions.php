<?php

$open_url = add_query_arg(
    array(
        'page' => 'competitions_menu',
        'status' => 'Open',
    ),
    admin_url('admin.php')
);

$finished_url = add_query_arg(
    array(
        'page' => 'competitions_menu',
        'status' => 'Finished',
    ),
    admin_url('admin.php')
);

$closed_url = add_query_arg(
    array(
        'page' => 'competitions_menu',
        'status' => 'Closed',
    ),
    admin_url('admin.php')
);

$draft_url = add_query_arg(
    array(
        'page' => 'competitions_menu',
        'is_draft' => '1',
    ),
    admin_url('admin.php')
);

global $wpdb;

$wpdb->competition = $wpdb->prefix . 'competitions';


if (isset($_REQUEST['delete']) && $_REQUEST['delete'] == 1 && isset($_REQUEST['id']) && $_REQUEST['id'] > 0) {

    $comp_ticket_table = $wpdb->prefix . 'competitions';

    $wpdb->delete(
        $wpdb->competition,
        array(
            'id' => $_REQUEST['id']
        )
    );

    $wpdb->delete(
        $wpdb->prefix . 'competition_tickets',
        array(
            'competition_id' => $_REQUEST['id']
        )
    );

    $wpdb->delete(
        $wpdb->prefix . 'comp_instant_prizes',
        array(
            'competition_id' => $_REQUEST['id']
        )
    );

    $wpdb->delete(
        $wpdb->prefix . 'comp_reward',
        array(
            'competition_id' => $_REQUEST['id']
        )
    );

}

$today = date("Y-m-d");

$active_status = false;

$status = "";

if (isset($_GET['status']) && !empty($_GET['status'])) {

    $status = $_GET['status'];

    if ($status == 'Open') {

        $query = $wpdb->prepare("SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->competition} comp 
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
        WHERE comp.is_draft = 0 and comp.draw_date >= %s and comp.status = %s ", $today, $status);

    } else {

        $query = $wpdb->prepare("SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->competition} comp 
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
        WHERE comp.is_draft = 0 and comp.status = %s", $status);
    }


    $active_status = true;

} else if (isset($_GET['is_draft']) && !empty($_GET['is_draft'])) {

    $query = $wpdb->prepare("SELECT comp.*, COUNT(t.id) AS total_ticket_sold  FROM {$wpdb->competition} comp 
    LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
    WHERE comp.is_draft = %s", $_GET['is_draft']);

    $status = "Draft";

    $active_status = true;

} else {

    $query = "SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->prefix}competitions comp 
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
        WHERE comp.is_draft = '0' ";

}

$pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;

$limit = 10; // number of rows in page
$offset = ($pagenum - 1) * $limit;

$sql = explode("FROM", $query);

$total = $wpdb->get_var("SELECT COUNT(DISTINCT(comp.id)) FROM " . $sql['1']);

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

$query .= " GROUP BY comp.id ORDER BY comp.id desc LIMIT $offset, $limit";

$records = $wpdb->get_results($query, ARRAY_A);

if (isset($_REQUEST['remove_temp']) && $_REQUEST['remove_temp'] > 0) {

    $temp_table_name = $wpdb->prefix . 'competitions_temp';

    $wpdb->delete(
        $temp_table_name,
        array(
            'record' => $_REQUEST['remove_temp']
        )
    );

    $wpdb->delete(
        "{$wpdb->prefix}competition_tickets_temp",
        array(
            'competition_id' => $_REQUEST['remove_temp'],
        )
    );

}

?>
<div id="competitions-plugin-container">
    <div class="header_content">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-md-3 text-white">All Competitions</h3>
                <div class="col-md-6">
                    <div class="btn-group" role="group" aria-label="Status Filter">
                        <a href="<?php echo $open_url; ?>"
                            class="btn btn-sm <?php echo ($active_status && $status == 'Open') ? 'btn-accent' : 'btn-black'; ?>">Open</a>
                        <a href="<?php echo $finished_url; ?>"
                            class="btn btn-sm <?php echo ($active_status && $status == 'Finished') ? 'btn-accent' : 'btn-black'; ?>">Finished</a>
                        <a href="<?php echo $closed_url; ?>"
                            class="btn btn-sm <?php echo ($active_status && $status == 'Closed') ? 'btn-accent' : 'btn-black'; ?>">Closed</a>
                        <a href="<?php echo $draft_url; ?>"
                            class="btn btn-sm <?php echo ($active_status && $status == 'Draft') ? 'btn-accent' : 'btn-black'; ?>">Draft</a>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <a href="<?php echo admin_url('admin.php?page=create-competition'); ?>"
                        class="btn btn-sm btn-accent create_btn">Add New</button></a>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
    <table class="table wp-list-table widefat fixed striped table-view-list" id="competitions_table">
        <thead>
            <tr>
                <th width="7%" class="text-start px-3 <?php if (isset($_GET['is_draft']) && !empty($_GET['is_draft']))
                    echo 'd-none'; ?>">
                    Status</th>
                <th width="40%" class="text-start">Comp</th>
                <th>Max</th>
                <th>Sold</th>
                <th>Left</th>
                <th width="9%">Draw</th>
                <th width="9%">End</th>
                <th width="11%">Instant Wins</th>
            </tr>
        </thead>
        <tbody>

            <?php

            if (!empty($records)) {
                foreach ($records as $record) {

                    $delete_params = array(
                        'page' => 'competitions_menu',
                        'id' => $record['id'],
                        'delete' => 1,
                    );

                    if (isset($_GET['status']) && !empty($_GET['status'])) {

                        $delete_params['status'] = $_GET['status'];

                    }

                    if (isset($_GET['is_draft']) && !empty($_GET['is_draft'])) {

                        $delete_params['is_draft'] = $_GET['is_draft'];
                    }

                    $delete_com_url = add_query_arg(
                        $delete_params,
                        admin_url('admin.php')
                    );

                    $entrant_url = add_query_arg(
                        array(
                            'page' => 'entrants',
                            'id' => $record['id'],
                        ),
                        admin_url('admin.php')
                    );

                    $dup_com_url = add_query_arg(
                        array(
                            'page' => 'create-competition',
                            'record' => $record['id'],
                            'isDuplicate' => true
                        ),
                        admin_url('admin.php')
                    );

                    $view_url = "https://cggprelive.co.uk/competition/details/";

                    $result = preg_split('/\W+/', $record['title']);

                    $view_url .= implode("-", $result) . "-" . $record['id'];

                    ?>
                    <tr>
                        <td class="text-center <?php if (isset($_GET['is_draft']) && !empty($_GET['is_draft']))
                            echo 'd-none'; ?>">
                            <?php
                            if ($record['status'] == 'Open') {
                                $badge_color = 'bg-lighter';
                            } else if ($record['status'] == 'Finished') {
                                $badge_color = 'bg-orange';
                            } else if ($record['status'] == 'Closed') {
                                $badge_color = 'bg-grey';
                            }
                            ?>
                            <span class="badge <?php echo $badge_color; ?> rounded-circle">&nbsp;</span>
                        </td>
                        <td>
                            <div class="comp-image">
                                <?php
                                if (!empty($record['image'])) {
                                    echo "<div class=''><img src='" . esc_url($record['image']) . "' alt='Competitions Featured Image' class='competition_photo'></div>";
                                }
                                echo "<div class='ps-2'><span class='text-content'>" . $record['title'] . "</span>";
                                echo '<div class="row-actionss sub-text"><span class="edit"><a href="?page=edit-competition&id=' . $record['id'] . '">Edit</a></span><span class="entries"><a href="' . $entrant_url . '">Entries</a>  </span><span class="view"><a href="' . $view_url . '" aria-label="View posts by admin" target="_blank">View</a></span>';
                                if (!$record['total_ticket_sold'])
                                    echo '<span class="delete_com"><a class="delete_comp" href="#" data-url="' . $delete_com_url . '">Delete</a></span>';

                                echo '<span class="duplicate_com"><a class="duplicate_comp" href="' . $dup_com_url . '">Duplicate</a></span>';
                                echo '</div></div>';
                                ?>
                            </div>
                        </td>
                        <td class="normal-text">
                            <?php echo number_format($record['total_sell_tickets']); ?>
                        </td>
                        <td class="text-content">
                            <?php echo $record['total_ticket_sold']; ?>
                        </td>
                        <td class="text-content">
                            <?php $left = ($record['total_sell_tickets'] - $record['total_ticket_sold']);
                            echo number_format($left); ?>
                        </td>
                        <td class="text-content">
                            <?php echo mysql2date("d/m/y", $record['draw_date']); ?>
                        </td>
                        <td class="text-content">
                            <?php echo mysql2date("d/m/y", $record['closing_date']); ?>
                        </td>

                        <!-- This is used when user want global option settings -->
                        <!-- <td class="text-content">
                            <?php echo wp_date(get_option('date_format'), $record['draw_date']); ?>
                        </td>
                        <td class="text-content">
                            <?php
                            echo wp_date(get_option('date_format'), strtotime($record['closing_date']));
                            ?>
                        </td> -->

                        <td>

                            <?php if ($record['enable_instant_wins'] == 1) {
                                $image_path = '../_inc/img/Frame6.png';

                                // Get the URL of the image using plugins_url
                                $image_url = plugins_url($image_path, __FILE__);
                                ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="Image">
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='7' class='text-center px-5 py-5'><span class='empty_message'>No Competition Found</span></td></tr>";
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
    <!-- Modal -->
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Competition</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete competition?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="close-modal" data-bs-dismiss="modal">No</button>
                    <a href="#" class="btn btn-danger delete_competition">Yes</a>
                </div>
            </div>
        </div>
    </div>

</div>