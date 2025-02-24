<?php
/**
 * Login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woo.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     7.0.1
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (is_user_logged_in()) {
	return;
}

?>
<div class="checkout-section-login mob-log-section-hide" id="desktop_login" style="display:none;">

	<div class="woocommerce-form woocommerce-form-login loginn" method="post" <?php echo ($hidden) ? 'style="display:none;"' : ''; ?>>

		<?php do_action('woocommerce_login_form_start'); ?>

		<?php echo ($message) ? wpautop(wptexturize($message)) : ''; // @codingStandardsIgnoreLine ?>

		<div class="checkout-details-head">
			<h4>LOGIN</h4>
		</div>

		<div class="checkout-details-filed">
			<div class="name-area">
				<div class="date-fields">
					<input type="text" class="input-textt" name="username" id="username" autocomplete="username"
						placeholder="<?php esc_html_e('Username / Email', 'woocommerce'); ?> *" />
				</div>
				<div class="date-fields">
					<input class="input-textt woocommerce-Inputt" type="password" name="password" id="password"
						autocomplete="current-password"
						placeholder="<?php esc_html_e('Password', 'woocommerce'); ?> *" />
				</div>
			</div>
			<?php do_action('woocommerce_login_form'); ?>
		</div>
		<div class="log-on">
			<div class="form-group">
				<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme"
					type="checkbox" id="rememberme" value="forever" />
				<label for="rememberme"
					class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__remembermee">
					<span><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
				</label>
				<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
				<input type="hidden" name="redirect" value="<?php echo esc_url($redirect); ?>" />
			</div>
		</div>
		<div class="forgot-password lost_password">
			<a
				href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Forgot password?', 'woocommerce'); ?></a>
		</div>

		<div class="log-on-login">
			<button type="submit"
				class="cgg_login log-on-login-btn woocommerce-button button woocommerce-form-login__submitt<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
				name="login"
				value="<?php esc_attr_e('Login', 'woocommerce'); ?>"><?php esc_html_e('Login', 'woocommerce'); ?></button>
		</div>

		<?php do_action('woocommerce_login_form_end'); ?>

	</div>
</div>