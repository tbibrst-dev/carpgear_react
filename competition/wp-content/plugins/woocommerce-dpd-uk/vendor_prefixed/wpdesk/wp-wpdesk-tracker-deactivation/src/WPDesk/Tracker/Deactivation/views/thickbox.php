<?php

namespace DpdUKVendor;

/**
 * @var $plugin_title string
 * @var $plugin_file string
 * @var $plugin_slug string
 * @var $thickbox_id string
 * @var $ajax_action string
 * @var $ajax_nonce string
 */
if (!\defined('ABSPATH')) {
    exit;
}
?><div id="<?php 
echo $thickbox_id;
?>" style="display:none;">
	<h2><?php 
echo \sprintf(\__('You are deactivating %s plugin.', 'woocommerce-dpd-uk'), $plugin_title);
?></h2>
	<div class="wpdesk_tracker_deactivate <?php 
echo $thickbox_id;
?>">
		<div class="body">
			<div class="panel" data-panel-id="confirm"><p></p></div>
			<div class="panel active" data-panel-id="reasons">
				<h4><strong><?php 
\_e('If you have a moment, please let us know why you are deactivating the plugin (anonymous feedback):', 'woocommerce-dpd-uk');
?></strong></h4>
				<ul class="reasons-list">
					<li class="reason">
						<label>
	            	        <span>
	            		        <input type="radio" name="selected-reason" value="plugin_stopped_working">
                            </span>
							<span><?php 
\_e('The plugin suddenly stopped working', 'woocommerce-dpd-uk');
?></span>
						</label>
					</li>
					<li class="reason">
						<label>
	            	        <span>
	            		        <input type="radio" name="selected-reason" value="broke_my_site">
                            </span>
							<span><?php 
\_e('The plugin broke my site', 'woocommerce-dpd-uk');
?></span>
						</label>
					</li>
					<li class="reason has-input">
						<label>
		                    <span>
		                        <input type="radio" name="selected-reason" value="found_better_plugin" data-show="found-better-plugin">
	                        </span>
							<span><?php 
\_e('I have found a better plugin', 'woocommerce-dpd-uk');
?></span>
						</label>
						<div class="found-better-plugin" class="reason-input" style="display: none">
							<input type="text" class="additional-info" name="better_plugin_name" placeholder="<?php 
\_e('What\'s the plugin\'s name?', 'woocommerce-dpd-uk');
?>">
						</div>
					</li>
					<li class="reason">
						<label>
	            	        <span>
	            		        <input type="radio" name="selected-reason" value="plugin_for_short_period">
                            </span>
						    <span><?php 
\_e('I only needed the plugin for a short period', 'woocommerce-dpd-uk');
?></span>
						</label>
					</li>
					<li class="reason">
						<label>
	            	        <span>
	            		        <input type="radio" name="selected-reason" value="no_longer_need">
                            </span>
							<span><?php 
\_e('I no longer need the plugin', 'woocommerce-dpd-uk');
?></span>
						</label>
					</li>
					<li class="reason">
						<label>
    	            	    <span>
	                		    <input type="radio" name="selected-reason" value="temporary_deactivation">
                            </span>
                            <span><?php 
\_e('It\'s a temporary deactivation. I\'m just debugging an issue.', 'woocommerce-dpd-uk');
?></span>
						</label>
					</li>
					<li class="reason has-input">
						<label>
    	                	<span>
	                    		<input type="radio" name="selected-reason" value="other" data-show="other">
                            </span>
						    <span><?php 
\_e('Other', 'woocommerce-dpd-uk');
?></span>
						</label>
						<div class="other" class="reason-input" style="display: none">
							<input type="text" name="other" class="additional-info" placeholder="<?php 
\_e('Please let us know how we can improve our plugin', 'woocommerce-dpd-uk');
?>">
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="footer">
			<a href="#" class="button button-secondary button-close tracker-button-close"><?php 
\_e('Cancel', 'woocommerce-dpd-uk');
?></a>
			<a href="#" class="button button-primary button-deactivate allow-deactivate"><?php 
\_e('Skip &amp; Deactivate', 'woocommerce-dpd-uk');
?></a>
		</div>
	</div>
</div><?php 
