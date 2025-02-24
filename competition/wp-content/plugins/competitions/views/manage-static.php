<?php

global $wpdb;

$currentSettings = 'manageStatic';

$recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);



?>
<div id="competitions-plugin-container">
    <div class="header_content">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-md-3 text-white">Global Settings</h3>
                <div class="col-md-7">
                    <div class="btn-group" role="group" aria-label="Status Filter">
                        <a href="<?php echo admin_url('admin.php?page=global-settings'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'general') ? 'btn-accent' : 'btn-black'; ?>">General</a>
                        <a href="<?php echo admin_url('admin.php?page=question-settings'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'question') ? 'btn-accent' : 'btn-black'; ?>">Questions</a>
                        <a href="<?php echo admin_url('admin.php?page=HPSlider'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'hpslider') ? 'btn-accent' : 'btn-black'; ?>">HP Slider</a>
                        <a href="<?php echo admin_url('admin.php?page=manageStatic'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'manageStatic') ? 'btn-accent' : 'btn-black'; ?>">Manage Statistics</a>
                            <a href="<?php echo admin_url('admin.php?page=managePinnedMessage'); ?>"
                            class="btn btn-sm <?php echo ($currentSettings == 'managePinnedMessage') ? 'btn-accent' : 'btn-black'; ?>">Cometchat Pinned Message</a>
                    </div>
                </div>
                <!-- <div class="col-md-4 text-end">
                    <a href="#" class="btn btn-sm btn-accent create_btn manageStatictab">Save</button></a>
                </div> -->
            </div>
        </div>
    </div>
    <div class="global_content_statstics">

        <h6 class="text-uppercase mt-5 my-3 header_label">Manage Statistics - Winners And Prizes</h6>


        <div class="row mt-3">
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

            <div class="col-3 staticssvediv">

            <a href="#" class="btn btn-sm btn-accent create_btn manageStaticWinners">Save</button></a>

            </div>


        </div>

        <h6 class="text-uppercase mt-5 my-3 header_label">Manage Statistics - Charity and Followers</h6>

        <div class="row mt-3">


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
            <div class="col-3  staticssvediv">
            <a href="#" class="btn btn-sm btn-accent create_btn manageStaticCharity" id="manageStaticCharity">Save</button></a>

            </div>

        </div>


    </div>
</div>