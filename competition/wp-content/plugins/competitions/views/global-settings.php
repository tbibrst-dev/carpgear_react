<?php

global $wpdb;

$currentSettings = 'general';

$recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);

if (!empty($recordData['live_draw_info']))
    $recordData['live_draw_info'] = html_entity_decode(stripslashes($recordData['live_draw_info']), ENT_QUOTES, 'UTF-8');
if (!empty($recordData['work_step_1']))
    $recordData['work_step_1'] = html_entity_decode(stripslashes($recordData['work_step_1']), ENT_QUOTES, 'UTF-8');
if (!empty($recordData['work_step_2']))
    $recordData['work_step_2'] = html_entity_decode(stripslashes($recordData['work_step_2']), ENT_QUOTES, 'UTF-8');
if (!empty($recordData['work_step_3']))
    $recordData['work_step_3'] = html_entity_decode(stripslashes($recordData['work_step_3']), ENT_QUOTES, 'UTF-8');

if (!empty($recordData['reward_prize_info']))
    $recordData['reward_prize_info'] = html_entity_decode(stripslashes($recordData['reward_prize_info']), ENT_QUOTES, 'UTF-8');
if (!empty($recordData['instant_wins_info']))
    $recordData['instant_wins_info'] = html_entity_decode(stripslashes($recordData['instant_wins_info']), ENT_QUOTES, 'UTF-8');
if (!empty($recordData['main_competition']))
    $recordData['main_competition'] = html_entity_decode(stripslashes($recordData['main_competition']), ENT_QUOTES, 'UTF-8');
if (!empty($recordData['postal_entry_info']))
    $recordData['postal_entry_info'] = html_entity_decode(stripslashes($recordData['postal_entry_info']), ENT_QUOTES, 'UTF-8');

if (!empty($recordData['competition_rules']))
    $recordData['competition_rules'] = html_entity_decode(stripslashes($recordData['competition_rules']), ENT_QUOTES, 'UTF-8');
if (!empty($recordData['competition_faq']))
    $recordData['competition_faq'] = html_entity_decode(stripslashes($recordData['competition_faq']), ENT_QUOTES, 'UTF-8');

if (!empty($recordData['announcement']))
    $recordData['announcement'] = html_entity_decode(stripslashes($recordData['announcement']), ENT_QUOTES, 'UTF-8');

if (!empty($recordData['frontend_scripts']))
    $recordData['frontend_scripts'] = html_entity_decode(stripslashes($recordData['frontend_scripts']), ENT_QUOTES, 'UTF-8');

?>
<div id="competitions-plugin-container">
    <div class="header_content global_sec">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-xl-3 mb-2 text-white">Global Settings</h3>
                <div class="col-xl-7 mb-2">
                    <div class="btn-group" role="group" aria-label="Status Filter">
                        <a href="<?php echo admin_url('admin.php?page=global-settings'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'general') ? 'btn-accent' : 'btn-black'; ?>">General</a>
                        <a href="<?php echo admin_url('admin.php?page=question-settings'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'question') ? 'btn-accent' : 'btn-black'; ?>">Questions</a>
                        <a href="<?php echo admin_url('admin.php?page=HPSlider'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'hpslider') ? 'btn-accent' : 'btn-black'; ?>">HP
                            Slider</a>
                        <a href="<?php echo admin_url('admin.php?page=manageStatic'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'manageStatic') ? 'btn-accent' : 'btn-black'; ?>">Manage Statistics</a>
                        <a href="<?php echo admin_url('admin.php?page=managePinnedMessage'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'managePinnedMessage') ? 'btn-accent' : 'btn-black'; ?>">Cometchat Pinned Message</a>
                    </div>
                </div>
                <div class="col-md-2 text-end">
                    <a href="#" class="btn btn-sm btn-accent create_btn save_global_settings">Save</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="global_content">
        <div class="pt-3">

            <h6 class="text-uppercase header_label">General Information</h6>

            <div class="mt-3">
                <label for="LiveDrawInfo" class="form-label">Live Draw Information</label>
                <textarea class="form-control" id="live_draw_info"
                    name="live_draw_info"><?php echo isset($recordData['live_draw_info']) ? $recordData['live_draw_info'] : ''; ?></textarea>
            </div>

            <div class="mt-3">
                <label for="PostalEntryInfo" class="form-label">Postal Entry Information</label>
                <textarea class="form-control" id="postal_entry_info"
                    name="postal_entry_info"><?php echo isset($recordData['postal_entry_info']) ? $recordData['postal_entry_info'] : ''; ?></textarea>
            </div>


            <div class="mt-3">
                <label for="CompetitionAnnouncement" class="form-label">Announcement</label>
                <textarea name="announcement" class="form-control" id="announcement"
                    rows="3"><?php echo $recordData['announcement']; ?></textarea>
            </div>

            <h6 class="text-uppercase mt-5 my-3 header_label">How it Works</h6>

            <div class="mt-3">
                <label for="MainCompetition" class="form-label">Main Competition</label>
                <textarea class="form-control" id="main_competition"
                    name="main_competition"><?php echo isset($recordData['main_competition']) ? $recordData['main_competition'] : ''; ?></textarea>
            </div>

            <div class="mt-3">
                <label for="InstantWins" class="form-label">Instant Wins</label>
                <textarea class="form-control" id="instant_wins_info"
                    name="instant_wins_info"><?php echo isset($recordData['instant_wins_info']) ? $recordData['instant_wins_info'] : ''; ?></textarea>
            </div>
            <div class="mt-3">
                <label for="RewardPrizes" class="form-label">Reward Prizes</label>
                <textarea class="form-control" id="reward_prize_info"
                    name="reward_prize_info"><?php echo isset($recordData['reward_prize_info']) ? $recordData['reward_prize_info'] : ''; ?></textarea>
            </div>

            <div class="row mt-3 d-none">
                <div class="col-8">
                    <label for="CompetitionSliderSpeed" class="form-label">Competition Slider Speed</label>
                    <input type="text" name="slider_speed" class="form-control w-25"
                        value="<?php echo $recordData['slider_speed']; ?>" />
                </div>
            </div>

        </div>

        <h6 class="text-uppercase mt-5 my-3 header_label">Home Page - How it Works</h6>

        <div class="mt-3">
            <label for="step1" class="form-label">Step 1</label>
            <textarea class="form-control" id="work_step_1"
                name="work_step_1"><?php echo isset($recordData['work_step_1']) ? $recordData['work_step_1'] : ''; ?></textarea>
        </div>

        <div class="mt-3">
            <label for="step2" class="form-label">Step 2</label>
            <textarea class="form-control" id="work_step_2"
                name="work_step_2"><?php echo isset($recordData['work_step_2']) ? $recordData['work_step_2'] : ''; ?></textarea>
        </div>

        <div class="mt-3">
            <label for="step3" class="form-label">Step 3</label>
            <textarea class="form-control" id="work_step_3"
                name="work_step_3"><?php echo isset($recordData['work_step_3']) ? $recordData['work_step_3'] : ''; ?></textarea>
        </div>


        <h6 class="text-uppercase mt-5 my-3 header_label">Competition</h6>

        <div class="mt-3">
            <label for="InstantWins" class="form-label">Rules</label>
            <textarea class="form-control" id="competition_rules"
                name="competition_rules"><?php echo isset($recordData['competition_rules']) ? $recordData['competition_rules'] : ''; ?></textarea>
        </div>
        <div class="mt-3">
            <label for="RewardPrizes" class="form-label">FAQs</label>
            <textarea class="form-control" id="competition_faq"
                name="competition_faq"><?php echo isset($recordData['competition_faq']) ? $recordData['competition_faq'] : ''; ?></textarea>
        </div>

        <div class="row mt-3">
            <div class="col-8">
                <label for="CompetitionSuggestedTickets" class="form-label">Recommended Ticket count</label>
                <input type="text" name="suggested_tickets" class="form-control w-25"
                    value="<?php echo $recordData['suggested_tickets']; ?>" />
            </div>
        </div>

        <h6 class="text-uppercase mt-5 my-3 header_label d-none">Manage Statistics</h6>


        <div class="row mt-3 d-none">
            <div class="col-3">
                <label for="manageWinnerStats" class="form-label">Winners</label>
                <input type="text" name="winner_stat" class="form-control w-75 number_statics_stats"
                    value="<?php echo number_format($recordData['winner_stat']); ?>" />
            </div>
            <div class="col-3">
                <label for="managePrizeStats" class="form-label">Prizes</label>
                <input type="text" name="prizes_stat" class="form-control w-75 number_statics_stats"
                    value="<?php echo number_format($recordData['prizes_stat']); ?>" />
            </div>

            <div class="col-3">
                <label for="manageDonatedStats" class="form-label">Donated</label>
                <input type="text" name="donated_stat" class="form-control w-75 number_statics_stats"
                    value="<?php echo number_format($recordData['donated_stat']); ?>" />
            </div>
            <div class="col-3">
                <label for="manageFollowerStats" class="form-label">Followers</label>
                <input type="text" name="followers_stat" class="form-control w-75 number_statics_stats"
                    value="<?php echo number_format($recordData['followers_stat']); ?>" />
            </div>
        </div>

        <h6 class="text-uppercase mt-5 my-3 header_label">Manage Scripts</h6>
        <div class="row mt-3">
            <div class="col-12">
                <label for="manageScripts" class="form-label">Scripts</label>
                <textarea name="manageScripts" id="manageScripts" rows="10" class="form-control"><?php echo $recordData['frontend_scripts']; ?></textarea>
            </div>
        </div>

    </div>
</div>