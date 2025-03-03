<?php

namespace DpdUKVendor;

/**
 * Settings sidebar.
 *
 * @var string $title
 * @var array  $features
 * @var string $url
 * @var string $label
 * @var int $min_width
 * @var int $position_right
 * @var string $align_top_to_element
 */
?>
<div class="oct-metabox" style="display: none;">
	<h3 class="oct-metabox-title"><?php 
echo \esc_html($title);
?></h3>
	<ul>
		<?php 
foreach ($features as $feature) {
    ?>
			<li><?php 
    echo \esc_html($feature);
    ?></li>
		<?php 
}
?>
	</ul>
	<div>
		<a class="oct-metabox-btn" href="<?php 
echo \esc_url($url);
?>" target="_blank"><?php 
echo \esc_html($label);
?></a>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		const oct_metabox = jQuery('.oct-metabox');
		const min_width = <?php 
echo \esc_attr($min_width);
?>;
		const position_right = <?php 
echo \esc_attr($position_right);
?>;
		const align_top_to_element = '<?php 
echo \esc_attr($align_top_to_element);
?>';

		function show_oct_metabox() {
			oct_metabox.addClass( 'fixed' )
				.css( 'top', jQuery( align_top_to_element ).position().top + 20 )
				.css( 'right', position_right )
				.toggle( window.innerWidth > min_width );
		}

		setTimeout(	show_oct_metabox, 1000 );

		jQuery( window ).on( 'resize', function() {
			show_oct_metabox();
		});
	});
</script>
<?php 
