<table class="form-table">
	<tr>
		<th scope="row">
			<?php _e( 'Status', 'flexible-printing' ); ?>
		</th>
		<td>
			<?php if ( $this->access_token == '' ) : ?>
				<?php _e( 'Not authenticated', 'flexible-printing' ); ?>
			<?php else : ?>
				<?php _e( 'Authenticated', 'flexible-printing' ); ?>
			<?php endif; ?>
			<br/>
		</td>
	</tr>
	<tr>
		<th scope="row">
		</th>
		<td>
			<?php if ( $this->access_token == '' ) : ?>
				<button id="fp_authenticate" class="button button-primary"><?php _e( 'Save and Authenticate', 'flexible-printing' ); ?></button>
			<?php else : ?>
				<a class="button button-primary" href="<?php echo admin_url( '?flexible-printing=revoke' ); ?>"><?php _e( 'Revoke', 'flexible-printing' ); ?></a>
			<?php endif; ?>
			<br/>
		</td>
</table>
<script type="text/javascript">
	<?php if ( $this->access_token == '' ) : ?>
    jQuery(document).ready(function(){
        jQuery('#submit').parent().hide();
    });
	<?php endif; ?>
</script>
