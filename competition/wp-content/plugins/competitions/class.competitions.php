<?php
require_once (COMPETITIONS__PLUGIN_DIR . 'class.competitions-admin.php');
class Competitions
{

	private static $initiated = false;

	public static function init()
	{
		if (!self::$initiated) {
			self::init_hooks();
		}
	}

	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks()
	{
		self::$initiated = true;

		add_action("admin_init", array("Competitions", "admin_init"));
		add_action('save_post', array("Competitions", 'save_details'));

		add_action("competition_auto_close_every_minute", array('Competitions_Admin', 'updateDrawDatePassedCompetitionss'));
		add_action("competition_auto_set_spending_limit_every_minute", array('Competitions_Admin', 'autoUpdateSpendingLimit'));
		add_action("competition_auto_unlock_user_every_minute", array('Competitions_Admin', 'autoUnlockUsers'));
		add_action("competition_reward_prize_level_reached_every_minute", array('Competitions_Admin', 'competitionRewardPrizeLevelReachedNotification'));
		add_action("competition_sold_out_every_minute", array('Competitions_Admin', 'competitionSoldOutNotification'));
	}

	public static function view($name, array $args = array())
	{

		$args = apply_filters('competitions_view_arguments', $args, $name);

		foreach ($args as $key => $val) {
			$$key = $val;
		}

		$file = COMPETITIONS__PLUGIN_DIR . 'views/' . $name . '.php';

		include ($file);
	}

	public static function plugin_activation()
	{
		add_option('Activated_Competitions', true);

		$timestamp = wp_next_scheduled('competition_auto_close_every_minute');

		if (false === $timestamp) {
			wp_schedule_event(time(), 'every_minute', 'competition_auto_close_every_minute');
		}

		$timestamp = wp_next_scheduled('competition_auto_set_spending_limit_every_minute');

		if (false === $timestamp) {
			wp_schedule_event(time(), 'every_minute', 'competition_auto_set_spending_limit_every_minute');
		}

		$timestamp = wp_next_scheduled('competition_auto_unlock_user_every_minute');

		if (false === $timestamp) {
			wp_schedule_event(time(), 'every_minute', 'competition_auto_unlock_user_every_minute');
		}

		$timestamp = wp_next_scheduled('competition_reward_prize_level_reached_every_minute');

		if (false === $timestamp) {
			wp_schedule_event(time(), 'every_minute', 'competition_reward_prize_level_reached_every_minute');
		}

		$timestamp = wp_next_scheduled('competition_sold_out_every_minute');

		if (false === $timestamp) {
			wp_schedule_event(time(), 'every_minute', 'competition_sold_out_every_minute');
		}


	}

	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation()
	{

	}

	public static function register_post_type_winners()
	{

		$supports = array(
			'title', // post title
			'editor', // post content
			'author', // post author
			'thumbnail', // featured images
			'excerpt', // post excerpt
			//'custom-fields', // custom fields
			'comments', // post comments
			'revisions', // post revisions
			'post-formats', // post formats
		);
		$labels = array(
			'name' => _x('Winners', 'plural'),
			'singular_name' => _x('Winners', 'singular'),
			'menu_name' => _x('Winners', 'admin menu'),
			'name_admin_bar' => _x('Winners', 'admin bar'),
			'add_new' => _x('Add Winner', 'add new'),
			'add_new_item' => __('Add New Winner'),
			'new_item' => __('New winner'),
			'edit_item' => __('Edit winner'),
			'view_item' => __('View winner'),
			'all_items' => __('All Winners'),
			'search_items' => __('Search Winners'),
			'not_found' => __('No winner found.'),
		);
		$args = array(
			'supports' => $supports,
			'labels' => $labels,
			'public' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'winners'),
			'has_archive' => true,
			'hierarchical' => false,
			'show_in_menu' => false,
			'show_in_rest' => true,
			'rest_base' => 'winners'
		);

		register_post_type('winners', $args);
	}



	public static function admin_init()
	{
		//add_meta_box("year_completed-meta", "Year Completed", "year_completed", "winners", "side", "low");
		add_meta_box("winners_meta", "Winner information", ["Competitions", "winners_meta"], "winners", "normal", "low");
	}

	public static function winners_meta()
	{
		global $post;

		$custom = get_post_meta($post->ID);

		$fields = ['customer_name', 'customer_county', 'competition_name', 'ticket_number'];

		echo '<table style="width:100%; border-collapse:collapse;">';

		for ($i = 0; $i < count($fields); $i += 2) {

			echo "<tr style='border-bottom: 1px solid #ccc;'>";

			$field1 = $fields[$i];
			$value1 = $custom[$field1][0];//get_post_meta($post->ID, $field1, true);

			echo "<td style='width:20%; padding:8px;'><label for='winner_$field1'>" . ucwords(str_replace('_', ' ', $field1)) . ":</label></td>";

			echo "<td style='width:30%; padding:8px;'><input type='text' lass='form-control' id='winner_$field1' name='$field1' value='$value1' style='width:100%;' /></td>";

			if ($i + 1 < count($fields)) {
				$field2 = $fields[$i + 1];
				$value2 = $custom[$field2][0];//get_post_meta($post->ID, $field2, true);
				echo "<td style='width:20%; padding:8px;'><label for='winner_$field2'>" . ucwords(str_replace('_', ' ', $field2)) . ":</label></td>";
				echo "<td style='width:30%; padding:8px;'><input type='text' class='form-control' id='winner_$field2' name='$field2' value='$value2' style='width:100%;' /></td>";
			} else {
				echo "<td style='width:20%; padding:8px;'></td>";
				echo "<td style='width:30%; padding:8px;'></td>";
			}

			echo "</tr>";
		}

		echo '</table>';
	}



	public static function save_details()
	{
		global $post;

		$fields = ['customer_name', 'customer_county', 'competition_name', 'ticket_number'];

		foreach ($fields as $field) {
			if (!empty($post))
				update_post_meta($post->ID, $field, $_POST[$field]);
		}
	}
}

