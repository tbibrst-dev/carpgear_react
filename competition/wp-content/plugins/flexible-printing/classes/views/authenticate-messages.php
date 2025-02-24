<?php if ( isset( $_GET['state'] ) && isset( $_GET['message'] ) ) : ?>
	<div class="updated notice">
		<p>
			<?php echo esc_html( $_GET['message'] ); ?>
		</p>
	</div>
<?php endif; ?>
<?php if ( isset( $_GET['error'] ) ) : ?>
	<div class="error notice">
		<p>
			<?php echo esc_html( $_GET['error'] ); ?>
		</p>
	</div>
<?php endif;
