<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}
$coupon_class = "";
$userAgent = $_SERVER['HTTP_USER_AGENT'];
if (stripos($userAgent, 'Mac') !== false) {
    $coupon_class = "mac_input";
}
?>

<?php

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

$com_queries = WC()->session->get('check_comp_queries', []);

?>
<div class="container">
    <div class="comp_checkout_notice"></div>

    <form name="checkout" method="post" class="checkout woocommerce-checkout"
        action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">

        <div class="checkout-section">
            <div class="container">
                <div class="checkout-section-all">
                    <div class="checkout-section-left">

                        <?php
                        if (is_user_logged_in() || 'no' === get_option('woocommerce_enable_checkout_login_reminder')) {
                        } else {
                            woocommerce_login_form(
                                array(
                                    'message' => '',//esc_html__('If you have shopped with us before, please enter your details below. If you are a new customer, please proceed to the Billing section.', 'woocommerce'),
                                    'redirect' => wc_get_checkout_url(),
                                    'hidden' => true,
                                )
                            );
                        }
                        ?>

                        <?php if ($checkout->get_checkout_fields()): ?>

                            <?php

                            if (!empty($com_queries)) {
                                echo "<input type='hidden' name='comp_queries' value='" . json_encode($com_queries) . "' />";
                            }

                            global $wpdb;

                            $globalSettings = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);

                            $orderProducts = [];

                            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

                                $orderProducts[] = $cart_item['product_id'];

                            }

                            $productIds = implode(',', array_map('intval', $orderProducts));

                            $query = "SELECT * FROM {$wpdb->prefix}competitions WHERE competition_product_id IN ($productIds) AND comp_question = 1";

                            $com_result = $wpdb->get_results($query);

                            $question = $question_id = "";

                            $ques_options = [];

                            if (!empty($com_result)) {

                                if (count($com_result) == count($orderProducts) && count($com_queries) == count($orderProducts)) {

                                    //Do not show question
                        
                                } else {

                                    $query = "SELECT * FROM {$wpdb->prefix}global_questions where type = 'global' and enabled=1 ORDER BY RAND() LIMIT 1";

                                    $global_ques_result = $wpdb->get_row($query);

                                    $question = $global_ques_result->question;

                                    $ques_options = $global_ques_result->options;

                                    $question_id = $global_ques_result->id;

                                }

                            } else {

                                $query = "SELECT * FROM {$wpdb->prefix}global_questions where type = 'global' and enabled=1 ORDER BY RAND() LIMIT 1";

                                $global_ques_result = $wpdb->get_row($query);

                                $question = $global_ques_result->question;

                                $ques_options = $global_ques_result->options;

                                $question_id = $global_ques_result->id;

                            }

                            if (!empty($ques_options))
                                $ques_options = json_decode($ques_options, true);

                            if (!empty($question) && $globalSettings['show_question'] == 1) {
                                ?>
                                <div class="checkout-entry-question">
                                    <h4>ENTRY QUESTION</h4>
                                    <p>Answer this question correctly to be entered into the live draw</p>
                                    <div class="checkout-use-boat">
                                        <p><?php echo $question; ?></p>
                                        <input type="hidden" name="comp_question" value="<?php echo $question_id; ?>" />
                                        <div class="check-bait">

                                            <div class="check-bait-all">
                                                <?php foreach ($ques_options as $option_key => $option) { ?>
                                                    <div class="form-group check-bait-one"
                                                        id="<?php echo $option_key . "_options"; ?>">
                                                        <input type="radio" id="<?php echo $option_key; ?>" name="comp_quest_answer"
                                                            value="<?php echo $option; ?>">
                                                        <label for="<?php echo $option_key; ?>"><?php echo $option; ?></label>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php } ?>


                            <div class="checkout-section-details">
                                <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                                <div class="" id="customer_details">
                                    <!-- col2-set -->
                                    <?php do_action('woocommerce_checkout_billing'); ?>

                                    <div class="d-none">
                                        <?php do_action('woocommerce_checkout_shipping'); ?>
                                    </div>
                                </div>

                                <?php do_action('woocommerce_checkout_after_customer_details'); ?>

                            </div>
                        </div>

                    <?php endif; ?>

                    <div class="checkout-section-right">

                        <?php if (wc_coupons_enabled()) { ?>


                            <div class="checkout-section-right-coupon">

                                <div class="checkout-section-right-head-coupon">
                                    <h4 id="order_review_heading">
                                        <?php esc_html_e('COUPON', 'woocommerce'); ?>
                                    </h4>
                                </div>

                                <div class="checkout-coupon-section">

                                    <div class="name-area">
                                        <div class="coupon-field">
                                            <input type="text" name="coupon_code"
                                                class="input-text <?php echo $coupon_class; ?>"
                                                placeholder="<?php esc_attr_e('Enter a coupon code', 'woocommerce'); ?>"
                                                id="custom_coupon_code" value="" />
                                        </div>
                                        <div class="btn-field">
                                            <button type="button" id="custom_wc_coupon"
                                                class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?> coupon-btn"
                                                name="apply_coupon_custom"
                                                value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_html_e('Apply', 'woocommerce'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                        <div class="checkout-section-right-top">

                            <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

                            <div class="checkout-section-right-head">
                                <h4 id="order_review_heading">
                                    <?php esc_html_e('Your Tickets', 'woocommerce'); ?>
                                </h4>
                            </div>

                            <?php do_action('woocommerce_checkout_before_order_review'); ?>

                            <div id="order_review" class="woocommerce-checkout-review-order">
                                <?php do_action('woocommerce_checkout_order_review'); ?>
                            </div>

                            <?php do_action('woocommerce_checkout_after_order_review'); ?>
                        </div>
                    </div>


                </div>
            </div>
        </div>

    </form>
</div>
<?php do_action('woocommerce_after_checkout_form', $checkout); ?>