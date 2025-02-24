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

				<?php if ($checkout->get_checkout_fields()): ?>
					<!-- basket-section-start -->

					<div class="checkout-section-left">

						<div class="checkout-section-login  mob-log-section-show" id="mobile_login">
							<form action="">
								<div class="checkout-details-head">
									<h4>LOGIN</h4>
								</div>
								<div class="checkout-details-filed">
									<div class="name-area">
										<div class="date-fields">
											<input placeholder="Username / Email">
										</div>
										<div class="date-fields">
											<input type="password" placeholder="Password">
										</div>
									</div>
								</div>


								<div class="log-on">
									<div class="form-group">
										<input type="checkbox" id="logg">
										<label for="logg">
											<p> Remember me</p>
										</label>
									</div>
								</div>
								<div class="forgot-password">
									<a href="#">Forgot Password?</a>
								</div>

								<div class="log-on-login">
									<button type="button" class="log-on-login-btn">
										Login
									</button>
								</div>
							</form>
						</div>

						<div class="checkout-section-login mob-log-section-hide" id="desktop_login" style="display:none;">
							<form action="">
								<div class="checkout-details-head">
									<h4>LOGIN</h4>
								</div>
								<div class="checkout-details-filed">
									<div class="name-area">
										<div class="date-fields">
											<input placeholder="Username / Email">
										</div>
										<div class="date-fields">
											<input type="password" placeholder="Password">
										</div>
									</div>
								</div>


								<div class="log-on">
									<div class="form-group">
										<input type="checkbox" id="log">
										<label for="log">
											<p> Remember me</p>
										</label>
									</div>
								</div>
								<div class="forgot-password">
									<a href="#">Forgot Password?</a>
								</div>

								<div class="log-on-login">
									<button type="button" class="log-on-login-btn">
										Login
									</button>
								</div>
							</form>
						</div>


						<div class="checkout-entry-question">
							<h4>ENTRY QUESTION</h4>
							<p>Answer this question correctly to be entered into the live draw</p>
							<div class="checkout-use-boat">
								<p>Why use a bait boat?</p>
								<div class="check-bait">
									<form>
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
									</form>
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

							<div class="checkout-section-signup">
								<div class="carp-login-check-it">
									<div class="carp-login-check-one">
										<div class="form-group">
											<input type="radio" id="mail">
											<label for="mail">
												<p> Sign me up to receive email updates and news</p>
											</label>
										</div>
									</div>
									<div class="carp-login-check-one">
										<div class="form-group">
											<input type="radio" id="sms">
											<label for="sms">
												<p>Sign me up to receive SMS updates and news</p>
											</label>
										</div>
									</div>
								</div>
							</div>
							<div class="checkout-section-bottom">
								<p>By checking this box and entering your phone number above, you consent to receive
									marketing text messages from Carp Gear Giveaways at the number provided. Consent is
									not a condition of any purchase. View our Privacy Policy and Terms of Service for
									more information.</p>
							</div>
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