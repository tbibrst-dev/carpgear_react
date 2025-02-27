<?php
/**
 * Customer on-hold order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-on-hold-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 7.3.0
 */

defined('ABSPATH') || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
$email_heading = "";

do_action('woocommerce_email_header', $email_heading, $email); ?>
<style>
    .email_heading a {
        color: #fff;
        text-decoration: none;
    }
</style>
<div style="width: 100%; max-width: 450px; margin: auto;">
    <div style="display: block; gap: 24px;">
        <div style="text-align: center;">
            <img style="margin-bottom:15px;"
                src="<?php echo bloginfo('stylesheet_directory') . '/images/check_1.png'; ?>" alt="Logo">

            <h2
                style="text-align: center; color: white !important;font-family: 'Roboto', sans-serif;font-size: 18px;font-weight: 900;line-height: 19.8px;">
                YOU’RE IN IT TO WIN IT</h2>
            <p
                style="text-align: center; color: white !important; margin-top: -15px !important;font-family: 'Roboto', sans-serif;font-size: 16px;font-weight: 300;line-height: 22.35px;">
                Your entry details are
                listed
                below:</p>
        </div>

        <div
            style=" flex: 1 1 calc(33.333% - 20px); background-color: #202323; padding: 15px; border-top-left-radius: 8px; border-top-right-radius: 8px; box-sizing: border-box; overflow: hidden; border: 1px solid #FFFFFF1A;">
            <h4
                style="text-align: center; color: white !important; margin: 10px 0 10px; font-size: 15px;font-weight: bold;">
                <?php echo $order->get_order_number(); ?>
            </h4>
            <p
                style="text-transform: uppercase;text-align: center; margin: 0; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #8f9191 !important;">
                Order Number</p>
        </div>
        <div
            style=" flex: 1 1 calc(33.333% - 20px); background-color: #202323; padding: 15px; box-sizing: border-box; overflow: hidden; border: 1px solid #FFFFFF1A;font-weight: bold;">
            <h4 style="text-align: center; color: white !important; margin: 10px 0 10px; font-size: 18px;">
                <?php echo wc_format_datetime($order->get_date_created(), 'd.m.y'); ?>
            </h4>
            <p
                style="text-transform: uppercase;text-align: center; margin: 0; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #8f9191 !important;">
                Order Date</p>
        </div>
        <div
            style=" flex: 1 1 calc(33.333% - 20px); background-color: #202323; padding: 15px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; box-sizing: border-box; overflow: hidden; border: 1px solid #FFFFFF1A;">
            <h4 class="email_heading"
                style="text-align: center; color: white !important; margin: 10px 0 10px; font-size: 18px;">
                <a
                    style="text-decoration: none; display: block; color: white !important; font-weight: bold;"><?php echo $order->get_billing_email(); ?></a>
            </h4>
            <p
                style="text-transform: uppercase;text-align: center; margin: 0; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #8f9191 !important;">
                Email</p>
        </div>

        <?php

        global $wpdb;

        $orderItems = $order->get_items();

        $order_user_id = $order->get_user_id();

        $totalItems = count($orderItems);

        $index = 0;

        foreach ($orderItems as $lineItem) {
            $index++;
            $comp_tickets = [];

            $query = $wpdb->prepare(
                "SELECT {$wpdb->prefix}competition_tickets.ticket_number, {$wpdb->prefix}competitions.* FROM {$wpdb->prefix}competition_tickets 
                INNER JOIN {$wpdb->prefix}competitions on {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_tickets.competition_id 
                WHERE {$wpdb->prefix}competition_tickets.user_id = %d and {$wpdb->prefix}competition_tickets.order_id = %d
                and {$wpdb->prefix}competitions.competition_product_id = %d and {$wpdb->prefix}competition_tickets.is_purchased = 1",
                $order_user_id,
                $order->id,
                $lineItem['product_id']
            );

            $records = $wpdb->get_results($query, ARRAY_A);

            foreach ($records as $record) {
                $comp_tickets[] = $record['ticket_number'];
            }

            $additional_styles = '';
            if ($index === 1) {
                $additional_styles .= ' border-top-left-radius: 8px; border-top-right-radius: 8px; margin-top: 15px !important;';
            }
            if ($index === $totalItems) {
                $additional_styles .= ' border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;';
            }

            echo '<div
    style="flex: 1 1 calc(33.333% - 20px); background-color: #202323; padding: 15px; box-sizing: border-box; overflow: hidden; border: 1px solid #FFFFFF1A;' . $additional_styles . '">
    <p
        style="text-transform: uppercase;text-align: center; margin: 0; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #8f9191 !important;">
        Competition</p>
    <h4 style="text-align: center; color: white !important; margin: 10px 0 10px; font-size: 18px;">' . $record['title'] . '</h4>
    <p
        style="text-align: center; margin: 0; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #8f9191 !important;">
        ' . implode(", ", $comp_tickets) . '</p>
    </div>';
        }



        ?>
    </div>
</div>

<?php /* translators: %s: Customer first name */ ?>
<p><?php //printf(esc_html__('Hi %s,', 'woocommerce'), esc_html($order->get_billing_first_name())); ?></p>
<p><?php //esc_html_e('Thanks for your order. It’s on-hold until we confirm that payment has been received.', 'woocommerce'); ?>
</p>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
//do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
//do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
//do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ($additional_content) {
    //echo wp_kses_post(wpautop(wptexturize($additional_content)));
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
//do_action('woocommerce_email_footer', $email);