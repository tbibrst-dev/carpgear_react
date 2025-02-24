<?php

global $wpdb;

$wpdb->competition = $wpdb->prefix . 'competitions';

$wpdb->competition_temp = $wpdb->prefix . 'competitions_temp';

$query = $wpdb->prepare("SELECT * FROM {$wpdb->competition_temp} WHERE record = %s", $_REQUEST['id']);

$recordData = $wpdb->get_row($query, ARRAY_A);

if (!empty($recordData)) {

    $instant_wins = json_decode($recordData['instant_prizes'], true);

    $reward_wins = json_decode($recordData['reward_prizes'], true);

    $prize_tickets = [];

    if (!empty($instant_wins)) {

        foreach ($instant_wins as $index => $instant_win) {

            $prize_tickets[$index] = explode(",", $instant_win['tickets']);

            $instant_wins[$index]['id'] = $index;

            unset($instant_wins[$index]['tickets']);
        }
    }

    $original_qty = $wpdb->get_var($wpdb->prepare("SELECT total_sell_tickets FROM {$wpdb->competition} WHERE id = %s", $_REQUEST['id']));
} else {

    $query = $wpdb->prepare("SELECT * FROM {$wpdb->competition} WHERE id = %s", $_REQUEST['id']);

    $recordData = $wpdb->get_row($query, ARRAY_A);

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes WHERE competition_id = %s", $_REQUEST['id']);

    $instant_wins = $wpdb->get_results($query, ARRAY_A);

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes_tickets WHERE competition_id = %s", $_REQUEST['id']);

    $prize_res = $wpdb->get_results($query, ARRAY_A);

    $prize_tickets = [];

    if (!empty($prize_res)) {

        foreach ($prize_res as $res) {

            $prize_tickets[$res['instant_id']][] = $res['ticket_number'];
        }
    }

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_reward WHERE competition_id = %s", $_REQUEST['id']);

    $reward_wins = $wpdb->get_results($query, ARRAY_A);

    $original_qty = $recordData['total_sell_tickets'];
}

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
if (!empty($recordData['faq']))
    $recordData['faq'] = html_entity_decode(stripslashes($recordData['faq']), ENT_QUOTES, 'UTF-8');
if (!empty($recordData['competition_rules']))
    $recordData['competition_rules'] = html_entity_decode(stripslashes($recordData['competition_rules']), ENT_QUOTES, 'UTF-8');
if (!empty($recordData['live_draw_info']))
    $recordData['live_draw_info'] = html_entity_decode(stripslashes($recordData['live_draw_info']), ENT_QUOTES, 'UTF-8');

$recordData['gallery_videos'] = json_decode($recordData['gallery_videos'], true);

$slider_sorting = stripslashes($recordData['slider_sorting']);
$recordData['slider_sorting'] = json_decode(stripslashes($recordData['slider_sorting']), true);

$mode = "edit";

$comp_tickets_purchased = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT count(*) as total_tickets FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %s and is_purchased = 1",
        $_REQUEST['id']
    )
);

$today = date("Y-m-d");

// $query = $wpdb->prepare("SELECT id, title FROM " . $wpdb->prefix . "competitions WHERE status = 'Open' and (draw_date <= %s OR closing_date <= %s)", $today, $today);
// $query = $wpdb->prepare("SELECT id, title FROM " . $wpdb->prefix . "competitions WHERE status = 'Open'");
$query = $wpdb->prepare("SELECT id, title FROM " . $wpdb->prefix . "competitions WHERE status = 'Open'  and total_sell_tickets > total_ticket_sold and (draw_date > %s )", $today);


$open_competitions = $wpdb->get_results($query, ARRAY_A);

// echo "<pre>";print_r($recordData);echo '</pre>';exit;

?>
<div id="competitions-plugin-container">
    <div class="header_container">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-md-6 header-text">Edit Competition</h3>
                <div class="col-md-6 text-end">
                    <a href="<?php echo admin_url('admin.php?page=competitions_menu&remove_temp=' . $_REQUEST['id']); ?>"
                        id="cancel_btn"><button type="button" class="btn btn-sm btn-default">Cancel</button></a>
                    <button type="button"
                        class="btn btn-sm btn-accent create_competition <?php echo (!empty($recordData['is_draft']) && $recordData['is_draft'] == 1) ? '' : 'd-none'; ?>"
                        id="save_as_draft_edit">Save As Draft</button>
                    <button type="button" class="btn btn-sm btn-accent edit_competition" id="save_comp">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content pt-3">
        <ul class="nav nav-tabs mb-3" id="edit-comp" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active active-lighter" id="details-tab" href="#details_content" data-bs-toggle="tab"
                    role="tab" aria-controls="details_content" aria-selected="true">Details</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link tab-brand" id="products-tab" href="#products_content" data-bs-toggle="tab" role="tab"
                    aria-controls="products_content" aria-selected="false">Products</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link tab-brand" id="question-tab" href="#question_content" data-bs-toggle="tab" role="tab"
                    aria-controls="question_content" aria-selected="false">Question</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link tab-brand" id="instant-tab" href="#insant_wins" data-bs-toggle="tab" role="tab"
                    aria-controls="insant_wins" aria-selected="false">Instant Wins</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link tab-brand" id="reward-tab" href="#reward_wins" data-bs-toggle="tab" role="tab"
                    aria-controls="reward_wins" aria-selected="false">Reward Wins</a>
            </li>
        </ul>
        <div class="tab-content" id="edit-comp-content">
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