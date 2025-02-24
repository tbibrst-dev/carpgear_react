<?php

global $wpdb;

$currentSettings = 'question';

if (isset($_REQUEST['delete']) && $_REQUEST['delete'] == 1 && isset($_REQUEST['id']) && $_REQUEST['id'] > 0) {

    $wpdb->delete(
        $wpdb->prefix . 'global_questions',
        array(
            'id' => $_REQUEST['id']
        )
    );

    wp_redirect(admin_url('/admin.php?page=question-settings'));
}

if (isset($_REQUEST['do_update']) && $_REQUEST['do_update'] == 1) {

    $wpdb->update($wpdb->prefix . 'global_questions', ['enabled' => $_REQUEST['enabled']], ['id' => $_REQUEST['id']]);
}

if (isset($_GET['download_csv'])) {

    $recordData = $wpdb->get_results("SELECT gq.id AS question_id, gq.question,gq.options,gq.correct_option, COUNT(uq.id) AS total_attempts, SUM(CASE WHEN gq.correct_option = uq.answer THEN 1 ELSE 0 END) AS correct_attempts, 
    gq.answer1 AS answer1, IFNULL(SUM(CASE WHEN gq.answer1 = uq.answer THEN 1 ELSE 0 END) / COUNT(uq.id) * 100, 0) AS answer1_selected_percentage, gq.answer2 AS answer2, IFNULL(SUM(CASE WHEN gq.answer2 = uq.answer THEN 1 ELSE 0 END) / COUNT(uq.id) * 100, 0) AS answer2_selected_percentage, gq.answer3 AS answer3, IFNULL(SUM(CASE WHEN gq.answer3 = uq.answer THEN 1 ELSE 0 END) / COUNT(uq.id) * 100, 0) AS answer3_selected_percentage FROM {$wpdb->prefix}global_questions gq LEFT JOIN {$wpdb->prefix}user_quest uq ON gq.id = uq.question_id GROUP BY gq.id, gq.question, gq.correct_option", ARRAY_A);

    header('Content-Type: text/csv');

    header('Content-Disposition: attachment; filename="export.csv"');

    ob_end_clean();

    $fp = fopen('php://output', 'w');

    $header_row = array(
        'Question',
        'Question Views',
        'Answer 1',
        'Answer 1 selected percentage',
        'Is correct flag',
        'Answer 2',
        'Answer 2 selected percentage',
        'Is correct flag',
        'Answer 3',
        'Answer 3 selected percentage',
        'Is correct flag'
    );

    fputcsv($fp, $header_row);

    if (!empty($recordData)) {
        foreach ($recordData as $record) {
            $OutputRecord = array(
                $record['question'],
                $record['total_attempts'],
                $record['answer1'],
                $record['answer1_selected_percentage'],
                ($record['answer1'] == $record['correct_option']) ? "Yes" : "No",
                $record['answer2'],
                $record['answer2_selected_percentage'],
                ($record['answer2'] == $record['correct_option']) ? "Yes" : "No",
                $record['answer3'],
                $record['answer3_selected_percentage'],
                ($record['answer3'] == $record['correct_option']) ? "Yes" : "No",
            );
            fputcsv($fp, $OutputRecord);
        }
    }

    fclose($fp);
    exit;
}

$recordData = $wpdb->get_results("SELECT gq.id AS question_id, gq.question,gq.options,gq.correct_option, gq.enabled, COUNT(uq.id) AS total_attempts, SUM(CASE WHEN gq.correct_option = uq.answer THEN 1 ELSE 0 END) AS correct_attempts, (SUM(CASE WHEN gq.correct_option = uq.answer THEN 1 ELSE 0 END) / COUNT(uq.id)) * 100 AS correctness_percentage FROM {$wpdb->prefix}global_questions gq LEFT JOIN {$wpdb->prefix}user_quest uq ON gq.id = uq.question_id WHERE gq.type='global' GROUP BY gq.id, gq.question, gq.correct_option", ARRAY_A);

$show_question = $wpdb->get_var("SELECT show_question FROM {$wpdb->prefix}global_settings");

?>
<div id="competitions-plugin-container">
    <div class="header_content">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-md-2 text-white">Global Settings</h3>
                <div class="col-md-7">
                    <div class="btn-group" role="group" aria-label="Status Filter">
                        <a href="<?php echo admin_url('admin.php?page=global-settings'); ?>" class="btn btn-sm <?php echo ($currentSettings == 'general') ? 'btn-accent' : 'btn-black'; ?>">General</a>
                        <a href="<?php echo admin_url('admin.php?page=question-settings'); ?>" class="btn btn-sm <?php echo ($currentSettings == 'question') ? 'btn-accent' : 'btn-black'; ?>">Questions</a>
                        <a href="<?php echo admin_url('admin.php?page=HPSlider'); ?>" class="btn btn-sm <?php echo ($currentSettings == 'hpslider') ? 'btn-accent' : 'btn-black'; ?>">HP
                            Slider</a>
                        <a href="<?php echo admin_url('admin.php?page=manageStatic'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'manageStatic') ? 'btn-accent' : 'btn-black'; ?>">Manage Statistics</a>
                        <a href="<?php echo admin_url('admin.php?page=managePinnedMessage'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'managePinnedMessage') ? 'btn-accent' : 'btn-black'; ?>">Cometchat Pinned Message</a>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <div class='question_actions'>
                        <a href="<?php echo admin_url('admin.php?page=question-settings'); ?>&download_csv=true" id="generate_csv" class="csv_header_text">CSV Export</a>
                        <a href="<?php echo admin_url('admin.php?page=create-question'); ?>" class="btn btn-sm btn-accent create_ques_btn">New Question</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="question_content">

        <div id="question-status-div-enabled" class="hide">
            <span>Sitewide Questions Enabled</span>
        </div>

        <div id="question-status-div-disabled" class="hide">
            <span>Sitewide Questions Disabled</span>
        </div>

        <div class="pt-3">
            <div class="mb-3">
                <span class="form-check form-switch d-flex justify-content-end">
                    <label class="form-check-label mx-3 text-secondary custom_label" for="custom">
                        Sitewide Questions
                    </label>
                    <input type="hidden" name="show_question" value="0">
                    <label class="switch">
                        <input type="checkbox" id="globalQuestionSetting" name="show_question" value="<?php echo $show_question; ?>" <?php if ($show_question == 1)
                                                                                                                                            echo "checked"; ?>>
                        <span class="sliders rounds"></span>
                    </label>
                </span>
            </div>
            <table class="table wp-list-table widefat fixed striped table-view-list" id="competitions_table">
                <thead>
                    <tr>
                        <th width="90%" class="text-start">Question</th>
                        <th>Correctness</th>
                    </tr>
                </thead>
                <tbody>

                    <?php

                    if (!empty($recordData)) {
                        foreach ($recordData as $record) {

                    ?>
                            <tr>
                                <td class="normal-text">
                                    <?php echo $record['question']; ?>
                                    <span class="sub_text">
                                        <?php if ($record['enabled'] == 1) : ?>
                                            <a href="?page=question-settings&enabled=0&do_update=1&id=<?php echo $record['question_id']; ?>">Disable</a>
                                        <?php else : ?>
                                            <a href="?page=question-settings&enabled=1&do_update=1&id=<?php echo $record['question_id']; ?>">Enable</a>
                                        <?php endif; ?>
                                        <?php if ($record['total_attempts'] == 0) : ?>
                                            <a href="?page=edit-question&id=<?php echo $record['question_id']; ?>">Edit</a>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td class=" text-content text-center">
                                    <?php echo round($record['correctness_percentage']); ?>%
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='2' class='text-center px-5 py-5'><span class='empty_message'>No Global Question Found</span></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>