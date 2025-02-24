<?php

global $wpdb;

$currentSettings = 'general';

$recordData = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}seo_settings", ARRAY_A);

?>
<div id="competitions-plugin-container">
    <div class="header_content">
        <div class="container-fluid">

            <div class="row">
                <h3 class="col-md-8 text-white">SEO Settings</h3>

                <div class="col-md-4 text-end">
                    <a href="#" class="btn btn-sm btn-accent create_btn save_seo_settings">Save</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="seo_content pt-3">

        <form class="mb-3 seo_main_content" id="seo_settings">
            <input type="hidden" id="total_seo_pages" name="total_seo_pages" value="1" />
            <div class="itemRowclone d-none row g-3">
                <div class="col-md-3">
                    <label for="pages" class="form-label">Page</label>
                    <div class="d-flex">
                        <div class="deleteRow pe-2">
                            <input type="hidden" class="rowNumber" value="" />
                            <a class="delete_item_row" href="#"><img
                                    src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                        </div>
                        <select class="form-select form-control page" name="page0" id="page0">
                            <option selected>Select Page</option>
                            <option value="home">Home</option>
                            <option value="competitions">Competitions</option>
                            <option value="instant_win">Instant Win Comps</option>
                            <option value="drawn_next">Drawn Next</option>
                            <option value="the_big_gear">The Big Gear</option>
                            <option value="the_accessories_and_bait">THE ACCESSORIES & BAIT</option>
                            <option value="finished_and_sold_out">FINISHED / SOLD OUT</option>
                            <option value="competition">Single Competition Page</option>
                            <option value="contact_us">Contact Us Page</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="pageTitle" class="form-label">Page Title</label>
                    <input type="text" class="form-control page_title" value="" name="page_title0" id="page_title0">
                </div>
                <div class="col-md-3">
                    <label for="metaTitle" class="form-label">Meta Title</label>
                    <textarea class="form-control meta_title" value="" name="meta_title0" id="meta_title0" rows="5"></textarea>
                </div>
                <div class="col-md-3">
                    <label for="metaDescription" class="form-label">Meta Description</label>
                    <textarea class="form-control meta_description" rows="5" name="meta_description0"
                        id="meta_description0"></textarea>
                </div>

            </div>

            <?php if(empty($recordData)){ ?>
            <div class="row g-3 itemRow" data-row="1" id="row1">
                <div class="col-md-3">
                    <label for="pages" class="form-label">Page</label>
                    <div class="d-flex">
                        <div class="deleteRow pe-2">
                            <input type="hidden" class="rowNumber" value="1" />
                            <a class="delete_item_row" href="#"><img
                                    src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                        </div>
                        <select id="page1" class="form-select form-control page" name="page1" id="page1">
                            <option selected>Select Page</option>
                            <option value="home">Home</option>
                            <option value="competitions">Competitions</option>
                            <!-- <option value="category">Category Page</option> -->
                            <option value="instant_win">Instant Win Comps</option>
                            <option value="drawn_next">Drawn Next</option>
                            <option value="the_big_gear">The Big Gear</option>
                            <option value="the_accessories_and_bait">THE ACCESSORIES & BAIT</option>
                            <option value="finished_and_sold_out">FINISHED / SOLD OUT</option>
                            <option value="competition">Single Competition Page</option>
                            <option value="contact_us">Contact Us Page</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="pageTitle" class="form-label">Page Title</label>
                    <input type="text" class="form-control page_title" value="" name="page_title1" id="page_title1">
                </div>
                <div class="col-md-3">
                    <label for="metaTitle" class="form-label">Meta Title</label>
                    <textarea class="form-control meta_title" value="" name="meta_title1" id="meta_title1" rows="5"></textarea>
                </div>
                <div class="col-md-3">
                    <label for="metaDescription" class="form-label">Meta Description</label>
                    <textarea class="form-control meta_description" rows="5" name="meta_description1"
                        id="meta_description1"></textarea>
                </div>
            </div>
            <?php 
                } else {
                    $rowNumber = 0;
                    foreach($recordData as $itemRow){ 
                        $rowNumber++;
            ?>
                <div class="row g-3 itemRow" data-row="1" id="row<?php echo $rowNumber; ?>">
                <div class="col-md-3">
                    <label for="pages" class="form-label">Page</label>
                    <div class="d-flex">
                        <div class="deleteRow pe-2">
                            <input type="hidden" class="rowNumber" value="<?php echo $rowNumber; ?>" />
                            <a class="delete_item_row" href="#"><img
                                    src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                        </div>
                        <select id="page1" class="form-select form-control page" name="page<?php echo $rowNumber; ?>" id="page<?php echo $rowNumber; ?>">
                            <option selected>Select Page</option>
                            <option value="home" <?php echo ($itemRow['page'] == 'home')?'selected':''; ?>>Home</option>
                            <option value="competitions" <?php echo ($itemRow['page'] == 'competitions')?'selected':''; ?>>Competitions</option>
                            <!-- <option value="category">Category Page</option> -->
                            <option value="instant_win" <?php echo ($itemRow['page'] == 'instant_win')?'selected':''; ?>>Instant Win Comps</option>
                            <option value="drawn_next" <?php echo ($itemRow['page'] == 'drawn_next')?'selected':''; ?>>Drawn Next</option>
                            <option value="the_big_gear" <?php echo ($itemRow['page'] == 'the_big_gear')?'selected':''; ?>>The Big Gear</option>
                            <option value="the_accessories_and_bait" <?php echo ($itemRow['page'] == 'the_accessories_and_bait')?'selected':''; ?>>THE ACCESSORIES & BAIT</option>
                            <option value="finished_and_sold_out" <?php echo ($itemRow['page'] == 'finished_and_sold_out')?'selected':''; ?>>FINISHED / SOLD OUT</option>
                            <option value="competition" <?php echo ($itemRow['page'] == 'competition')?'selected':''; ?>>Single Competition Page</option>
                            <option value="contact_us" <?php echo ($itemRow['page'] == 'contact_us')?'selected':''; ?>>Contact Us Page</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="pageTitle" class="form-label">Page Title</label>
                    <input type="text" class="form-control page_title" name="page_title<?php echo $rowNumber; ?>" id="page_title<?php echo $rowNumber; ?>" value="<?php echo $itemRow['page_title']; ?>" />
                </div>
                <div class="col-md-3">
                    <label for="metaTitle" class="form-label">Meta Title</label>
                    <textarea class="form-control meta_title" name="meta_title<?php echo $rowNumber; ?>" id="meta_title<?php echo $rowNumber; ?>" rows="5"><?php echo $itemRow['meta_title']; ?></textarea>
                </div>
                <div class="col-md-3">
                    <label for="metaDescription" class="form-label">Meta Description</label>
                    <textarea class="form-control meta_description" rows="5" name="meta_description<?php echo $rowNumber; ?>"
                        id="meta_description<?php echo $rowNumber; ?>"><?php echo $itemRow['meta_description']; ?></textarea>
                </div>
            </div>
            <?php }} ?>
        </form>


        <div class="col-12 add_more_container">
            <div class="col-6 mx-auto text-center">
                <a id="add_more" href="#"><img
                        src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/plus_icon.png'; ?>" /></a>
            </div>
        </div>

    </div>
</div>