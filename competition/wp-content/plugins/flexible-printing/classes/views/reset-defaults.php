<?php if ( isset( $_GET['message'] ) && empty( $_GET['settings-updated'] ) ) : ?>
<div class="updated notice fade">
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
			<a href="<?php echo admin_url( 'admin.php?page=flexible-printing-settings&tab=printers&reset=1&section=' . $section ); ?>"
			   class="button"><?php _e( 'Reset default settings', 'flexible-printing' ); ?></a>
		</td>
	</tr>
</table>
