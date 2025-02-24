<?php

// echo "<pre>";
// print_r($recordData);
// echo "</pre>";



$startingTimeSales = isset($recordData['sale_start_time']) && $recordData['sale_start_time'] != ''
    ? $recordData['sale_start_time']
    : '';

// Default values for hour and minute
$selectedHourSalesstarting = $startingTimeSales ? date('H', strtotime($startingTimeSales)) : '';
$selectedMinuteSalesstarting = $startingTimeSales ? date('i', strtotime($startingTimeSales)) : '';


$closingTimeSales = isset($recordData['sale_end_time']) && $recordData['sale_end_time'] != ''
    ? $recordData['sale_end_time']
    : '';

// Default values for hour and minute
$selectedHourSalesClosing = $closingTimeSales ? date('H', strtotime($closingTimeSales)) : '';
$selectedMinuteSalesClosing = $closingTimeSales ? date('i', strtotime($closingTimeSales)) : '';




?>

<div class="produtcs_content">
    <form id="savProductsContent" method="post" class="form-horizontal" action="" enctype="multipart/form-data">
        <input type="hidden" name="record" value="<?php echo isset($_REQUEST['id']) ? $_REQUEST['id'] : ''; ?>" />
        <input type="hidden" name="mode" value="<?php echo $mode; ?>" />
        <input type="hidden" name="step" value="products" />
        <div class="row">

            <div class="col-xl-6">
                <h6 class="text-uppercase header_label">Competition</h6>
                <div class="row">
                    <div class="col-12">
                        <div class="input-fild">
                            <label for="status" class="form-label">Status*</label>
                            <div class="select">
                                <select class="form-selected" name="status" id="format" required>
                                    <option value="Open" <?php echo (isset($recordData['status']) && $recordData['status'] == 'Open') ? 'selected' : ''; ?>>Open</option>
                                    <option value="Closed" <?php echo (isset($recordData['status']) && $recordData['status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="input-fild">
                            <label for="status" class="form-label">Prize Type*</label>
                            <div class="select">
                                <select class="form-control main_prize_type" name="prize_type" required>
                                    <option value="Prize" <?php echo (isset($recordData['prize_type']) && $recordData['prize_type'] == 'Prize') ? 'selected' : ''; ?>>Prize</option>
                                    <option value="Points" <?php echo (isset($recordData['prize_type']) && $recordData['prize_type'] == 'Points') ? 'selected' : ''; ?>>Points</option>
                                    <option value="Tickets" <?php echo (isset($recordData['prize_type']) && $recordData['prize_type'] == 'Tickets') ? 'selected' : ''; ?>>Tickets</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="Cashalternative" class="form-label">Number of Winners*</label>
                        <input type="number" class="form-control" placeholder=""
                            value="<?php echo (isset($recordData['total_winners']) && $recordData['total_winners'] != '') ? $recordData['total_winners'] : ''; ?>"
                            name="total_winners" min="0" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 prize_cash">
                        <label for="Cashalternative" class="form-label">Cash Alternative</label>
                        <div class="input-group">
                            <span class="input-group-text"> &#163;</span>
                            <input type="number" class="form-control" placeholder=""
                                value="<?php echo (isset($recordData['cash']) && $recordData['cash'] != '') ? $recordData['cash'] : ''; ?>"
                                name="cash" min="0" />
                        </div>
                    </div>

                    <div class="col-6 prize_cash">
                        <label for="Cashalternative" class="form-label">Prize Value</label>
                        <div class="input-group">

                            <input type="number" class="form-control" placeholder=""
                                value="<?php echo (isset($recordData['prize_value']) && $recordData['prize_value'] != '') ? $recordData['prize_value'] : ''; ?>"
                                name="prize_value" min="0" />
                        </div>
                    </div>

                    <div class="col-6 prize_points">
                        <label for="PointsAwarded" class="form-label">Points Awarded*</label>
                        <input type="number" class="form-control" placeholder=""
                            value="<?php echo (isset($recordData['points']) && $recordData['points'] != '') ? $recordData['points'] : ''; ?>"
                            name="points" min="0" required />
                    </div>
                    <div class="col-6 prize_tickets">
                        <label for="CompetitionPrize" class="form-label">Competition*</label>
                        <div class="select">
                            <select class="form-control" name="competitions_prize" id="competitions_prize" required>
                                <?php foreach ($open_competitions as $open_competition): ?>
                                    <option value="<?php echo $open_competition['id']; ?>" <?php echo (isset($recordData['competitions_prize']) && $recordData['competitions_prize'] == $open_competition['id']) ? 'selected' : ''; ?>>
                                        <?php echo $open_competition['title']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-6 prize_tickets">
                        <label for="Ticketscount" class="form-label">Number of tickets*</label>
                        <input type="number" class="form-control" placeholder=""
                            value="<?php echo (isset($recordData['prize_tickets']) && $recordData['prize_tickets'] != '') ? $recordData['prize_tickets'] : ''; ?>"
                            name="prize_tickets" min="0" required />
                    </div>

                    <div class="col-6 mt-3">
                        <input type="hidden" name="webOrder" value="0" />
                        <input class="form-check-input" type="checkbox" id="webOrder" name="webOrder"
                            value="1" <?php echo (isset($recordData['web_order']) && $recordData['web_order'] == '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label">Web Order</label>
                    </div>
                    
                </div>
                <h6 class="text-uppercase mt-5 my-3 header_label">Tickets</h6>
                <div class="row">
                    <div class="col-6">
                        <label for="Priceperticket" class="form-label">Price per ticket*</label>
                        <div class="input-group mt-2">
                            <span class="input-group-text"> &#163;</span>
                            <input type="number" class="form-control" placeholder="" name="price_per_ticket"
                                value="<?php echo (isset($recordData['price_per_ticket']) && $recordData['price_per_ticket'] != '') ? $recordData['price_per_ticket'] : ''; ?>"
                                min="0" required />
                        </div>
                    </div>
                    <div class="col-6">
                        <label for="Totalsellableticket" class="form-label">Total Sellable ticket*</label>
                        <div class="form-group mt-2">
                            <input type="number" step="1" class="form-control greaterThanQTY checkEligibility checkQuantity"
                                placeholder="" name="total_sell_tickets"
                                value="<?php echo (isset($recordData['total_sell_tickets']) && $recordData['total_sell_tickets'] != '') ? $recordData['total_sell_tickets'] : ''; ?>"
                                min="0" required <?php echo ($comp_tickets_purchased > 0) ? "readonly" : ""; ?>
                                data-value="<?php echo (isset($original_qty) && $original_qty > 0) ? $original_qty : ''; ?>">
                        </div>
                    </div>
                    <div class="col-6 mt-3">
                        <label for="Minimumticket" class="form-label">Maximum Tickets per user*</label>
                        <input type="number" step="1" class="form-control matchQuantity" placeholder="" name="max_ticket_per_user"
                            value="<?php echo (isset($recordData['max_ticket_per_user']) && $recordData['max_ticket_per_user'] != '') ? $recordData['max_ticket_per_user'] : ''; ?>"
                            min="0" required />
                    </div>
                    <div class="col-6 mt-3">
                        <label for="Cashalternative" class="d-flex justify-content-between form-label">Default
                            Quantity <span class="text-secondary"><small class="additional_text">(Default
                                    10)</small></span></label>
                        <div class="form-group mt-2">
                            <input type="text" step="1" class="form-control checkTickets" placeholder="" name="quantity"
                                value="<?php echo (isset($recordData['quantity']) && $recordData['quantity'] != '') ? $recordData['quantity'] : '10'; ?>" />
                        </div>
                    </div>
                </div>
                <h6 class="text-uppercase mt-5 my-3 header_label">Sale</h6>

                <span>
                    <?php
                    // Get the current server time in a specific format
                    echo "Current Server Time: " . date('Y-m-d H:i:s') . "\n";

                    ?>
                    <br>
                    <?php echo "Server Timezone: " . date_default_timezone_get() . "\n"; ?>
                </span>

                <div class="row mt-3">
                    <div class="col-6">
                        <label for="Cashalternative" class="form-label">Sale Price</label>
                        <div class="input-group mt-2">
                            <span class="input-group-text"> &#163;</span>
                            <input type="number" class="form-control" placeholder="" name="sale_price"
                                value="<?php echo (isset($recordData['sale_price']) && $recordData['sale_price'] != '') ? $recordData['sale_price'] : ''; ?>"
                                min="0">
                        </div>
                    </div>
                </div>

                <div class="row mt-3">


                    <div class="col-6">
                        <label for="Cashalternative" class="form-label">Sale Price Start Date</label>
                        <div class="form-group mt-2">
                            <input type="text" class="form-control datepicker lessThanSaleEnd"
                                data-date-format="<?php echo get_option('date_format'); ?>" name="sale_start_date"
                                value="<?php echo (isset($recordData['sale_start_date']) && $recordData['sale_start_date'] != '') ? $recordData['sale_start_date'] : ''; ?>">
                        </div>
                    </div>

                    <div class="col-3 ">
                        <label for="Cashalternative" class="form-label">Hour</label>
                        <div class="input-group mt-2">
                            <select name="sale_price_start_time_hour" id="sale_price_start_time_hour" class="form-control greaterThanSalesDateTime">
                            <?php for ($h = 0; $h < 24; $h++): ?>
                                    <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>"
                                        <?= ($selectedHourSalesstarting == str_pad($h, 2, '0', STR_PAD_LEFT)) ? 'selected' : '' ?>>
                                        <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-3 ">
                        <label for="Cashalternative" class="form-label">Time</label>
                        <div class="input-group mt-2">
                            <select name="sale_price_start_time_minute" id="sale_price_start_time_minute" class="greaterThanSalesDateTime form-control">
                            <?php for ($m = 0; $m < 60; $m++): ?>
                                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>"
                                        <?= ($selectedMinuteSalesstarting == str_pad($m, 2, '0', STR_PAD_LEFT)) ? 'selected' : '' ?>>
                                        <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>


                </div>
                <div class="row mt-3">
                    <div class="col-6 ">
                        <label for="Cashalternative" class="form-label">Sale Price End Date</label>
                        <div class="input-group mt-2">
                            <input type="text" class="form-control datepicker greaterThanSaleStart"
                                data-date-format="<?php echo get_option('date_format'); ?>" name="sale_end_date"
                                value="<?php echo (isset($recordData['sale_end_date']) && $recordData['sale_end_date'] != '') ? $recordData['sale_end_date'] : ''; ?>">
                        </div>
                    </div>
                    <div class="col-3 ">
                        <label for="Cashalternative" class="form-label">Hour</label>
                        <div class="input-group mt-2">
                            <select name="sale_price_end_time_hour" id="sale_price_end_time_hour" class="greaterThanSalesDateTime form-control">
                                <?php for ($h = 0; $h < 24; $h++): ?>
                                    <option value="<?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>"
                                        <?= ($selectedHourSalesClosing == str_pad($h, 2, '0', STR_PAD_LEFT)) ? 'selected' : '' ?>>
                                        <?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-3 ">
                        <label for="Cashalternative" class="form-label">Time</label>
                        <div class="input-group mt-2">
                            <select name="sale_price_end_time_minute" id="sale_price_end_time_minute" class="greaterThanSalesDateTime form-control">
                                <?php for ($m = 0; $m < 60; $m++): ?>
                                    <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>"
                                        <?= ($selectedMinuteSalesClosing == str_pad($m, 2, '0', STR_PAD_LEFT)) ? 'selected' : '' ?>>
                                        <?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                </div>

                <h6 class="text-uppercase mt-5 my-3 header_label d-none">Short Description</h6>
                <div class="row d-none">
                    <div class="col-12">
                        <textarea class="form-control" id="short_description" rows="5"
                            name="short_description"><?php echo (isset($recordData['short_description']) && $recordData['short_description'] != '') ? $recordData['short_description'] : ''; ?></textarea>
                    </div>
                </div>

            </div>

            <div class="col-xl-6 px-xl-5">
                <h6 class="text-uppercase header_label">Draw</h6>
                <div class="row">

                    <div class="col-6">
                        <label for="Cashalternative" class="form-label">Closing Date*</label>
                        <input type="text" class="form-control datepicker greaterThanDrawDate"
                            data-date-format="<?php echo get_option('date_format'); ?>" name="closing_date"
                            value="<?php echo (isset($recordData['closing_date']) && $recordData['closing_date'] != '') ? $recordData['closing_date'] : ''; ?>"
                            required>
                    </div>
                    <div class="col-6">
                        <label for="Cashalternative" class="form-label">Closing Time*</label>
                        <input type="text" class="form-control show_time greaterThanDrawDateTime" placeholder="" name="closing_time"
                            value="<?php echo (isset($recordData['closing_time']) && $recordData['closing_time'] != '') ? $recordData['closing_time'] : ''; ?>"
                            required>
                    </div>


                    <div class="col-6  mt-3">
                        <label for="Cashalternative" class="form-label">Draw Date*</label>
                        <input type="text" class="form-control datepicker lessThanClosingDate"
                            data-date-format="<?php echo get_option('date_format'); ?>" placeholder="" name="draw_date"
                            value="<?php echo (isset($recordData['draw_date']) && $recordData['draw_date'] != '') ? $recordData['draw_date'] : ''; ?>"
                            required>
                    </div>
                    <div class="col-6  mt-3">
                        <label for="Cashalternative" class="form-label ">Draw Time*</label>
                        <input type="text" class="form-control show_time greaterThanDrawDateTime" placeholder="" name="draw_time"
                            value="<?php echo (isset($recordData['draw_time']) && $recordData['draw_time'] != '') ? $recordData['draw_time'] : ''; ?>"
                            required>
                    </div>



                    <div class="col-12 mt-3">
                        <label for="Draw" class="d-flex justify-content-between text-secondary order-1">
                            <small class="form-label"> Custom Live Draw Information </small>
                            <span class=" form-check form-switch">
                                <label class="pe-2">Active</label>
                                <input type="hidden" name="live_draw" value="0" />
                                <label class="switch">
                                    <input type="checkbox" id="mySwitch" name="live_draw" value="1" <?php echo (isset($recordData['live_draw']) && $recordData['live_draw'] == '1') ? 'checked' : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </span>
                        </label>
                        <textarea class="form-control" id="live_draw_info"
                            name="live_draw_info"><?php echo (isset($recordData['live_draw_info']) && $recordData['live_draw_info'] != '') ? $recordData['live_draw_info'] : ''; ?></textarea>
                    </div>
                </div>
                <h6 class="text-uppercase mt-5 my-3 header_label">Options</h6>
                <div class="row">

                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="form-check">
                            <input type="hidden" name="disable_tickets" value="0" />
                            <input class="form-check-input" type="checkbox" id="disable_tickets" name="disable_tickets"
                                value="1" <?php echo (isset($recordData['disable_tickets']) && $recordData['disable_tickets'] == '1') ? 'checked' : ''; ?>>
                            <label class="form-check-label">Disable Tickets</label>
                        </div>

                    </div>

                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="form-check">
                            <input type="hidden" name="hide_ticket_count" value="0" />
                            <input class="form-check-input" type="checkbox" id="hide_ticket_count"
                                name="hide_ticket_count" value="1" <?php echo (isset($recordData['hide_ticket_count']) && $recordData['hide_ticket_count'] == '1') ? 'checked' : ''; ?>>
                            <label class="form-check-label">Hide Ticket Count</label>
                        </div>

                    </div>

                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="form-check">
                            <input type="hidden" name="hide_timer" value="0" />
                            <input class="form-check-input" type="checkbox" id="hide_timer" name="hide_timer" value="1"
                                <?php echo (isset($recordData['hide_timer']) && $recordData['hide_timer'] == '1') ? 'checked' : ''; ?>>
                            <label class="form-check-label">Hide Countdown</label>
                        </div>

                    </div>

                    <div class="col-md-3 col-6 mb-2 mb-md-0">
                        <div class="form-check d-none">
                            <input type="hidden" name="is_featured" value="0" />
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                value="1" <?php echo (isset($recordData['is_featured']) && $recordData['is_featured'] == '1') ? 'checked' : ''; ?>>
                            <label class="form-check-label">Show as Featured</label>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>