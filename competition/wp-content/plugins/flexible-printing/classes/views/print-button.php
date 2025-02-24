<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<button
	id="<?php echo esc_attr( $args['id'] ); ?>"
	data-integration="<?php echo esc_attr( $integration ); ?>"
	data-security="<?php echo wp_create_nonce( 'flexible-printing-' . $integration ); ?>"
	data-tip="<?php echo esc_attr( $tip ); ?>"
	class="button tips flexible-printing-button-print <?php echo $class; ?>"
	title="<?php echo esc_attr( $title ); ?>"
	<?php if ( isset( $args['data'] ) && is_array( $args['data'] ) ) : ?>
		<?php foreach ( $args['data'] as $key => $data ) : ?>
			data-<?php echo $key; ?>="<?php echo esc_attr( $data ); ?>"
		<?php endforeach; ?>
	<?php endif; ?>
>
	<?php if ( $icon ) : ?>
		<i class="flexible-printing-print" aria-hidden="true"></i>
	<?php endif; ?>
	<?php echo $label; ?>
</button>
