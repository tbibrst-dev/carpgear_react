<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;
?>

<div class="shop_table woocommerce-checkout-review-order-table">
    <!-- <thead>
        <tr>
            <th class="product-name">
                <?php //esc_html_e('Product', 'woocommerce'); ?>
            </th>
            <th class="product-total">
                <?php //esc_html_e('Subtotal', 'woocommerce'); ?>
            </th>
        </tr>
    </thead>
    <tbody> -->
    <?php
    do_action('woocommerce_review_order_before_cart_contents');

    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
            ?>
            <!-- <tr
                class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                <td class="product-name">
                    <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)) . '&nbsp;'; ?>
                    <?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times;&nbsp;%s', $cart_item['quantity']) . '</strong>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    ?>
                    <?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    ?>
                </td>
                <td class="product-total">
                    <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    ?>
                </td>
            </tr> -->

            <div
                class="checkout-section-right-top-box <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                <div class="checkout-section-right-top-box-left">
                    <div class="checkout-section-right-top-box-left-pic">
                        <?php
                        echo $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('full'), $cart_item, $cart_item_key);
                        ?>
                    </div>
                </div>
                <div class="checkout-section-right-top-box-right">
                    <h4 class="product-name">
                        <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)) . '&nbsp;'; ?>
                        <?php echo wc_get_formatted_cart_item_data($cart_item); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    ?>
                    </h4>

                    <div class="checkout-section-right-ticket">
                        <div class="checkout-section-right-ticket-txt">
                            <p> <span class="tick-txt">
                                    <?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <span class="product-quantity">' . sprintf($cart_item['quantity']) . '</span>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    ?>
                                    Tickets
                                </span>
                                <span class="slash-straight product-total">|</span> <span class="tick-price">
                                    <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    do_action('woocommerce_review_order_after_cart_contents');

    $point_conversion_rate = get_option("point_conversion_rate", 100);
    ?>

    <div class="your-ticket-order">
        <div class="your-ticket-order-star">

            <svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M11.0002 17.0842L4.50229 20.5001L5.74335 13.2642L0.484375 8.13999L7.74968 7.08419L11.0002 0.499947L14.2497 7.08419L21.515 8.13999L16.2571 13.2642L17.4992 20.5001L11.0002 17.0842Z"
                    fill="#EEC273" />
            </svg>
            <p>Complete your order to earn
                <span id="earn_points"><?php echo round(WC()->cart->get_subtotal() * $point_conversion_rate); ?></span>
                points
            </p>
        </div>
    </div>
    <div class="your-ticket-totals order-total d-none">
        <div class="your-ticket-total">
            <p><?php esc_html_e('Subtotal', 'woocommerce'); ?></p>
        </div>
        <div class="your-ticket-rate">
            <?php wc_cart_totals_subtotal_html(); ?>
        </div>

    </div>
    <!-- </tbody>
    <tfoot>

        <tr class="cart-subtotal">
            <th>
                <?php //esc_html_e('Subtotal', 'woocommerce'); ?>
            </th>
            <td>
                <?php //wc_cart_totals_subtotal_html(); ?>
            </td>
        </tr> -->

    <?php foreach (WC()->cart->get_coupons() as $code => $coupon): ?>
        <div class="your-ticket-totals cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
            <div class="your-ticket-total">
                <p><?php wc_cart_totals_coupon_label($coupon); ?></p>
            </div>
            <div class="your-ticket-rate">
                <?php wc_cart_totals_coupon_html($coupon); ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()): ?>

        <?php do_action('woocommerce_review_order_before_shipping'); ?>

        <?php // wc_cart_totals_shipping_html(); ?>

        <?php do_action('woocommerce_review_order_after_shipping'); ?>

    <?php endif; ?>

    <?php foreach (WC()->cart->get_fees() as $fee): ?>
        <tr class="fee">
            <th>
                <?php echo esc_html($fee->name); ?>
            </th>
            <td>
                <?php wc_cart_totals_fee_html($fee); ?>
            </td>
        </tr>
    <?php endforeach; ?>

    <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()): ?>
        <?php if ('itemized' === get_option('woocommerce_tax_total_display')): ?>
            <?php foreach (WC()->cart->get_tax_totals() as $code => $tax): // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited    ?>
                <tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?>">
                    <th>
                        <?php echo esc_html($tax->label); ?>
                    </th>
                    <td>
                        <?php echo wp_kses_post($tax->formatted_amount); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr class="tax-total">
                <th>
                    <?php echo esc_html(WC()->countries->tax_or_vat()); ?>
                </th>
                <td>
                    <?php wc_cart_totals_taxes_total_html(); ?>
                </td>
            </tr>
        <?php endif; ?>
    <?php endif; ?>

    <?php do_action('woocommerce_review_order_before_order_total'); ?>

    <!-- <tr class="order-total">
            <th>
                <?php esc_html_e('Total', 'woocommerce'); ?>
            </th>
            <td>
                <?php wc_cart_totals_order_total_html(); ?>
            </td>
        </tr> -->
    <div class="your-ticket-totals order-total">
        <div class="your-ticket-total">
            <p>
                <?php esc_html_e('Total', 'woocommerce'); ?>
            </p>
        </div>
        <div class="your-ticket-rate">
            <?php wc_cart_totals_order_total_html(); ?>
        </div>

    </div>

    <?php do_action('woocommerce_review_order_after_order_total'); ?>

    <!-- </tfoot> -->
</div>