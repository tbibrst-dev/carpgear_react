<?php if ( isset( $_GET['message'] ) ) : ?>
<div class="updated notice">
	<p>
		<?php echo esc_html( $_GET['message'] ); ?>
	</p>
</div>
<?php endif; ?>
<table class="form-table">
	<tr>
		<th scope="row">
		</th>
		<td>
			<a href="<?php echo admin_url( 'admin.php?page=flexible-printing-settings&tab=printers&refresh=1' ); ?>" class="button"><?php _e( 'Refresh printers', 'flexible-printing' ); ?></a>
		</td>
	</tr>
</table>
