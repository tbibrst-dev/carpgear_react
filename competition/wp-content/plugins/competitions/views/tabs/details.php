<div class="details_content">
    <form id="createBasicCompetition" method="post" class="form-horizontal" action="" enctype="multipart/form-data">
        <input type="hidden" name="step" value="details" />
        <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
        <?php if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0) { ?>
            <input type="hidden" name="record" value="<?php echo $_REQUEST['id']; ?>" />
        <?php } ?>
        <div class="row">
            <div class="col-xl-6">
                <div class="mb-3  input-fild">
                    <label for="title" class="form-label">Title*</label>
                    <input type="text" class="form-control" id="title" name="title"
                        value="<?php echo (isset($recordData['title']) && $recordData['title'] != '') ? $recordData['title'] : ''; ?>"
                        required placeholder="">
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="input-fild">
                            <label for="category" class="form-label">Category*</label>
                            <div class="select">
                                <select name="category" class="form-selected" id="format" required>
                                    <option selected disabled>Choose an option</option>
                                    <option value="instant_win_comps" <?php echo (isset($recordData['category']) && $recordData['category'] == 'instant_win_comps') ? 'selected' : ''; ?>>
                                        Instant Win
                                        Comps</option>
                                    <!-- <option value="drawn_next" <?php echo (isset($recordData['category']) && $recordData['category'] == 'drawn_next') ? 'selected' : ''; ?>>Drawn Next</option> -->
                                    <option value="the_big_gear" <?php echo (isset($recordData['category']) && $recordData['category'] == 'the_big_gear') ? 'selected' : ''; ?>>
                                        The Big Gear
                                    </option>
                                    <option value="the_accessories_and_bait" <?php echo (isset($recordData['category']) && $recordData['category'] == 'the_accessories_and_bait') ? 'selected' : ''; ?>>
                                        The
                                        Accessories & Bait</option>
                                    <option value="finished_and_sold_out" <?php echo (isset($recordData['category']) && $recordData['category'] == 'finished_and_sold_out') ? 'selected' : ''; ?>>
                                        Finished/Sold Out</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <label for="mobileAppOnly" class="form-label">Mobile App Only</label>
                        <div class="mobile-align">
                            <div class="form-check">

                                <input type="hidden" name="via_mobile_app" value="0" />
                                <input type="checkbox" class="form-check-input" id="mobileAppOnly" name="via_mobile_app"
                                    value="1" <?php echo (isset($recordData['via_mobile_app']) && $recordData['via_mobile_app'] == '1') ? 'checked' : ''; ?> />
                                <label for="mobileAppOnly" class="form-check-label">Only show for App users</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="input-fild">
                            <label for="category" class="form-label">Promotional Message</label>
                            <input type="text" class="form-control" id="promotional_messages"
                                name="promotional_messages"
                                value="<?php echo (isset($recordData['promotional_messages']) && $recordData['promotional_messages'] != '') ? $recordData['promotional_messages'] : ''; ?>"
                                placeholder="">
                        </div>
                    </div>
                    <div class="col">
                        <label for="instantWinOnly" class="form-label">Instant Win Only</label>
                        <div class="mobile-align">
                            <div class="form-check">
                                <input type="hidden" name="instant_win_only" value="0" />
                                <input type="checkbox" class="form-check-input" id="instantWinOnly"
                                    name="instant_win_only" value="1" <?php echo (isset($recordData['instant_win_only']) && $recordData['instant_win_only'] == '1') ? 'checked' : ''; ?> />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row file-up mb-3">
                    <div class="col-lg-5 col-md-12">
                        <label for="featuredImage" class="form-label">Featured Image*</label>
                        <div class="file-featured">

                            <?php if (isset($recordData['image']) && !empty($recordData['image'])) { ?>

                                <div class="upload-btn-wrapper d-none" id="feature_img_upload_container">
                                    <img src="<?php echo site_url('wp-content/uploads/2024/04/img-stack-1.png'); ?>" alt="">
                                    <button class="btn-upload">Choose Image </button>
                                    <input type="file" name="featured_image" required id="feature_image" />
                                </div>
                                <div id="feature-image-container" class="">
                                    <div id="img-content">
                                        <img src="<?php echo $recordData['image']; ?>" alt="" width="150px"
                                            height="150px" />
                                    </div>
                                    <div class="sub-text text-center"><a href="#" id="remove_featured_image">Remove</a>
                                    </div>
                                    <input type="hidden" name="featured_image"
                                        value="<?php echo $recordData['image']; ?>" />
                                </div>

                            <?php } else { ?>

                                <div class="upload-btn-wrapper" id="feature_img_upload_container">
                                    <img src="<?php echo site_url('wp-content/uploads/2024/04/img-stack-1.png'); ?>" alt="">
                                    <button class="btn-upload">Choose Image </button>
                                    <input type="file" name="featured_image" required id="feature_image" />
                                </div>
                                <div id="feature-image-container" class="d-none">
                                    <div id="img-content"></div>
                                    <div class="sub-text text-center"><a href="#" id="remove_featured_image">Remove</a>
                                    </div>
                                    <input type="hidden" name="featured_image" value="" />
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12">
                        <label for="galleryImage" class="form-label">Gallery</label>
                        <div class="file-bor">

                            <?php
                            if (isset($recordData['gallery_images']) && !empty($recordData['gallery_images'])) {
                            ?>

                                <div class="upload-btn-wrapper" id="gallery_img_upload_container">
                                    <img src="<?php echo home_url('wp-content/uploads/2024/04/img-stack-1.png'); ?>" alt="">
                                    <button class="btn-upload">Choose Gallery Images</button>
                                    <input type="file" id="gallery_image" value="" />
                                    <input type="hidden" name="gallery_image"
                                        value="<?php echo $recordData['gallery_images']; ?>" />
                                </div>
                                <div id="gallery-image-container">
                                    <?php

                                    $gallery_images = explode(",", $recordData['gallery_images']);

                                    foreach ($gallery_images as $gallery_image) {

                                        $url_type = Competitions_Admin::is_image_or_video($gallery_image);

                                        if ($url_type != 'image')
                                            continue;
                                    ?>

                                        <div class="gallery_preview">
                                            <div class="gallery-img-content">
                                                <img src="<?php echo $gallery_image; ?>" alt="" />
                                            </div>
                                            <div class="sub-text text-center"><a href="#"
                                                    class="remove_gallery_image">Remove</a></div>
                                        </div>

                                    <?php
                                    }

                                    foreach ($gallery_images as $gallery_image) {

                                        $url_type = Competitions_Admin::is_image_or_video($gallery_image);

                                        if ($url_type == 'image')
                                            continue;

                                    ?>

                                        <div class="gallery_preview">
                                            <div class="gallery-img-content">
                                                <video src="<?php echo $gallery_image; ?>" controls="controls"
                                                    preload="metadata"></video>
                                            </div>
                                            <div class="sub-text text-center"><a href="#"
                                                    class="remove_gallery_image">Remove</a></div>
                                        </div>

                                    <?php
                                    }
                                    echo "</div>";
                                } else { ?>

                                    <div class="upload-btn-wrapper" id="gallery_img_upload_container">
                                        <img src="<?php echo home_url('wp-content/uploads/2024/04/img-stack-1.png'); ?>"
                                            alt="">
                                        <button class="btn-upload">Choose Gallery Images</button>
                                        <input type="file" id="gallery_image" value="" />
                                        <input type="hidden" name="gallery_image" value="" />
                                    </div>


                                <?php } ?>

                                <div id="gallery-image-container" class="d-none">
                                    <div class="gallery_preview gallery_content_clone d-none">
                                        <div class="gallery-img-content"></div>
                                        <div class="sub-text text-center"><a href="#"
                                                class="d-none remove_gallery_image">Remove</a></div>
                                    </div>
                                </div>

                                </div>

                        </div>

                    </div>

                    <div class="my-5 input-fild gallery_video_container">



                        <?php

                        if (isset($recordData['gallery_videos']) && !empty($recordData['gallery_videos'])) {

                            foreach ($recordData['gallery_videos'] as $index => $gallery_video) {
                                $gallery_video_type = key($gallery_video);
                                $gallery_video_url = $gallery_video[$gallery_video_type];
                                $gallery_video_thumb = isset($gallery_video['thumb']) ? $gallery_video['thumb'] : '';
                                $gallery_video_url = stripslashes($gallery_video_url);

                        ?>

                                <div class="mb-3 videoRow lineItemRow " data-row="<?php echo $index; ?>" id="row[<?php echo $index; ?>]">
                                    <?php if ($index == 1) { ?>
                                        <label for="title" class="form-label">Gallery Video URLS</label>
                                    <?php } ?>
                                    <div class="form-check ps-0">
                                        <?php if ($index == 1) { ?>
                                            <a id="add_video" href="#">
                                                <img
                                                    src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/plus_icon.png'; ?>" />
                                            </a>

                                        <?php } else { ?>
                                            <a class="delete_video_url" href="#">
                                                <img
                                                    src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" />
                                            </a>
                                        <?php } ?>

                                        <div class="col-3">
                                            <select class="form-control form-select gallery_video_type"
                                                name="gallery_video_type[<?php echo $index; ?>]">
                                                <option value="youtube" <?php if ($gallery_video_type == 'youtube')
                                                                            echo "selected"; ?>>YouTube</option>
                                                <option value="vimeo" <?php if ($gallery_video_type == 'vimeo')
                                                                            echo "selected"; ?>>
                                                    Vimeo</option>
                                            </select>
                                        </div>

                                        <input type="text" class="form-control gallery_video_urls"
                                            name="gallery_video_urls[<?php echo $index; ?>]"
                                            value="<?php echo $gallery_video_url; ?>"
                                            placeholder="Please enter the Vimeo or YouTube video url">

                                        <div class="col-3 gallery_video_thumb">
                                            <div class="image_editor">
                                                <a href="#" class="sub-text wp_media_frame btn <?php echo empty($gallery_video_thumb) ? '' : 'd-none'; ?>">Add Thumbnail </a>
                                                <input type="hidden" class="image prize_image" id="gallery_video_thumb[<?php echo $index; ?>]" name="gallery_video_thumb[<?php echo $index; ?>]" value="<?php echo $gallery_video_thumb; ?>" />
                                                <div class="image_preview_container <?php echo empty($gallery_video_thumb) ? 'd-none' : ''; ?>">
                                                    <div class="img-content"><?php if (!empty($gallery_video_thumb)) echo '<img src="' . $gallery_video_thumb . '" />'; ?></div>
                                                    <div class="sub-text text-center ps-1"><a href="#" class="remove_detail_thumb_media">Remove</a></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            <?php }
                        } else { ?>

                            <div class="mb-3 videoRow lineItemRow">

                                <label for="title" class="form-label">Gallery Video URLS</label>

                                <div class="form-check ps-0">

                                    <a id="add_video" href="#">

                                        <img
                                            src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/plus_icon.png'; ?>" />

                                    </a>

                                    <div class="col-3">
                                        <select class="form-control form-select gallery_video_type"
                                            name="gallery_video_type[1]">
                                            <option value="youtube">YouTube</option>
                                            <option value="vimeo">Vimeo</option>
                                        </select>
                                    </div>

                                    <input type="text" class="form-control gallery_video_urls" name="gallery_video_urls[1]"
                                        value="" placeholder="Please enter the Vimeo or YouTube video url">

                                    <div class="col-3 gallery_video_thumb">
                                        <!-- <label for="image" class="form-label">Image*</label> -->
                                        <div class="image_editor">
                                            <a href="#" class="sub-text wp_media_frame btn ">Add Thumbnail </a>
                                            <input type="hidden" class="image prize_image" id="gallery_video_thumb[1]" name="gallery_video_thumb[1]" />
                                            <div class="image_preview_container d-none">
                                                <div class="img-content"></div>
                                                <div class="sub-text text-center ps-1"><a href="#"
                                                        class="remove_detail_thumb_media">Remove</a></div>
                                            </div>

                                        </div>
                                    </div>

                                </div>

                            </div>

                        <?php } ?>

                    </div>

                    <div class="d-none cloneVideoURLRow lineItemRow" data-row="0" id="row">
                        <div class="mb-3">
                            <div class="form-check ps-0">
                                <a class="delete_video_url" href="#">
                                    <img
                                        src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" />
                                </a>
                                <div class="col-3">
                                    <select class="form-control form-select gallery_video_type">
                                        <option value="youtube">YouTube</option>
                                        <option value="vimeo">Vimeo</option>
                                    </select>
                                </div>
                                <input type="text" class="form-control gallery_video_urls" value=""
                                    placeholder="Please enter the Vimeo or YouTube video url">
                                <div class="col-3 gallery_video_thumb">
                                    <!-- <label for="image" class="form-label">Image*</label> -->
                                    <div class="image_editor">
                                        <a href="#" class="sub-text wp_media_frame btn">Add Thumbnail </a>
                                        <input type="hidden" class="image prize_image" />
                                        <div class="image_preview_container d-none">
                                            <div class="img-content"></div>
                                            <div class="sub-text text-center ps-1"><a href="#"
                                                    class="remove_detail_thumb_media">Remove</a></div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="mb-3 slider_sorting">
                        <label for="sliderSorting" class="form-label">Slider Sorting Order</label>
                        <select multiple="multiple" name="selections[]" id="selections" class="form-control"
                            style="width:100%;">
                            <option value="Feature Image" <?php echo (isset($recordData['slider_sorting']) && in_array("Feature Image", $recordData['slider_sorting'])) ? 'selected' : ''; ?>>
                                Feature Image
                            </option>
                            <option value="Gallery Images" <?php echo (isset($recordData['slider_sorting']) && in_array("Gallery Images", $recordData['slider_sorting'])) ? 'selected' : ''; ?>>
                                Gallery Images
                            </option>
                            <option value="Video URLs" <?php echo (isset($recordData['slider_sorting']) && in_array("Video URLs", $recordData['slider_sorting'])) ? 'selected' : ''; ?>>
                                Video URLs
                            </option>
                        </select>
                        <input type="hidden" name="slidersortinglist" value='<?php echo $slider_sorting; ?>' />
                    </div>

                </div>
                <div class="col-xl-6">
                    <div class="mb-3">
                        <label for="descriptionEditor" class="form-label">Description*</label>
                        <textarea id="description_editor" style="min-height: 350px;"
                            name="description"><?php echo (isset($recordData['description']) && $recordData['description'] != '') ? $recordData['description'] : ''; ?></textarea>
                    </div>
                </div>
            </div>
    </form>
</div>