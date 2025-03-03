<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
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
				src="<?php echo S3_UPLOADS_BASEURL_THEME . '/images/check_1.png'; ?>" alt="Logo">

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
				style="text-align: center; margin: 10px 0 10px; color: #FFFFFF; font-size: 16px;font-weight: 700;line-height: 24px;">
				<?php echo $order->get_order_number(); ?>
			</h4>
			<p
				style="text-transform: uppercase;text-align: center; margin: 0; font-size: 12px;font-weight: 400;line-height: 24px;color:#FFFFFF;opacity: 50%;">
				Order Number</p>
		</div>
		<div
			style=" flex: 1 1 calc(33.333% - 20px); background-color: #202323; padding: 15px; box-sizing: border-box; overflow: hidden; border: 1px solid #FFFFFF1A;">
			<h4
				style="text-align: center; margin: 10px 0 10px; color: #FFFFFF; font-size: 16px;font-weight: 700;line-height: 24px;">
				<?php echo wc_format_datetime($order->get_date_created(), 'd.m.y'); ?>
			</h4>
			<p
				style="text-transform: uppercase;text-align: center; margin: 0; font-size: 12px;font-weight: 400;line-height: 24px;color:#FFFFFF;opacity: 50%;">
				Order Date </p>
		</div>
		<div
			style=" flex: 1 1 calc(33.333% - 20px); background-color: #202323; padding: 15px; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px; box-sizing: border-box; overflow: hidden; border: 1px solid #FFFFFF1A;">
			<h4 class="email_heading"
				style="text-align: center; margin: 10px 0 10px; color: #FFFFFF; font-size: 16px;font-weight: 700;line-height: 24px;">
				<a
					style="text-decoration: none; display: block; color: #FFFFFF; font-size: 16px;font-weight: 700;line-height: 24px;"><?php echo $order->get_billing_email(); ?></a>
			</h4>
			<p
				style="text-transform: uppercase;text-align: center; margin: 0; font-size: 12px;font-weight: 400;line-height: 24px;color:#FFFFFF;opacity: 50%;">
				Email</p>
		</div>

		<?php

		global $wpdb;

		$orderItems = $order->get_items();

		$order_user_id = $order->get_user_id();

		$totalItems = count($orderItems);

		$index = 0;

		error_log("email send for reciet orderItems". print_r( $orderItems ,true));
        error_log("email send for reciet order_user_id". print_r( $order_user_id ,true));
        error_log("email send for reciet order". print_r($order ,true));

		foreach ($orderItems as $lineItem) {
			$index++;
			$comp_tickets = [];
			error_log("email send for reciet lineItem". print_r($lineItem ,true));

			// $query = $wpdb->prepare(
			// 	"SELECT {$wpdb->prefix}competition_tickets.ticket_number, {$wpdb->prefix}competitions.* FROM {$wpdb->prefix}competition_tickets 
            //     INNER JOIN {$wpdb->prefix}competitions on {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_tickets.competition_id 
            //     WHERE {$wpdb->prefix}competition_tickets.user_id = %d and {$wpdb->prefix}competition_tickets.order_id = %d
            //     and {$wpdb->prefix}competitions.competition_product_id = %d and {$wpdb->prefix}competition_tickets.is_purchased = 1",
			// 	$order_user_id,
			// 	$order->id,
			// 	$lineItem['product_id']
			// );

			// $query = $wpdb->prepare(
			// 	"SELECT {$wpdb->prefix}competition_tickets.ticket_number, {$wpdb->prefix}competitions.* FROM {$wpdb->prefix}competition_tickets 
            //     INNER JOIN {$wpdb->prefix}competitions on {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_tickets.competition_id 
            //     WHERE {$wpdb->prefix}competition_tickets.user_id = %d and {$wpdb->prefix}competition_tickets.order_id = %d
            //     and {$wpdb->prefix}competitions.competition_product_id = %d and {$wpdb->prefix}competition_tickets.is_purchased = 1",
			// 	$order_user_id,
			// 	$order->get_id(),
			// 	$lineItem['product_id']
			// );
			// Prepare the query
			$query = $wpdb->prepare(
				"SELECT {$wpdb->prefix}competition_tickets.ticket_number, {$wpdb->prefix}competitions.* 
				FROM {$wpdb->prefix}competition_tickets 
				INNER JOIN {$wpdb->prefix}competitions 
				ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_tickets.competition_id 
				WHERE {$wpdb->prefix}competition_tickets.user_id = %d 
				AND {$wpdb->prefix}competition_tickets.order_id = %d 
				AND {$wpdb->prefix}competitions.competition_product_id = %d 
				AND {$wpdb->prefix}competition_tickets.is_purchased = 1",
				$order_user_id,
				$order->get_id(),
				$lineItem['product_id']
			);

			$records = $wpdb->get_results($query, ARRAY_A);
			
			error_log("email send for reciet query". print_r( $query ,true));
            error_log("email send for reciet records". print_r( $records ,true));
            error_log("email send fe']". print_r( $lineItem['name'] ,true));

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
				style="text-transform: uppercase;text-align: center; margin: 0; font-size: 12px;font-weight: 400;line-height: 24px;color:#FFFFFF;opacity: 50%;">
				Competition</p>
			<h4 style="font-family: \'Roboto\', sans-serif;font-size: 15px;font-weight: 700;line-height: 18px;text-align: center; color: #FFFFFF; margin: 10px 0 10px;">' . (!empty($record['title']) ? $record['title'] : $lineItem['name']) . '</h4>
			<p
				style="font-family:  \'Roboto\', sans-serif;opacity: 75%;font-size: 12px;font-weight: 100;line-height: 18px;text-align: center; margin: 0; font-size: 14px;color: #FFFFFF !important;">
				' . implode(", ", $comp_tickets) . '</p>
			</div>';
		}



		?>
	</div>
</div>