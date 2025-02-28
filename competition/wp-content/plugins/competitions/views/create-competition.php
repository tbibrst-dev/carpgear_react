<?php

$recordData = [];

$mode = "create";

$comp_tickets_purchased = 0;

global $wpdb;

if (isset($_REQUEST['record']) && $_REQUEST['record'] > 0) {

    $wpdb->competition = $wpdb->prefix . 'competitions';

    $query = $wpdb->prepare("SELECT * FROM {$wpdb->competition} WHERE id = %s", $_REQUEST['record']);

    $recordData = $wpdb->get_row($query, ARRAY_A);

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes WHERE competition_id = %s", $_REQUEST['record']);

    $instant_wins = $wpdb->get_results($query, ARRAY_A);

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes_tickets WHERE competition_id = %s", $_REQUEST['record']);

    $prize_res = $wpdb->get_results($query, ARRAY_A);

    $prize_tickets = [];

    if (!empty($prize_res)) {

        foreach ($prize_res as $res) {

            $prize_tickets[$res['instant_id']][] = $res['ticket_number'];
        }
    }

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_reward WHERE competition_id = %s", $_REQUEST['record']);

    $reward_wins = $wpdb->get_results($query, ARRAY_A);

    $original_qty = $recordData['total_sell_tickets'];

    if (!empty($recordData['draw_date']))
        $recordData['draw_date'] = date("d/m/Y", strtotime($recordData['draw_date']));
    if (!empty($recordData['closing_date']))
        $recordData['closing_date'] = date("d/m/Y", strtotime($recordData['closing_date']));
    if (!empty($recordData['sale_start_date']))
        $recordData['sale_start_date'] = date("d/m/Y", strtotime($recordData['sale_start_date']));
    if (!empty($recordData['sale_end_date']))
        $recordData['sale_end_date'] = date("d/m/Y", strtotime($recordData['sale_end_date']));
    $recordData['question_options'] = json_decode($recordData['question_options'], true);

    if (!empty($recordData['description']))
        $recordData['description'] = html_entity_decode(stripslashes($recordData['description']), ENT_QUOTES, 'UTF-8');
    if (!empty($recordData['live_draw_info']))
        $recordData['live_draw_info'] = html_entity_decode(stripslashes($recordData['live_draw_info']), ENT_QUOTES, 'UTF-8');

    $recordData['gallery_videos'] = json_decode($recordData['gallery_videos'], true);

    $slider_sorting = stripslashes($recordData['slider_sorting']);
    $recordData['slider_sorting'] = json_decode(stripslashes($recordData['slider_sorting']), true);
}

$today = date("Y-m-d");

$query = $wpdb->prepare("SELECT id, title FROM " . $wpdb->prefix . "competitions WHERE status = 'Open' and total_sell_tickets > total_ticket_sold and (draw_date > %s )", $today);
// $query = $wpdb->prepare("SELECT id, title FROM " . $wpdb->prefix . "competitions WHERE status = 'Open' AND 'draw_date' >".);

$open_competitions = $wpdb->get_results($query, ARRAY_A);

?>

<div id="competitions-plugin-container">
    <div class="header_container">
        <div class="container-fluid">

          


            <div class="row">
                <h3 class="col-md-6 header-text">Add a new Competition</h3>
                <div class="col-md-6 text-end">
                    <a href="<?php echo admin_url('admin.php?page=competitions_menu'); ?>" id="cancel_btn"><button
                            type="button" class="btn btn-sm btn-default">Cancel</button></a>
                    <button type="button" class="btn btn-sm btn-accent create_competition d-none " id="save_as_draft">Save As Draft</button>

                    <button type="button" class="btn btn-sm btn-default move_back d-none" id="back_btn">Back</button>
                    <button type="button" class="btn btn-sm btn-accent create_competition" id="next_btn">Next</button>
                    <button type="button" class="btn btn-sm btn-accent create_competition d-none"
                        id="publish_btn">Publish</button>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content pt-3">
        <ul class="nav nav-tabs mb-3" id="create-comp" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active active-accent" id="details-tab" href="#details_content" data-bs-toggle="tab"
                    role="tab" aria-controls="details_content" aria-selected="true">Details</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="products-tab" href="#products_content" data-bs-toggle="tab" role="tab"
                    aria-controls="products_content" aria-selected="false">Products</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="question-tab" href="#question_content" data-bs-toggle="tab" role="tab"
                    aria-controls="question_content" aria-selected="false">Question</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="instant-tab" href="#insant_wins" data-bs-toggle="tab" role="tab"
                    aria-controls="insant_wins" aria-selected="false">Instant Wins</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="reward-tab" href="#reward_wins" data-bs-toggle="tab" role="tab"
                    aria-controls="reward_wins" aria-selected="false">Reward Wins</a>
            </li>
        </ul>
        <div class="tab-content" id="create-comp-content">
            <div class="tab-pane fade show active" id="details_content" role="tabpanel" aria-labelledby="details-tab">
                <?php include "tabs/details.php"; ?>
            </div>
            <div class="tab-pane fade" id="products_content" role="tabpanel" aria-labelledby="products-tab">
                <?php include "tabs/products.php"; ?>
            </div>
            <div class="tab-pane fade" id="question_content" role="tabpanel" aria-labelledby="question-tab">
                <?php include "tabs/question.php"; ?>
            </div>
            <div class="tab-pane fade" id="insant_wins" role="tabpanel" aria-labelledby="instant-tab">
                <?php include "tabs/instant.php"; ?>
            </div>
            <div class="tab-pane fade" id="reward_wins" role="tabpanel" aria-labelledby="reward-tab">
                <?php include "tabs/reward.php"; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="csvModal" tabindex="-1" aria-labelledby="csvModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close close_btn" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <div class="csv_modal_content" id="dropArea">
                        <div class="csv_modal_content">
                            <div class="form-group">
                                <label class="modal-label">Drag & drop a file or</label>
                                <br />
                                <label for="csvFile" class="btn btn-sm btn-accent btn-acc-sm">Choose a file</label>
                                <input type="file" id="csvFile" accept=".csv" class="d-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>