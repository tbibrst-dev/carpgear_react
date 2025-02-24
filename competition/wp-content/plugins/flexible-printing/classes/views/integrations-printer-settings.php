<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php if ( isset( $_GET['message'] ) && empty( $_GET['settings-updated'] ) ) : ?>
<div class="updated notice fade">
	<p>
		<?php echo esc_html( $_GET['message'] ); ?>
	</p>
</div>
<?php endif; ?>

<table class="form-table form-table-reset">
	<tr>
		<th scope="row">
		</th>
		<td>
			<a href="<?php echo admin_url( 'admin.php?page=flexible-printing-settings&tab=integrations&reset=1&section=' . $section ); ?>"
			   class="button"><?php _e( 'Reset default settings', 'flexible-printing' ); ?></a>
		</td>
	</tr>
</table>
<script type="text/javascript">
    jQuery(document).ready(function(){
        function select_printer() {
            jQuery('.printer-setting').hide();
            var printer = jQuery('.integration-printer select').val();
            jQuery('.' + printer).show();
            if ( printer == '-1' || jQuery('.printer-no-setting.' + printer).length ) {
                jQuery('.form-table-reset').hide();
            }
            else {
                jQuery('.form-table-reset').show();
            }
        }
        jQuery('.integration-printer select').change(function(){
            select_printer();
        })
        select_printer();
    })
</script>
