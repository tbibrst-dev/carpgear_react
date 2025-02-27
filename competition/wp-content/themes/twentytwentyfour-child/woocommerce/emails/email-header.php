<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 7.4.0
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title>Carp Gear Giveaways</title>
</head>

<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0"
	offset="0"
	style="padding: 36px 16px 60px 16px; font-family: 'Roboto', sans-serif; background-color: #0F1010;font-weight: 900;font-style: normal;">
	<table width="100%" id="outer_wrapperr">
		<tr>
			<td><!-- Deliberately empty to support consistent sizing and layout across multiple email clients. --></td>
			<td width="100%">
				<div id="wrapperr" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
					<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
						<tr>
							<td align="center" valign="top">
								<div id="template_header_image">
									<?php
									$img = get_option('woocommerce_email_header_image');

									if ($img) {
										echo '<p style="margin:0;"><img src="' . esc_url($img) . '" alt="' . esc_attr(get_bloginfo('name', 'display')) . '" width="132px" height="45px" /></p>';
									}
									?>
								</div>
								<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container">

									<tr>
										<td align="center" valign="top">
											<!-- Body -->
											<table border="0" cellpadding="0" cellspacing="0" width="100%"
												id="template_body">
												<tr>
													<td valign="top" id="body_contentt">
														<!-- Content -->
														<table border="0" cellpadding="20" cellspacing="0" width="100%">
															<tr>
																<td valign="top">
																	<div id="body_content_inner"
																		style="font-family: 'Roboto', sans-serif;">