<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       miniorange

 * @package    Miniorange_Api_Authentication
 */

if (!defined('ABSPATH')) {
	exit;
}
/**
 * Adding required files.
 */
require_once plugin_dir_path(__FILE__) . '../includes/class-miniorange-api-authentication-deactivator.php';
require plugin_dir_path(__FILE__) . '/class-miniorange-api-authentication-customer.php';
require plugin_dir_path(__FILE__) . '/class-miniorange-api-competition.php';
/** Lottery competition change */
require 'partials/class-mo-api-authentication-admin-menu.php';
require 'partials/flow/mo-api-authentication-flow.php';
require 'partials/flow/mo-token-api-flow.php';
require 'partials/support/class-mo-api-authentication-feedback.php';

/**
 * Handle Admin actions
 */
class Miniorange_API_Authentication_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 *
	 * @return void
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$mo_path = (dirname(dirname(plugin_basename(__FILE__))));
		$mo_path = $mo_path . '/miniorange-api-authentication.php';
		add_filter('plugin_action_links_' . $mo_path, array($this, 'add_action_links'));
		add_action('admin_menu', array($this, 'miniorange_api_authentication_save_settings'));
		add_action('admin_enqueue_scripts', array($this, 'plugin_settings_style'));
		add_action('admin_enqueue_scripts', array($this, 'plugin_settings_script'));

		add_filter('rest_exposed_cors_headers', array($this, 'allowed_cors_headers'));
		add_filter('rest_allowed_cors_headers', array($this, 'allowed_cors_headers'));

		//add_action( 'woocommerce_add_to_cart', [$this, 'add_competition_data_into_cart'], 10, 6);

		// add_action( 'woocommerce_add_to_cart', array( $this, 'calculate_totals' ), 20, 0 );
		// add_action( 'woocommerce_applied_coupon', array( $this, 'calculate_totals' ), 20, 0 );
		// add_action( 'woocommerce_cart_item_removed', array( $this, 'calculate_totals' ), 20, 0 );
		// add_action( 'woocommerce_cart_item_restored', array( $this, 'calculate_totals' ), 20, 0 );
		// add_action( 'woocommerce_check_cart_items', array( $this, 'check_cart_items' ), 1 );

		//add_filter( 'woocommerce_add_cart_item', [ $this, 'add_competition_data_into_cart' ] );
		//add_filter( 'woocommerce_get_item_data', array( $this, 'add_competition_data_into_cart' ), 20, 2 );

		//add_filter( 'woocommerce_rest_prepare_product_object', 'add_competition_data_into_product', 10, 2 );

		//add_filter('woocommerce_cart_contents_changed', [$this, 'add_competition_data_into_cart']);

		// add_filter('woocommerce_store_api_disable_nonce_check', '__return_false');
	}


	public static function allowed_cors_headers($allowed_headers)
	{
		$allowed_headers[] = 'Cart-Token';
		$allowed_headers[] = 'Nonce';
		$allowed_headers[] = 'X-WC-Store-API-Nonce';
		$allowed_headers[] = 'Nonce-Timestamp';
		return $allowed_headers;
	}

	public static function add_competition_data_into_cart($cart_content)
	{


		$cartKey = key($cart_content);

		$cart_content[$cartKey]['competition'] = ['rere'];
		return $cart_content;
		// print_r($product);
		//print_r($cart);
		//exit;

	}

	// Function to add the Premium settings in Plugin's section.

	/**
	 * Add action links
	 *
	 * @param mixed $actions Hook actions.
	 * @return array
	 */
	public function add_action_links($actions)
	{

		$url = esc_url(
			add_query_arg(
				'page',
				'mo_api_authentication_settings',
				get_admin_url() . 'admin.php'
			)
		);
		$url2 = $url . '&tab=licensing';
		$settings_link = "<a href='$url'>" . esc_attr('Configure') . '</a>';
		$settings_link2 = "<a href='$url2' style=><b>" . esc_attr('Upgrade to Premium') . '</b></a>';
		array_push($actions, $settings_link2);
		array_push($actions, $settings_link);
		return array_reverse($actions);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @return void
	 */
	public function plugin_settings_style()
	{
		wp_enqueue_style('mo_api_authentication_admin_settings_style', plugins_url('css/style_settings.min.css', __FILE__), MINIORANGE_API_AUTHENTICATION_VERSION, array(), false, false);
		wp_enqueue_style('mo_api_authentication_admin_settings_phone_style', plugins_url('css/phone.min.css', __FILE__), MINIORANGE_API_AUTHENTICATION_VERSION, array(), false, false);
	}

	/**
	 * Register the scripts for the admin area.
	 *
	 * @return void
	 */
	public function plugin_settings_script()
	{
		wp_enqueue_script('mo_api_authentication_admin_settings_phone_script', plugins_url('js/phone.min.js', __FILE__), MINIORANGE_API_AUTHENTICATION_VERSION, array(), false, false);
	}

	/**
	 * Enqueue styles.
	 *
	 * @return void
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Miniorange_Api_Authentication_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Miniorange_Api_Authentication_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style('mo_rest_api_material_icon', plugin_dir_url(__FILE__) . 'css/materialdesignicons.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/miniorange-api-authentication-admin.min.css', array(), $this->version, 'all');
		if (isset($_REQUEST['tab']) && sanitize_text_field(wp_unslash($_REQUEST['tab'])) === 'licensing') { //phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Ignoring nonce validation because we are getting data from URL and not form submission.
			wp_enqueue_style('mo-api-auth-license', plugin_dir_url(__FILE__) . 'css/miniorange-api-authentication-license.min.css', array(), $this->version, 'all');
			wp_enqueue_style('mo_api_authentication_bootstrap_css', plugins_url('css/bootstrap/bootstrap.min.css', __FILE__), MINIORANGE_API_AUTHENTICATION_VERSION, array(), false, false);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @return void
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Miniorange_Api_Authentication_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Miniorange_Api_Authentication_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
	}

	/**
	 * Show cURL error.
	 *
	 * @return void
	 */
	private function mo_api_authentication_show_curl_error()
	{
		if ($this->mo_api_authentication_is_curl_installed() === 0) {
			update_option('mo_api_auth_message', '<a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a> is not installed or disabled. Please enable it to continue.');
			mo_api_auth_show_error_message();
			return;
		}
	}

	/**
	 * Check if cURL is installed.
	 *
	 * @return integer
	 */
	private function mo_api_authentication_is_curl_installed()
	{
		if (in_array('curl', get_loaded_extensions(), true)) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * Register admin menu.
	 *
	 * @return void
	 */
	public function mo_api_auth_admin_menu()
	{

		$page = add_menu_page('API Authentication Settings ' . __('Configure Authentication', 'mo_api_authentication_settings'), 'miniOrange API Authentication', 'administrator', 'mo_api_authentication_settings', array($this, 'mo_api_auth_menu_options'), plugin_dir_url(__FILE__) . 'images/miniorange.png');
	}

	/**
	 * Admin menu options.
	 *
	 * @return void
	 */
	public function mo_api_auth_menu_options()
	{
		global $wpdb;
		mo_api_authentication_is_customer_registered();
		mo_api_authentication_main_menu();
	}

	/**
	 * Return REST API access to current endpoint.
	 *
	 * @param mixed $access access to route.
	 * @return string
	 */
	public static function whitelist_routes($access)
	{

		$current_route = self::get_current_route();

		if (self::is_whitelisted($current_route)) {
			return false;
		}

		return $access;
	}

	/**
	 * Check if whitelisted.
	 *
	 * @param mixed $current_route current REST API endpoint requested.
	 * @return bool
	 */
	public static function is_whitelisted($current_route)
	{
		return array_reduce(
			self::get_route_whitelist_option(),
			function ($is_matched, $pattern) use ($current_route) {
				return $is_matched || (bool) preg_match('@^' . htmlspecialchars_decode($pattern) . '$@i', $current_route);
			},
			false
		);
	}

	/**
	 * Get route whitelist option.
	 *
	 * @return array
	 */
	public static function get_route_whitelist_option()
	{
		return (array) get_option('mo_api_authentication_protectedrestapi_route_whitelist', array());
	}

	/**
	 * API shortlist.
	 *
	 * @return void
	 */
	public static function mo_api_auth_else()
	{
		self::mo_api_shortlist();
	}

	/**
	 * Get current route.
	 *
	 * @return string
	 */
	public static function get_current_route()
	{
		$rest_route = !empty($GLOBALS['wp']->query_vars['rest_route']) ? $GLOBALS['wp']->query_vars['rest_route'] : '';

		return (empty($rest_route) || '/' === $rest_route) ?
			$rest_route :
			untrailingslashit($rest_route);
	}

	/**
	 * Check if REST API is allowed.
	 *
	 * @return bool
	 */
	public static function allow_rest_api()
	{
		return (bool) apply_filters('dra_allow_rest_api', is_user_logged_in());
	}

	/**
	 * Config settings.
	 *
	 * @return void
	 */
	public function mo_api_authentication_config_settings()
	{
		mo_api_authentication_config_app_settings();
	}

	/**
	 * Export plugin configuration.
	 *
	 * @return void
	 */
	public function mo_api_authentication_export_plugin_configuration()
	{
		mo_api_authentication_export_plugin_config();
	}

	/**
	 * Convergence.
	 *
	 * @return void
	 */
	public static function mo_api_shortlist()
	{
		self::convergence();
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_rest_routes()
	{
		register_rest_route(
			'api/v1',
			'token-validate',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'mo_rest_jwt_validate_token'),
				'permission_callback' => '__return_true',
			)
		);
		register_rest_route(
			'api/v1',
			'token',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'mo_rest_token_generation_callback'),
				'permission_callback' => '__return_true',
			)
		);

		/** Lottery competition change */
		register_rest_route(
			'api/v1',
			'instant_wins_competition',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'mo_rest_competition_callback'),
				'permission_callback' => '__return_true',
			)
		);



		register_rest_route(
			'api/v1',
			'competition',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'competition_data_callback'),
				'permission_callback' => '__return_true',
				'args' => array(
					'limit' => array(
						'validate_callback' => function ($param, $request, $key) {
							return is_numeric($param) && $param > 0;
						}
					),
					'status' => array(
						'validate_callback' => function ($param, $request, $key) {
							return is_string($param);
						}
					),
					'category' => array(
						'validate_callback' => function ($param, $request, $key) {
							return is_string($param);
						}
					),
				),
				'headers' => ['Cache-Control' => 'max-age=3600'],
			)
		);

		register_rest_route(
			'api/v1',
			'drawn_next_competition',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'get_drawn_next_competition'),
				'permission_callback' => '__return_true',
			)
		);


		register_rest_route(
			'api/v1',
			'finished_soldout_competition',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'finished_soldout_competition_callback'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'featured_competition',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'get_featured_competition'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'getcompetition',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'get_competition_details'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'getsettings',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'get_global_settings'),
				'permission_callback' => '__return_true',
			)
		);


		register_rest_route(
			'api/v1',
			'getSEOSettings',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getSEOSettings'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'getOtherComps',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getOtherComps'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'login',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'userLogin'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'logout',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'userLogout'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'subscribe_mailing',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'subscribeMailing'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'get-nonce',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'expose_nonce'),
				'permission_callback' => '__return_true',
			)
		);


		register_rest_route(
			'api/v1',
			'check-auth',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'checkUserAuthToken'),
				'permission_callback' => '__return_true',
			)
		);


		register_rest_route(
			'api/v1',
			'addItem',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'addToCart'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'update-item',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'updateCartItem'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'cart-items',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'getCartItem'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'remove-item',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'removeCartItem'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'clear-cart',
			array(
				'methods'  => 'POST',
				'callback' => array($this, 'clearAllCartItems'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'forget_password',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'forgotPassword'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'reset_password',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'resetUserPassword'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'check_competition_prize',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'checkCompetitionPrize'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'get_purchased_competition',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getUserPurchasedCompetitions'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'get_user_competition',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getUserCompetitions'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'get_user_details',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getUserByToken'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'update_user_details',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'updateUserByToken'),
				'permission_callback' => '__return_true',
			)
		);


		register_rest_route(
			'api/v1',
			'points',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getUserPoints'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'updateProfile',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'updateUserProfileData'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'orders',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getUserOrders'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'order_detail',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getOrderDetailById'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'get_instant_winners',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getCompetitionInstantWins'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'get_competition_winner',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getCompetitionWinner'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'get_recent_winner',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getRecentWinner'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'create_contact_entry',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'createContactEntry'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'get_singular_competition',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'get_singular_competition'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'competition/v1',
			'/webhook',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'competitions_webhook_handler'),
				'permission_callback' => '__return_true',
			)
		);



		register_rest_route(
			'api/v1',
			'/checkZemplerData',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'checkZemplerData'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'/checkClaimPrize',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'check_competitions_claim_prize_form'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'/get_slider_settings',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getHomePageSliderSettings'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'/get_realtime_winners_prize_value',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'getALLWinnersAndPrizeValue'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'/get_review_all',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'getReviewAll'),
				'permission_callback' => '__return_true',
			)
		);


		register_rest_route(
			'api/v1',
			'/pinned-message',
			array(
				'methods' => 'POST',
				'callback' => array($this, 'getPinnedMessage'),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'api/v1',
			'/get_bank_details',
			array(
				'methods' => 'GET',
				'callback' => array($this, 'getbankdetails'),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Handle Token endpoint.
	 *
	 * @param mixed $request_body request body of REST API call.
	 * @return void
	 */
	public function mo_rest_token_generation_callback($request_body)
	{
		$json = $request_body->get_params();
		$username = isset($json['username']) ? sanitize_user($json['username']) : false;
		$password = isset($json['password']) ? $json['password'] : false;
		$json = array(
			'username' => $username,
			'password' => $password,
		);
		mo_api_auth_token_endpoint_flow($json);
	}

	/**
	 * Initialize flow.
	 *
	 * @return void
	 */
	public function mo_api_auth_initialize_api_flow()
	{
		mo_api_auth_restrict_rest_api_for_invalid_users();
	}

	/**
	 * Validate JWT token.
	 *
	 * @param bool $return_response response to be returned.
	 * @return void
	 */
	public function mo_rest_jwt_validate_token($return_response = true)
	{
		$headerkey = mo_api_auth_getallheaders();
		$headerkey = array_change_key_case($headerkey, CASE_UPPER);
		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);
		if (true === $response) {
			$response = array(
				'status' => 'TRUE',
				'message' => 'VALID_TOKEN',
				'code' => '200',
			);
		}
		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}
		wp_send_json($response);
	}

	/**
	 * Save temporary data.
	 *
	 * @return void
	 */
	public function save_temporary_data()
	{
		if (!empty($_SERVER['REQUEST_METHOD']) && !empty($_POST['nonce']) && sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) === 'POST' && current_user_can('administrator') && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'mo_rest_api_temporal_data_nonce')) {
			if (isset($_POST['auth_method']) && sanitize_text_field(wp_unslash($_POST['auth_method'])) === 'basic_auth') {
				$api_temp = array();
				$api_temp['algo'] = !empty($_POST['algo']) ? sanitize_text_field(wp_unslash($_POST['algo'])) : '';
				$api_temp['token_type'] = !empty($_POST['token_type']) ? sanitize_text_field(wp_unslash($_POST['token_type'])) : '';
				update_option('mo_rest_api_ajax_method_data', $api_temp);
			}
			$response = array(
				'success' => 'true',
			);
			wp_send_json($response, 200);
		}
	}

	/**
	 * Handle convergence.
	 *
	 * @return void
	 */
	public static function convergence()
	{
		if (!mo_api_auth_is_valid_request()) {
			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'error_description' => 'Sorry, you are not allowed to access REST API.',
			);
			wp_send_json($response, 401);
		}
	}


	/**
	 * Remove registered user.
	 *
	 * @return void
	 */
	public function miniorange_api_authentication_remove_registered_user()
	{
		delete_option('mo_api_authentication_new_registration');
		delete_option('mo_api_authentication_admin_email');
		delete_option('mo_api_authentication_admin_phone');
		delete_option('mo_api_authentication_admin_fname');
		delete_option('mo_api_authentication_admin_lname');
		delete_option('mo_api_authentication_admin_company');
		delete_option('mo_api_authentication_verify_customer');
		delete_option('mo_api_authentication_admin_customer_key');
		delete_option('mo_api_authentication_admin_api_key');
		delete_option('mo_api_authentication_new_customer');
		delete_option('mo_api_authentication_registration_status');
		delete_option('mo_api_authentication_customer_token');
	}

	/**
	 * Save settings in Database.
	 *
	 * @return void
	 */
	public function miniorange_api_authentication_save_settings()
	{
		if (!empty($_SERVER['REQUEST_METHOD']) && sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) === 'POST' && current_user_can('administrator')) {

			if (isset($_POST['option']) && sanitize_text_field(wp_unslash($_POST['option'])) === 'mo_api_authentication_change_email_address' && isset($_REQUEST['mo_api_authentication_change_email_address_form_fields']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mo_api_authentication_change_email_address_form_fields'])), 'mo_api_authentication_change_email_address_form')) {
				$this->miniorange_api_authentication_remove_registered_user();
				return;
			} elseif (isset($_POST['option']) && 'mo_api_authentication_register_customer' === sanitize_text_field(wp_unslash($_POST['option'])) && isset($_REQUEST['mo_api_authentication_register_form_fields']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mo_api_authentication_register_form_fields'])), 'mo_api_authentication_register_form')) {    // register the admin to miniOrange
				// validation and sanitization.
				$email = '';
				$phone = '';
				$password = '';
				$confirm_password = '';
				$fname = '';
				$lname = '';
				$company = '';
				if ((empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password']))) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- As we are not storing password in the database, so we can ignore sanitization.
					update_option('mo_api_auth_message', 'All the fields are required. Please enter valid entries.');
					update_option('mo_api_auth_message_flag', 2);
					return;
				} elseif (strlen($_POST['password']) < 8 || strlen($_POST['confirm_password']) < 8) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- As we are not storing password in the database, so we can ignore sanitization.
					update_option('mo_api_auth_message', 'Choose a password with minimum length 8.');
					update_option('mo_api_auth_message_flag', 2);
					return;
				} else {
					$email = !empty($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
					$phone = !empty($_POST['phone']) ? stripslashes(sanitize_text_field(wp_unslash($_POST['phone']))) : '';
					$password = stripslashes(sanitize_text_field($_POST['password'])); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Adding PHPCS ignore as there are special chars in password.
					$confirm_password = stripslashes(sanitize_text_field($_POST['confirm_password'])); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- Adding PHPCS ignore as there are special chars in password.
					$fname = !empty($_POST['fname']) ? sanitize_text_field(wp_unslash($_POST['fname'])) : '';
					$lname = !empty($_POST['lname']) ? sanitize_text_field(wp_unslash($_POST['lname'])) : '';
					$company = !empty($_POST['company']) ? sanitize_text_field(wp_unslash($_POST['company'])) : '';
				}

				update_option('mo_api_authentication_admin_email', $email);
				update_option('mo_api_authentication_admin_phone', $phone);
				update_option('mo_api_authentication_admin_fname', $fname);
				update_option('mo_api_authentication_admin_lname', $lname);
				update_option('mo_api_authentication_admin_company', $company);

				if (strcmp($password, $confirm_password) === 0) {
					$customer = new Miniorange_API_Authentication_Customer();
					$email = get_option('mo_api_authentication_admin_email');
					$content = json_decode($customer->check_customer(), true);

					if (strcasecmp($content['status'], 'CUSTOMER_NOT_FOUND') === 0) {
						$response = json_decode($customer->create_customer($password), true);

						if (strcasecmp($response['status'], 'SUCCESS') !== 0) {
							update_option('mo_api_auth_message', 'Failed to create customer. Try again.');
							update_option('mo_api_auth_message_flag', 2);
						} else {
							update_option('mo_api_authentication_verify_customer', 'true');
							update_option('mo_api_auth_message', sanitize_text_field($response['message']));
							update_option('mo_api_auth_message_flag', 1);
						}
					} elseif (strcasecmp($content['status'], 'SUCCESS') === 0) {
						update_option('mo_api_authentication_verify_customer', 'true');
						update_option('mo_api_auth_message', 'Account already exist. Please Login.');
						update_option('mo_api_auth_message_flag', 2);
					} elseif (is_null($content)) {
						update_option('mo_api_auth_message', 'Failed to create customer. Try again.');
						update_option('mo_api_auth_message_flag', 2);
					} else {
						update_option('mo_api_auth_message', sanitize_text_field($content['message']));
						update_option('mo_api_auth_message_flag', 1);
					}
				} else {
					update_option('mo_api_auth_message', 'Passwords do not match.');
					delete_option('mo_api_authentication_verify_customer');
					update_option('mo_api_auth_message_flag', 2);
				}
			} elseif (isset($_POST['option']) && sanitize_text_field(wp_unslash($_POST['option'])) === 'mo_api_authentication_goto_login' && isset($_REQUEST['mo_api_authentication_goto_login_fields']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mo_api_authentication_goto_login_fields'])), 'mo_api_authentication_goto_login')) {
				delete_option('mo_api_authentication_new_registration');
				update_option('mo_api_authentication_verify_customer', 'true');
			} elseif (isset($_POST['option']) && sanitize_text_field(wp_unslash($_POST['option'])) === 'mo_api_authentication_verify_customer' && isset($_REQUEST['mo_api_authentication_verify_customer_form_fields']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mo_api_authentication_verify_customer_form_fields'])), 'mo_api_authentication_verify_customer_form')) {  // login the admin to miniOrange.
				// validation and sanitization.
				$email = '';
				$password = '';
				if (empty($_POST['email']) || empty($_POST['password'])) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- As we are not storing password in the database, so we can ignore sanitization
					update_option('mo_api_auth_message', 'All the fields are required. Please enter valid entries.');
					mo_api_auth_show_error_message();
					return;
				} else {
					$email = !empty($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
					$password = stripslashes($_POST['password']); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash --  Adding PHPCS ignore as there are special chars in password.
				}

				update_option('mo_api_authentication_admin_email', $email);
				$customer = new Miniorange_API_Authentication_Customer();
				$content = $customer->get_customer_key($password);
				$customer_key = json_decode($content, true);
				if (json_last_error() === JSON_ERROR_NONE && isset($customer_key['status']) && 'SUCCESS' === $customer_key['status']) {
					update_option('mo_api_authentication_admin_customer_key', sanitize_text_field($customer_key['id']));
					update_option('mo_api_authentication_admin_api_key', sanitize_text_field($customer_key['apiKey']));
					update_option('mo_api_authentication_customer_token', sanitize_text_field($customer_key['token']));
					if (isset($customer_key['phone'])) {
						update_option('mo_api_authentication_admin_phone', sanitize_text_field($customer_key['phone']));
					}
					delete_option('password');
					update_option('mo_api_auth_message', 'Customer retrieved successfully');
					delete_option('mo_api_authentication_verify_customer');
					update_option('mo_api_auth_message_flag', 1);
				} else {
					update_option('mo_api_auth_message', 'Invalid username or password. Please try again.');
					update_option('mo_api_auth_message_flag', 2);
				}
			} elseif (isset($_POST['option']) && sanitize_text_field(wp_unslash($_POST['option'])) === 'mo_api_authentication_skip_feedback' && isset($_REQUEST['mo_api_authentication_skip_feedback_form_fields']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mo_api_authentication_skip_feedback_form_fields'])), 'mo_api_authentication_skip_feedback_form')) {
				$path = plugin_dir_path(dirname(__FILE__)) . 'miniorange-api-authentication.php';
				deactivate_plugins($path);
				update_option('mo_api_auth_message', 'Plugin deactivated successfully');
				mo_api_auth_show_success_message();
			} elseif (isset($_POST['mo_api_authentication_feedback']) && sanitize_text_field(wp_unslash($_POST['mo_api_authentication_feedback'])) === 'true' && isset($_REQUEST['mo_api_authentication_feedback_fields']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mo_api_authentication_feedback_fields'])), 'mo_api_authentication_feedback_form')) {
				$user = wp_get_current_user();

				$message = 'Plugin Deactivated:';
				if (isset($_POST['deactivate_reason_select'])) {
					$deactivate_reason = sanitize_text_field(wp_unslash($_POST['deactivate_reason_select']));
				}

				$deactivate_reason_message = array_key_exists('query_feedback', $_POST) ? sanitize_text_field(wp_unslash($_POST['query_feedback'])) : false;

				if ($deactivate_reason) {
					$message .= $deactivate_reason;
					if (isset($deactivate_reason_message)) {
						$message .= ': ' . $deactivate_reason_message;
					}

					if (isset($_POST['rate'])) {
						$rate_value = sanitize_text_field(wp_unslash($_POST['rate']));
					}

					$rating = '[Rating: ' . $rate_value . ']';

					$email = !empty($_POST['query_mail']) ? sanitize_email(wp_unslash($_POST['query_mail'])) : '';
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$email = get_option('mo_api_authentication_admin_email');
						if (empty($email)) {
							$email = $user->user_email;
						}
					}

					$reply = $rating;

					$phone = get_option('mo_api_authentication_admin_phone');

					// only reason.
					$feedback_reasons = new Miniorange_API_Authentication_Customer();
					$submitted = $feedback_reasons->mo_api_authentication_send_email_alert($email, $phone, $reply, $message, 'WordPress REST API Authentication by miniOrange');

					$path = plugin_dir_path(dirname(__FILE__)) . 'miniorange-api-authentication.php';
					deactivate_plugins($path);
					if (false === $submitted) {
						update_option('mo_api_auth_message', 'Your query could not be submitted. Please try again.');
						update_option('mo_api_auth_message_flag', 2);
					} else {
						update_option('mo_api_auth_message', 'Thanks for getting in touch! We shall get back to you shortly.');
						mo_api_auth_show_success_message();
					}
				} else {
					update_option('message', 'Please Select one of the reasons ,if your reason is not mentioned please select Other Reasons');
					update_option('mo_api_auth_message_flag', 2);
				}
			} elseif (isset($_POST['option']) && sanitize_text_field(wp_unslash($_POST['option'])) === 'mo_api_authentication_contact_us_query_option' && isset($_REQUEST['mo_api_authentication_contact_us_query_form_fields']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mo_api_authentication_contact_us_query_form_fields'])), 'mo_api_authentication_contact_us_query_form')) {

				// Contact Us query.
				$email = !empty($_POST['mo_api_authentication_contact_us_email']) ? sanitize_email(wp_unslash($_POST['mo_api_authentication_contact_us_email'])) : '';
				$phone = !empty($_POST['mo_api_authentication_contact_us_phone']) ? sanitize_text_field(wp_unslash($_POST['mo_api_authentication_contact_us_phone'])) : '';
				$query = !empty($_POST['mo_api_authentication_contact_us_query']) ? sanitize_text_field(wp_unslash($_POST['mo_api_authentication_contact_us_query'])) : '';

				$customer = new Miniorange_API_Authentication_Customer();
				if (empty($email) || empty($query)) {
					update_option('mo_api_auth_message', 'Please fill up Email and Query fields to submit your query.');
					mo_api_auth_show_error_message();
				} else {
					$submitted = $customer->submit_contact_us($email, $phone, $query);
					if (false === $submitted) {
						update_option('mo_api_auth_message', 'Your query could not be submitted. Please try again.');
						update_option('mo_api_auth_message_flag', 2);
						return;
					} else {
						update_option('mo_api_auth_message', 'Thanks for getting in touch! We shall get back to you shortly.');
						update_option('mo_api_auth_message_flag', 1);
						return;
					}
				}
			} elseif (isset($_POST['option']) && sanitize_text_field(wp_unslash($_POST['option'])) === 'mo_api_authentication_license_contact_form' && isset($_REQUEST['mo_api_authentication_license_contact_fields']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mo_api_authentication_license_contact_fields'])), 'mo_api_authentication_license_contact_form')) {
				$email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
				$phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
				$query = isset($_POST['query']) ? sanitize_text_field(wp_unslash($_POST['query'])) : '';
				$plugin_config = mo_api_authentication_export_plugin_config();
				// only reason.
				$payment_plan = new Miniorange_API_Authentication_Customer();
				if (empty($email) || empty($query)) {
					update_option('mo_api_auth_message', 'Please fill up Email and Query fields to submit your query.');
					update_option('mo_api_auth_message_flag', 2);
				} else {
					$submitted = $payment_plan->mo_api_authentication_send_email_alert($email, $phone, '', $query, 'Payment Plan Information: WordPress REST API Authentication');
					if (false === $submitted) {
						update_option('mo_api_auth_message', 'Your query could not be submitted. Please try again.');
						update_option('mo_api_auth_message_flag', 2);
					} else {
						update_option('mo_api_auth_message', 'Thanks for getting in touch! We shall get back to you shortly.');
						update_option('mo_api_auth_message_flag', 1);
					}
				}
			} elseif (isset($_POST['option']) && sanitize_text_field(wp_unslash($_POST['option'])) === 'mo_api_authentication_demo_request_form' && isset($_REQUEST['mo_api_authentication_demo_request_field']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mo_api_authentication_demo_request_field'])), 'mo_api_authentication_demo_request_form')) {
				// Demo Request.
				if ($this->mo_api_authentication_is_curl_installed() === 0) {
					return $this->mo_api_authentication_show_curl_error();
				}

				$email = !empty($_POST['mo_api_authentication_demo_email']) ? sanitize_email(wp_unslash($_POST['mo_api_authentication_demo_email'])) : '';
				$demo_plan = !empty($_POST['mo_api_authentication_demo_plan']) ? sanitize_text_field(wp_unslash($_POST['mo_api_authentication_demo_plan'])) : '';
				$query = !empty($_POST['mo_api_authentication_demo_usecase']) ? sanitize_text_field(wp_unslash($_POST['mo_api_authentication_demo_usecase'])) : '';

				$auth_methods_selected = '';
				$auth_methods = array(
					'mo_api_authentication_demo_basic_auth' => 'Basic Authentication',
					'mo_api_authentication_demo_jwt_auth' => 'JWT Authentication',
					'mo_api_authentication_demo_apikey_auth' => 'API Key Authentication',
					'mo_api_authentication_demo_oauth_auth' => 'OAuth 2.0 Authentication',
					'mo_api_authentication_demo_thirdparty_auth' => 'Third Party Authentication',
				);
				foreach ($auth_methods as $key => $value) {
					if (isset($_POST[$key]) && sanitize_text_field(wp_unslash($_POST[$key])) === 'on') {
						$auth_methods_selected .= $value . ', ';
					}
				}

				$auth_methods_selected = rtrim($auth_methods_selected, ', ');

				$query .= '<br /><b> Auth Methods: </b>' . $auth_methods_selected;

				$endpoints_selected = '';
				$endpoints = array(
					'mo_api_authentication_demo_endpoints_wp_rest_api' => 'WP REST APIs',
					'mo_api_authentication_demo_endpoints_custom_api' => 'WP Third Party/ Custom APIs',
				);
				foreach ($endpoints as $key => $value) {
					if (isset($_POST[$key]) && sanitize_text_field(wp_unslash($_POST[$key])) === 'on') {
						$endpoints_selected .= $value . ', ';
					}
				}

				$endpoints_selected = rtrim($endpoints_selected, ', ');

				$query .= '<br /><b> Endpoints Selected: </b>' . $endpoints_selected;

				if (empty($email) || empty($demo_plan) || empty($query)) {
					update_option('message', 'Please fill up Usecase, Email field and Requested demo plan to submit your query.');
					update_option('mo_api_auth_message_flag', 2);
				} else {
					$url = 'https://demo.miniorange.com/wordpress-oauth/';

					$headers = array(
						'Content-Type' => 'application/x-www-form-urlencoded',
						'charset' => 'UTF - 8',
					);
					$args = array(
						'method' => 'POST',
						'body' => array(
							'option' => 'mo_auto_create_demosite',
							'mo_auto_create_demosite_email' => $email,
							'mo_auto_create_demosite_usecase' => $query,
							'mo_auto_create_demosite_demo_plan' => $demo_plan,
							'mo_auto_create_demosite_plugin_name' => 'mo-rest-api-authentication',
						),
						'timeout' => '20',
						'redirection' => '5',
						'httpversion' => '1.0',
						'blocking' => true,
						'headers' => $headers,
					);

					$response = wp_remote_post($url, $args);

					if (is_wp_error($response)) {
						$error_message = $response->get_error_message();
						echo 'Something went wrong: ' . esc_html($error_message);
						exit();
					}
					$output = wp_remote_retrieve_body($response);

					$output = json_decode($output);

					if (is_null($output)) {
						update_option('mo_api_auth_message', 'Something went wrong! contact to your administrator');
						mo_api_auth_show_error_message();
					}

					if ('SUCCESS' === $output->status) {

						if (isset($output->demo_credentials)) {
							$demo_credentials = array();

							$site_url = esc_url_raw($output->demo_credentials->site_url);
							$email = sanitize_email($output->demo_credentials->email);
							$temporary_password = $output->demo_credentials->temporary_password;
							$password_link = esc_url_raw($output->demo_credentials->password_link);

							$sanitized_demo_credentials = array(
								'site_url' => $site_url,
								'email' => $email,
								'temporary_password' => $temporary_password,
								'password_link' => $password_link,
								'validity' => gmdate('d F, Y', strtotime('+10 day')),
							);

							update_option('mo_api_authentication_demo_creds', $sanitized_demo_credentials);

							$output->message = 'Your trial has been generated successfully. Please use the below credentials to access the trial.';
						}

						update_option('mo_api_auth_message', sanitize_text_field($output->message));
						update_option('mo_api_auth_message_flag', 1);
					} else {
						update_option('mo_api_auth_message', sanitize_text_field($output->message));
						update_option('mo_api_auth_message_flag', 2);
					}
				}
			}
		}
	}

	/** Lottery competition change */
	public function mo_rest_competition_callback($request_body)
	{
		$json = $request_body->get_params();

		Miniorange_API_Competition::getInstantWinsCompetitions();
	}

	public function competition_data_callback($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getCompetitions($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	function get_drawn_next_competition($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getDrawnNextCompetitions($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}


	function finished_soldout_competition_callback($request_body)
	{
		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);
		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getSoldOutFinishedCompetitions($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}
	}

	function get_featured_competition($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getFeaturedCompetitions($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function get_competition_details($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (!isset($request_body['id']) || $request_body['id'] == '') {

			$response = array(
				'status' => 'error',
				'error' => 'Invalid Competition id',
				'code' => '401',
				'error_description' => 'Competition ID is required',
			);

			wp_send_json($response);
		}

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getCompetitionDetail($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function get_global_settings($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getGlobalSettings($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getSEOSettings($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getSEOPageSettings($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getOtherComps($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getOtherCompetitions($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function userLogin($request_body)
	{

		global $wpdb;

		$params = $request_body->get_params();

		$data = array();

		$data['user_login'] = $params["username"];

		$data['user_password'] = $params["password"];

		$data['remember'] = true;

		$user = wp_signon($data, false);

		if (!is_wp_error($user)) {

			$user_data = $user->data;

			if (empty($user_data->user_auth_token)) {

				$auth_token = wp_generate_password(64, false);

				$wpdb->query(
					$wpdb->prepare(
						"UPDATE $wpdb->users 
						SET user_auth_token = %s 
						WHERE ID = %d",
						$auth_token,
						$user_data->ID
					)
				);
			} else {

				$auth_token = $user_data->user_auth_token;
			}

			$userData = Miniorange_API_Competition::getUserInfoByToken(['token' => $auth_token]);

			$response = [];

			$response['success'] = true;

			$response['data'] = $userData; //['name' => $user_data->display_name, 'email' => $user_data->user_email, 'token' => $auth_token];

			wp_send_json($response);
		} else {

			if ('incorrect_password' == $user->get_error_code()) {
				$error_message = "The password you entered is incorrect.";
			} else {
				$error_message = $user->get_error_message();
			}

			$response = array(
				'status' => 'error',
				'error' => $user->get_error_code(),
				'code' => '401',
				'error_description' => $error_message,
			);

			wp_send_json_error($response);
		}
	}

	public static function checkUserAuthToken($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);

			wp_send_json($response);
		}

		$params = $request_body->get_params();

		$token = $params['token'];

		if (empty($token)) {
			return new WP_Error('no_token', __('Token is missing', 'custom-api'), array('status' => 401));
		}

		$user_details = self::custom_api_validate_token($token);

		if (!$user_details) {
			return new WP_Error('invalid_token', __('Invalid token', 'custom-api'), array('status' => 401));
		}

		$userData = Miniorange_API_Competition::getUserInfoByToken(['token' => $token]);

		return new WP_REST_Response(
			array(
				'success' => true,
				'data' => $userData
			),
			200
		);
	}

	public static function custom_api_validate_token($token)
	{

		global $wpdb;

		$query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

		return $wpdb->get_row($query, ARRAY_A);
	}

	public static function userLogout($request_body)
	{
		global $wpdb;

		$params = $request_body->get_params();

		$token = $params['token'];

		$user_details = self::custom_api_validate_token($token);

		if (!$user_details) {
			return new WP_Error('invalid_token', __('Invalid token', 'custom-api'), array('status' => 401));
		}

		wp_logout();

		$wpdb->query(
			$wpdb->prepare(
				"
				UPDATE $wpdb->users 
				SET user_auth_token = NULL
				WHERE ID = %d",
				$user_details['ID']
			)
		);

		$response = array(
			'status' => 'TRUE',
			'message' => 'Logout Successfully',
			'code' => '200',
		);
		wp_send_json($response);
	}

	public static function subscribeMailing($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::subscribeMailing($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	function expose_nonce()
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$response = array(
				'status' => 'true',
				'nonce' => wp_create_nonce('wc_store_api'),
				'code' => '200',
			);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	function addToCart($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$body = $request_body->get_params();

			$nonce = $body['nonce'];

			unset($body['nonce']);

			$headers = [
				'X-WC-Store-api-nonce' => $nonce,
				'Content-Type' => 'application/json'
			];

			if (isset($body['cart_header'])) {

				$cookies = $body['cart_header'];

				unset($body['cart_header']);

				$cookies = base64_decode($cookies);

				$cookies = explode("|##|", $cookies);

				$cookies = implode("; ", $cookies);

				$headers['Cookie'] = $cookies;

				$headers['Authorization'] = $headerkey['AUTHORIZATION'];
			}

			$response = wp_remote_post(
				'https://stagingbackend.cggprelive.co.uk/index.php/wp-json/?rest_route=/wc/store/v1/cart/add-item',
				array(
					'body' => json_encode($body),
					'headers' => $headers
				)
			);

			$cookies = wp_remote_retrieve_header($response, 'set-cookie');

			$competition_hash = "";

			if (!empty($cookies)) {

				if (!is_array($cookies)) {
					$cookies = [$cookies];
				}

				$outputArray = array_map(function ($str) {
					return explode("; ", $str)[0];
				}, $cookies);


				if (isset($headers['Cookie']) && !empty($headers['Cookie'])) {

					$header_cookies = $headers['Cookie'];

					$header_cookies = explode("; ", $header_cookies);

					foreach ($outputArray as $cookie) {
						list($name, $value) = explode('=', $cookie, 2);
						$found = false;
						foreach ($header_cookies as &$header_cookie) {
							list($header_name, $header_value) = explode('=', $header_cookie, 2);
							if ($header_name === $name) {
								$header_cookie = $cookie;
								$found = true;
								break;
							}
						}
						if (!$found) {
							$header_cookies[] = $cookie;
						}
					}

					$outputArray = $header_cookies;
				}

				$competition_hash = base64_encode(implode("|##|", $outputArray));
			}

			$response = wp_remote_retrieve_body($response);

			if (!empty($response)) {

				$response = json_decode($response, true);

				if (isset($response['items']) && !empty($response['items'])) {

					foreach ($response['items'] as $key => $item) {

						$comp_details = self::getCompetitionDetailByProductId($item['id']);

						$response['items'][$key]['competition'] = $comp_details;
					}
				}

				$response['cart_header'] = $competition_hash;
			}

			wp_send_json($response);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getCompetitionDetailByProductId($id)
	{

		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}competitions WHERE competition_product_id = %d";

		$prepared_query_args = [$id];

		$prepared_query = $wpdb->prepare($query, $prepared_query_args);

		$result = $wpdb->get_row($prepared_query, ARRAY_A);

		if (!empty($result['description']))
			$result['description'] = self::decode_html($result['description']);
		if (!empty($result['faq']))
			$result['faq'] = self::decode_html($result['faq']);
		if (!empty($result['competition_rules']))
			$result['competition_rules'] = self::decode_html($result['competition_rules']);
		if (!empty($result['live_draw_info']))
			$result['live_draw_info'] = self::decode_html($result['live_draw_info']);

		$comp_tickets_purchased = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT count(*) as total_tickets FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %s and is_purchased = 1",
				$result['id']
			)
		);

		$result['total_ticket_sold'] = $comp_tickets_purchased;

		$result['competition_sold_prcnt'] = ($comp_tickets_purchased / $result['total_sell_tickets']) * 100;

		$query = $wpdb->prepare("SELECT reward.*, CASE
        WHEN reward.user_id IS NOT NULL THEN u.display_name ELSE NULL END AS full_name  
        FROM " . $wpdb->prefix . "comp_reward reward 
        LEFT JOIN " . $wpdb->prefix . "users u ON reward.user_id = u.id WHERE competition_id = %s", $result['id']);

		$reward_wins = $wpdb->get_results($query, ARRAY_A);

		if (!empty($reward_wins)) {

			foreach ($reward_wins as $reward_index => $reward_win) {

				$reward_wins[$reward_index]['reward_open'] = ($reward_win['prcnt_available'] <= $result['competition_sold_prcnt']) ? true : false;
			}
		}

		$result['reward_wins'] = $reward_wins;

		$query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes WHERE competition_id = %s", $result['id']);

		$instant_wins = $wpdb->get_results($query, ARRAY_A);

		$result['instant_wins'] = $instant_wins;

		$query = $wpdb->prepare("SELECT instant.*, CASE
        WHEN instant.user_id IS NOT NULL THEN u.display_name ELSE NULL END AS full_name  
        FROM " . $wpdb->prefix . "comp_instant_prizes_tickets instant 
        LEFT JOIN " . $wpdb->prefix . "users u ON instant.user_id = u.id 
        WHERE competition_id = %s", $result['id']);

		$instant_wins_tickets = $wpdb->get_results($query, ARRAY_A);

		$result['instant_wins_tickets'] = $instant_wins_tickets;

		return $result;
	}


	public static function decode_html($content)
	{

		return html_entity_decode(stripslashes($content), ENT_QUOTES, 'UTF-8');
	}


	public static function updateCartItem($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$body = $request_body->get_params();

			$nonce = $body['nonce'];

			$cookies = $body['cart_header'];

			unset($body['nonce']);

			unset($body['cart_header']);

			$cookies = base64_decode($cookies);

			$cookies = explode("|##|", $cookies);

			$cookies = implode("; ", $cookies);

			$response = wp_remote_post(
				'https://stagingbackend.cggprelive.co.uk/index.php/wp-json/?rest_route=/wc/store/v1/cart/update-item',
				array(
					'body' => json_encode($body),
					'headers' => [
						'X-WC-Store-api-nonce' => $nonce,
						'Content-Type' => 'application/json',
						'Cookie' => $cookies
					]
				)
			);

			$response = wp_remote_retrieve_body($response);

			if (!empty($response)) {

				$response = json_decode($response, true);

				if (isset($response['items']) && !empty($response['items'])) {

					foreach ($response['items'] as $key => $item) {

						$comp_details = self::getCompetitionDetailByProductId($item['id']);

						$response['items'][$key]['competition'] = $comp_details;
					}

					wp_send_json($response);
				}
			} else {

				echo $response;

				die();
			}
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getCartItem($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);



		if (true === $response) {

			$body = $request_body->get_params();

			echo "<pre>";
			print_r($body);

			error_log('cart bofy' . print_r($body, true));

			$nonce = $body['nonce'];

			$cookies = $body['cart_header'];

			unset($body['nonce']);

			unset($body['cart_header']);

			$cookies = base64_decode($cookies);

			$cookies = explode("|##|", $cookies);

			$cookies = implode("; ", $cookies);

			$response = wp_remote_get(
				'https://stagingbackend.cggprelive.co.uk/index.php/wp-json/?rest_route=/wc/store/v1/cart/items',
				array(
					'headers' => [
						'X-WC-Store-api-nonce' => $nonce,
						'Content-Type' => 'application/json',
						'Cookie' => $cookies
					]
				)
			);

			$response_body = wp_remote_retrieve_body($response);

			if (!empty($response_body)) {

				$response_body = json_decode($response_body, true);

				foreach ($response_body as $key => &$item) {

					$comp_details = self::getCompetitionDetailByProductId($item['id']);

					$item['competition'] = $comp_details;
				}

				wp_send_json($response_body);
			} else {

				echo $response;

				die();
			}
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function removeCartItem($request_body)
	{

		die('here');
		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$body = $request_body->get_params();

			$nonce = $body['nonce'];

			$cookies = $body['cart_header'];

			unset($body['nonce']);

			unset($body['cart_header']);

			$cookies = base64_decode($cookies);

			$cookies = explode("|##|", $cookies);

			$cookies = implode("; ", $cookies);

			$response = wp_remote_post(
				'https://stagingbackend.cggprelive.co.uk/index.php/wp-json/?rest_route=/wc/store/v1/cart/remove-item',
				array(
					'body' => json_encode($body),
					'headers' => [
						'X-WC-Store-api-nonce' => $nonce,
						'Content-Type' => 'application/json',
						'Cookie' => $cookies
					]
				)
			);

			$response = wp_remote_retrieve_body($response);

			echo $response;

			die();
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}
	public static function clearAllCartItems($request_body)
	{
		$headerkey = mo_api_auth_getallheaders();
		$headerkey = array_change_key_case($headerkey, CASE_UPPER);
		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {
			$body = $request_body->get_params();
			$nonce = $body['nonce'];
			$cookies = $body['cart_header'];

			unset($body['nonce']);
			unset($body['cart_header']);

			$cookies = base64_decode($cookies);
			$cookies = explode("|##|", $cookies);
			$cookies = implode("; ", $cookies);

			// Clear all items from the WooCommerce cart
			$clear_cart_response = wp_remote_post(
				'https://stagingbackend.cggprelive.co.uk/index.php/wp-json/?rest_route=/wc/store/v1/cart/empty',
				array(
					'headers' => [
						'X-WC-Store-api-nonce' => $nonce,
						'Content-Type' => 'application/json',
						'Cookie' => $cookies
					]
				)
			);

			$response_body = wp_remote_retrieve_body($clear_cart_response);

			echo $response_body;
			die();
		}

		if (false === $response) {
			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}


	public static function forgotPassword($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$email = $request_body->get_param('email');

			$userdata = get_user_by('email', $email);

			if (empty($userdata)) {
				$userdata = get_user_by('login', $email);
			}

			if (empty($userdata)) {

				$response = array(
					'status' => 'error',
					'error' => 'UNAUTHORIZED_USER',
					'code' => '401',
					'error_description' => 'User not found.',
				);
			} else {

				$user = new WP_User(intval($userdata->ID));
				$reset_key = get_password_reset_key($user);
				$wc_emails = WC()->mailer()->get_emails();
				$wc_emails['WC_Email_Customer_Reset_Password']->trigger($user->user_login, $reset_key);

				$response = array(
					'status' => true,
					'message' => 'Password reset link has been sent to your registered email.',
				);
			}
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function resetUserPassword($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$parameters = $request_body->get_params();

			$key = sanitize_text_field($parameters['key']);

			$new_password = sanitize_text_field($parameters['new_password']);

			$user = check_password_reset_key($key, sanitize_text_field($parameters['login']));

			if (is_wp_error($user)) {

				$response = array(
					'status' => 'error',
					'error' => 'INVALIDKEY',
					'code' => '400',
					'error_description' => 'Invalid or expired key',
				);

				wp_send_json($response);
			}

			// Reset the user's password
			$updated = wp_set_password($new_password, $user->ID);

			if (is_wp_error($updated)) {

				$response = array(
					'status' => 'error',
					'error' => 'password_reset_failed',
					'code' => '500',
					'error_description' => 'Failed to reset password',
				);
				wp_send_json($response);
			}

			$response = array('status' => true, 'message' => 'Password reset successfully');
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function checkCompetitionPrize($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::checkUserCompetitionInstantPrizes($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getUserPurchasedCompetitions($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getUserPurchasedCompetitions($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getUserCompetitions($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);



		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getUserCompetitions($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}
		wp_send_json($response);
	}

	public static function getUserByToken($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getUserInfoByToken($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function updateUserByToken($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::updateUserInfoByToken($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getUserPoints($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getUserPoints($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function updateUserProfileData($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::updateProfileDetails($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}
	public static function getUserOrders($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getUserOrders($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getOrderDetailById($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getOrderDetailById($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getCompetitionInstantWins($request_body)
	{
		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getCompetitionInstantWins($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getCompetitionWinner($request_body)
	{
		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getCompetitionWinners($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getRecentWinner($request_body)
	{
		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getRecentWinners($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function createContactEntry($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::createGFEntry($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function get_singular_competition($request_body)
	{
		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getSingularCompetitions($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function checkZemplerData($request_body)
	{

		global $wpdb;

		// $data = [
		// 	"competition_id" => 24,
		// 	"competition_type" => "instant",
		// 	"order_id" => 2923,
		// 	"payout_mode" => "Cash",
		// 	"prize_id" => 215,
		// 	"submission_id" => "9eff64fe-9998-4f3c-b31c-2d25de456aaf",
		// 	"ticket_number" => 1,
		// 	"user_id" => 2
		// ];

		$data = [
			"competition" => "Kenn Test for AutoApply Coupon",
			"competition_id" => 35,
			"competition_type" => "main",
			"order_id" => 2963,
			"payout_mode" => "Cash",
			"prize_id" => "3",
			"submission_id" => "2d7da92a-c6c4-4073-bafc-17346d270984",
			"ticket_number" => 8,
			"user_id" => 44599
		];


		if (isset($data['competition_type'])) {

			$competition_type = $data['competition_type'];
			$payout_mode = $data['payout_mode'];

			$order_id = $data['order_id'];
			$user_id = $data['user_id'];
			$user_details = $wpdb->get_row(
				$wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE ID = %d", $user_id)
			);


			$ticketNumber = $data['ticket_number'];
			$cashtoallocated = 0;
			if (!empty($order_id)) {

				$order = wc_get_order($order_id);

				if (!empty($order)) {

					$order_user_id = $order->get_user_id();

					if ($competition_type == 'reward') {

						$query = $wpdb->prepare(
							"select * from {$wpdb->prefix}comp_reward_winner where competition_id = %d and 
							reward_id = %d and user_id = %d and ticket_number = %d",
							array(
								$data['competition_id'],
								$data['prize_id'],
								$order_user_id,
								$data['ticket_number']
							)
						);

						$prize_data = $wpdb->get_row($query, ARRAY_A);

						if (!empty($prize_data)) {

							$wpdb->update("{$wpdb->prefix}comp_reward_winner", ["is_admin_declare_winner" => 1], ["id" => $prize_data['id']]);

							$query = $wpdb->prepare(
								"select * from {$wpdb->prefix}comp_reward where competition_id = %d and 
								id = %d",
								array(
									$prize_data['competition_id'],
									$prize_data['reward_id']

								)
							);

							$prize_data_chek = $wpdb->get_row($query, ARRAY_A);

							$cashtoallocated = $prize_data_chek['value'];
							error_log('cashtoallocated comp_reward_winner prize: ' . print_r($cashtoallocated, true));


							if ($payout_mode == 'Cash') {
								$wpdb->update("{$wpdb->prefix}comp_reward_winner", ["claimed_as" => 'Cash Alt'], ["id" => $prize_data['id']]);
							} else {
								$wpdb->update("{$wpdb->prefix}comp_reward_winner", ["claimed_as" => 'Prize'], ["id" => $prize_data['id']]);
							}
						}
					}

					if ($competition_type == 'instant') {

						$query = $wpdb->prepare(
							"select * from {$wpdb->prefix}comp_instant_prizes_tickets where competition_id = %d and 
						instant_id = %d and user_id = %d and ticket_number = %d",
							array(
								$data['competition_id'],
								$data['prize_id'],
								$order_user_id,
								$data['ticket_number']
							)
						);

						$prize_data = $wpdb->get_row($query, ARRAY_A);

						if (!empty($prize_data)) {

							$wpdb->update("{$wpdb->prefix}comp_instant_prizes_tickets", ["is_admin_declare_winner" => 1], ["id" => $prize_data['id']]);

							$query = $wpdb->prepare(
								"select * from {$wpdb->prefix}comp_instant_prizes where competition_id = %d and 
								id = %d",
								array(
									$prize_data['competition_id'],
									$prize_data['instant_id']

								)
							);

							$prize_data_chek = $wpdb->get_row($query, ARRAY_A);

							$cashtoallocated = $prize_data_chek['value'];
							error_log('cashtoallocated comp_instant_prizes_tickets prize: ' . print_r($cashtoallocated, true));

							if ($payout_mode == 'Cash') {
								$wpdb->update("{$wpdb->prefix}comp_instant_prizes_tickets", ["claimed_as" => 'Cash Alt'], ["id" => $prize_data['id']]);
							} else {
								$wpdb->update("{$wpdb->prefix}comp_instant_prizes_tickets", ["claimed_as" => 'Prize'], ["id" => $prize_data['id']]);
							}
						}
					}

					if ($competition_type == 'main') {

						$query = $wpdb->prepare(
							"select * from {$wpdb->prefix}competition_winners where competition_id = %d and 
							id = %d and user_id = %d and ticket_number = %d",
							array(
								$data['competition_id'],
								$data['prize_id'],
								$order_user_id,
								$data['ticket_number']
							)
						);

						$prize_data = $wpdb->get_row($query, ARRAY_A);
						error_log('prize_data main prize: ' . print_r($prize_data, true));

						if (!empty($prize_data)) {

							$wpdb->update("{$wpdb->prefix}competition_winners", ["is_admin_declare_winner" => 1], ["id" => $prize_data['id']]);

							$query = $wpdb->prepare(
								"select * from {$wpdb->prefix}competitions where id = %d",
								array(
									$prize_data['competition_id']
								)
							);

							$prize_data_chek = $wpdb->get_row($query, ARRAY_A);
							error_log('prize_data_chek main prize: ' . print_r($prize_data_chek, true));


							$cashtoallocated = $prize_data_chek['cash'];
							error_log('cashtoallocated main prize: ' . print_r($cashtoallocated, true));

							if ($payout_mode == 'Cash') {
								$wpdb->update("{$wpdb->prefix}competition_winners", ["claimed_as" => 'Cash Alt'], ["id" => $prize_data['id']]);
							} else {
								$wpdb->update("{$wpdb->prefix}competition_winners", ["claimed_as" => 'Prize'], ["id" => $prize_data['id']]);
							}
						}
					}


					try {
						$order = wc_get_order($order_id);
						// error_log('Order Object: ' . print_r($order, true));
						// Update billing address
						$order->set_billing_address_1($data['address_line1']);
						$order->set_billing_address_2($data['address_line2']);
						$order->set_billing_city($data['city']);
						$order->set_billing_state($data['state']);
						$order->set_billing_postcode($data['code']);
						$order->set_billing_country($data['country']);

						// Update shipping address
						$order->set_shipping_address_1($data['address_line1']);
						$order->set_shipping_address_2($data['address_line2']);
						$order->set_shipping_city($data['city']);
						$order->set_shipping_state($data['state']);
						$order->set_shipping_postcode($data['code']);
						$order->set_shipping_country($data['country']);

						// Save changes
						$order->save();

						error_log('Order addresses updated successfully.');
					} catch (Exception $e) {

						error_log('Error updating order addresses: ' . $e->getMessage());
					}
				}

				$cashtoallocated = floatval($cashtoallocated); // Convert to float

				// Ensure the amount has 2 decimal places
				$formattedAmount = number_format($cashtoallocated, 2, '.', '');
				echo gettype($formattedAmount) . "<br>";
				// echo  $formattedAmount;
				// wp_send_json($formattedAmount);


				$url = 'https://api.zempler.tech/identity/auth/connect/token';
				$body = array(
					'grant_type' => 'client_credentials',
					'client_id' => 'CarpGearGiveawaysLimited.production.client',
					'client_secret' => 'y+h1UMHlkpHw2TxQJMnvw385y66obpNRpEjGWQwqLXgI+72AmBR9LmHntuAJaSghETBAeAzi25NDIpFhbzflt3OVvC8wgBgac2VO',
					'scope' => 'payments aps_profile'
				);
				$response = wp_remote_post($url, array(
					'body' => $body,
					'headers' => array(
						'Content-Type' => 'application/x-www-form-urlencoded',
					),
					'timeout' => 10,
				));
				$response_body = wp_remote_retrieve_body($response);
				$response_body = json_decode($response_body, true); // Convert JSON string to an associative array
				$token  = $response_body['access_token'];


				$urlnew = 'https://api.zempler.tech/payments/payments';
				$idempotency_key = bin2hex(random_bytes(16)); // 32-character random key

				$name_parts = explode(' ', trim($reference));

				// Determine the reference
				if (count($name_parts) > 1) {
					// Use the last name if available
					$reference = $name_parts[count($name_parts) - 1];
				} else {
					// Use the first name if there's no last name
					$reference = $name_parts[0];
				}

				if ($reference != '') {

					// Limit to 10 characters
					$reference = substr($reference, 0, 10);
				} else {

					$reference = "";
				}

				$account_number = 50773980;
				$sort_code = 205976;

				$body = array(
					"data" => array(
						"initiation" => array(
							"instructionIdentification" => "123QWESFG12",
							"endToEndIdentification" => "AQSW122EDWS",
							"instructedAmount" => array(
								"amount" => $formattedAmount,
								"currency" => "GBP"
							),
							"debtorAccount" => array(
								"schemeName" => "SortCodeAccountNumber",
								"identification" => "08719909061308"
							),
							"creditorAccount" => array(
								"schemeName" => "SortCodeAccountNumber",
								"identification" => $sort_code.$account_number,
								"name" => "Ken",
								"secondaryIdentification" => "33071960"
							),
							"remittanceInformation" => array(
								"unstructured" => "3F601QC9C8531D05A156E40AB21",
								"reference" => "testing123"
							)
						)
					),
					"risk" => array(
						"paymentContextCode" => "Other",
						"merchantCategoryCode" => "0123",
						"merchantCustomerIdentification" => "BAC",
						"deliveryAddress" => array(
							"addressLine" => $data['address_line1'],
							"streetName" => $data['address_line2'],
							"buildingNumber" => "61",
							"postCode" => $data['code'],
							"townName" => $data['city'],
							"countrySubDivision" => $data['state'],
							"country" => "GB"
						)
					)
				);


				error_log('initiate_zempler_payment:++++++++++++body ' . print_r($body, true));

				$headers = array(
					'Authorization' => 'Bearer ' . $token,
					'x-idempotency-key' => $idempotency_key,
					'x-fapi-interaction-id' => '1324511WE',
					'Accept' => 'application/json',
					'Content-Type' => 'application/json'
				);

				$response = wp_remote_post($urlnew, array(
					'body' => json_encode($body),
					'headers' => $headers,
					'timeout' => 15,
				));

				if (is_wp_error($response)) {
					// Handle error
					return 'Error: ' . $response->get_error_message();
				}

				$response_body = wp_remote_retrieve_body($response);
				wp_send_json($response_body);
			}
		}
	}


	public static function competitions_webhook_handler(WP_REST_Request $request)
	{

		$json = file_get_contents('php://input');
		$data = json_decode($json, true);

		//$data = $request->get_json_params();

		$log_file = plugin_dir_path(__FILE__) . 'webhook.log';
		file_put_contents($log_file, print_r($data, true), FILE_APPEND);



		$cashvalue = 0.00;

		if (!empty($data)) {

			global $wpdb;

			error_log('data value webhook before: ' . print_r($data, true));


			unset($data['competition']);

			error_log('data value webhook after: ' . print_r($data, true));


			$wpdb->insert(
				$wpdb->prefix . "claim_prize_data",
				$data
			);



			if (isset($data['competition_type'])) {

				$competition_type = $data['competition_type'];
				$payout_mode = $data['payout_mode'];

				$order_id = $data['order_id'];
				$user_id = $data['user_id'];
				$user_details = $wpdb->get_row(
					$wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE ID = %d", $user_id)
				);


				$ticketNumber = $data['ticket_number'];
				$cashtoallocated = 0;
				if (!empty($order_id)) {

					$order = wc_get_order($order_id);

					if (!empty($order)) {

						$order_user_id = $order->get_user_id();

						if ($competition_type == 'reward') {

							$query = $wpdb->prepare(
								"select * from {$wpdb->prefix}comp_reward_winner where competition_id = %d and 
								reward_id = %d and user_id = %d and ticket_number = %d",
								array(
									$data['competition_id'],
									$data['prize_id'],
									$order_user_id,
									$data['ticket_number']
								)
							);

							$prize_data = $wpdb->get_row($query, ARRAY_A);

							if (!empty($prize_data)) {

								$wpdb->update("{$wpdb->prefix}comp_reward_winner", ["is_admin_declare_winner" => 1], ["id" => $prize_data['id']]);

								$query = $wpdb->prepare(
									"select * from {$wpdb->prefix}comp_reward where competition_id = %d and 
									id = %d",
									array(
										$prize_data['competition_id'],
										$prize_data['reward_id']

									)
								);

								$prize_data_chek = $wpdb->get_row($query, ARRAY_A);

								$cashtoallocated = $prize_data_chek['value'];
								error_log('cashtoallocated comp_reward_winner prize: ' . print_r($cashtoallocated, true));


								if ($payout_mode == 'Cash') {
									$wpdb->update("{$wpdb->prefix}comp_reward_winner", ["claimed_as" => 'Cash Alt'], ["id" => $prize_data['id']]);
								} else {
									$wpdb->update("{$wpdb->prefix}comp_reward_winner", ["claimed_as" => 'Prize'], ["id" => $prize_data['id']]);
								}
							}
						}

						if ($competition_type == 'instant') {

							$query = $wpdb->prepare(
								"select * from {$wpdb->prefix}comp_instant_prizes_tickets where competition_id = %d and 
							instant_id = %d and user_id = %d and ticket_number = %d",
								array(
									$data['competition_id'],
									$data['prize_id'],
									$order_user_id,
									$data['ticket_number']
								)
							);

							$prize_data = $wpdb->get_row($query, ARRAY_A);

							if (!empty($prize_data)) {

								$wpdb->update("{$wpdb->prefix}comp_instant_prizes_tickets", ["is_admin_declare_winner" => 1], ["id" => $prize_data['id']]);

								$query = $wpdb->prepare(
									"select * from {$wpdb->prefix}comp_instant_prizes where competition_id = %d and 
									id = %d",
									array(
										$prize_data['competition_id'],
										$prize_data['instant_id']

									)
								);

								$prize_data_chek = $wpdb->get_row($query, ARRAY_A);

								$cashtoallocated = $prize_data_chek['value'];
								error_log('cashtoallocated comp_instant_prizes_tickets prize: ' . print_r($cashtoallocated, true));

								if ($payout_mode == 'Cash') {
									$wpdb->update("{$wpdb->prefix}comp_instant_prizes_tickets", ["claimed_as" => 'Cash Alt'], ["id" => $prize_data['id']]);
								} else {
									$wpdb->update("{$wpdb->prefix}comp_instant_prizes_tickets", ["claimed_as" => 'Prize'], ["id" => $prize_data['id']]);
								}
							}
						}

						if ($competition_type == 'main') {

							$query = $wpdb->prepare(
								"select * from {$wpdb->prefix}competition_winners where competition_id = %d and 
								id = %d and user_id = %d and ticket_number = %d",
								array(
									$data['competition_id'],
									$data['prize_id'],
									$order_user_id,
									$data['ticket_number']
								)
							);

							$prize_data = $wpdb->get_row($query, ARRAY_A);

							if (!empty($prize_data)) {

								$wpdb->update("{$wpdb->prefix}competition_winners", ["is_admin_declare_winner" => 1], ["id" => $prize_data['id']]);

								$query = $wpdb->prepare(
									"select * from {$wpdb->prefix}competitions where id = %d",
									array(
										$prize_data['competition_id']
									)
								);

								$prize_data_chek = $wpdb->get_row($query, ARRAY_A);

								$cashtoallocated = $prize_data_chek['cash'];
								error_log('cashtoallocated main prize: ' . print_r($cashtoallocated, true));

								if ($payout_mode == 'Cash') {
									$wpdb->update("{$wpdb->prefix}competition_winners", ["claimed_as" => 'Cash Alt'], ["id" => $prize_data['id']]);
								} else {
									$wpdb->update("{$wpdb->prefix}competition_winners", ["claimed_as" => 'Prize'], ["id" => $prize_data['id']]);
								}
							}
						}


						try {
							$order = wc_get_order($order_id);
							// error_log('Order Object: ' . print_r($order, true));
							// Update billing address
							$order->set_billing_address_1($data['address_line1']);
							$order->set_billing_address_2($data['address_line2']);
							$order->set_billing_city($data['city']);
							$order->set_billing_state($data['state']);
							$order->set_billing_postcode($data['code']);
							$order->set_billing_country($data['country']);

							// Update shipping address
							$order->set_shipping_address_1($data['address_line1']);
							$order->set_shipping_address_2($data['address_line2']);
							$order->set_shipping_city($data['city']);
							$order->set_shipping_state($data['state']);
							$order->set_shipping_postcode($data['code']);
							$order->set_shipping_country($data['country']);

							// Save changes
							$order->save();

							error_log('Order addresses updated successfully.');
						} catch (Exception $e) {

							error_log('Error updating order addresses: ' . $e->getMessage());
						}
					}



					if ($payout_mode == 'Cash') {

						$url = 'https://api.zempler.tech/identity/auth/connect/token';
						$body = array(
							'grant_type' => 'client_credentials',
							'client_id' => 'CarpGearGiveawaysLimited.production.client',
							'client_secret' => 'y+h1UMHlkpHw2TxQJMnvw385y66obpNRpEjGWQwqLXgI+72AmBR9LmHntuAJaSghETBAeAzi25NDIpFhbzflt3OVvC8wgBgac2VO',
							'scope' => 'payments aps_profile'
						);
						$response = wp_remote_post($url, array(
							'body' => $body,
							'headers' => array(
								'Content-Type' => 'application/x-www-form-urlencoded',
							),
							'timeout' => 10,
						));
						$response_body = wp_remote_retrieve_body($response);
						$response_body = json_decode($response_body, true); // Convert JSON string to an associative array
						$token  = $response_body['access_token'];
						// error_log('Error updating order response_body token: ' . print_r($response_body, true));
						$payment_response = self::initiate_zempler_payment($token, $data, $cashtoallocated, $ticketNumber, $user_details);
						// error_log('initiate_zempler_payment: ' . print_r($payment_response, true));
						$payment_response = json_decode($payment_response, true); // Convert JSON string to an associative array
						error_log('initiate_zempler_payment json_decode payment_response: ' . print_r($payment_response, true));
						// Extract the data from the response
						$payment_data = $payment_response['data'];
						$payment_id = $payment_data['paymentId'];
						$status = $payment_data['status'];
						$creation_datetime = $payment_data['creationDateTime'];
						$instructed_amount = $payment_data['initiation']['instructedAmount']['amount'];
						$currency = $payment_data['initiation']['instructedAmount']['currency'];
						$debtor_account = $payment_data['initiation']['debtorAccount']['identification'];
						$creditor_account = $payment_data['initiation']['creditorAccount']['identification'];
						$creditor_name = $payment_data['initiation']['creditorAccount']['name'];
						$reference = $payment_data['initiation']['remittanceInformation']['reference'];
						$competition_type = $competition_type;
						// Prepare the data for insertion
						$table_name = $wpdb->prefix . 'zempler_payments'; // Replace with your actual table name

						$data = array(
							'payment_id' => $payment_id,
							'user_id' => $user_id,
							'comptetion_id' => $data['competition_id'],
							'comp_name' => $data['competition_type'],
							'status' => $status,
							'creation_datetime' => $creation_datetime,
							'instructed_amount' => $instructed_amount,
							'currency' => $currency,
							'debtor_account' => $debtor_account,
							'creditor_account' => $creditor_account,
							'creditor_name' => $creditor_name,
							'reference' => $reference,
							'comp_type' => $competition_type,
							'prize_id' => $data['prize_id']
						);
						// Insert the data into the database
						$inserted = $wpdb->insert($table_name, $data);

						if ($inserted) {
							error_log("Payment data saved successfully with Payment ID: " . $payment_id);
						} else {
							error_log("Failed to save payment data for Payment ID: " . $payment_id);
						}
					}
				}
			}
		}

		return new WP_REST_Response('Webhook received', 200);
	}


	function initiate_zempler_payment($token, $data, $cashtoallocated, $ticketNumber, $user_details)
	{
		error_log('initiate_zempler_payment:++++++++++++cashtoallocatedbefore ' . print_r($cashtoallocated, true));

		$url = 'https://api.zempler.tech/payments/payments';
		// $idempotency_key = bin2hex(random_bytes(16)).time(); // 32-character random key
		$idempotency_key = substr(bin2hex(random_bytes(20)), 0, 20) . time();
		$cashtoallocated = floatval($cashtoallocated);
		$formattedAmount = number_format($cashtoallocated, 2, '.', '');

		error_log('initiate_zempler_payment:++++++++++++user_details ' . print_r($user_details, true));
		error_log('initiate_zempler_payment:++++++++++++data ' . print_r($data, true));
		error_log('initiate_zempler_payment:++++++++++++cashtoallocated ' . print_r($formattedAmount, true));
		error_log('initiate_zempler_payment:++++++++++++ticketNumber ' . print_r($ticketNumber, true));
		error_log('initiate_zempler_payment:++++++++++++token ' . print_r($token, true));

		// $reference = $user_details['display_name'];
		$reference = is_object($user_details) ? $user_details->display_name : $user_details['display_name'];
		$credit_account = is_object($user_details) ? $user_details->account_number : $user_details['account_number'];
		$credit_sort = is_object($user_details) ? $user_details->sort_code : $user_details['sort_code'];

		//$reference = preg_replace('/[^a-zA-Z0-9 ]/', '', $reference);
		//$reference = substr($reference, 0, 10);

		// Remove unwanted characters
		$reference = preg_replace('/[^a-zA-Z0-9 ]/', '', $reference);

		// Split the name into an array
		$name_parts = explode(' ', trim($reference));

		// Determine the reference
		if (count($name_parts) > 1) {
			// Use the last name if available
			$reference = $name_parts[count($name_parts) - 1];
		} else {
			// Use the first name if there's no last name
			$reference = $name_parts[0];
		}

		if ($reference != '') {

			// Limit to 10 characters
			$reference = substr($reference, 0, 10);
		} else {

			$reference = "";
		}

		$body = array(
			"data" => array(
				"initiation" => array(
					"instructionIdentification" => "123QWESFG12",
					"endToEndIdentification" => "AQSW122EDWS",
					"instructedAmount" => array(
						"amount" => $formattedAmount,
						"currency" => "GBP"
					),
					"debtorAccount" => array(
						"schemeName" => "SortCodeAccountNumber",
						"identification" => "08719909061308"
					),
					"creditorAccount" => array(
						"schemeName" => "SortCodeAccountNumber",
						"identification" => $credit_sort . $credit_account,
						"name" => $reference,
						"secondaryIdentification" => "abc205976"
					),
					"remittanceInformation" => array(
						"unstructured" => "3F601QC9C8531D05A156E40AB21",
						"reference" => $reference . $ticketNumber
					)
				)
			),
			"risk" => array(
				"paymentContextCode" => "Other",
				"merchantCategoryCode" => "0123",
				"merchantCustomerIdentification" => "BAC",
				"deliveryAddress" => array(
					"addressLine" => $data['address_line1'],
					"streetName" => $data['address_line2'],
					"buildingNumber" => "61",
					"postCode" => $data['code'],
					"townName" => $data['city'],
					"countrySubDivision" => $data['state'],
					"country" => "GB"
				)
			)
		);


		error_log('initiate_zempler_payment:++++++++++++body ' . print_r($body, true));

		

		$headers = array(
			'Authorization' => 'Bearer ' . $token,
			'x-idempotency-key' => $idempotency_key,
			'x-fapi-interaction-id' => '1324511WE',
			'Accept' => 'application/json',
			'Content-Type' => 'application/json'
		);

		error_log('initiate_zempler_payment:++++++++++++headers ' . print_r($headers, true));


		$response = wp_remote_post($url, array(
			'body' => json_encode($body),
			'headers' => $headers,
			'timeout' => 15,
		));

		error_log('initiate_zempler_payment:++++++++++++response+++ ' . print_r($response, true));


		if (is_wp_error($response)) {
			// Handle error
			return 'Error: ' . $response->get_error_message();
		}

		$response_body = wp_remote_retrieve_body($response);
		return $response_body;
	}

	public static function check_competitions_claim_prize_form($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::checkClaimPrizeForm($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getHomePageSliderSettings($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {

			$params = $request_body->get_params();

			Miniorange_API_Competition::getHomePageSlider($params);
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getALLWinnersAndPrizeValue()
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {



			Miniorange_API_Competition::getALLWinnersAndPrizeValues();
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getReviewAll($request_body)
	{

		$url = 'https://api.reviews.co.uk/reviews';
		$query_params = [
			'store' => 'www.carpgeargiveaways.co.uk',
			'page' => 1,
			'per_page' => 6,
			'sort' => 'date_dsc',
			'minRating' => 5,
			'dateFrom' => '2024-01-01',
		];
		// Build the full URL with query parameters
		$full_url = add_query_arg($query_params, $url);

		// Set up the request headers
		$args = [
			'headers' => [
				'Content-Type' => 'application/json',
				'Authorization' => 'Bearer ••••••', // Replace with your actual token
			],
			'timeout' => 10,
		];


		$response = wp_remote_get($full_url, $args);

		if (is_wp_error($response)) {
			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}
		wp_send_json($response, 200);
	}

	public static function getPinnedMessage()
	{

		$headerkey = mo_api_auth_getallheaders();

		$headerkey = array_change_key_case($headerkey, CASE_UPPER);

		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);

		if (true === $response) {



			Miniorange_API_Competition::getPinnedMessageData();
		}

		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}

	public static function getbankdetails($request_body)
	{

		$headerkey = mo_api_auth_getallheaders();
		//
		$headerkey = array_change_key_case($headerkey, CASE_UPPER);
		//
		$response = Mo_API_Authentication_JWT_Auth::mo_api_auth_is_valid_request($headerkey);
		//
		if (true === $response) {

			$params = $request_body->get_params();

			error_log('++++++++++++++++++++++++++++++++++1++++++++++++' . print_r($params, true));
			global $wpdb;

			$token = $params['token'];
			$account = isset($params['account_number']) ? intval($params['account_number']) : 0;
			$sort = isset($params['sort_code']) ? intval($params['sort_code']) : 0;

			$query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

			error_log('array++++++++++++++++account' . print_r($params['account_number'], true));
			error_log('array++++++++++++++++sort' . print_r($params['sort_code'], true));





			$user = $wpdb->get_row($query, ARRAY_A);

			$id = intval($user['ID']);
			error_log('array++++++++++++++++id' . print_r($id, true));


			if (empty($user)) {
				wp_send_json(['success' => false, 'error' => 'Invalid Token'], 401);
			}


			if (empty($account)) {
				wp_send_json(['success' => false, 'error' => 'Please enter value in Account Number and try again after sometime.'], 400);
			} else if (empty($sort)) {

				// wp_send_json(['success' => false, 'error' => 'Please enter your sort code and try again after sometime.'], 400);
				wp_send_json(['success' => false, 'error' => __('Please enter your sort code and try again after sometime', 'woocommerce')], 400);
			}

			if ($account && $sort) {

				$url = "http://api.addressy.com/BankAccountValidation/Batch/Validate/v1.00/xmla.ws?";
				$url .= "&Key=ZH81-MH87-EG49-JD72";
				$url .= "&AccountNumbers=" . urlencode($account);
				$url .= "&SortCodes=" . urlencode($sort);

				//Make the request to Postcode Anywhere and parse the XML returned
				$file = simplexml_load_file($url);

				//Check for an error, if there is one then throw an exception
				if ($file->Rows->Row->attributes()->StatusInformation != "OK") {
					if ($file->Rows->Row->attributes()->StatusInformation == 'InvalidAccountNumber') {

						wp_send_json(['success' => false, 'error' => ('Please check your Account Number and try again after sometime.')], 400);
						return;
					} else {

						wp_send_json(['success' => false, 'error' => __('Please check your Sort Code and try again after sometime.')], 400);
					}
					//   throw new Exception("[ID] " . $file->Rows->Row->attributes()->Error . " [DESCRIPTION] " . $file->Rows->Row->attributes()->Description . " [CAUSE] " . $file->Rows->Row->attributes()->Cause . " [RESOLUTION] " . $file->Rows->Row->attributes()->Resolution);
				}

				//Copy the data
				if (!empty($file->Rows) && ($file->Rows->Row['StatusInformation'] != 'InvalidAccountNumber'  || $file->Rows->Row['StatusInformation'] != 'UnknownSortCode')) {


					error_log('Account: ' . var_export($account, true));
					error_log('Sort Code: ' . var_export($sort, true));
					error_log('User ID: ' . var_export($id, true));

					$query = $wpdb->prepare(
						"UPDATE {$wpdb->users} SET account_number = %d, sort_code = %d WHERE ID = %d",
						$account,
						$sort,
						$id
					);

					// Log the query to check its structure
					error_log('Generated query: ' . $query);

					// Run the query
					$result = $wpdb->query($query);

					if ($result === false) {
						error_log('Error in query: ' . $wpdb->last_error);
					} else {
						error_log('Rows affected: ' . $result);
					}



					$query =  $wpdb->query(
						$wpdb->prepare(
							"SELECT * FROM {$wpdb->users} WHERE ID =%d",
							$id
						)
					);
					$user = $wpdb->get_row($query, ARRAY_A);
					$response = array(
						'success' => 'true',
						'data' => $file->Rows->Row->attributes()->StatusInformation,
						'message' => 'User Update Successfully.',
					);

					//wp_send_json_success(['message' => 'Account number added succesfully.']);
					wp_send_json($response, 200);
				}
			} else {

				wp_send_json(['success' => false, 'error' => 'Account number missmatch. Please try again.'], 400);
			}
		}
		//
		if (false === $response) {

			$response = array(
				'status' => 'error',
				'error' => 'UNAUTHORIZED',
				'code' => '401',
				'error_description' => 'Incorrect JWT Format.',
			);
		}

		wp_send_json($response);
	}
}
