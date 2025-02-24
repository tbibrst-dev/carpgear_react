<div class="instant_content">
    <form id="saveInstantContent" method="post" class="form-horizontal" action="" enctype="multipart/form-data">
        <input type="hidden" name="record" value="<?php echo isset($_REQUEST['id']) ? $_REQUEST['id'] : ''; ?>" />
        <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
        <input type="hidden" name="step" value="instant" />
        <input type="hidden" name="total_prizes" id="total_prizes" value="1" />
        <input type="hidden" id="prize_ticket_list" value='' />
        <div class="row mb-3">
            <div class="col-6">
                <div class="form-check">
                    <input type="hidden" name="enable_instant_wins" value="0" />
                    <input type="checkbox" name="enable_instant_wins" id="enable_instant_wins" value="1"
                        class="form-check-input" <?php echo (isset($recordData['enable_instant_wins']) && $recordData['enable_instant_wins'] == '1') ? 'checked' : ''; ?> />
                    <label for="mobileAppOnly" class="form-check-label">Enable Instant Wins</label>
                </div>
            </div>
            <div class="col-6">
                <div class="text-end">
                    <span class=" total_prize_label">Total Prizes <span id="instant_prize_total">(0)</span></span>
                    <a href="#" id="generate_ticket_numbers" <?php echo ($comp_tickets_purchased > 0) ? "class='ticket_disabled'" : ""; ?>>Generate Ticket Numbers</a>
                    <button type="button" class="btn btn-sm btn-accent btn-acc-sm" data-bs-toggle="modal"
                        data-bs-target="#csvModal" <?php echo ($comp_tickets_purchased > 0) ? "disabled" : ""; ?>>CSV
                        Import</button>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <h6 class="text-uppercase header_label addInstantWinHeaderLabel">Prizes</h6>
            </div>
        </div>

        <div class="mb-3" id="instant_wins_content">

            <div class="lineItemCloneCopy d-none" data-row="0">

                <div class="instant-row row mb-3">

                    <div class="col-4">
                        <label for="title" class="form-label">Title*</label>
                        <input type="text" class="form-control prize_title title" id="title0" name="title0"
                            placeholder="">
                    </div>
                    <div class="col-2">
                        <label for="type" class="form-label prize_type">Type*</label>
                        <select class="form-control price_type reward-type-dropdown " name="price_type0" id="price_type0">
                            <option value="Prize">Prize</option>
                            <option value="Points">Points</option>
                            <option value="Tickets">Tickets</option>
                        </select>
                    </div>
                    <div class="col-2 value_col">
                        <label for="cashvalue " class="form-label labelCashInstant">Cash Alt</label>
                        <input type="number" class="form-control cash_value" id="cash_value0" name="cash_value0"
                            min="0">
                    </div>

                    <div class="col-2 value_col">
                        <label for="cashvalue" class="form-label">Prize Value*</label>
                        <input type="number" class="form-control cash_value prize_value" id="prize_value0" name="prize_value0"
                            min="0">
                    </div>

                    <div class="col-2 ticket_col">
                        <label for="cashvalue" class="form-label">Competition*</label>
                        <select class="form-control competition_prize" name="competition_prize0"
                            id="competition_prize0">
                            <?php foreach ($open_competitions as $open_competition): ?>
                                <option value="<?php echo $open_competition['id']; ?>">
                                    <?php echo $open_competition['title']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-2 ticket_col">
                        <label for="prize_total_tickets" class="form-label">Number of tickets*</label>
                        <input type="number" step="1" class="form-control prize_total_tickets" id="prize_total_tickets0"
                            name="prize_total_tickets0" value="" min="0">
                    </div>
                    <div class="col-1">
                        <label for="qty" class="form-label">Quantity*</label>
                        <input type="number" step="1" class="form-control quantity prize_qty" id="quantity0"
                            name="quantity0" value="1" min="0">
                    </div>

                    <div class="col-1">
                        <label class="form-label">Web Order</label>
                        <input class="form-check-input webOrderInstant" type="checkbox" id="webOrderInstant0" name="webOrderInstant0"
                            value="1">

                    </div>

                    <div class="col-2">
                        <label for="image" class="form-label">Image*</label>
                        <div class="image_editor">
                            <a href="#" class="sub-text wp_media_frame btn">Add Image </a>
                            <input type="hidden" class="image" id="image0" name="image0" />
                            <div class="image_preview_container d-none">
                                <div class="img-content"></div>
                                <div class="sub-text text-center ps-1"><a href="#"
                                        class="remove_instant_media">Remove</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-1 deleteRow">
                        <input type="hidden" class="rowNumber" value="" />
                        <a class="delete_price" href="#"><img
                                src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                    </div>
                </div>

                <div class="row mb-3 prize_tickets">
                    <div class="col-2">
                        <input type="text" name="ticket0_1" id="ticket0_1" value=""
                            class="form-control instant_tickets ticket0_1" />
                    </div>
                </div>

                <div class="mb-3 instant_prize_description">
                    <div class="mb-3">
                        <span class="form-check form-switch d-flex">
                            <label class="form-check-label me-3 text-secondary custom_label" for="custom">
                                Description
                            </label>
                            <input type="hidden" name="show_description0" value="0" class="show_description">
                            <label class="switch">
                                <input type="checkbox" id="show_description0" name="show_description0" value="1"
                                    checked="">
                                <span class="sliders rounds toggle_description"></span>
                            </label>
                        </span>
                    </div>
                    <div class="row description-container">
                        <div class="col-12">
                            <textarea class="form-control prize_description" id="prize_description0"
                                name="prize_description0"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            if (($mode == 'edit' || $mode == 'create') && !empty($instant_wins)) {
                $index = 0;
                foreach ($instant_wins as $instant_win) {
                    $index++;
            ?>
                    <div class="lineItemRow" data-row="<?php echo $index; ?>" id="row<?php echo $index; ?>">

                        <div class="instant-row row mb-3">

                            <div class="col-xxl-4 mb-3">
                                <label for="title" class="form-label">Title*</label>
                                <input type="text" class="form-control prize_title title" id="title<?php echo $index; ?>"
                                    name="title<?php echo $index; ?>" value="<?php echo $instant_win['title']; ?>" required>
                            </div>

                            <div class="col-xxl-2 col-md-4 mb-3">
                                <label for="type" class="form-label prize_type">Type*</label>
                                <select class="form-control price_type reward-type-dropdown" name="price_type<?php echo $index; ?>"
                                    id="price_type<?php echo $index; ?>">
                                    <option value="Prize" <?php echo ($instant_win['type'] == 'Prize') ? "selected" : ""; ?>>Prize
                                    </option>
                                    <option value="Points" <?php echo ($instant_win['type'] == 'Points') ? "selected" : ""; ?>>
                                        Points
                                    </option>
                                    <option value="Tickets" <?php echo ($instant_win['type'] == 'Tickets') ? "selected" : ""; ?>>
                                        Tickets</option>
                                </select>
                            </div>

                            <div class="col-xxl-2 col-md-4 mb-3 value_col">
                                <label for="cashvalue " class="form-label labelCashInstant"> <?php
                                                                                                if ($instant_win['type'] == 'Prize') {
                                                                                                    echo "Cash Alt";
                                                                                                }else{
                                                                                                    echo "Points Value";
                                                                                                }   
                                                                                                ?></label>
                                <input type="number" class="form-control cash_value" id="cash_value<?php echo $index; ?>"
                                    name="cash_value<?php echo $index; ?>" min="0" value="<?php echo $instant_win['value']; ?>">
                            </div>

                            <div class="col-xxl-2 col-md-4 mb-3 value_col">
                                <label for="cashvalue" class="form-label">Prize Value*</label>
                                <input type="number" class="form-control cash_value prize_value" id="prize_value<?php echo $index; ?>"
                                    name="prize_value<?php echo $index; ?>" min="0" value="<?php echo $instant_win['prize_value']; ?>" required>
                            </div>

                            <div class="col-xxl-2 col-md-4 mb-3 ticket_col">
                                <label for="cashvalue" class="form-label">Competition*</label>
                                <select class="form-control competition_prize" name="competition_prize<?php echo $index; ?>"
                                    id="competition_prize<?php echo $index; ?>">
                                    <?php foreach ($open_competitions as $open_competition): ?>
                                        <option value="<?php echo $open_competition['id']; ?>" <?php echo ($instant_win['competition_prize'] == $open_competition['id']) ? "selected" : ""; ?>>
                                            <?php echo $open_competition['title']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-xxl-2 col-md-4 mb-3 ticket_col">
                                <label for="prize_total_tickets" class="form-label">Number of tickets*</label>
                                <input type="number" step="1" class="form-control prize_total_tickets"
                                    id="prize_total_tickets<?php echo $index; ?>"
                                    name="prize_total_tickets<?php echo $index; ?>"
                                    value="<?php echo $instant_win['prize_total_tickets']; ?>" min="0">
                            </div>

                            <div class="col-xxl-1 col-md-4 mb-3">
                                <label for="qty" class="form-label">Quantity*</label>
                                <input type="number" step="1" class="form-control quantity prize_qty"
                                    id="quantity<?php echo $index; ?>" name="quantity<?php echo $index; ?>"
                                    value="<?php echo $instant_win['quantity']; ?>" min="0">
                            </div>

                            <div class="col-xxl-1 col-md-4 mb-3">
                                <label class="form-label web_lab">Web Order</label>


                                <input class="form-check-input webOrderInstant" type="checkbox" id="webOrderInstant<?php echo $index; ?>" name="webOrderInstant<?php echo $index; ?>"
                                    value="1" <?php echo (isset($instant_win['web_order_instant']) && $instant_win['web_order_instant'] == '1') ? 'checked' : ''; ?>>
                            </div>

                            <div class="col-xxl-2 col-md-4 mb-3">
                                <label for="image" class="form-label">Image*</label>
                                <div class="image_editor">
                                    <a href="#" class="sub-text wp_media_frame btn d-none">Add Image </a>
                                    <input type="hidden" class="image prize_image" id="image<?php echo $index; ?>"
                                        name="image<?php echo $index; ?>" value="<?php echo $instant_win['image']; ?>" />
                                    <div class="image_preview_container">
                                        <div class="img-content"><img src="<?php echo $instant_win['image']; ?>" alt=""
                                                width="150px" height="150px" /></div>
                                        <div class="sub-text text-center ps-1"><a href="#"
                                                class="remove_instant_media">Remove</a></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-1 deleteRow">
                                <input type="hidden" class="rowNumber" value="<?php echo $index; ?>" />
                                <a class="delete_price" href="#"><img
                                        src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                            </div>

                        </div>
                        <div class="row mb-3 prize_tickets">

                            <?php
                            if (!empty($prize_tickets) && isset($prize_tickets[$instant_win['id']]) && !empty($prize_tickets[$instant_win['id']])) {
                                $t_num = 0;
                                foreach ($prize_tickets[$instant_win['id']] as $prize_ticket) {
                                    $t_num++;
                            ?>
                                    <div class="col-xxl-2 col-md-4 mb-3">
                                        <input type="text" name="ticket<?php echo $index; ?>_<?php echo $t_num; ?>"
                                            id="ticket<?php echo $index; ?>_<?php echo $t_num; ?>" value="<?php echo $prize_ticket; ?>"
                                            class="form-control instant_tickets ticket<?php echo $index; ?>_<?php echo $t_num; ?>" />

                                    </div>
                            <?php }
                            } ?>

                        </div>
                        <div class="mb-3 instant_prize_description">
                            <div class="mb-3">
                                <span class="form-check form-switch d-flex">
                                    <label class="form-check-label me-3 text-secondary custom_label" for="custom">
                                        Description
                                    </label>
                                    <input type="hidden" name="show_description<?php echo $index; ?>" value="0"
                                        class="show_description">
                                    <label class="switch">
                                        <input type="checkbox" id="show_description<?php echo $index; ?>"
                                            name="show_description<?php echo $index; ?>" value="1" <?php echo (isset($instant_win['show_description']) && $instant_win['show_description'] == '1') ? 'checked' : ''; ?>>
                                        <span class="sliders rounds toggle_description"></span>
                                    </label>
                                </span>
                            </div>
                            <div class="row description-container <?php if ($instant_win['show_description'] == '0')
                                                                        echo 'd-none'; ?>">
                                <div class="col-12">
                                    <textarea class="form-control prize_description" id="prize_description<?php echo $index; ?>"
                                        name="prize_description<?php echo $index; ?>"><?php echo html_entity_decode(stripslashes($instant_win['prize_description'])); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="lineItemRow" data-row="1" id="row1">

                    <div class="instant-row row mb-3">

                        <div class="col-4">
                            <label for="title" class="form-label">Title*</label>
                            <input type="text" class="form-control prize_title title" id="title1" name="title1"
                                placeholder="" required>
                        </div>
                        <div class="col-2">
                            <label for="type" class="form-label prize_type">Type*</label>
                            <select class="form-control price_type reward-type-dropdown" name="price_type1" id="price_type1">
                                <option value="Prize">Prize</option>
                                <option value="Points">Points</option>
                                <option value="Tickets">Tickets</option>
                            </select>
                        </div>
                        <div class="col-2 value_col">
                            <label for="cashvalue " class="form-label labelCashInstant">Cash Alt</label>
                            <input type="number" class="form-control cash_value" id="cash_value1" name="cash_value1"
                                min="0">
                        </div>

                        <div class="col-2 value_col">
                            <label for="cashvalue" class="form-label">Prize Value*</label>
                            <input type="number" class="form-control cash_value prize_value" id="prize_value1" name="prize_value1"
                                min="0" required>
                        </div>

                        <div class="col-2 ticket_col">
                            <label for="cashvalue" class="form-label">Competition*</label>
                            <select class="form-control competition_prize" name="competition_prize1"
                                id="competition_prize1">
                                <?php foreach ($open_competitions as $open_competition): ?>
                                    <option value="<?php echo $open_competition['id']; ?>">
                                        <?php echo $open_competition['title']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-2 ticket_col">
                            <label for="prize_total_tickets" class="form-label">Number of tickets*</label>
                            <input type="number" step="1" class="form-control prize_total_tickets" id="prize_total_tickets1"
                                name="prize_total_tickets1" value="" min="0">
                        </div>
                        <div class="col-1">
                            <label for="qty" class="form-label">Quantity*</label>
                            <input type="number" step="1" class="form-control quantity prize_qty" id="quantity1"
                                name="quantity1" value="1" min="0">
                        </div>
                        <div class="col-1">
                            <label class="form-label">Web Order</label>
                            <input class="form-check-input webOrderInstant" type="checkbox" id="webOrderInstant1" name="webOrderInstant1"
                                value="1">

                        </div>

                        <div class="col-2">
                            <label for="image" class="form-label">Image*</label>
                            <div class="image_editor">
                                <a href="#" class="sub-text wp_media_frame btn">Add Image </a>
                                <input type="hidden" class="image prize_image" id="image1" name="image1" />
                                <div class="image_preview_container d-none">
                                    <div class="img-content"></div>
                                    <div class="sub-text text-center ps-1"><a href="#"
                                            class="remove_instant_media">Remove</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-1 deleteRow">
                            <input type="hidden" class="rowNumber" value="1" />
                            <a class="delete_price" href="#"><img
                                    src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                        </div>
                    </div>

                    <div class="row mb-3 prize_tickets">
                        <div class="col-2">
                            <input type="text" name="ticket1_1" id="ticket1_1" value=""
                                class="form-control instant_tickets ticket1_1" />
                        </div>
                    </div>

                    <div class="mb-3 instant_prize_description">
                        <div class="mb-3">
                            <span class="form-check form-switch d-flex">
                                <label class="form-check-label me-3 text-secondary custom_label" for="custom">
                                    Description
                                </label>
                                <input type="hidden" name="show_description1" value="0" class="show_description">
                                <label class="switch">
                                    <input type="checkbox" id="show_description1" name="show_description1" value="1"
                                        checked="">
                                    <span class="sliders rounds toggle_description"></span>
                                </label>
                            </span>
                        </div>
                        <div class="row description-container">
                            <div class="col-12">
                                <textarea class="form-control prize_description" id="prize_description1"
                                    name="prize_description1"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
        <div class="row">
            <div class="col-6 mx-auto text-center addInstantWinPlus">
                <a id="add_price" href="#"><img
                        src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/plus_icon.png'; ?>" /></a>
            </div>
        </div>
    </form>
</div>
<div class="show_loader d-none">
    <div class="modal-backdrop show"></div>
    <div class="d-flex justify-content-center comp_loader">
        <div class="spinner-border" role="status">
            <span class="sr-only"></span>
        </div>
    </div>
</div>