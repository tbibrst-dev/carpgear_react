<div id="competitions-plugin-container">

    <div class="header_content ticket_sec">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-xxl-2 text-white">Add Tickets
                </h3>

                <div class="col-xxl-5 col-lg-6"><span class="sub-text text-white fw-semibold" style="font-size:14px;">Use
                        this tool
                        to manually add tickets
                        to the ticket
                        database.</span></div>
                <div class="col-xxl-5 col-lg-6 users_search">
                    <form id="users-filter" method="get" action="">
                        <input type="hidden" name="page" value="add_tickets" />
                        <input type="text" name="user_name" class="me-2"
                            value="<?php echo isset($_REQUEST['user_name']) ? $_REQUEST['user_name'] : ""; ?>" />
                        <input type="submit" name="search_user" class="btn btn-sm btn-accent" value="Search Users" />

                    </form>
                </div>

            </div>
        </div>
    </div>

    <?php
    if (isset($_REQUEST['search_user']) && isset($_REQUEST['user_name']) && !empty($_REQUEST['user_name'])):

        $search = $_REQUEST['user_name'];

        $users = get_users([
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => 'first_name',
                    'value' => $search,
                    'compare' => 'LIKE',
                ],
                [
                    'key' => 'last_name',
                    'value' => $search,
                    'compare' => 'LIKE',
                ],
            ],
        ]);

        if (!empty($users)):

            ?>
            <div class="users_results">
                <table class="table wp-list-table widefat fixed striped table-view-list users" id="competitions_table">
                    <thead>
                        <th width="7%">User ID</th>
                        <th width="15%">Name</th>
                        <th width="20%" style="text-align:start !important;">Email</th>
                        <th>&nbsp;</th>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user):
                            $user_url = add_query_arg(
                                array(
                                    'page' => 'add_tickets',
                                    'user' => $user->ID,
                                ),
                                admin_url('admin.php')
                            );
                            ?>
                            <tr>
                                <td><?php echo $user->ID; ?></td>
                                <td><?php echo $user->display_name; ?></td>
                                <td><?php echo $user->user_email; ?></td>
                                <td>
                                    <a href="<?php echo $user_url; ?>" name="select_user"
                                        class="btn btn-sm btn-accent select_user">Select
                                        User</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p>Showing all <?php echo count($users); ?> results matching "<?php echo $search; ?>".</p>
            </div>
        <?php endif; ?>
    <?php elseif (!isset($_REQUEST['user'])): ?>
        <div class="users_results">
            <table class="table wp-list-table widefat fixed striped table-view-list users" id="competitions_table">
                <thead>
                    <th width="7%">User ID</th>
                    <th width="30%">Name</th>
                    <th style="text-align:start !important;">Email</th>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3">No User found</td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <?php
    if (isset($_REQUEST['user']) && $_REQUEST['user'] > 0):

        $user_info = get_user_by('id', $_REQUEST['user']);

        global $wpdb;

        $user_id = $_REQUEST['user'];

        $query = "SELECT comp.*, COUNT(t.id) AS total_ticket_sold, SUM(CASE WHEN t.user_id = $user_id THEN 1 ELSE 0 END) AS
    total_ticket_sold_by_user FROM {$wpdb->prefix}competitions comp
    LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1
    WHERE comp.is_draft = '0' ";

        if (isset($_REQUEST['competition_search']) && !empty($_REQUEST['competition_search'])) {

            $query .= " AND comp.title LIKE '%" . $_REQUEST['competition_search'] . "%'";
        }

        $pagenum = isset($_GET['pagenum']) ? absint($_GET['pagenum']) : 1;

        $limit = 20;

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
        ?>
        <hr />
        <p class="sub-heading-text">Assign tickets to <?php echo $user_info->display_name; ?>'s account (ID :
            <?php echo $user_info->ID; ?>):
        </p>

        <div class="tickets_listing">

            <form id="competition-filter" method="get" action="">
                <input type="hidden" name="page" value="add_tickets" />
                <input type="hidden" name="user" value="<?php echo $user_id; ?>" />
                <p class="comp-search-box">
                    <input type="search" id="comp-search-input" name="competition_search"
                        value="<?php echo (isset($_REQUEST['competition_search']) && !empty($_REQUEST['competition_search'])) ? $_REQUEST['competition_search'] : ''; ?>">
                    <input type="submit" id="search-submit" class="btn btn-sm btn-accent" value="Search Competitions">
                </p>
            </form>

            <div class="competitions_content">

                <?php

                if ($page_links) { ?>
                    <div class="tablenavv float-end d-none">
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
                <table class="table wp-list-table widefat fixed striped table-view-list competitions"
                    id="competitions_table">
                    <thead>
                        <th width="5%" class="text-start px-3 ">ID</th>
                        <th width="20%" class="text-start manage-column column-title sortable desc">Title
                        <th>Status</th>
                        <th>Remaining</th>
                        <th>Max Per User</th>
                        <th>Existing Tickets</th>
                        <th width="20%">Draw Date</th>
                        <th width="18%">Add Tickets</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($records as $record):
                            $remaining_tickets = ($record['total_sell_tickets'] - $record['total_ticket_sold']);
                            if ($remaining_tickets == 0) {
                                $exiting_tickets = 0;
                            } else {
                                $exiting_tickets = ($record['max_ticket_per_user'] - $record['total_ticket_sold_by_user']);
                            }

                            if ($record['status'] == 'Finished' || $record['status'] == 'Closed') {
                                $exiting_tickets = 0;
                            }

                            $date = new DateTime($record['draw_date'] . " " . $record['draw_time']);

                            $formatted_date = $date->format('j F, Y g:i a');

                            ?>
                            <tr>
                                <td>
                                    <?php echo $record['id']; ?>
                                </td>
                                <td>
                                    <?php echo $record['title']; ?>
                                </td>
                                <td>
                                    <?php if ($record['status'] == 'Open'): ?>
                                        <button class="btn button-secondary" type="button"><?php echo $record['status']; ?></button>
                                    <?php elseif ($record['status'] == 'Finished'): ?>
                                        <button class="btn button-danger" type="button"><?php echo $record['status']; ?></button>
                                    <?php elseif ($record['status'] == 'Closed'): ?>
                                        <button class="btn button-danger" type="button"><?php echo $record['status']; ?></button>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $remaining_tickets; ?></td>
                                <td><?php echo $record['max_ticket_per_user']; ?></td>
                                <td><?php echo $record['total_ticket_sold_by_user']; ?></td>
                                <td><?php echo $formatted_date; ?></td>
                                <td>
                                    <div class="d-flex">
                                        <input type="text" class="add_tickets_input w-50" value="0" />
                                        <input type="button" name="add_ticket" class="btn btn-sm btn-accent add_tickets"
                                            value="Add tickets" data-id="<?php echo $record['id']; ?>"
                                            data-status="<?php echo $record['status']; ?>"
                                            data-remaining="<?php echo $remaining_tickets; ?>"
                                            data-purchased="<?php echo $record['total_ticket_sold_by_user']; ?>"
                                            data-max-tickets="<?php echo $exiting_tickets; ?>" name="add_tickets"
                                            data-max-tickets-per-person="<?php echo $record['max_ticket_per_user']; ?>" name="add_tickets"
                                            data-user="<?php echo $user_id; ?>" />
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
        </div>
    <?php endif; ?>
    <div class="show_loader d-none">
        <div class="modal-backdrop show"></div>
        <div class="d-flex justify-content-center comp_loader">
            <div class="spinner-border" role="status">
                <span class="sr-only"></span>
            </div>
        </div>
    </div>
</div>