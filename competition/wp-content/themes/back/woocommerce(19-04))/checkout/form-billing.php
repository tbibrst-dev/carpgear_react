<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined('ABSPATH') || exit;
?>
<div class="woocommerce-billing-fields">
	<div class="detail-sec">
		<?php if (wc_ship_to_billing_address_only() && WC()->cart->needs_shipping()): ?>

			<h3>
				<?php esc_html_e('Billing &amp; Shipping', 'woocommerce'); ?>
			</h3>

		<?php else: ?>

			<!-- <h3><?php esc_html_e('Billing details', 'woocommerce'); ?></h3> -->

			<div class="checkout-details-head">
				<h4>Details</h4>
			</div>

		<?php endif; ?>

		<?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

		<div class="checkout-details-fileds woocommerce-billing-fields__field-wrapper">
			<?php
			$fields = $checkout->get_checkout_fields('billing');

			woocommerce_form_field('billing_first_name', $fields['billing_first_name'], $checkout->get_value('billing_first_name'));
			woocommerce_form_field('billing_last_name', $fields['billing_last_name'], $checkout->get_value('billing_last_name'));
			woocommerce_form_field('billing_dob', $fields['billing_dob'], $checkout->get_value('billing_dob'));
			woocommerce_form_field('billing_phone', $fields['billing_phone'], $checkout->get_value('billing_phone'));
			woocommerce_form_field('billing_email', $fields['billing_email'], $checkout->get_value('billing_email'));
			?>
		</div>
	</div>
	<?php if (!is_user_logged_in()) { ?>
	<div class="Account-sec">
		<div class="checkout-details-head-acc">
			<h4>ACCOUNT</h4>
		</div>
		<div class="checkout-details-fileds">
			<div class="name-area">
				<div class="date-field">
					<input placeholder="Email*" name="new_user_email" id="new_user_email">
				</div>
				<div class="date-field">
					<input type="password" placeholder="Create a password*" name="new_user_password" id="new_user_password">
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	<div class="billing-sec">
		<div class="checkout-details-head-bill">
			<h4>BILLING ADDRESS</h4>
		</div>
		<div class="checkout-details-fileds woocommerce-billing-fields__field-wrapper">
			<?php
			
			$billing_fields = ['billing_address_1', 'billing_address_2', 'billing_city','billing_postcode'];

			$ignoreFields = ['billing_first_name', 'billing_last_name', 'billing_dob', 'billing_phone', 'billing_email'];

			foreach ($fields as $key => $field) {

				if(in_array($key, $ignoreFields)){
					continue;
				}

				if(!in_array($key, $billing_fields)){

					$field['class'][] = 'd-none';

				} else {

					//print_r($field);
				}

				woocommerce_form_field($key, $field, $checkout->get_value($key));
			}
			?>
		</div>

		<?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
	</div>
</div>

<?php if (!is_user_logged_in() && $checkout->is_registration_enabled()): ?>
	<div class="woocommerce-account-fields">
		<?php if (!$checkout->is_registration_required()): ?>

			<p class="form-row form-row-wide create-account d-none">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount"
						<?php checked((true === $checkout->get_value('createaccount') || (true === apply_filters('woocommerce_create_account_default_checked', false))), true); ?> type="checkbox"
						name="createaccount" value="1" /> <span>
						<?php esc_html_e('Create an account?', 'woocommerce'); ?>
					</span>
				</label>
			</p>

			<div class="checkout-section-signup">
				<div class="carp-login-check-it">
					<div class="carp-login-check-one">
						<div class="form-group">
							<input type="radio" id="mail" name="receive_email_updates">
							<label for="mail">
								<p> Sign me up to receive email updates and news</p>
							</label>
						</div>
					</div>
					<div class="carp-login-check-one">
						<div class="form-group">
							<input type="radio" id="sms" name="receive_sms_updates">
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

		<?php endif; ?>

		<?php do_action('woocommerce_before_checkout_registration_form', $checkout); ?>

		<?php if ($checkout->get_checkout_fields('account')): ?>

			<div class="create-account">
				<?php foreach ($checkout->get_checkout_fields('account') as $key => $field): ?>
					<?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action('woocommerce_after_checkout_registration_form', $checkout); ?>
	</div>
<?php endif; ?>