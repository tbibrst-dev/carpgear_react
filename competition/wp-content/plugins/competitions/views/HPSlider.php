<?php

global $wpdb;

$currentSettings = 'hpslider';

$recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);

$sliderData = $wpdb->get_results("select * from {$wpdb->prefix}homepage_sliders", ARRAY_A);

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
                <div class="col-md-2 text-end">
                    <a href="#" class="btn btn-sm btn-accent create_btn save_hp_slider_settings">Save</button></a>
                </div>
            </div>
        </div>
    </div>
    <div class="hpslider_content">
        <div class="pt-3">
            <form id="slider_settings">
                <h6 class="text-uppercase header_label">Slides</h6>

                <div class="sliders_content pt-3">
                    <input type="hidden" id="total_slides" name="total_slides" value="1" />

                    <div class="row mt-3 g-3 itemRowCloneCopy d-none" data-row="0" id="row0">
                        <div class="col-md-2">
                            <label for="pages" class="form-label">Title</label>
                            <div class="d-flex">
                                <div class="deleteRow pe-2">
                                    <input type="hidden" class="rowNumber" value="0" />
                                    <a class="delete_item_row" href="#"><img
                                            src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                                </div>
                                <input type="text" class="form-control slider_title" value="" name="slider_title0"
                                    id="slider_title0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="pageTitle" class="form-label">Sub Title</label>
                            <input type="text" class="form-control sub_title" value="" name="sub_title0"
                                id="sub_title0">
                        </div>
                        <div class="col-md-2">
                            <label for="metaTitle" class="form-label">Link</label>
                            <input type="text" class="form-control link" value="" name="link0" id="link0" required />
                        </div>
                        <div class="col-md-2">
                            <label for="metaTitle" class="form-label">Button</label>
                            <input type="text" class="form-control btn_text" value="" name="btn_text0" id="btn_text0"
                                required />
                        </div>
                        <div class="col-md-2 wp_media_container">
                            <label for="metaDescription" class="form-label">Desktop Image</label>
                            <div class="upload-btn-wrapper" id="desktop_img_upload_container">
                                <button class="btn-upload">Desktop Image </button>
                                <input type="file" name="desktop_image0" required id="desktop_image0"
                                    class="desktop_image wp_media_frame" />
                            </div>
                            <div id="desktop-image-container" class="desktop-image-container wp_media_preview d-none">
                                <div class="img-content"></div>
                                <div class="sub-text text-center"><a href="#" class="remove_desktop_image">Remove</a>
                                </div>
                                <input type="hidden" name="desktop_image0" value="" class="desktop_image" />
                            </div>
                        </div>
                        <div class="col-md-2 wp_media_container">
                            <label for="metaDescription" class="form-label">Mobile Image</label>
                            <div class="upload-btn-wrapper" id="mobile_img_upload_container">
                                <button class="btn-upload">Mobile Image </button>
                                <input type="file" name="mobile_image0" required id="mobile_image0"
                                    class="mobile_image wp_media_frame" />
                            </div>
                            <div id="mobile-image-container" class="mobile-image-container wp_media_preview d-none">
                                <div class="img-content"></div>
                                <div class="sub-text text-center"><a href="#" class="remove_mobile_image">Remove</a>
                                </div>
                                <input type="hidden" name="mobile_image0" class="mobile_image" value="" />
                            </div>
                        </div>
                    </div>

                    <?php
                    if (!empty($sliderData)):
                        $row = 0;
                        foreach ($sliderData as $slideInfo):
                            $row++;
                    ?>
                            <div class="row g-3 mt-3 itemRow" data-row="<?php echo $row; ?>" id="row<?php echo $row; ?>">
                                <div class="col-md-2">
                                    <label for="pages" class="form-label">Title</label>
                                    <div class="d-flex">
                                        <div class="deleteRow pe-2">
                                            <input type="hidden" class="rowNumber" value="<?php echo $row; ?>" />
                                            <a class="delete_item_row" href="#"><img
                                                    src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                                        </div>
                                        <input type="text" class="form-control slider_title" value="<?php echo $slideInfo['slider_title']; ?>" name="slider_title<?php echo $row; ?>"
                                            id="slider_title<?php echo $row; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="pageTitle" class="form-label">Sub Title</label>
                                    <input type="text" class="form-control sub_title" value="<?php echo $slideInfo['sub_title']; ?>" name="sub_title<?php echo $row; ?>"
                                        id="sub_title<?php echo $row; ?>">
                                </div>
                                <div class="col-md-2">
                                    <label for="metaTitle" class="form-label">Link</label>
                                    <input type="text" class="form-control link" value="<?php echo $slideInfo['link']; ?>" name="link<?php echo $row; ?>" id="link<?php echo $row; ?>" required />
                                </div>
                                <div class="col-md-2">
                                    <label for="metaTitle" class="form-label">Button</label>
                                    <input type="text" class="form-control btn_text" value="<?php echo $slideInfo['btn_text']; ?>" name="btn_text<?php echo $row; ?>" id="btn_text<?php echo $row; ?>"
                                        required />
                                </div>
                                <div class="col-md-2 wp_media_container">
                                    <label for="metaDescription" class="form-label">Desktop Image</label>
                                    <div class="upload-btn-wrapper" id="desktop_img_upload_container">
                                        <button class="btn-upload d-none">Desktop Image </button>
                                        <input type="file" name="desktop_image<?php echo $row; ?>" required id="desktop_image<?php echo $row; ?>"
                                            class="wp_media_frame d-none" />
                                    </div>
                                    <div id="desktop-image-container" class="desktop-image-container wp_media_preview">
                                        <div class="img-content"><img src="<?php echo $slideInfo['desktop_image']; ?>" alt="" width="150px" height="150px"></div>
                                        <div class="sub-text text-center"><a href="#" class="remove_desktop_image">Remove</a>
                                        </div>
                                        <input type="hidden" name="desktop_image<?php echo $row; ?>" value="<?php echo $slideInfo['desktop_image']; ?>"
                                            class="desktop_image wp_media_url" />
                                    </div>
                                </div>
                                <div class="col-md-2 wp_media_container">
                                    <label for="metaDescription" class="form-label">Mobile Image</label>
                                    <div class="upload-btn-wrapper" id="mobile_img_upload_container">
                                        <button class="btn-upload d-none">Mobile Image </button>
                                        <input type="file" name="mobile_image<?php echo $row; ?>" required id="mobile_image<?php echo $row; ?>"
                                            class="wp_media_frame d-none" />
                                    </div>
                                    <div id="mobile-image-container" class="mobile-image-container wp_media_preview">
                                        <div class="img-content"><img src="<?php echo $slideInfo['mobile_image']; ?>" alt="" width="150px" height="150px"></div>
                                        <div class="sub-text text-center"><a href="#" class="remove_mobile_image">Remove</a>
                                        </div>
                                        <input type="hidden" name="mobile_image<?php echo $row; ?>" class="mobile_image wp_media_url" value="<?php echo $slideInfo['mobile_image']; ?>" />
                                    </div>
                                </div>
                            </div>
                        <?php
                        endforeach;
                    else:
                        ?>

                        <div class="row g-3 mt-3 itemRow" data-row="1" id="row1">
                            <div class="col-md-2">
                                <label for="pages" class="form-label">Title</label>
                                <div class="d-flex">
                                    <div class="deleteRow pe-2">
                                        <input type="hidden" class="rowNumber" value="1" />
                                        <a class="delete_item_row" href="#"><img
                                                src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                                    </div>
                                    <input type="text" class="form-control slider_title" value="" name="slider_title1"
                                        id="slider_title1" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="pageTitle" class="form-label">Sub Title</label>
                                <input type="text" class="form-control sub_title" value="" name="sub_title1"
                                    id="sub_title1">
                            </div>
                            <div class="col-md-2">
                                <label for="metaTitle" class="form-label">Link</label>
                                <input type="text" class="form-control link" value="" name="link1" id="link1" required />
                            </div>
                            <div class="col-md-2">
                                <label for="metaTitle" class="form-label">Button</label>
                                <input type="text" class="form-control btn_text" value="" name="btn_text1" id="btn_text1"
                                    required />
                            </div>
                            <div class="col-md-2 wp_media_container">
                                <label for="metaDescription" class="form-label">Desktop Image</label>
                                <div class="upload-btn-wrapper" id="desktop_img_upload_container">
                                    <button class="btn-upload">Desktop Image </button>
                                    <input type="file" name="desktop_image1" required id="desktop_image1"
                                        class="wp_media_frame" />
                                </div>
                                <div id="desktop-image-container" class="desktop-image-container wp_media_preview d-none">
                                    <div class="img-content"></div>
                                    <div class="sub-text text-center"><a href="#" class="remove_desktop_image">Remove</a>
                                    </div>
                                    <input type="hidden" name="desktop_image1" value=""
                                        class="desktop_image wp_media_url" />
                                </div>
                            </div>
                            <div class="col-md-2 wp_media_container">
                                <label for="metaDescription" class="form-label">Mobile Image</label>
                                <div class="upload-btn-wrapper" id="mobile_img_upload_container">
                                    <button class="btn-upload">Mobile Image </button>
                                    <input type="file" name="mobile_image1" required id="mobile_image1"
                                        class="wp_media_frame" />
                                </div>
                                <div id="mobile-image-container" class="mobile-image-container wp_media_preview d-none">
                                    <div class="img-content"></div>
                                    <div class="sub-text text-center"><a href="#" class="remove_mobile_image">Remove</a>
                                    </div>
                                    <input type="hidden" name="mobile_image1" class="mobile_image wp_media_url" value="" />
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12 add_more_container pt-3">
                    <div class="col-6 mx-auto text-center">
                        <a id="add_slide" href="#">
                            <img
                                src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/plus_icon.png'; ?>" /></a>
                    </div>
                </div>


                <h6 class="text-uppercase mt-5 my-3 header_label">Slider Speed</h6>

                <div class="row mt-3">
                    <div class="col-8">
                        <label for="CompetitionSliderSpeed" class="form-label">Competition Slider Speed</label>
                        <input type="text" name="slider_speed" class="form-control w-25"
                            value="<?php echo $recordData['slider_speed']; ?>" />
                    </div>
                </div>

                <h6 class="text-uppercase mt-5 my-3 header_label">Slider Height</h6>

                <div class="row mt-3">
                    <div class="col-3">
                        <label for="CompetitionSliderHeight" class="form-label">Desktop</label>
                        <input type="text" name="slider_height_desktop" class="form-control w-75"
                            value="<?php echo $recordData['slider_height_desktop']; ?>" />
                    </div>
                    <div class="col-3">
                        <label for="CompetitionSliderHeight" class="form-label">Tablet</label>
                        <input type="text" name="slider_height_tablet" class="form-control w-75"
                            value="<?php echo $recordData['slider_height_tablet']; ?>" />
                    </div>
                    <div class="col-3">
                        <label for="CompetitionSliderHeight" class="form-label">Mobile</label>
                        <input type="text" name="slider_height_mobile" class="form-control w-75"
                            value="<?php echo $recordData['slider_height_mobile']; ?>" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>