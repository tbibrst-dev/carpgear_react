<?php

global $wpdb;

$currentSettings = 'managePinnedMessage';

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
                            class="btn btn-sm <?php echo ($currentSettings == 'hpslider') ? 'btn-accent' : 'btn-black'; ?>">HP
                            Slider</a>
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

        <h6 class="text-uppercase mt-5 my-3 header_label">Cometchat Pinned Message</h6>
        <div class="col-6 mt-3">
            <input class="form-check-input" type="checkbox" id="showpinnedMessage" name="showpinnedMessage"
                value="1" <?php echo (isset($recordData['showpinnedMessage']) && $recordData['showpinnedMessage'] == '1') ? 'checked' : ''; ?>>
            <label class="form-check-label">Show Pinned Message In All Groups.</label>
        </div>


        <div class="row mt-3">
            <div class="col-12">
                <label for="pinnedMessage" class="form-label">Message</label> 
                <textarea name="pinnedMessage" id="pinnedMessage" rows="10" class="form-control" style="height: 80px; padding: 10px"><?php echo wp_kses_post($recordData['pinnedMessage']); ?></textarea>
            </div>

            
            

            <div class="col-3 mt-3">
                <a href="#" class="btn btn-sm btn-accent create_btn managePinnedMessage" id="managePinnedMessage">Save</button></a>

            </div>

        </div>


    </div>
</div>