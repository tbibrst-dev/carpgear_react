<?php
/**
 * Header template
 *
 * This file contains the header content.
 *
 * @package MyThemeChild
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title(); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
        <!-- Your site's logo, navigation menu, etc. -->
        <?php
            // Include WooCommerce header
            if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
                get_template_part( 'woocommerce/header' );
            } else {
                // Your default header content
            }
        ?>
    </header>
