<?php

// echo "<pre>";
// print_r($reward_wins);
// echo "</pre>";
?>

<div class="reward_content">
    <form id="saveRewardContent" method="post" class="form-horizontal" action="" enctype="multipart/form-data">
        <input type="hidden" name="record" value="<?php echo isset($_REQUEST['id']) ? $_REQUEST['id'] : ''; ?>" />
        <input type="hidden" name="step" value="reward" />
        <input type="hidden" name="total_reward" id="total_reward" value="1" />
        <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
        <input type="hidden" name="is_draft" value="1" />

        <div class="row mb-3">
            <div class="col-6">
                <div class="form-check">
                    <input type="hidden" name="enable_reward_wins" value="0" />
                    <input type="checkbox" name="enable_reward_wins" id="enable_reward_wins" value="1"
                        class="form-check-input" <?php echo (isset($recordData['enable_reward_wins']) && $recordData['enable_reward_wins'] == '1') ? 'checked' : ''; ?> />
                    <label for="enableRewardWins" class="form-check-label">Enable Reward Wins</label>
                </div>
            </div>
            <div class="col-6">
                <div class="text-end">
                    <button type="button" class="btn btn-sm btn-accent btn-acc-sm" data-bs-toggle="modal"
                        data-bs-target="#csvModal">CSV Import</button>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <h6 class="text-uppercase header_label rewardWinHeaderLabel">Prizes</h6>
            </div>
        </div>

        <div class="mb-3" id="reward_wins_content">

            <div class="lineItemCloneCopy d-none" data-row="0">

                <div class="reward-row row mb-3">

                    <div class="col-4">
                        <label for="title" class="form-label">Title*</label>
                        <input type="text" class="form-control prize_title title" id="title0" name="title0"
                            placeholder="">
                    </div>
                    <div class="col-2">
                        <label for="type" class="form-label prize_type">Type*</label>
                        <select class="form-control price_type reward-type-dropdown reward-type-dropdown" name="price_type0" id="price_type0">
                            <option value="Prize">Prize</option>
                            <option value="Points">Points</option>
                            <option value="Tickets">Tickets</option>
                        </select>
                    </div>
                    <div class="col-1">
                        <label for="available" class="form-label">% Split</label>
                        <input type="number" class="form-control prct_available" id="prct_available0"
                            name="prct_available0" min="0">
                    </div>
                    <div class="col-2 value_col">
                        <label for="cashvalue " class="form-label labelCashReward">Cash Alt</label>
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
                    <div class="col-2">
                        <label for="image" class="form-label">Image*</label>
                        <div class="image_editor">
                            <a href="#" class="sub-text wp_media_frame btn">Add Image </a>
                            <input type="hidden" class="image" id="image0" name="image0" />
                            <div class="image_preview_container d-none">
                                <div class="img-content"></div>
                                <div class="sub-text text-center ps-1"><a href="#"
                                        class="remove_reward_media">Remove</a></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-1">
                        <label class="form-label">Web Order</label>
                        <input class="form-check-input webOrderReward" type="checkbox" id="webOrderReward0" name="webOrderReward0"
                            value="1">
                    </div>

                    <div class="col-1 deleteRow">
                        <input type="hidden" class="rowNumber" value="" />
                        <a class="delete_reward_price" href="#"><img
                                src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                    </div>

                </div>
            </div>

            <?php
            if (($mode == 'edit' || $mode == 'create') && !empty($reward_wins)) {
                $index = 0;
                foreach ($reward_wins as $reward_win) {
                    $index++;
            ?>

                    <div class="lineItemRow" data-row="<?php echo $index; ?>" id="row<?php echo $index; ?>">

                        <div class="reward-row row mb-3">

                            <div class="col-4">
                                <label for="title" class="form-label">Title*</label>
                                <input type="text" class="form-control prize_title title" id="title<?php echo $index; ?>"
                                    name="title<?php echo $index; ?>" value="<?php echo $reward_win['title']; ?>" required>
                            </div>
                            <div class="col-2">
                                <label for="type" class="form-label prize_type">Type*</label>
                                <select class="form-control price_type reward-type-dropdown reward-type-dropdown" name="price_type<?php echo $index; ?>"
                                    id="price_type<?php echo $index; ?>">
                                    <option value="Prize" <?php echo ($reward_win['type'] == 'Prize') ? "selected" : ""; ?>>Prize
                                    </option>
                                    <option value="Points" <?php echo ($reward_win['type'] == 'Points') ? "selected" : ""; ?>>
                                        Points</option>
                                    <option value="Tickets" <?php echo ($reward_win['type'] == 'Tickets') ? "selected" : ""; ?>>
                                        Tickets</option>
                                </select>
                            </div>
                            <div class="col-1">
                                <label for="available" class="form-label">% Split</label>
                                <input type="number" class="form-control prct_available"
                                    id="prct_available<?php echo $index; ?>" name="prct_available<?php echo $index; ?>" min="0"
                                    value="<?php echo $reward_win['prcnt_available']; ?>">
                            </div>
                            <div class="col-2 value_col">
                                <label for="cashvalue " class="form-label labelCashReward"><?php
                                                                                            if ($reward_win['type'] == 'Prize') {
                                                                                                echo "Cash Alt";
                                                                                            } else {
                                                                                                echo "Points Value";
                                                                                            }
                                                                                            ?></label>
                                <input type="number" class="form-control cash_value" id="cash_value<?php echo $index; ?>"
                                    name="cash_value<?php echo $index; ?>" min="0" value="<?php echo $reward_win['value']; ?>">
                            </div>

                            <div class="col-2 value_col">
                                <label for="cashvalue" class="form-label">Prize Value</label>
                                <input type="number" class="form-control cash_value prize_value" id="prize_value<?php echo $index; ?>"
                                    name="prize_value<?php echo $index; ?>" min="0" value="<?php echo $reward_win['prize_value']; ?>" required>
                            </div>




                            <div class="col-2 ticket_col">
                                <label for="cashvalue" class="form-label">Competition*</label>
                                <select class="form-control competition_prize" name="competition_prize<?php echo $index; ?>"
                                    id="competition_prize<?php echo $index; ?>">
                                    <?php foreach ($open_competitions as $open_competition): ?>
                                        <option value="<?php echo $open_competition['id']; ?>" <?php echo ($reward_win['competition_prize'] == $open_competition['id']) ? "selected" : ""; ?>>
                                            <?php echo $open_competition['title']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-2 ticket_col">
                                <label for="prize_total_tickets" class="form-label">Number of tickets*</label>
                                <input type="number" step="1" class="form-control prize_total_tickets"
                                    id="prize_total_tickets<?php echo $index; ?>"
                                    name="prize_total_tickets<?php echo $index; ?>"
                                    value="<?php echo $reward_win['prize_total_tickets']; ?>" min="0">
                            </div>
                            <div class="col-2">
                                <label for="image" class="form-label">Image*</label>
                                <div class="image_editor">
                                    <a href="#" class="sub-text wp_media_frame btn d-none">Add Image </a>
                                    <input type="hidden" class="image prize_image" id="image<?php echo $index; ?>"
                                        name="image<?php echo $index; ?>" value="<?php echo $reward_win['image']; ?>" />
                                    <div class="image_preview_container">
                                        <div class="img-content"><img src="<?php echo $reward_win['image']; ?>" alt=""
                                                width="150px" height="150px" /></div>
                                        <div class="sub-text text-center ps-1"><a href="#"
                                                class="remove_reward_media">Remove</a></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-1">
                                <label class="form-label">Web Order</label>
                                <input class="form-check-input webOrderReward" type="checkbox" id="webOrderReward<?php echo $index; ?>" name="webOrderReward<?php echo $index; ?>"
                                    value="1" <?php echo (isset($reward_win['web_order_reward']) && $reward_win['web_order_reward'] == '1') ? 'checked' : ''; ?>>
                            </div>

                            <div class="col-1 deleteRow">
                                <input type="hidden" class="rowNumber" value="<?php echo $index; ?>" />
                                <a class="delete_reward_price" href="#"><img
                                        src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                ?>

                <div class="lineItemRow" data-row="1" id="row1">

                    <div class="reward-row row mb-3">

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
                        <div class="col-1">
                            <label for="available" class="form-label">% Split</label>
                            <input type="number" class="form-control prct_available" id="prct_available1"
                                name="prct_available1" min="0" value="100">
                        </div>

                        <div class="col-2 value_col">
                            <label for="cashvalue " class="form-label labelCashReward">Cash Alt</label>
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
                        <div class="col-2">
                            <label for="image" class="form-label">Image*</label>
                            <div class="image_editor">
                                <a href="#" class="sub-text wp_media_frame btn">Add Image </a>
                                <input type="hidden" class="image prize_image" id="image1" name="image1" />
                                <div class="image_preview_container d-none">
                                    <div class="img-content"></div>
                                    <div class="sub-text text-center ps-1"><a href="#"
                                            class="remove_reward_media">Remove</a></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-1">
                            <label class="form-label">Web Order</label>
                            <input class="form-check-input webOrderReward" type="checkbox" id="webOrderReward1" name="webOrderReward1"
                                value="1">
                        </div>
                        
                        <div class="col-1 deleteRow">
                            <input type="hidden" class="rowNumber" value="1" />
                            <a class="delete_reward_price" href="#"><img
                                    src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/remove_icon.png'; ?>" /></a>
                        </div>
                        
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col-6 mx-auto text-center rewardWinPlus">
                <a id="add_price" href="#"><img
                        src="<?php echo plugin_dir_url('competitions/inc') . '_inc/img/plus_icon.png'; ?>" /></a>
            </div>
        </div>
    </form>
</div>