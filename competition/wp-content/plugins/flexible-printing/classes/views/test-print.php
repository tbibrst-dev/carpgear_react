<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<table class="form-table">
	<tr>
		<th scope="row">
		</th>
		<td>
			<p>
				<?php echo apply_filters( 'flexible_printing_print_button', '', 'fp',
					array(
						'content' => 'print',
						'id'      => 'test_print',
						'icon'    => true,
						'tip'     => __( 'Print test page', 'flexible-printing' ),
						'label'   => __( 'Print test page', 'flexible-printing' ),
						'data'    => array(
							'id'      => 'test_print',
							'section' => $section,
						)
					)
				); ?>
			</p>
		</td>
	</tr>
</table>
