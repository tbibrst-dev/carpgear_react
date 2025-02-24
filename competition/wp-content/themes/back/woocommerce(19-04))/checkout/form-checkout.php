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

?>

<?php

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
	echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
	return;
}

?>

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

						<div class="checkout-entry-question">
							<h4>ENTRY QUESTION</h4>
							<p>Answer this question correctly to be entered into the live draw</p>
							<div class="checkout-use-boat">
								<p>Why use a bait boat?</p>
								<div class="check-bait">

									<div class="check-bait-all">
										<div class="form-group check-bait-one">
											<input type="radio" id="why-one" name="bait">
											<label for="why-one">To send out bait</label>
										</div>
										<div class="form-group check-bait-one">
											<input type="radio" id="why-two" name="bait">
											<label for="why-two">To send out Poop</label>
										</div>
										<div class="form-group check-bait-one">
											<input type="radio" id="why-three" name="bait">
											<label for="why-three">To send out Beers</label>
										</div>
									</div>

								</div>
							</div>
						</div>



						<div class="checkout-section-details">
							<?php do_action('woocommerce_checkout_before_customer_details'); ?>

							<div class="" id="customer_details"><!-- col2-set -->
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
<?php do_action('woocommerce_after_checkout_form', $checkout); ?>