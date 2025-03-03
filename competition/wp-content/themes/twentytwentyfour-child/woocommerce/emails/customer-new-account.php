<?php
/**
 * Customer new account email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-new-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 6.0.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_email_header', $email_heading, $email); ?>


<?php

if (filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
	$user_login_link = '<a style="color:#fff;" href="mailto:' . esc_attr($user_login) . '" target="_blank">' . esc_html($user_login) . '</a>';
} else {
	$user_login_link = esc_html($user_login);
}

?>
<p><?php printf(esc_html__("Hi %s,", 'woocommerce'), $user_login_link); ?></p>
<p>
	<?php printf(
		esc_html__('Thanks for creating an account on %1$s. Your username is %2$s. You can access your account area to view orders, change your password, and more at: %3$s', 'woocommerce'),
		esc_html($blogname),
		'<strong>' . $user_login_link . '</strong>',
		make_clickable(esc_url(FRONTEND_URL.'account/details'))
	);
	?>
</p>

<?php if ('yes' === get_option('woocommerce_registration_generate_password') && $password_generated && $set_password_url): ?>
	<?php // If the password has not been set by the user during the sign up process, send them a link to set a new password ?>
	<p><a
			href="<?php echo esc_attr($set_password_url); ?>"><?php printf(esc_html__('Click here to set your new password.', 'woocommerce')); ?></a>
	</p>
<?php endif; ?>
<p style="margin:0px;padding:0px;"></p>
<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ($additional_content) {
	echo wp_kses_post(wpautop(wptexturize($additional_content)));
}

//do_action( 'woocommerce_email_footer', $email );
