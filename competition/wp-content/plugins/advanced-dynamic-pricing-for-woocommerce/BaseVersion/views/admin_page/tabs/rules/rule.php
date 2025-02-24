<?php

use ADP\BaseVersion\Includes\Enums\RuleTypeEnum;
use ADP\BaseVersion\Includes\Helpers\Helpers;

defined('ABSPATH') or exit;

/**
 * @var \ADP\Settings\OptionsManager $options
 */

$isCouponEnabled = wc_coupons_enabled();

$pleaseEnableText = __("Please, enable coupons to use price replacements.",
    'advanced-dynamic-pricing-for-woocommerce');

?>

<form class="wdp-ruleitem wdp-ruleitem-{rule_type} postbox closed not-initialized" data-index="{r}">
    <input type="hidden" name="action" value="wdp_ajax">
    <input type="hidden" name="method" value="save_rule">
    <input type="hidden" name="rule[priority]" value="{p}" class="rule-priority"/>
    <input type="hidden" value="" name="rule[id]" class="rule-id">
    <input type="hidden" name="rule[type]" value="common" class="rule-type">
    <input type="hidden" name="rule[exclusive]" value="0">

    <input type="hidden" name="rule[additional][blocks][productFilters][isOpen]" value="0">
    <input type="hidden" name="rule[additional][blocks][productDiscounts][isOpen]" value="0">
    <input type="hidden" name="rule[additional][blocks][roleDiscounts][isOpen]" value="0">
    <input type="hidden" name="rule[additional][blocks][bulkDiscounts][isOpen]" value="0">
    <input type="hidden" name="rule[additional][blocks][freeProducts][isOpen]" value="0">
    <input type="hidden" name="rule[additional][blocks][autoAddToCart][isOpen]" value="0">
    <input type="hidden" name="rule[additional][blocks][advertising][isOpen]" value="0">
    <input type="hidden" name="rule[additional][blocks][cartAdjustments][isOpen]" value="0">
    <input type="hidden" name="rule[additional][blocks][conditions][isOpen]" value="0">
    <input type="hidden" name="rule[additional][blocks][limits][isOpen]" value="0">

    <div class="wdp-ruleitem-row hndle ui-sortable-handle">
        <div class="rule-type-bage">
            <input type="checkbox" class="bulk-action-mark">
        </div>

        <h2>
            <div class="wdp-column wdp-field-enabled">
                <select name="rule[enabled]" data-role="flipswitch" data-mini="true">
                    <option value="off">Off</option>
                    <option value="on" selected>On</option>
                </select>
            </div>
            <div class="wdp-disabled-automatically-prefix">[disabled automatically]</div>
            <span data-wdp-title></span>
        </h2>

        <div class="rule-date-from-to">
            <span><?php _e('From', 'advanced-dynamic-pricing-for-woocommerce') ?></span>
            <input style="max-width: 100px;" class="datepicker" name="rule[additional][date_from]" type="text">
            <span><?php _e('To', 'advanced-dynamic-pricing-for-woocommerce') ?></span>
            <input style="max-width: 100px;" class="datepicker" name="rule[additional][date_to]" type="text" placeholder="<?php _e('include', 'advanced-dynamic-pricing-for-woocommerce') ?>">
        </div>

        <div class="rule-type">
            <span><?php _e('Rule type', 'advanced-dynamic-pricing-for-woocommerce') ?></span>
            <select name="rule[rule_type]">
                <?php if ( $options->getOption("support_persistence_rules") ):?>
                    <option style="background-color: #c8f7d5a6;" value="<?php echo RuleTypeEnum::PERSISTENT()->getValue() ?>">
                        <?php _e('Product only', 'advanced-dynamic-pricing-for-woocommerce') ?>
                    </option>
                <?php endif;?>
                <option style="background-color: #f3f33f33;" value="<?php echo RuleTypeEnum::COMMON()->getValue() ?>">
                    <?php _e('Common', 'advanced-dynamic-pricing-for-woocommerce') ?>
                </option>
            </select>
        </div>

        <div class="rule-id-badge wdp-list-item-id-badge">
            <label><?php _e('#', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
            <label class="rule-id"></label>
        </div>

        <button type="button" class="button-link wdp_remove_rule">
            <span class="screen-reader-text"><?php _e('Delete', 'advanced-dynamic-pricing-for-woocommerce') ?>
                </span>
            <span class="dashicons dashicons-no-alt"
                  title="<?php _e('Delete', 'advanced-dynamic-pricing-for-woocommerce') ?>"></span>
        </button>

        <button type="button" class="button-link wdp_copy_rule">
            <span class="screen-reader-text"><?php _e('Clone', 'advanced-dynamic-pricing-for-woocommerce') ?>
                </span>
            <span class="dashicons dashicons-admin-page"
                  title="<?php _e('Clone', 'advanced-dynamic-pricing-for-woocommerce') ?>"></span>
        </button>

        <button type="button" class="handlediv" aria-expanded="false">
            <span class="screen-reader-text"><?php _e('Expand', 'advanced-dynamic-pricing-for-woocommerce') ?></span>
            <span class="toggle-indicator" aria-hidden="true"
                title="<?php _e('Expand', 'advanced-dynamic-pricing-for-woocommerce') ?>"></span>
        </button>
    </div>
    <!-- <div style="clear: both;"></div> -->
    <div class="inside">
        <div class="wdp-row wdp-options">
            <div class="wdp-row wdp-column wdp-field-title">
                <label><?php _e('Title', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                <input class="wdp-column wdp-title" type="text" name="rule[title]">
            </div>

            <div class="wdp-row wdp-column wdp-repeat">
                <label><?php _e('Can be applied', 'advanced-dynamic-pricing-for-woocommerce') ?>
                    <select name="rule[options][repeat]">
                        <option value="-1"><?php _e('Unlimited', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
                        <option value="1"><?php _e('Once', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </label>
            </div>

            <div class="wdp-row wdp-column wdp-apply-to">
                <label><?php _e('Apply at first to:', 'advanced-dynamic-pricing-for-woocommerce') ?>
                    <select name="rule[options][apply_to]">
                        <option value="expensive"><?php _e('Expensive products',
                                'advanced-dynamic-pricing-for-woocommerce') ?></option>
                        <option value="cheap"><?php _e('Cheap products',
                                'advanced-dynamic-pricing-for-woocommerce') ?></option>
                        <option value="appeared"><?php _e('As appears in the cart',
                                'advanced-dynamic-pricing-for-woocommerce') ?></option>
                    </select>
                </label>
            </div>
        </div>

        <div class="wdp-row wdp-options">
            <div class="buffer"></div>
            <div class="replace-adjustments">
                <div style="float: right" <?php echo $isCouponEnabled ? "" : "title='{$pleaseEnableText}'"; ?>>
                    <label>
                        <input type="checkbox"
                               name="rule[additional][is_replace]">
                        <?php _e("Add products to cart at normal cost and show discount as coupon",
                            'advanced-dynamic-pricing-for-woocommerce') ?>
                    </label>
                    <input type="text" name="rule[additional][replace_name]"
                           placeholder="<?php _e("coupon_name", 'advanced-dynamic-pricing-for-woocommerce') ?>"
                    >

                </div>
            </div>
        </div>

        <?php
        $discount_types = [
            'product_discount'   => [
                'title' => __('Product Discount', 'advanced-dynamic-pricing-for-woocommerce'),
                'description' => __('Make a fixed, percentage or fixed price discount for your products, categories, SKU and etc.', 'advanced-dynamic-pricing-for-woocommerce'),
            ],
            'gifts_discount'     => [
                'title' => __('Gifts', 'advanced-dynamic-pricing-for-woocommerce'),
                'description' => __('Give a gift according to the condition', 'advanced-dynamic-pricing-for-woocommerce'),
            ],
            'bogo_discount'      => [
                'title' => __('BOGO(free)', 'advanced-dynamic-pricing-for-woocommerce'),
                'description' => __('Buy one and get another one as a gift in the cart', 'advanced-dynamic-pricing-for-woocommerce'),
            ],
            'bulk_discount'      => [
                'title' => __('Bulk', 'advanced-dynamic-pricing-for-woocommerce'),
                'description' => __('Make a bulk discount for your products, categories, SKU and etc', 'advanced-dynamic-pricing-for-woocommerce'),
            ],
            'role_bulk_discount' => [
                'title' => __('Role Bulk', 'advanced-dynamic-pricing-for-woocommerce'),
                'description' => __('Make a bulk discount only for some user\'s roles', 'advanced-dynamic-pricing-for-woocommerce'),
            ],
            'role_discount'      => [
                'title' => __('Role Discount', 'advanced-dynamic-pricing-for-woocommerce'),
                'description' => __('Make a fixed, percentage or fixed price discount for some user\'s roles', 'advanced-dynamic-pricing-for-woocommerce'),
            ],
            'cart_discount'      => [
                'title' => __('Cart Discount', 'advanced-dynamic-pricing-for-woocommerce'),
                'description' => __('Give a whole cart discounts, fee or change the shipping price according to the condition', 'advanced-dynamic-pricing-for-woocommerce'),
            ]
        ];
        $discount_types_path = WC_ADP_PLUGIN_URL."/BaseVersion/assets/images/discount_types/";
        ?>

        <?php if(!$options->getOption("create_blank_rule")) { ?>
            <div class="wdp-row wdp-options wdp-discount-type"  style="display: none;">
                <div class="wdp-discount-type-title">
                    <h3><?php _e('Select discount type', 'advanced-dynamic-pricing-for-woocommerce'); ?></h3>
                </div>
                <div class="wdp-discount-type-list">
                    <?php foreach($discount_types as $type => $item) { ?>
                        <div class="wdp-discount-type-item" data-discount-type="<?php echo $type ?>">
                            <div class="wdp-discount-type-item_title" >
                                <?php include(WC_ADP_PLUGIN_PATH."/BaseVersion/assets/images/discount_types/".$type.".svg") ?>
                                <h4><?php echo $item['title'] ?></h4>
                            </div>
                            <div class="wdp-discount-type-item_description">
                                <?php echo $item['description'] ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="wdp-discount-type-skip">
                    <label>
                        <input type="checkbox" name="discount_type_skip" value="1">
                        <?php _e('Skip and create a blank rule next time', 'advanced-dynamic-pricing-for-woocommerce');?>
                    </label>
                    <button type="submit" class="button button-primary" data-discount-type=""><?php _e('Create rule', 'advanced-dynamic-pricing-for-woocommerce');?></button>
                </div>
            </div>
        <?php } ?>

        <div class="wdp-block wdp-filter-block wdp-row" style="display: none;">
            <div class="wdp-column wdp-column-help">
                <label><?php Helpers::ruleFilterLabel('Filter by products', 'advanced-dynamic-pricing-for-woocommerce'); ?></label><br>
                <label class="wdp-filter-warning" style="color:red"><?php _e('If you add many lines to this section – you will create product bundle',
                'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                <p class="wdp-rule-help">
                <?php
                    echo sprintf(
                        wp_kses(
                            __('Select what to discount: any products, certain products, collections, categories, category slugs, attributes, custom attributes, tags, SKUs, custom fields, sellers.', 'advanced-dynamic-pricing-for-woocommerce')
                            .'<br><br>' .__('Exclude products that wouldn’t be discounted: enter the values into the field “Exclude products” or turn on the checkboxes with the same name.', 'advanced-dynamic-pricing-for-woocommerce')
                            .'<br><a href="%s" target="_blank">' .__('Read docs', 'advanced-dynamic-pricing-for-woocommerce') .'</a>',
                        array('br' => array(), 'a' => array('href' => array(), 'target' => array()))
                    ),
                        esc_url('https://docs.algolplus.com/algol_pricing/product-filters-free/')
                    );
                ?>
                </p>
            </div>
            <div class="wdp-wrapper wdp_product_filter wdp-column">
                <div class="wdp-product-filter-container"></div>
                <div class="wdp-add-condition">
                    <button type="button" class="button add-product-filter"><?php _e('Add product filter',
                            'advanced-dynamic-pricing-for-woocommerce'); ?></button>
                </div>
            </div>
        </div>

        <div class="wdp-block wdp-product-adjustments wdp-row" style="display: none;">
            <div class="wdp-column wdp-column-help">
                <label><?php Helpers::ruleFilterLabel('Product discounts', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                <p class="wdp-rule-help">
                <?php
                        echo sprintf(
                            wp_kses(
                                    __('Select the discount type and enter its value.', 'advanced-dynamic-pricing-for-woocommerce').
                                    '<br><a href="%s" target="_blank">' .__('Read docs', 'advanced-dynamic-pricing-for-woocommerce') .'</a>',
                                array('a' => array('href' => array(), 'target' => array()), 'br' => array())
                            ),
                            esc_url('https://docs.algolplus.com/algol_pricing/product-discounts-free/')
                        );
                    ?>
                </p>
            </div>
            <div class="wdp-wrapper wdp-column">
                <div class="wdp-row">
                    <div class="wdp-column">
                        <label>
                            <input type="radio" name="rule[product_adjustments][type]"
                                class="adjustment-mode adjustment-mode-total"
                                data-readonly="1"
                                value="total"/><?php _e('Total', 'advanced-dynamic-pricing-for-woocommerce') ?>
                        </label>
                        <label>
                            <input type="radio" name="rule[product_adjustments][type]"
                                class="adjustment-mode adjustment-mode-split"
                                data-readonly="1"
                                value="split"
                                disabled
                            /><?php _e('Split', 'advanced-dynamic-pricing-for-woocommerce') ?>
                        </label>
                    </div>

                    <div class="wdp-column wdp-btn-remove wdp_product_adjustment_remove">
                        <div class="wdp-btn-remove-handle">
                            <span class="dashicons dashicons-no-alt"></span>
                        </div>
                    </div>
                </div>

                <div class="wdp-row" data-show-if="total">
                    <div class="wdp-column">
                        <select name="rule[product_adjustments][total][type]" class="adjustment-total-type">
                            <option value="discount__amount"><?php _e('Fixed discount',
                                    'advanced-dynamic-pricing-for-woocommerce') ?></option>
                            <option value="discount__percentage"><?php _e('Percentage discount',
                                    'advanced-dynamic-pricing-for-woocommerce') ?></option>
                            <option value="price__fixed"><?php _e('Fixed price',
                                    'advanced-dynamic-pricing-for-woocommerce') ?></option>
                        </select>
                    </div>

                    <div class="wdp-column">
                        <input name="rule[product_adjustments][total][value]" class="adjustment-total-value"
                            type="number" placeholder="0.00" min="0" step="any">
                        <span class="wdp-product-adjustments-total-value-note">
                            <?php _e('To increase the price, make a negative discount', 'advanced-dynamic-pricing-for-woocommerce') ?>
                        </span>
                    </div>
                </div>

                <div class="wdp-product-adjustments-split-container" data-show-if="split"></div>

                <div class="wdp-product-adjustments-options">
                    <div>
                        <div style="display: inline-block;margin: 0 10px 0 0;">
                            <label>
                                <?php _e('Limit discount to amount:', 'advanced-dynamic-pricing-for-woocommerce') ?>
                                <input style="display: inline-block; width: 200px;" name="rule[product_adjustments][max_discount_sum]" type="number" class="product-adjustments-max-discount" placeholder="0.00" min="0" step="any"/>
                            </label>
                        </div>

                        <div style="display: none;margin: 0 10px;width: 20rem;">
                            <div class="split-discount-controls">
                                <label>
                                    <?php _e('Split discount by:', 'advanced-dynamic-pricing-for-woocommerce') ?>
                                    <select name="rule[product_adjustments][split_discount_by]" style="display: inline-block; width: 200px;" class="adjustment-split-discount-type">
                                        <option class="split-discount-by-cost" value="cost"><?php _e('Item cost', 'advanced-dynamic-pricing-for-woocommerce'); ?></option>
                                    </select>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="wdp-sortable-blocks wdp-block-content">
            <div class="sortable-apply-mode-block" style="display: none;">
                <div class="wdp-column"></div>
                <div class="wdp-column" style="flex:20">
                    <div style="width:400px">
                        <label>
                            <?php _e('Role discounts and bulk discounts applied',
                                'advanced-dynamic-pricing-for-woocommerce'); ?>
                            <select class="sortable-apply-mode" name="rule[additional][sortable_apply_mode]"
                                    style="width:150px; display: inline-block">
                                <option value="consistently"><?php _e('Sequentially',
                                        'advanced-dynamic-pricing-for-woocommerce'); ?></option>
                                <option value="min_price_between"><?php _e('Use min price',
                                        'advanced-dynamic-pricing-for-woocommerce'); ?></option>
                                <option value="max_price_between"><?php _e('Use max price',
                                        'advanced-dynamic-pricing-for-woocommerce'); ?></option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            <!--            data-readonly="1" to prevent purge by "flushInputs"-->
            <div class="wdp-block wdp-role-discounts wdp-sortable-block" style="display: none;">
                <input data-readonly="1" type="hidden" class="priority_block_name"
                       name="rule[sortable_blocks_priority][]" value="roles">
                <div class="wdp-column wdp-drag-handle">
                    <span class="dashicons dashicons-menu"></span>
                </div>
                <div class="wdp-row">
                    <div class="wdp-column wdp-column-help">
                        <label><?php Helpers::ruleFilterLabel('Role discounts', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                        <p class="wdp-rule-help">
                        <?php
                            echo sprintf(
                                wp_kses(
                                        __('Choose a user role, which can get a discount, the discount type and amount.', 'advanced-dynamic-pricing-for-woocommerce')
                                        .'<br><a href="%s" target="_blank">' .__('Read docs','advanced-dynamic-pricing-for-woocommerce') .'</a>',
                                    array('br' => array(), 'a' => array('href' => array(), 'target' => array()), )
                                ),
                                esc_url('https://docs.algolplus.com/algol_pricing/role-discounts/')
                            );
                            ?>
                        </p>
                    </div>
                    <div class="wdp-wrapper wdp-column">
                        <div class="wdp-role-discounts-container"></div>
                        <div class="wdp-add-condition">
                            <button type="button" class="button add-role-discount"><?php _e('Add role discount',
                                    'advanced-dynamic-pricing-for-woocommerce'); ?></button>
                            <div>
                                <label class="dont-apply-bulk-if-roles-matched-check">
                                    <input type="checkbox" name="rule[role_discounts][dont_apply_bulk_if_roles_matched]"
                                        value="1">
                                    <?php _e('Skip bulk rules if role rule was applied',
                                        'advanced-dynamic-pricing-for-woocommerce'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wdp-block wdp-bulk-adjustments wdp-sortable-block" style="display: none;">
                <input data-readonly="1" type="hidden" class="priority_block_name"
                       name="rule[sortable_blocks_priority][]" value="bulk-adjustments">
                <div class="wdp-column wdp-drag-handle">
                    <span class="dashicons dashicons-menu"></span>
                </div>
                <div class="wdp-row">
                    <div class="wdp-column wdp-column-help">
                        <label><?php Helpers::ruleFilterLabel('Bulk mode', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                        <p class="wdp-rule-help">
                        <?php
                            echo sprintf(
                                wp_kses(
                                        __('Enter the discount amount based on the number of items in the cart. Put the product quantity in the range  and choose the type of bulk and discount.', 'advanced-dynamic-pricing-for-woocommerce')
                                        .'<br><a href="%s" target="_blank">' .__('Read docs', 'advanced-dynamic-pricing-for-woocommerce') .'</a>',
                                    array('br' => array(), 'a' =>array('href' => array(), 'target' => array()), )
                                ),
                                esc_url('https://docs.algolplus.com/algol_pricing/overview-bulk-mode/')
                            );
                            ?>
                        </p>
                    </div>
                    <div class="wdp-wrapper wdp-column">
                        <div class="wdp-row">
                            <span class="wdp-product-adjustments-type-value-note">
                                <?php
                                    echo sprintf(
                                        wp_kses(
                                            '<a href="%s" target="_blank">' .__('Please, read about difference between Tier and Bulk modes',
                                                'advanced-dynamic-pricing-for-woocommerce') .'</a>',
                                            array('a' => array('href' => array(), 'target' => array()), 'br' => array())
                                        ),
                                        esc_url('https://docs.algolplus.com/algol_pricing/overview-bulk-mode/#bulk-tier')
                                    );
                                ?>
                            </span>
                        </div>
                        <div class="wdp-row">
                            <div class="smaller-width">
                                <div class="wdp-column">
                                    <select name="rule[bulk_adjustments][type]" class="bulk-adjustment-type">
                                        <option value="bulk"><?php _e('Bulk',
                                                'advanced-dynamic-pricing-for-woocommerce') ?></option>
                                        <option value="tier"><?php _e('Tier',
                                                'advanced-dynamic-pricing-for-woocommerce') ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="smaller-width-column">
                                <div class="wdp-column">
                                    <select name="rule[bulk_adjustments][measurement]" class="bulk-measurement-type"></select>
                                </div>
                            </div>

                            <div class="wdp-column">
                                <select name="rule[bulk_adjustments][qty_based]" class="bulk-qty_based-type"></select>
                            </div>

                            <div class="wdp-column bulk-selected_categories-type">
                                <select multiple
                                        data-list="product_categories"
                                        data-field="autocomplete"
                                        data-placeholder="<?php _e("Select values",
                                            "advanced-dynamic-pricing-for-woocommerce") ?>"
                                        name="rule[bulk_adjustments][selected_categories][]">
                                </select>
                            </div>

                            <div class="wdp-column bulk-selected_products-type">
                                <select multiple
                                        data-list="products"
                                        data-field="autocomplete"
                                        data-placeholder="<?php _e("Select values",
                                            "advanced-dynamic-pricing-for-woocommerce") ?>"
                                        name="rule[bulk_adjustments][selected_products][]">
                                </select>
                            </div>

                            <div class="wdp-column">
                                <select name="rule[bulk_adjustments][discount_type]"
                                        class="bulk-discount-type"></select>
                            </div>

                            <div class="wdp-column wdp-btn-remove wdp_bulk_adjustment_remove">
                                <div class="wdp-btn-remove-handle">
                                    <span class="dashicons dashicons-no-alt"></span>
                                </div>
                            </div>
                        </div>

                        <div class="wdp-adjustment-ranges">
                            <div class="wdp-ranges wdp-sortable">
                                <div class="wdp-ranges-empty"><?php _e('No ranges',
                                        'advanced-dynamic-pricing-for-woocommerce') ?></div>
                            </div>

                            <div class="wdp-add-condition">
                                <button type="button" class="button add-range"><?php _e('Add range',
                                        'advanced-dynamic-pricing-for-woocommerce'); ?></button>
                            </div>
                        </div>

                        <div class="wdp-bulk-adjustment-options">
                            <div class="wdp-column">
                                <label>
                                    <?php _e('Bulk table message', 'advanced-dynamic-pricing-for-woocommerce') ?>
                                    <input type="text" name="rule[bulk_adjustments][table_message]"
                                            class="bulk-table-message"
                                            placeholder="<?php _e('If you leave this field empty, we will show default bulk description',
                                                'advanced-dynamic-pricing-for-woocommerce') ?>"/>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="wdp-block wdp-get-products-block wdp-get-products-options wdp-row" style="display: none;">
            <div class="wdp-column wdp-column-help">
                <label><?php Helpers::ruleFilterLabel('Free products', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                <p class="wdp-rule-help">
                <?php
                    echo sprintf(
                        wp_kses(
                                __('Select products that would be gifted to the customers.', 'advanced-dynamic-pricing-for-woocommerce')
                                .'<br><a href="%s" target="_blank">' .__('Read docs', 'advanced-dynamic-pricing-for-woocommerce') .'</a>',
                            array('br' => array(), 'a' => array('href' => array(), 'target' => array()), )
                        ),
                        esc_url('https://docs.algolplus.com/algol_pricing/free-products-free/')
                    );
                    ?>
                </p>
            </div>
            <div class="wdp-wrapper wdp-column">
                <div class="wdp-row wdp-get-products-repeat">
                    <div>
                        <label><?php _e('Can be applied', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                        <select name="rule[get_products][repeat]">
                            <optgroup label="<?php _e('Can be applied', 'advanced-dynamic-pricing-for-woocommerce') ?>">
                                <option value="-1"><?php _e('Unlimited',
                                        'advanced-dynamic-pricing-for-woocommerce') ?></option>
                                <option value="1"><?php _e('Once', 'advanced-dynamic-pricing-for-woocommerce') ?></option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </optgroup>
                            <optgroup label="<?php _e('Based on', 'advanced-dynamic-pricing-for-woocommerce') ?>">
                                <option value="based_on_subtotal"><?php _e('Subtotal (exc. VAT)',
                                        'advanced-dynamic-pricing-for-woocommerce') ?></option>
                                <option value="based_on_subtotal_inc"><?php _e('Subtotal (inc. VAT)',
                                        'advanced-dynamic-pricing-for-woocommerce') ?></option>
                            </optgroup>
                        </select>

                        <div class="repeat-subtotal" style="display: none">
                            <label><?php _e('Repeat counter = subtotal amount divided by',
                                    'advanced-dynamic-pricing-for-woocommerce'); ?>
                                <input class="repeat-subtotal-value" name="rule[get_products][repeat_subtotal]"
                                    placeholder="<?php _e("amount", 'advanced-dynamic-pricing-for-woocommerce') ?>">
                            </label>
                        </div>
                    </div>
                    <div class="replace-free-products">
                        <div
                            style="float: right;" <?php echo $isCouponEnabled ? "" : "title='Please, enable coupons to use price replacements.'"; ?>>
                            <label>
                                <input <?php echo $isCouponEnabled ? "" : "disabled"; ?> type="checkbox"
                                                                                        name="rule[additional][is_replace_free_products_with_discount]">
                                <?php _e("Add free products to cart at normal cost, and add a coupon that will deduce that cost",
                                    'advanced-dynamic-pricing-for-woocommerce') ?>
                            </label>
                            <input <?php echo $isCouponEnabled ? "" : "disabled"; ?> type="text"
                                                                                    name="rule[additional][free_products_replace_name]"
                                                                                    style="width: 100px; display: inline-block;"
                                                                                    placeholder="<?php _e("coupon_name",
                                                                                        'advanced-dynamic-pricing-for-woocommerce') ?>"
                            >
                        </div>
                    </div>
                </div>

                <div class="wdp-get-products"></div>

                <div class="wdp-add-condition">
                    <button type="button" class="button add-filter-get-product"><?php _e('Add product',
                            'advanced-dynamic-pricing-for-woocommerce'); ?></button>
                </div>
            </div>
        </div>

        <div class="wdp-block wdp-cart-adjustments wdp-sortable wdp-row" style="display: none;">
            <div class="wdp-column wdp-column-help">
                <label><?php Helpers::ruleFilterLabel('Cart adjustments', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                <p class="wdp-rule-help">
                <?php
                    echo sprintf(
                        wp_kses(
                                __('Set up a discount, fee, or shipping depending on the execution of a rule in the shopping cart.', 'advanced-dynamic-pricing-for-woocommerce')
                                .'<br><a href="%s" target="_blank">' .__('Read docs', 'advanced-dynamic-pricing-for-woocommerce') .'</a>',
                            array('br' => array(), 'a' => array('href' => array(), 'target' => array()), )
                        ),
                        esc_url('https://docs.algolplus.com/algol_pricing/cart-adjustments-free/')
                    );
                    ?>
                </p>
            </div>
            <div class="wdp-wrapper wdp-column">
                <div class="wdp-cart-adjustments-container"></div>
                <div class="add-cart-adjustment">
                    <button type="button" class="button"><?php _e('Add cart adjustment',
                            'advanced-dynamic-pricing-for-woocommerce'); ?></button>
                </div>
            </div>
        </div>

        <div class="wdp-block wdp-conditions wdp-sortable wdp-row" style="display: none;">
            <div class="wdp-column wdp-column-help">
                <label><?php Helpers::ruleFilterLabel('Conditions', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                <p class="wdp-rule-help">
                <?php
                    echo sprintf(
                        wp_kses(
                                __('Select a cart condition that would trigger a rule execution.', 'advanced-dynamic-pricing-for-woocommerce')
                                .'<br><a href="%s" target="_blank">' .__('Read docs', 'advanced-dynamic-pricing-for-woocommerce') .'</a>',
                            array('br' => array(), 'a' => array('href' =>array(), 'target' => array()))
                        ),
                        esc_url('https://docs.algolplus.com/algol_pricing/cart-conditions-free/')
                    );
                ?>
                <h4 style="margin-bottom: 0px;"><?php _e('Popular conditions:',
                            'advanced-dynamic-pricing-for-woocommerce'); ?></h4>
                <div class="wdp-description ">
                    <div class="wdp-description-content">
                        <ul class="wdp-rule-help" style="column-count: 2;">
                            <?php
                            $mostPopularConditions = [
                                \ADP\BaseVersion\Includes\Core\Rule\CartCondition\Impl\CartSubtotal::class
                                    => __('Subtotal',
                                            'advanced-dynamic-pricing-for-woocommerce'), //(Cart Condition "Subtotal (excl. VAT)”)
                                \ADP\BaseVersion\Includes\Core\Rule\CartCondition\Impl\CustomerRole::class
                                    => __('Role',
                                            'advanced-dynamic-pricing-for-woocommerce'),
                                \ADP\BaseVersion\Includes\Core\Rule\CartCondition\Impl\Date::class
                                    => __('Date',
                                            'advanced-dynamic-pricing-for-woocommerce'),
                                \ADP\BaseVersion\Includes\Core\Rule\CartCondition\Impl\CustomerOrderCount::class
                                    => __('First Order',
                                            'advanced-dynamic-pricing-for-woocommerce'),
                                \ADP\BaseVersion\Includes\Core\Rule\CartCondition\Impl\ShippingCountry::class
                                    => __('Shipping Country',
                                            'advanced-dynamic-pricing-for-woocommerce'),
                                \ADP\BaseVersion\Includes\Core\Rule\CartCondition\Impl\ProductsAll::class
                                    => __('Product in the Cart',
                                            'advanced-dynamic-pricing-for-woocommerce'),
                            ];

                            foreach($mostPopularConditions as $impl => $name) {?>
                                <li>
                                    <span class="wdp-add-popular-condition wdp-link"
                                        data-condition-type="<?php echo $impl::getType() ?>"
                                        <?php if($impl === \ADP\BaseVersion\Includes\Core\Rule\CartCondition\Impl\CustomerOrderCount::class) {?>
                                            data-condition-value="1"
                                        <?php } ?>
                                    >
                                        <?php echo $name ?>
                                </span>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="wdp-description-cut">
                        <span class="wdp-description-cut-more wdp-link"><?php _e('More', 'advanced-dynamic-pricing-for-woocommerce')?></span>
                        <span class="wdp-description-cut-less wdp-link"><?php _e('Less', 'advanced-dynamic-pricing-for-woocommerce')?></span>
                    </div>
                </div>
                <p class="wdp-rule-help">
                    <?php
                    echo sprintf(
                        wp_kses(
                                __('Didn’t find a necessary condition?', 'advanced-dynamic-pricing-for-woocommerce')
                                .'<br><a href="%s" target="_blank">' .__('Program your own custom condition', 'advanced-dynamic-pricing-for-woocommerce') .'</a>',
                            array('br' => array(), 'a' => array('href' =>array(), 'target' => array()))
                        ),
                        esc_url('https://docs.algolplus.com/algol_pricing/program-custom-condition/')
                    );
                    ?>
                </p>

                <a href="https://algolplus.com/plugins/downloads/advanced-dynamic-pricing-woocommerce-pro/"
                   target=_blank><?php _e('Need more conditions?', 'advanced-dynamic-pricing-for-woocommerce') ?></a>
            </div>
            <div class="wdp-wrapper wdp-column">
                <div class="wdp-conditions-relationship">
                    <label><?php _e('Conditions relationship', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                    <label><input type="radio" name="rule[additional][conditions_relationship]" value="and"
                                    checked><?php _e('AND', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                    <label><input type="radio" name="rule[additional][conditions_relationship]"
                                    value="or"><?php _e('OR', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                </div>
                <div class="wdp-conditions-container"></div>
                <div class="add-condition">
                    <button type="button" class="button"><?php _e('Add condition',
                            'advanced-dynamic-pricing-for-woocommerce'); ?></button>
                </div>
            </div>
        </div>

        <div class="wdp-block wdp-limits wdp-sortable wdp-row" style="display: none;">
            <div class="wdp-column wdp-column-help">
                <label><?php Helpers::ruleFilterLabel('Limits', 'advanced-dynamic-pricing-for-woocommerce'); ?></label>
                <p class="wdp-rule-help">
                <?php
                    echo sprintf(
                        wp_kses(
                                __('Configure how often the rule would be applied.', 'advanced-dynamic-pricing-for-woocommerce')
                                .'<br><a href="%s" target="_balnk">' .__('Read docs', 'advanced-dynamic-pricing-for-woocommerce') .'</a>',
                            array('br' => array(), 'a' => array('href' => array(), 'target' => array()))
                        ),
                        esc_url('https://docs.algolplus.com/algol_pricing/limits-free/')
                    );
                    ?>
                </p>
            </div>
            <div class="wdp-wrapper wdp-column">
                <div class="wdp-limits-container"></div>
                <div class="add-limit">
                    <button type="button" class="button"><?php _e('Add limit',
                            'advanced-dynamic-pricing-for-woocommerce'); ?></button>
                </div>
            </div>
        </div>

        <div class="wdp-add-condition">
            <button type="button" class="button wdp-btn-add-product-filter"><?php _e('Product filters',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></button>
            <button type="button" class="button wdp-btn-add-product-adjustment"><?php _e('Product discounts',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></button>
            <button type="button" class="button wdp-btn-add-role-discount"><?php _e('Role discounts',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></button>
            <button type="button" class="button wdp-btn-add-bulk"><?php _e('Bulk rules',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></button>
            <button type="button" class="button wdp-btn-add-getproduct"><?php _e('Free products',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></button>
            <button type="button" class="button wdp-btn-add-cart-adjustment"><?php _e('Cart adjustments',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></button>
            <button type="button" class="button wdp-btn-add-condition"><?php _e('Cart conditions',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></button>
            <button type="button" class="button wdp-btn-add-limit"><?php _e('Limits',
                    'advanced-dynamic-pricing-for-woocommerce'); ?></button>
            <button type="submit" class="button button-primary save-rule"><?php _e('Save changes',
                    'advanced-dynamic-pricing-for-woocommerce') ?></button>
        </div>
    </div>
</form>
