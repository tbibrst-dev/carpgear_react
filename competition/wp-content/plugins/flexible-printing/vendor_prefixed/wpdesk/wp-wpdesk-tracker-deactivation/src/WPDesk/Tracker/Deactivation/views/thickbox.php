<?php

namespace FPrintingVendor;

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
echo \sprintf(\__('You are deactivating %s plugin.', 'flexible-printing'), $plugin_title);
?></h2>
	<div class="wpdesk_tracker_deactivate <?php 
echo $thickbox_id;
?>">
		<div class="body">
			<div class="panel" data-panel-id="confirm"><p></p></div>
			<div class="panel active" data-panel-id="reasons">
				<h4><strong><?php 
\_e('If you have a moment, please let us know why you are deactivating the plugin (anonymous feedback):', 'flexible-printing');
?></strong></h4>
				<ul class="reasons-list">
					<li class="reason">
						<label>
	            	        <span>
	            		        <input type="radio" name="selected-reason" value="plugin_stopped_working">
                            </span>
							<span><?php 
\_e('The plugin suddenly stopped working', 'flexible-printing');
?></span>
						</label>
					</li>
					<li class="reason">
						<label>
	            	        <span>
	            		        <input type="radio" name="selected-reason" value="broke_my_site">
                            </span>
							<span><?php 
\_e('The plugin broke my site', 'flexible-printing');
?></span>
						</label>
					</li>
					<li class="reason has-input">
						<label>
		                    <span>
		                        <input type="radio" name="selected-reason" value="found_better_plugin" data-show="found-better-plugin">
	                        </span>
							<span><?php 
\_e('I have found a better plugin', 'flexible-printing');
?></span>
						</label>
						<div class="found-better-plugin" class="reason-input" style="display: none">
							<input type="text" class="additional-info" name="better_plugin_name" placeholder="<?php 
\_e('What\'s the plugin\'s name?', 'flexible-printing');
?>">
						</div>
					</li>
					<li class="reason">
						<label>
	            	        <span>
	            		        <input type="radio" name="selected-reason" value="plugin_for_short_period">
                            </span>
						    <span><?php 
\_e('I only needed the plugin for a short period', 'flexible-printing');
?></span>
						</label>
					</li>
					<li class="reason">
						<label>
	            	        <span>
	            		        <input type="radio" name="selected-reason" value="no_longer_need">
                            </span>
							<span><?php 
\_e('I no longer need the plugin', 'flexible-printing');
?></span>
						</label>
					</li>
					<li class="reason">
						<label>
    	            	    <span>
	                		    <input type="radio" name="selected-reason" value="temporary_deactivation">
                            </span>
                            <span><?php 
\_e('It\'s a temporary deactivation. I\'m just debugging an issue.', 'flexible-printing');
?></span>
						</label>
					</li>
					<li class="reason has-input">
						<label>
    	                	<span>
	                    		<input type="radio" name="selected-reason" value="other" data-show="other">
                            </span>
						    <span><?php 
\_e('Other', 'flexible-printing');
?></span>
						</label>
						<div class="other" class="reason-input" style="display: none">
							<input type="text" name="other" class="additional-info" placeholder="<?php 
\_e('Please let us know how we can improve our plugin', 'flexible-printing');
?>">
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="footer">
			<a href="#" class="button button-secondary button-close tracker-button-close"><?php 
\_e('Cancel', 'flexible-printing');
?></a>
			<a href="#" class="button button-primary button-deactivate allow-deactivate"><?php 
\_e('Skip &amp; Deactivate', 'flexible-printing');
?></a>
		</div>
	</div>
</div><?php 
