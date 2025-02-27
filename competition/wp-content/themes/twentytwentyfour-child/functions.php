<?php
// Your code to enqueue parent theme styles
function enqueue_parent_styles()
{

   wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

   if (is_checkout()) {

      //wp_enqueue_style( 'child-style', get_stylesheet_uri() );
      wp_enqueue_style('child-style', get_stylesheet_uri());

      wp_enqueue_style('swiper-style', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
      wp_enqueue_style('bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');
      wp_enqueue_style('jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

      wp_enqueue_script('swiper-bundle.js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array());
      wp_enqueue_script('bootstrap-bundle.js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array());

      wp_enqueue_script('jquery-ui.js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js', array());

      wp_enqueue_script('jquery-moment.js', "https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js", array());
      wp_enqueue_script('custom.js', get_stylesheet_directory_uri() . '/custom.js', array('jquery'), '', true);
   }
}

add_action('wp_enqueue_scripts', 'enqueue_parent_styles');

function mytheme_add_woocommerce_support()
{
   add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'mytheme_add_woocommerce_support');

add_filter('woocommerce_billing_fields', 'override_default_billing_address_checkout_fields', 20, 1);
function override_default_billing_address_checkout_fields($address_fields)
{
   $address_fields['billing_first_name']['placeholder'] = 'First Name';
   $address_fields['billing_last_name']['placeholder'] = 'Last Name';
   $address_fields['billing_address_1']['placeholder'] = 'House Name / No.';
   $address_fields['billing_address_2']['placeholder'] = 'Street Address*';
   $address_fields['billing_state']['placeholder'] = 'County';
   $address_fields['billing_postcode']['placeholder'] = 'Postcode*';
   $address_fields['billing_city']['placeholder'] = 'Town / City*';
   $address_fields['billing_phone']['placeholder'] = 'Phone*';
   $address_fields['billing_email']['placeholder'] = 'Email Address*';

   $address_fields['billing_phone']['class'] = ['form-row-last'];
   $address_fields['billing_city']['class'] = ['form-row-first'];
   $address_fields['billing_postcode']['class'] = ['form-row-last'];
   //$address_fields['billing_country']['class'] = ['form-row-last'];

   return $address_fields;
}

add_filter('woocommerce_checkout_fields', 'custom_woocommerce_billing_fields');

function custom_woocommerce_billing_fields($fields)
{
   $fields['billing']['billing_dob'] = array(
      'label' => __('Date of birth', 'woocommerce'), // Add custom field label
      'placeholder' => _x('Date of birth (DD-MM-YYYY)*', 'placeholder', 'woocommerce'), // Add custom field placeholder
      'required' => 1, // if field is required or not
      'clear' => false, // add clear or not
      'type' => 'text', // add field type
      'class' => ['form-row-first']   // add class name
   );

   $fields['billing']['billing_phone']['class'] = ['form-row-last'];
   $fields['billing']['billing_city']['class'] = ['form-row-first'];
   $fields['billing']['billing_postcode']['class'] = ['form-row-last'];

   $postcodepriority = $fields['billing']['billing_postcode']['priority'];
   $fields['billing']['billing_postcode']['priority'] = $fields['billing']['billing_state']['priority'];
   $fields['billing']['billing_state']['priority'] = $postcodepriority;

   $fields['billing']['billing_city']['priority'] = 70;
   $fields['billing']['billing_postcode']['priority'] = 80;
   $fields['billing']['billing_state']['priority'] = 90;

   return $fields;
}

add_action('wp_head', 'showCheckoutHeader');

function showCheckoutHeader()
{
   if (is_checkout()) {
      require_once 'checkoutHeader.php';
   }
}


add_action('wp_footer', 'showCheckoutFooter');

function showCheckoutFooter()
{
   if (is_checkout()) {
      require_once 'checkoutFooter.php';
   }
}


// add_action('woocommerce_thankyou', 'auto_complete_paid_orders');

// function auto_complete_paid_orders($order_id) {
//     if (!$order_id) return;

//     // Get the order
//     $order = wc_get_order($order_id);

//     // Check if the order has been paid
//     if ($order->is_paid() && $order->get_status() == 'processing') {
//         // Change order status to 'completed'
//         $order->update_status('completed');
//     }
// }

add_action('woocommerce_order_status_processing', 'auto_complete_processing_orders');

function auto_complete_processing_orders($order_id)
{
   // Get the order
   $order = wc_get_order($order_id);

   // Check if the order has been paid and is in the 'processing' status
   if ($order && $order->is_paid()) {
      // Change order status to 'completed'
      $order->update_status('completed');
   }
}


add_action('woocommerce_review_order_before_payment', 'showPaymentPoints');

function showPaymentPoints()
{

   echo '</div>
   </div>';

   if (is_user_logged_in()) {

      global $wpdb, $current_user, $wc_points_rewards;

      $current_user_id = get_current_user_id();

      $points_balance = WC_Points_Rewards_Manager::get_users_points($current_user_id);

      // Get the Redemption Conversion Rate
      $conversion_rate = get_option('woocommerce_points_and_rewards_conversion_rate');

      // Output or use the conversion rate as needed
      // echo 'Redemption Conversion Rate: ' . $conversion_rate;

      // if (!empty($points_balance)) {




      //    echo '
      // 	<div class="checkout-section-right-points">
      //       <div class="pay-radio">
      //             <div class="form-group">
      //                <input type="checkbox" id="woo_points_pay">
      //                <label for="woo_points_pay">
      //                   <div class="checkout-section-right-points-right-star">
      //                         <svg class="click-star" width="24" height="24" viewBox="0 0 24 24" fill="none"
      //                            xmlns="http://www.w3.org/2000/svg">
      //                            <path
      //                               d="M12.0002 18.5843L5.50229 22.0001L6.74335 14.7643L1.48438 9.64004L8.74968 8.58425L12.0002 2L15.2497 8.58425L22.515 9.64004L17.2571 14.7643L18.4992 22.0001L12.0002 18.5843Z"
      //                               fill="#EEC273" />
      //                         </svg>

      //                         <p>' . $points_balance . ' Points</p>
      //                   </div>
      //                </label>
      //             </div>
      //       </div>
      //    </div>
      //    <div class="use-point">
      //       <p>Use your points to save <span>' . get_woocommerce_currency_symbol() . (WC()->cart->subtotal) . '</span> </p>
      //    </div>';
      // }
      //    if (!empty($points_balance)) {
      //       // Calculate the amount in pounds
      //       $pound_value = $points_balance / 2000;

      //       // Round the value and check if it's less than 1
      //       if ($pound_value < 1) {
      //           // If the value is less than 1, display pennies
      //           $formatted_value = round($pound_value * 100); // Convert to pennies
      //           echo '
      //               <div class="checkout-section-right-points">
      //                   <div class="pay-radio">
      //                       <div class="form-group">
      //                           <input type="checkbox" id="woo_points_pay">
      //                           <label for="woo_points_pay">
      //                               <div class="checkout-section-right-points-right-star">
      //                                   <svg class="click-star" width="24" height="24" viewBox="0 0 24 24" fill="none"
      //                                        xmlns="http://www.w3.org/2000/svg">
      //                                       <path
      //                                           d="M12.0002 18.5843L5.50229 22.0001L6.74335 14.7643L1.48438 9.64004L8.74968 8.58425L12.0002 2L15.2497 8.58425L22.515 9.64004L17.2571 14.7643L18.4992 22.0001L12.0002 18.5843Z"
      //                                           fill="#EEC273" />
      //                                   </svg>
      //                                   <p>' . $points_balance . ' Points</p>
      //                               </div>
      //                           </label>
      //                       </div>
      //                   </div>
      //               </div>
      //               <div class="use-point">
      //                   <p>Use your points to save <span>' . $formatted_value . ' pennies</span></p>
      //               </div>';
      //       } else {
      //           // If the value is greater than or equal to 1, display pounds
      //           $formatted_value = round($pound_value, 2); // Round to 2 decimal places
      //           echo '
      //               <div class="checkout-section-right-points">
      //                   <div class="pay-radio">
      //                       <div class="form-group">
      //                           <input type="checkbox" id="woo_points_pay">
      //                           <label for="woo_points_pay">
      //                               <div class="checkout-section-right-points-right-star">
      //                                   <svg class="click-star" width="24" height="24" viewBox="0 0 24 24" fill="none"
      //                                        xmlns="http://www.w3.org/2000/svg">
      //                                       <path
      //                                           d="M12.0002 18.5843L5.50229 22.0001L6.74335 14.7643L1.48438 9.64004L8.74968 8.58425L12.0002 2L15.2497 8.58425L22.515 9.64004L17.2571 14.7643L18.4992 22.0001L12.0002 18.5843Z"
      //                                           fill="#EEC273" />
      //                                   </svg>
      //                                   <p>' . $points_balance . ' Points</p>
      //                               </div>
      //                           </label>
      //                       </div>
      //                   </div>
      //               </div>
      //               <div class="use-point">
      //                   <p>Use your points to save <span>' . get_woocommerce_currency_symbol() . $formatted_value . '</span></p>
      //               </div>';
      //       }
      //   }
      if (!empty($points_balance)) {
         // Calculate the amount in pounds
         $pound_value = $points_balance / 2000;

         // Format the value as pounds
         $formatted_value = round($pound_value, 2); // Round to 2 decimal places

         echo '
          <div class="checkout-section-right-points">
              <div class="pay-radio">
                  <div class="form-group">
                      <input type="checkbox" id="woo_points_pay">
                      <label for="woo_points_pay">
                          <div class="checkout-section-right-points-right-star">
                              <svg class="click-star" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                  xmlns="http://www.w3.org/2000/svg">
                                  <path
                                      d="M12.0002 18.5843L5.50229 22.0001L6.74335 14.7643L1.48438 9.64004L8.74968 8.58425L12.0002 2L15.2497 8.58425L22.515 9.64004L17.2571 14.7643L18.4992 22.0001L12.0002 18.5843Z"
                                      fill="#EEC273" />
                              </svg>
                              <p>' . $points_balance . ' Points</p>
                          </div>
                      </label>
                  </div>
              </div>
          </div>
          <div class="use-point">
              <p>Use your points to save <span>' . get_woocommerce_currency_symbol() . number_format($formatted_value, 2) . '</span></p>
          </div>';
      }
   }
}

add_filter('woocommerce_add_to_cart_redirect', function ($url, $adding_to_cart) {
   return wc_get_checkout_url();
}, 10, 2);


add_filter('woocommerce_order_button_text', 'comp_custom_button_text', 9999);

function comp_custom_button_text($button_text)
{

   return "Enter Now";
}

add_action('woocommerce_checkout_process', 'customFieldValidateCheckoutFields');

function customFieldValidateCheckoutFields()
{

   $dateOfBirth = filter_input(INPUT_POST, 'billing_dob');

   if (!empty($dateOfBirth)) {

      $dob = DateTime::createFromFormat('d-m-Y', $dateOfBirth);

      if (!$dob || $dob->format('d-m-Y') !== $dateOfBirth) {
         wc_add_notice(__('Invalid date format. Please enter date in dd-mm-yyyy format.'), 'error');
      }

      $age = $dob->diff(new DateTime())->y;

      if ($age < 18) {
         wc_add_notice(__('You must be over 18 years old to enter competitions.'), 'error');
      }
   }



   if (isset($_POST['comp_question'])) {

      $comp_quest_answer = filter_input(INPUT_POST, 'comp_quest_answer');

      if (empty($comp_quest_answer)) {
         wc_add_notice(__('<strong>Entry Question</strong> is a required field.'), 'error');
      }
   }
}


function handle_cashflow_failure_callback()
{
   if (isset($_GET['wc-api']) && $_GET['wc-api'] === 'iccf_gateway_return_failure') {
      // Get the order ID from the callback parameters
      $order_id = isset($_GET['real_id']) ? intval($_GET['real_id']) : 0;


      if ($order_id > 0) {
         // Get the order
         $order = wc_get_order($order_id);

         if ($order) {
            // Mark the order as failed if it's not already
            if (!$order->has_status('failed')) {
               $order->update_status('failed', __('Payment failed', 'woocommerce'));
            }

            // Add a note to the order
            wc_add_notice(__('Payment failed. Please try again.', 'woocommerce'), 'error');

            // Set a flag in the session to prevent "Order Received" logic
            WC()->session->set('payment_failed', true);
            // Redirect to cart page
            wp_safe_redirect(wc_get_checkout_url());
            exit;
         }
      }
   }
}
add_action('init', 'handle_cashflow_failure_callback');


function handle_cashflow_success_callback()
{
   if (isset($_GET['wc-api']) && $_GET['wc-api'] === 'iccf_gateway_return_success') {
      // Get the order ID from the callback parameters
      $order_id = isset($_GET['real_id']) ? intval($_GET['real_id']) : 0;
      error_log("This is handle_cashflow_success_callback $order_id");


      if ($order_id > 0) {
         // Get the order
         $order = wc_get_order($order_id);

         if ($order) {
            // Mark the order as completed if it's not already
            if (!$order->has_status('completed')) {
               $order->update_status('completed', __('Payment successful', 'woocommerce'));
            }

            // Add a note to the order
            // wc_add_notice(__('Payment successful. Thank you for your order!', 'woocommerce'), 'success');

            // Set a flag in the session to indicate the payment was successful
            WC()->session->set('payment_successful', true);

            // Call the check_cart function to handle redirection or other logic
            check_cart_after();  // This will redirect if payment is successful
         }
      }
   }
}
add_action('init', 'handle_cashflow_success_callback');


//add_action("woocommerce_cart_is_empty", "check_cart");

add_action('template_redirect', 'check_cart');

function check_cart()
{

   global $current_user, $wpdb;

   error_log('+++++++++++++++++++++++++++++++++++++++++++');
   // die();

   if (WC()->cart->is_empty() && is_cart()) {
      wp_safe_redirect(esc_url(FRONTEND_URL . 'cart'));
      exit;
   }


   if (!is_checkout()) {
      wp_safe_redirect(esc_url( FRONTEND_URL));
      exit;
   }

   if (
      is_checkout() && WC()->cart && !WC()->cart->is_empty() &&
      isset($_REQUEST['answers']) && !empty($_REQUEST['answers'])
   ) {


      if (isset($_REQUEST['answers']) && !empty($_REQUEST['answers'])) {

         WC()->session->set('check_comp_queries', json_decode(stripslashes($_REQUEST['answers']), true));
      }
   }

   if (is_checkout() && WC()->cart && !WC()->cart->is_empty()) {

      if (!WC()->session->get('comp_total_items', false)) {

         $total_items = WC()->cart->get_cart_contents_count();

         WC()->session->set('comp_total_items', $total_items);
      }

      if (is_user_logged_in()) {

         $removeComp = [];

         WC()->session->__unset('proceed_to_checkout');

         foreach (WC()->cart->get_cart() as $cart_item) {

            $qty = $cart_item['quantity'];

            if (isset($cart_item['competition']) && !empty($cart_item['competition'])) {

               $competition_info = $cart_item['competition'];
            } else {

               $competition_info = getCompetitionDetailByProductId($cart_item['product_id']);
            }

            $user_allowed_tickets = $competition_info['max_ticket_per_user'];

            $user_purchased_tickets = getUserPurchasedTickets($competition_info['id']);

            if (!empty($user_purchased_tickets) && isset($user_purchased_tickets['total_tickets'])) {

               $left_purchased_ticket_count = $user_allowed_tickets - $user_purchased_tickets['total_tickets'];

               $comp_left_tickets = $competition_info['total_sell_tickets'] - $competition_info['total_ticket_sold'];

               if ($user_purchased_tickets['total_tickets'] >= $user_allowed_tickets) {

                  $removeComp[] = "<strong>" . $competition_info['title'] . "</strong>";
               } else if ($qty > $left_purchased_ticket_count) {

                  wc_add_notice("you've already bought <strong>" . $user_purchased_tickets['total_tickets'] . "</strong> tickets for <strong>" . $competition_info['title'] . "</strong> competition. You can only buy <strong>$left_purchased_ticket_count</strong> more tickets for this competition. Please update your cart to procceed checkout", 'error');

                  WC()->session->set('proceed_to_checkout', 'no');
               } else if ($comp_left_tickets == 0) {

                  wc_add_notice("Tickets for the  <strong>" . $competition_info['title'] . "</strong> competition are sold out. Please update your cart to proceed to checkout.", 'error');

                  WC()->session->set('proceed_to_checkout', 'no');
               } else if ($qty > $comp_left_tickets) {

                  wc_add_notice("You can only buy $comp_left_tickets tickets for <strong>" . $competition_info['title'] . "</strong> competition. Please update your cart to procceed checkout", 'error');

                  WC()->session->set('proceed_to_checkout', 'no');
               }
            }
         }

         if (!empty($removeComp)) {

            wc_add_notice('You have reached the maximum ticket limit for ' . implode(", ", $removeComp) . ' Competition. Please remove it from your cart to proceed', 'error');

            WC()->session->set('proceed_to_checkout', 'no');

            // if (count($removeComp) == 1) {

            //    wc_add_notice($removeComp['0'] . ' has been removed from the cart because you have already purchased maximum tickets for this Competition. Please update your cart to procceed checkout', 'error');

            // } else {

            //    wc_add_notice(implode(", ", $removeComp) . ' has been removed from the cart because you have already purchased maximum tickets for these competitions. Please update your cart to procceed checkout', 'error');

            // }

         }

         $current_user_id = get_current_user_id();

         $lock_account = get_user_meta($current_user_id, 'lock_account', true);

         if ($lock_account == 1) {

            $locking_period = get_user_meta($current_user_id, 'locking_period', true);

            wc_add_notice("Your account has been locked for $locking_period. You are not allowed to make purchases at this time.", 'error');

            WC()->session->set('proceed_to_checkout', 'no');
         }

         $cart_total_amount = WC()->cart->get_cart_contents_total();

         $limit_value = get_user_meta($current_user_id, 'limit_value', true);

         $current_spending = get_user_meta($current_user_id, 'current_spending', true);

         // if ($limit_value > 0) {

         //    if (empty($current_spending) || $current_spending === 0)
         //       $current_spending = "0.00";

         //    $pending_limit = (float) $limit_value - $current_spending;

         //    if (($cart_total_amount > $limit_value) || $cart_total_amount > $pending_limit) {

         //       $limit_duration = get_user_meta($current_user_id, 'limit_duration', true);

         //       $symbol = get_woocommerce_currency_symbol();

         //       $message = "Your current spending limit is $symbol$limit_value $limit_duration and you've spent $symbol$current_spending. You can only spend $symbol$pending_limit more";

         //       wc_add_notice($message, 'error');

         //       WC()->session->set('proceed_to_checkout', 'no');
         //    }
         // }
      }
   }

   if (is_checkout() && WC()->cart && !WC()->cart->is_empty()) {

      if (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) {

         $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $_REQUEST['token']);

         $user = $wpdb->get_row($query, ARRAY_A);

         if (!empty($user) && !is_wp_error($user)) {

            if (is_user_logged_in()) {

               $current_user = wp_get_current_user();

               $auth_token = $current_user->get("user_auth_token");

               if ($auth_token != $_REQUEST['token']) {

                  wp_clear_auth_cookie();
                  wp_set_current_user($user['ID']);
                  wp_set_auth_cookie($user['ID']);
               }
            } else {

               wp_clear_auth_cookie();
               wp_set_current_user($user['ID']);
               wp_set_auth_cookie($user['ID']);
            }
         }
      } else {

         WC()->session->set('user_login_on_checkout', 'no');
      }
   }

   if (

      is_checkout() && WC()->cart &&
      isset($_REQUEST['cart_header']) && !empty($_REQUEST['cart_header']) &&
      isset($_REQUEST['nonce']) && !empty($_REQUEST['nonce'])
   ) {

      echo 'You are not allowed here...';
      exit;

      if (isset($_REQUEST['token']) && !empty($_REQUEST['token'])) {

         $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $_REQUEST['token']);

         $user = $wpdb->get_row($query, ARRAY_A);

         if (!empty($user) && !is_wp_error($user)) {

            if (is_user_logged_in()) {

               $current_user = wp_get_current_user();

               $auth_token = $current_user->get("user_auth_token");

               if ($auth_token != $_REQUEST['token']) {

                  wp_clear_auth_cookie();
                  wp_set_current_user($user['ID']);
                  wp_set_auth_cookie($user['ID']);
               }
            } else {

               wp_clear_auth_cookie();
               wp_set_current_user($user['ID']);
               wp_set_auth_cookie($user['ID']);
            }
         }
      }
      if (!WC()->cart->is_empty())
         WC()->cart->empty_cart();

      $cart_header = $_REQUEST['cart_header'];

      $cookies = base64_decode($cart_header);

      $cookies = explode("|##|", $cookies);

      $cookies = implode("; ", $cookies);

      $nonce = $_REQUEST['nonce'];

      $response = wp_remote_get(
         get_site_url() . '/index.php?rest_route=/wc/store/v1/cart/items',
         array(
            'headers' => [
               'X-WC-Store-api-nonce' => $nonce,
               'Content-Type' => 'application/json',
               'Cookie' => $cookies
            ]
         )
      );

      $response_body = wp_remote_retrieve_body($response);

      $items = json_decode($response_body, true);

      if (!empty($items)) {

         WC()->cart->empty_cart();

         foreach ($items as $item) {

            WC()->cart->add_to_cart($item['id'], $item['quantity']);
         }

         WC()->cart->calculate_totals();

         if (isset($_REQUEST['answers']) && !empty($_REQUEST['answers'])) {

            WC()->session->set('check_comp_queries', json_decode(stripslashes($_REQUEST['answers']), true));
         }

         wp_redirect(wc_get_checkout_url() . "?item_count=" . WC()->cart->get_cart_contents_count());

         exit;
      }
   }

   
}

function get_custom_email_html($mailer, $email_data, $email_heading = false, $user_id = null)
{


   if ($email_data['type'] == 'Points') {
      $template = 'emails/instant-win-points-email.php';
   } elseif ($email_data['type'] == 'Tickets') {
      $template = 'emails/instant-win-prize-ticket-email.php';
   } elseif ($email_data['type'] == 'PointsAllocation') {
      $template = 'emails/instant-win-points-allocation-email.php';
   } else {
      $template = 'emails/instant-win-prize-email.php';
   }

   error_log('+++++++++++++++++++++get_custom_email_html +' . print_r($user_id, true));




   return wc_get_template_html(
      $template,
      array(
         'email_heading' => $email_heading,
         'sent_to_admin' => false,
         'plain_text' => false,
         'email' => $mailer,
         'title' => $email_data['title'],
         'type' => $email_data['type'],
         'quantity' => $email_data['quantity'],
         'value' => $email_data['value'],
         'image' => $email_data['image'],
         'comp_title' => $email_data['comp_title'],
         'ticket_number' => $email_data['ticket_number'],
         'prize_id' => $email_data['instant_id'],
         'competition_id' => $email_data['competition_id'],
         'order' => $email_data['order_id'],
         'user_id' => $user_id,
         'cash' => !empty($email_data['prize_value']) && $email_data['prize_value'] > 0 ? 1 : 0
      )
   );
}

// add_action('woocommerce_new_order', 'assign_tickets_to_user', 1, 2);
// add_action('woocommerce_order_status_processing', 'assign_tickets_to_user', 1, 2);

function assign_tickets_to_user($order_id, $order)
{

   global $wpdb;

   $user = $order->get_user();

   error_log("Order received logic triggered for order $order_id");
   error_log("user assigned" . print_r($user, true));

   foreach ($order->get_items() as $item_id => $item) {

      $product = $item->get_product();

      $product_id = $product->get_id();

      $item_quantity = $item->get_quantity();

      // $query = $wpdb->prepare("UPDATE {$wpdb->prefix}competition_tickets AS tickets
      // INNER JOIN {$wpdb->prefix}competitions AS competition ON tickets.competition_id = competition.id
      // SET tickets.is_purchased = 1 and tickets.user_id = %d
      // WHERE competition.competition_product_id = %d
      // ORDER BY RAND() LIMIT %d", $user->ID, $product_id, $item_quantity);

      // Get total tickets for the competition.










      // $query_percent = $wpdb->prepare(
      //    "SELECT {$wpdb->prefix}comp_instant_prizes_tickets.*"
      // );
      // $query_percent_results = $wpdb->get_results($query, ARRAY_A);

      $purchase_date = date("Y-m-d");

      // $query = $wpdb->prepare("UPDATE {$wpdb->prefix}competition_tickets AS tickets INNER JOIN 
      // ( SELECT id FROM {$wpdb->prefix}competition_tickets WHERE competition_id IN 
      //    ( SELECT id FROM {$wpdb->prefix}competitions WHERE competition_product_id = %d ) 
      // and is_purchased <> 1 and user_id IS NULL ORDER BY RAND() LIMIT %d ) 
      // AS subquery ON tickets.id = subquery.id SET tickets.is_purchased = 1, tickets.user_id = %d, tickets.purchased_on = %s, tickets.order_id = %d ,tickets.reward_milestone = %d", $product_id, $item_quantity, $user->ID, $purchase_date, $order_id, $current_milestone);

      // $wpdb->query($query);
      for ($i = 0; $i < $item_quantity; $i++) {

         $competition_id = $wpdb->get_var(
            $wpdb->prepare(
               "SELECT id FROM {$wpdb->prefix}competitions WHERE competition_product_id = %d",
               $product_id
            )
         );



         // Get total tickets sold for the competition.
         $tickets_sold = $wpdb->get_var(
            $wpdb->prepare(
               "SELECT COUNT(*) FROM {$wpdb->prefix}competition_tickets 
           WHERE competition_id = %d AND is_purchased = 1",
               $competition_id
            )
         );


         // Get total tickets for the competition.
         $total_tickets = $wpdb->get_var(
            $wpdb->prepare(
               "SELECT total_sell_tickets FROM {$wpdb->prefix}competitions WHERE id = %d",
               $competition_id
            )
         );

         // $total_tickets  = 100;

         $available_tickets = $total_tickets - $tickets_sold;


         // Get the prcnt_available values from wp_comp_reward for the given competition_id
         $milestones = $wpdb->get_results(
            $wpdb->prepare(
               "SELECT prcnt_available FROM {$wpdb->prefix}comp_reward WHERE competition_id = %d",
               $competition_id
            )
         );

         $milestones_array = [];
         foreach ($milestones as $milestone) {
            $milestones_array[] = (float) $milestone->prcnt_available;
         }

         $milestones = $milestones_array;


         $current_milestone = 0;




         // Check the milestones based on sold tickets after the current purchase
         foreach ($milestones as $milestone) {
            // $milestone_tickets = ($milestone / 100) * $total_tickets;
            $milestone_tickets = round(($milestone / 100) * $total_tickets, 2);
            error_log("Milestone: $milestone, Milestone Tickets: $milestone_tickets, Tickets Sold: " . ($tickets_sold + 1));

            if (($tickets_sold + 1) <= $milestone_tickets) {
               $current_milestone = $milestone;
               break; // Stop once the appropriate milestone is found
            }
         }

         // Ensure we have a valid milestone value (default to 100% if no other milestone is found)
         if ($current_milestone == 0) {
            $current_milestone = 100;
         }

         // Assign the ticket to the user with random selection
         $query = $wpdb->prepare(
            "UPDATE {$wpdb->prefix}competition_tickets AS tickets 
             INNER JOIN 
             ( 
                 SELECT id 
                 FROM {$wpdb->prefix}competition_tickets 
                 WHERE competition_id = %d 
                 AND is_purchased = 0 
                 AND user_id IS NULL 
                 ORDER BY RAND() 
                 LIMIT 1 
             ) AS subquery 
             ON tickets.id = subquery.id 
             SET tickets.is_purchased = 1, 
                 tickets.user_id = %d, 
                 tickets.purchased_on = %s, 
                 tickets.order_id = %d, 
                 tickets.reward_milestone = %d",
            $competition_id,
            $user->ID,
            $purchase_date,
            $order_id,
            $current_milestone
         );

         // Execute the query
         error_log("user assigned" . print_r($query, true));

         $result = $wpdb->query($query);

         // Increment the ticket sold counter
         $tickets_sold++;

         if ($result === false) {
            error_log("Database error: " . $wpdb->last_error);
         }
      }


      check_cart();
   }
}

// add_action( 'woocommerce_payment_complete', 'assign_tickets_to_user' );

function cgg_lostpassword_url($lostpassword_url, $redirect)
{

   if (isset($_REQUEST['_wp_http_referer']) && strpos($_REQUEST['_wp_http_referer'], '/competition/index.php/checkout/') !== false) {
      $custom_lostpassword_url = FRONTEND_URL .'auth/forgot/password';
   } else {
      $custom_lostpassword_url = $lostpassword_url;
   }
   return $custom_lostpassword_url;
}
add_filter('lostpassword_url', 'cgg_lostpassword_url', 10, 2);

add_filter('woocommerce_store_api_disable_nonce_check', '__return_false');

add_filter('woocommerce_cart_contents_changed', 'add_competition_info_to_cart', 10, 3);

function add_competition_info_to_cart($cart_content)
{

   if (!empty($cart_content)) {

      foreach ($cart_content as &$item) {

         $competition_info = getCompetitionDetailByProductId($item['product_id']);

         $item['competition'] = $competition_info;
      }
   }

   return $cart_content;
}

function getCompetitionDetailByProductId($id)
{

   global $wpdb;

   $query = "SELECT * FROM {$wpdb->prefix}competitions WHERE competition_product_id = %d";

   $prepared_query_args = [$id];

   $prepared_query = $wpdb->prepare($query, $prepared_query_args);

   $result = $wpdb->get_row($prepared_query, ARRAY_A);

   if (!empty($result['description']))
      $result['description'] = decode_html($result['description']);
   if (!empty($result['faq']))
      $result['faq'] = decode_html($result['faq']);
   if (!empty($result['competition_rules']))
      $result['competition_rules'] = decode_html($result['competition_rules']);
   if (!empty($result['live_draw_info']))
      $result['live_draw_info'] = decode_html($result['live_draw_info']);

   $comp_tickets_purchased = $wpdb->get_var(
      $wpdb->prepare(
         "SELECT count(*) as total_tickets FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %s and is_purchased = 1",
         $result['id']
      )
   );

   $result['total_ticket_sold'] = $comp_tickets_purchased;

   // error_log('The value of the variable is: ' . print_r($result, true));

   // $result['competition_sold_prcnt'] = ($comp_tickets_purchased / $result['total_sell_tickets']) * 100;
   if (isset($result['total_sell_tickets']) && $result['total_sell_tickets'] > 0) {
      $result['competition_sold_prcnt'] = ($comp_tickets_purchased / $result['total_sell_tickets']) * 100;
   } else {
      error_log('Division by zero: total_sell_tickets is either not set or zero for competition ID: ' . $result['id']);
      $result['competition_sold_prcnt'] = 0; // Set a default value, like 0%
   }

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

function decode_html($content)
{

   return html_entity_decode(stripslashes($content), ENT_QUOTES, 'UTF-8');
}

function getUserPurchasedTickets($id)
{

   global $wpdb, $current_user;

   $current_user_id = get_current_user_id();

   return $wpdb->get_row(
      $wpdb->prepare(
         "SELECT count(*) as total_tickets  FROM {$wpdb->prefix}competition_tickets 
          WHERE user_id = %d and is_purchased = 1 and competition_id = %d",
         $current_user_id,
         $id
      ),
      ARRAY_A
   );
}

add_filter('woocommerce_order_button_html', 'inactive_order_button_html');
function inactive_order_button_html($button)
{

   $proceed_to_checkout = WC()->session->get('proceed_to_checkout');

   /*if (is_user_logged_in()) {

      $current_user_id = get_current_user_id();

      $lock_account = get_user_meta($current_user_id, 'lock_account', false);

      if ($lock_account == 1) {
         $proceed_to_checkout = 'no';
      }
   }*/

   if ($proceed_to_checkout == 'no') {
      $order_button_text = "Enter Now";
      $button = '<div class="your-tickets-enter"><button type="submit" class="your-tickets-enter-btn button alt' . esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : '') . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '" disabled>' . esc_html($order_button_text) . '</button></div>';
   }
   return $button;
}

add_action('woocommerce_checkout_update_order_meta', 'save_competition_question', 10, 2);

function save_competition_question($order_id, $data)
{

   global $wpdb;

   $order = wc_get_order($order_id);

   $orderData = $order->get_data();

   if (isset($_POST['comp_queries']) && !empty($_POST['comp_queries'])) {

      $comp_queries = json_decode(stripslashes($_POST['comp_queries']), true);

      foreach ($comp_queries as $comp_product_id => $comp_ques_ans) {

         $question_id = getCompQuestionId($comp_product_id);

         if ($question_id > 0) {

            $wpdb->insert(
               $wpdb->prefix . "user_quest",
               array(
                  'question_id' => $question_id,
                  'user_id' => $order->get_user_id(),
                  'answer' => $comp_ques_ans,
                  'order_id' => $order_id,
                  'type' => isset($_POST['comp_question']) ? "override" : "main"
               )

            );
         }
      }
   }

   if (isset($_POST['comp_question'])) {

      $comp_quest_answer = filter_input(INPUT_POST, 'comp_quest_answer');

      $wpdb->insert(
         $wpdb->prefix . "user_quest",
         array(
            'question_id' => $_POST['comp_question'],
            'user_id' => $order->get_user_id(),
            'answer' => $comp_quest_answer,
            'order_id' => $order_id,
            'type' => 'main'
         )

      );
   }
}

function getCompQuestionId($record)
{
   global $wpdb;

   $main_table = $wpdb->prefix . 'competitions';

   $entry = $wpdb->get_row("SELECT * FROM " . $main_table . " WHERE competition_product_id = '" . $record . "'", ARRAY_A);

   return isset($entry['question_id']) ? $entry['question_id'] : false;
}


add_action('user_register', 'userSpendingLimitAccountLockouts');
function userSpendingLimitAccountLockouts($user_id)
{
   update_user_meta($user_id, 'limit_value', false);
   update_user_meta($user_id, 'limit_duration', false);
   update_user_meta($user_id, 'lockout_period', false);
   update_user_meta($user_id, 'current_spending', false);
   update_user_meta($user_id, 'lock_account', false);
   update_user_meta($user_id, 'locking_period', false);
}

function autoLoginCurrentUserToFrontend($user_login, $user)
{

   global $wpdb;

   if (isset($_REQUEST['show_login_form']) && $_REQUEST['show_login_form'] == 1) {

      $checkLogin = WC()->session->get('user_login_on_checkout', false);

      if ($checkLogin == 'no') {

         if (empty($user->user_auth_token)) {

            $auth_token = wp_generate_password(64, false);

            $wpdb->query(
               $wpdb->prepare(
                  "UPDATE $wpdb->users 
						SET user_auth_token = %s 
						WHERE ID = %d",
                  $auth_token,
                  $user->ID
               )
            );
         } else {

            $auth_token = $user->user_auth_token;
         }

         WC()->session->set('AUTH_TOKEN_KEY', base64_encode($auth_token));
         WC()->session->__unset('user_login_on_checkout');
      }
   }
}
add_action('wp_login', 'autoLoginCurrentUserToFrontend', 10, 2);

add_filter('wc_points_rewards_event_description', 'supportInstantPrizePointsEvent', 20, 3);

function supportInstantPrizePointsEvent($event_description, $event_type, $event)
{

   if ($event_type == 'order-placed-instant-prize') {
      $event_description = "Points earned for instant win competition purchase";
   }

   if ($event_type == 'order-placed-reward-prize') {
      $event_description = "Points earned for Reward win competition purchase";
   }

   if ($event_type == 'manually-add-points') {
      $event_description = "Points earned for Manually add tickets";
   }

   if ($event_type == 'main-competition-point-prize') {
      $event_description = "Points earned by winning the Main Competition";
   }

   if ($event_type == 'manually-add-points-on-competition-winning') {
      $event_description = "Points Adjusted by winning the Main Competition Tickets";
   }

   return $event_description;
}

add_filter('woocommerce_email_subject_customer_on_hold_order', 'change_admin_email_subject', 1, 2);
add_filter('woocommerce_email_subject_customer_processing_order', 'change_admin_email_subject', 1, 2);
// add_filter('woocommerce_email_subject_customer_completed_order', 'change_admin_email_subject', 10, 2);


function change_admin_email_subject($subject, $order)
{

   $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

   error_log("Customer on-hold order email triggered.");

   $subject = sprintf('Your CGG Order is now complete! 💚 - Order: %s', $order->id);

   return $subject;
}





function register_new_custom_order_statuses()
{
   register_post_status(
      'wc-admin-assigned',
      array(
         'label' => _x('Admin Assigned', 'Order status', 'woocommerce'),
         'public' => true,
         'exclude_from_search' => false,
         'show_in_admin_all_list' => true,
         'show_in_admin_status_list' => true,
         'label_count' => _n_noop('Admin Assigned <span class="count">(%s)</span>', 'Admin Assigned <span class="count">(%s)</span>')
      )
   );

   register_post_status(
      'wc-admin-comp-win',
      array(
         'label' => _x('Competition Winning Tickets', 'Order status', 'woocommerce'),
         'public' => true,
         'exclude_from_search' => false,
         'show_in_admin_all_list' => true,
         'show_in_admin_status_list' => true,
         'label_count' => _n_noop('Competition Winning Tickets <span class="count">(%s)</span>', 'Competition Winning Tickets <span class="count">(%s)</span>')
      )
   );
}

add_action('init', 'register_new_custom_order_statuses');

add_filter('wc_order_statuses', 'register_new_custom_wc_order_statuses');
function register_new_custom_wc_order_statuses($order_statuses)
{
   $order_statuses['wc-admin-assigned'] = _x('Admin Assigned', 'Order status', 'woocommerce');
   $order_statuses['wc-admin-comp-win'] = _x('Competition Winning Tickets', 'Order status', 'woocommerce');
   return $order_statuses;
}

function check_featured_image($post_id, $post, $update)
{
   if ($post->post_type !== 'winners') {
      return;
   }

   if ($post->post_status !== 'publish') {
      return;
   }

   if (!has_post_thumbnail($post_id)) {

      remove_action('save_post', 'check_featured_image', 10);

      wp_update_post(
         array(
            'ID' => $post_id,
            'post_status' => 'draft'
         )
      );

      add_action('save_post', 'check_featured_image', 10, 3);

      set_transient("admin_notice_featured_image_{$post_id}", true, 45);
   }
}
add_action('save_post', 'check_featured_image', 10, 3);

function featured_image_admin_notice()
{
   global $post;

   if (!empty($post) && get_transient("admin_notice_featured_image_{$post->ID}")) {
?>
      <div class="notice notice-error is-dismissible">
         <p><?php _e('You must set a featured image before publishing.'); ?></p>
      </div>
<?php
      delete_transient("admin_notice_featured_image_{$post->ID}");
   }
}
add_action('admin_notices', 'featured_image_admin_notice');

register_rest_field(
   'winners',
   'metadata',
   array(
      'get_callback' => function ($data) {
         return get_post_meta($data['id'], '', '');
      },
   )
);

add_filter("woocommerce_coupon_discount_amount_html", "change_woocommerce_coupon_discount_amount_html");

function change_woocommerce_coupon_discount_amount_html($discount_amount_html)
{

   if (substr($discount_amount_html, 0, 1) === '-') {
      return substr($discount_amount_html, 1);
   }

   return $discount_amount_html;
}


function assignTicketPrizeToUser($intant_prize, $order_id)
{
   global $wpdb;

   $user_id = $order_id;
   $instant_win_ids_tickets = [];

   $entry = $wpdb->get_row("SELECT comp.*, COUNT(t.id) AS total_ticket_sold, SUM(CASE WHEN t.user_id = $user_id THEN 1 ELSE 0 END) AS
        total_ticket_sold_by_user FROM {$wpdb->prefix}competitions comp
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1
        WHERE comp.id = '" . $intant_prize['competition_prize'] . "'", ARRAY_A);
   //echo "<pre>"; print_r($intant_prize) ; echo "</pre> <br>";
   //echo "entry>>> <pre>"; print_r($entry) ; echo "</pre>";;
   $user = get_userdata($user_id);

   $user_meta = get_user_meta($user->ID);

   // error_log("assignTicketPrizeToUser Assigning prize to user: " . print_r($intant_prize, true));
   // error_log("assignTicketPrizeToUser Order ID: " . $order_id);
   // error_log("Competition and ticket data: " . print_r($entry, true));


   $allowed_fields = array(
      'billing_first_name',
      'billing_last_name',
      'billing_address_1',
      'billing_city',
      'billing_state',
      'billing_postcode',
      'billing_country',
      'billing_email',
      'billing_address_2',
      'billing_phone'
   );

   $billing_address = [];

   foreach ($allowed_fields as $fieldname) {

      if (isset($user_meta[$fieldname]) && !empty($user_meta[$fieldname][0])) {

         $billing_address[str_replace("billing_", "", $fieldname)] = $user_meta[$fieldname][0];
      }
   }

   $order = wc_create_order(
      array(
         'customer_id' => $user_id,
      )
   );

   $order_id = $order->get_id();

   $order->add_product(get_product($entry['competition_product_id']), $intant_prize['prize_tickets']);

   $order->set_address($billing_address, 'billing');

   $order->calculate_totals();

   $order->update_status("wc-admin-comp-win", 'Competition Ticket Prize Winner', TRUE);

   $purchase_date = date("Y-m-d");

   $extra_tickets = 0;

   $total_ticket_sold_by_user = $entry['total_ticket_sold_by_user'];
   $price_per_ticket = $entry['price_per_ticket'];


   $prize_total_tickets = $intant_prize['prize_total_tickets'];

   $qty = 0;

   $tickets_left = $entry['max_ticket_per_user'] - $total_ticket_sold_by_user;

   $sendNotification = false;

   $user_points = 0;
   // error_log("tickets_left Order ID: " . $tickets_left);


   if ($tickets_left == 0) {
      $user_points = ($prize_total_tickets) * ($price_per_ticket *  100);

      $email_data = [
         'title' => 'Over allocation is going to be assigned to the account as points',
         'type' => 'PointsAllocation',
         'comp_title' => $entry['title'],
         'ticket_number' => 'Points',
         'instant_id' => 'Points',
         'competition_id' => $entry['id'],
         'order_id' => 'Points',

      ];

      $subject = "You’re an instant winner! - Carp Gear Giveaways";
      $mailer = WC()->mailer();
      $content = get_custom_email_html($mailer, $email_data, $subject);
      $headers = "Content-Type: text/html\r\n";
      $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);

      WC_Points_Rewards_Manager::increase_points($user_id, $user_points, 'manually-add-points-on-competition-winning', null, $order_id);

      $prize_total_tickets = 0;
   }

   if ($tickets_left > 0 &&  $tickets_left < $prize_total_tickets) {
      $points_allocation = $prize_total_tickets -  $tickets_left;
      $user_points = ($points_allocation) * ($price_per_ticket *  100);

      $email_data = [
         'title' => 'Over allocation is going to be assigned to the account as points',
         'type' => 'PointsAllocation',
         'comp_title' => $entry['title'],
         'ticket_number' => 'Points',
         'instant_id' => 'Points',
         'competition_id' => $entry['id'],
         'order_id' => 'Points',

      ];

      $subject = "You’re an instant winner! - Carp Gear Giveaways";
      $mailer = WC()->mailer();
      $content = get_custom_email_html($mailer, $email_data, $subject);
      $headers = "Content-Type: text/html\r\n";
      $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);

      WC_Points_Rewards_Manager::increase_points($user_id, $user_points, 'manually-add-points-on-competition-winning', null, $order_id);
      $prize_total_tickets = $tickets_left;
   }


   if ($prize_total_tickets > 0) {

      $query = $wpdb->prepare(
         "UPDATE {$wpdb->prefix}competition_tickets AS tickets 
                INNER JOIN ( SELECT id FROM {$wpdb->prefix}competition_tickets WHERE competition_id IN 
                ( SELECT id FROM {$wpdb->prefix}competitions WHERE id = %d ) 
                and is_purchased <> 1 and user_id IS NULL ORDER BY RAND() LIMIT %d ) 
                AS subquery ON tickets.id = subquery.id SET tickets.is_purchased = 1, 
                tickets.user_id = %d, tickets.purchased_on = %s, tickets.order_id = %d",
         $entry['id'],
         $prize_total_tickets,
         $user_id,
         $purchase_date,
         $order_id
      );

      $wpdb->query($query);

      $params = [$entry['id']];

      $params[] = $order_id;

      $params[] = $user_id;

      $query = $wpdb->prepare(
         "SELECT {$wpdb->prefix}comp_instant_prizes_tickets.*, {$wpdb->prefix}comp_instant_prizes.title,
            {$wpdb->prefix}comp_instant_prizes.type,{$wpdb->prefix}comp_instant_prizes.value,{$wpdb->prefix}comp_instant_prizes.quantity,
            {$wpdb->prefix}comp_instant_prizes.image, {$wpdb->prefix}competitions.title as comp_title FROM `{$wpdb->prefix}comp_instant_prizes_tickets`
            INNER JOIN {$wpdb->prefix}competitions ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}comp_instant_prizes_tickets.competition_id
            INNER JOIN {$wpdb->prefix}competition_tickets ON {$wpdb->prefix}competition_tickets.competition_id = {$wpdb->prefix}comp_instant_prizes_tickets.competition_id
            INNER JOIN {$wpdb->prefix}comp_instant_prizes ON {$wpdb->prefix}comp_instant_prizes.id = {$wpdb->prefix}comp_instant_prizes_tickets.instant_id
            WHERE {$wpdb->prefix}competition_tickets.ticket_number = {$wpdb->prefix}comp_instant_prizes_tickets.ticket_number
            AND {$wpdb->prefix}competitions.enable_instant_wins = 1
            AND {$wpdb->prefix}competition_tickets.competition_id = %d 
            AND {$wpdb->prefix}competition_tickets.order_id = %d 
            AND {$wpdb->prefix}competition_tickets.is_purchased = 1
            AND {$wpdb->prefix}competition_tickets.user_id = %d
            AND {$wpdb->prefix}comp_instant_prizes_tickets.user_id IS NULL",
         $params
      );

      $prize_results = $wpdb->get_results($query, ARRAY_A);

      error_log('prize result instant win' . print_r($prize_results, true));




      if (!empty($prize_results)) {



         foreach ($prize_results as $p_row) {
            error_log("Second Level Assigning prize data " . print_r($p_row, true));

            // error_log("Before appending: " . print_r($instant_win_ids_tickets, true));
            $instant_win_ids_tickets[] = $p_row['id'];
            // error_log("After appending: " . print_r($instant_win_ids_tickets, true));
            // error_log("Current prize data ID: " . $p_row['id']);


            $is_admin_declare_winner = 0;

            if ($p_row['type'] == 'Tickets') {

               //$mailSent = assignTicketPrizeToUser($p_row , $user_id);
               $subject = "You’re an instant winner! - Carp Gear Giveaways";
            } else {

               if ($p_row['type'] == 'Points') {

                  WC_Points_Rewards_Manager::increase_points($user_id, $p_row['value'], 'order-placed-instant-prize', null, $order->id);

                  $subject = "You’re an instant winner! - Carp Gear Giveaways";

                  $is_admin_declare_winner = 1;
               } else {

                  $subject = "You’re an instant winner! - Carp Gear Giveaways";
               }
            }

            $mailSent = 0;

            $mailer = WC()->mailer();

            $content = get_custom_email_html($mailer, $p_row, $subject, $user_id);

            $headers = "Content-Type: text/html\r\n";

            $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);

            $updated_at = gmdate("Y-m-d H:i:s");

            $wpdb->query(
               $wpdb->prepare(
                  "UPDATE {$wpdb->prefix}comp_instant_prizes_tickets 
                   SET is_admin_declare_winner = %d, user_id = %d, mail_sent = %d, updated_at = %s  
                   WHERE competition_id = %d AND ticket_number = %d AND instant_id = %d",
                  $is_admin_declare_winner,
                  $user_id,
                  $mailSent,
                  $updated_at,
                  $p_row['competition_id'],
                  $p_row['ticket_number'],
                  $p_row['instant_id'],
               )
            );

            // Trigger the social proof notification
            // if ($p_row['type'] == 'Prize' && $mailSent) {
            do_action('instant_win_notification', array(
               'user_id'    => $user_id,
               'comp_title' => $p_row['title'],
               'image_url'  => wp_get_attachment_url($p_row['image']),
            ));
            // }
         }

         // error_log("instant_win_ids_tickets to user: " . print_r($instant_win_ids_tickets, true));
      }
   }

   // Final logging and return
   error_log("instant_win_ids to user: " . print_r($instant_win_ids_tickets, true));
   return $instant_win_ids_tickets;
}

function get_competition_email_content($mailer, $email_data, $email_heading = false)
{


   // error_log("Sending email data: " . print_r($email_data, true));
   // error_log("******************************************************");



   return wc_get_template_html(
      $template,
      array(
         'email_heading' => $email_heading,
         'sent_to_admin' => false,
         'plain_text' => false,
         'email' => $mailer,
         'title' => $email_data['title'],
         'type' => $email_data['type'],
         'quantity' => $email_data['quantity'],
         'value' => $email_data['value'],
         'image' => $email_data['image'],
         'comp_title' => $email_data['comp_title'],
         'ticket_number' => $email_data['ticket_number'],
         'prize_id' => $email_data['instant_id'],
         'competition_id' => $email_data['competition_id'],
         'order' => $email_data['order_id']
      )
   );
}

function send_instant_win_to_firebase($args)
{
   $firebase_url = "https://instantwin-notifications-default-rtdb.firebaseio.com/instant-wins.json?auth=48xcNRclEVjllKoLLJct9GLuASHUcs3JhMfS36Ee"; // Your Firebase Realtime Database URL
   $firebase_auth_key = "48xcNRclEVjllKoLLJct9GLuASHUcs3JhMfS36Ee"; // Your Firebase Database Secret

   // Prepare the data to be sent
   $data = array(
      'user' => $args['user_id'],
      'prize' => $args['comp_title'],
      'image' => $args['image_url'],
      'timestamp' => time(),
   );

   // Make the request to Firebase using wp_remote_post
   $response = wp_remote_post($firebase_url, array(
      'body'    => json_encode($data),
      'headers' => array(
         'Content-Type'  => 'application/json',
         //   'Authorization' => 'key=' . $firebase_auth_key,
      ),
   ));

   if (is_wp_error($response)) {
      error_log('Firebase push notification failed: ' . $response->get_error_message());
   } else {
      error_log('Firebase push notification sent successfully');
   }
}

// Hook to trigger the instant win notification
add_action('instant_win_notification', 'send_instant_win_to_firebase', 10, 1);





function generate_custom_thumbnail_for_slider($attachment_id)
{
   $upload_dir = wp_upload_dir();
   $time = current_time('mysql');
   $m = substr($time, 5, 2);

   // Define the base homepage thumb directory
   $base_thumb_dir = $upload_dir['basedir'] . '/thumbs/slider';

   // Create the base directory if it doesn't exist
   if (!file_exists($base_thumb_dir)) {
      wp_mkdir_p($base_thumb_dir);
   }


   // Create the month subdirectory
   $month_thumb_dir = $base_thumb_dir . '/' . $m . '/';
   if (!file_exists($month_thumb_dir)) {
      wp_mkdir_p($month_thumb_dir);
   }

   $file_path = get_attached_file($attachment_id);

   if ($file_path && file_is_valid_image($file_path)) {
      $image = wp_get_image_editor($file_path);

      if (!is_wp_error($image)) {
         $image->resize(650, 650, true);

         $filename =  basename($file_path);
         $thumb_path = $month_thumb_dir . $filename;

         $image->save($thumb_path);

         //  return wp_get_upload_dir()['baseurl'] . '/thumbs/slider/' . $filename;
      }
   }

   return false;
}



function generate_custom_thumbnail($attachment_id)
{
   $upload_dir = wp_upload_dir();
   $time = current_time('mysql'); // Current time for directory structure
   $m = substr($time, 5, 2); // Extract the month from the current date

   // Define the base thumbnail directories
   $base_thumb_dir = $upload_dir['basedir'] . '/thumbs/home';
   $thumb_dir_list = $upload_dir['basedir'] . '/thumbs/list';

   // Create base directories if they don't exist
   if (!file_exists($base_thumb_dir)) {
      wp_mkdir_p($base_thumb_dir);
   }

   if (!file_exists($thumb_dir_list)) {
      wp_mkdir_p($thumb_dir_list);
   }

   // Create the month subdirectories
   $month_thumb_dir = $base_thumb_dir . '/' . $m . '/';
   if (!file_exists($month_thumb_dir)) {
      wp_mkdir_p($month_thumb_dir);
   }

   $month_list_dir = $thumb_dir_list . '/' . $m . '/';
   if (!file_exists($month_list_dir)) {
      wp_mkdir_p($month_list_dir);
   }

   // Get the original file path of the attachment
   $file_path = get_attached_file($attachment_id);

   // Check if the file is a valid image
   if ($file_path && file_is_valid_image($file_path)) {
      $image = wp_get_image_editor($file_path);
      $image1 = wp_get_image_editor($file_path);

      if (!is_wp_error($image)) {
         // Resize the image to desired dimensions
         $image->set_quality(90);
         $image1->set_quality(90);

         $image->resize(390, 400, false); // Thumbnail for home
         $image1->resize(490, 500, false); // Thumbnail for list

         $filename = basename($file_path);
         $thumb_path_list = $month_list_dir . $filename;
         $thumb_path_home = $month_thumb_dir . $filename;

         // Save the thumbnails to the respective directories
         $image1->save($thumb_path_list);
         $image->save($thumb_path_home);

         return [
            'home_thumb' => wp_get_upload_dir()['baseurl'] . '/thumbs/home/' . $m . '/' . $filename,
            'list_thumb' => wp_get_upload_dir()['baseurl'] . '/thumbs/list/' . $m . '/' . $filename
         ];
      }
   }

   return false;
}

// Hook to generate thumbnail when media is updated
add_action('wp_update_attachment_metadata', 'generate_custom_thumbnail_on_update', 10, 2);

function generate_custom_thumbnail_on_update($metadata, $attachment_id)
{
   // Call the thumbnail generation function
   $thumb_urls = generate_custom_thumbnail($attachment_id);

   if ($thumb_urls) {
      // If needed, you can store or return the thumbnail URLs
      update_post_meta($attachment_id, '_custom_thumb_home', $thumb_urls['home_thumb']);
      update_post_meta($attachment_id, '_custom_thumb_list', $thumb_urls['list_thumb']);
   }

   return $metadata; // Return the metadata to complete the process
}

// AJAX handler for thumbnail generation (manual trigger via AJAX if needed)
add_action('wp_ajax_generate_thumbnail', 'ajax_generate_thumbnail');
function ajax_generate_thumbnail()
{
   if (!isset($_POST['attachment_id'])) {
      wp_send_json_error('No attachment ID provided');
   }

   $attachment_id = intval($_POST['attachment_id']);
   $thumb_urls = generate_custom_thumbnail_for_slider($attachment_id);

   if ($thumb_urls) {
      wp_send_json_success(['thumb_urls' => $thumb_urls]);
   } else {
      wp_send_json_error('Failed to generate thumbnail');
   }
}

add_action('woocommerce_checkout_create_order', 'set_default_shipping_method', 20, 1);

function set_default_shipping_method($order)
{
   // Replace 'dpd_uk' with the shipping method ID for 'DPD UK'
   $shipping_method_id = 'dpd_uk';

   $available_shipping_methods = WC()->shipping()->load_shipping_methods();

   // Check if the default shipping method is available
   if (array_key_exists($shipping_method_id, $available_shipping_methods)) {
      $shipping = new WC_Order_Item_Shipping();
      $shipping->set_method_title($available_shipping_methods[$shipping_method_id]->get_method_title());
      $shipping->set_method_id($shipping_method_id);
      $shipping->set_total(0); // Set shipping cost if needed

      // Add the shipping method to the order
      $order->add_item($shipping);
   }
}


add_action('woocommerce_product_get_date_on_sale_to', 'extend_same_day_sale_end_time', 10, 2);
function extend_same_day_sale_end_time($date_on_sale_to, $product)
{
   //  error_log("Filter Triggered for Product: " . $product->get_name());

   if ($date_on_sale_to) {
      $sale_start = $product->get_date_on_sale_from();
      if ($sale_start && $sale_start->date('Y-m-d') === $date_on_sale_to->date('Y-m-d')) {
         // Extend the sale end time to 23:59:59 of the same day
         $adjusted_time = new WC_DateTime($date_on_sale_to->date('Y-m-d 23:59:59'));
         // error_log("Adjusted Sale End Time for Product: " . $product->get_name() . " - " . $adjusted_time->date('Y-m-d H:i:s'));
         return $adjusted_time;
      }
   }

   //  error_log("Original Sale End Time for Product: " . $product->get_name() . " - " . ($date_on_sale_to ? $date_on_sale_to->date('Y-m-d H:i:s') : 'None'));
   return $date_on_sale_to;
}

add_action('woocommerce_before_calculate_totals', 'update_cart_item_prices_based_on_sale_time');
function update_cart_item_prices_based_on_sale_time($cart)
{
   if (is_admin() && !defined('DOING_AJAX')) {
      return;
   }

   foreach ($cart->get_cart() as $cart_item) {
      $product = $cart_item['data'];

      if ($product->is_on_sale()) {
         $sale_price = $product->get_sale_price();
         $regular_price = $product->get_regular_price();

         if ($sale_price && $regular_price) {
            // Set the price to the sale price
            $cart_item['data']->set_price($sale_price);
         }
      }
   }
}

add_filter('woocommerce_product_get_price', 'apply_correct_price_based_on_sale_time', 10, 2);
function apply_correct_price_based_on_sale_time($price, $product)
{
   if ($product->is_on_sale()) {
      $current_date = current_time('Y-m-d');
      $current_time = current_time('H:i');

      $sale_start = $product->get_date_on_sale_from();
      $sale_end = $product->get_date_on_sale_to();

      if ($sale_start && $sale_end) {
         if ($current_date >= $sale_start->date('Y-m-d') && $current_date <= $sale_end->date('Y-m-d')) {
            // Return sale price
            return $product->get_sale_price();
         }
      }
   }

   return $price; // Default price
}


add_action('woocommerce_before_calculate_totals', 'debug_cart_item_prices');
function debug_cart_item_prices($cart)
{
   foreach ($cart->get_cart() as $cart_item) {
      $product = $cart_item['data'];
      $price = $product->get_price();
      //   error_log("Cart Product: " . $product->get_name() . ", Price: $price");
   }
}


add_filter('woocommerce_product_is_on_sale', 'force_same_day_sales_fix', 10, 2);
function force_same_day_sales_fix($is_on_sale, $product)
{
   $current_date = current_time('Y-m-d');
   $current_time = current_time('H:i:s');

   $sale_start_date = $product->get_date_on_sale_from() ? $product->get_date_on_sale_from()->date('Y-m-d') : null;
   $sale_start_time = $product->get_date_on_sale_from() ? $product->get_date_on_sale_from()->date('H:i:s') : null;

   $sale_end_date = $product->get_date_on_sale_to() ? $product->get_date_on_sale_to()->date('Y-m-d') : null;
   $sale_end_time = $product->get_date_on_sale_to() ? $product->get_date_on_sale_to()->date('H:i:s') : null;

   if ($sale_start_date === $current_date && $sale_end_date === $current_date) {
      if ($current_time >= $sale_start_time && $current_time <= $sale_end_time) {
         return true;
      }
      return false;
   }

   return $is_on_sale; // Default for other dates
}


add_action('woocommerce_before_calculate_totals', 'debug_sale_date_logic');
function debug_sale_date_logic()
{
   foreach (WC()->cart->get_cart() as $cart_item) {
      $product = $cart_item['data'];
      $current_date = current_time('Y-m-d H:i:s');
      $sale_start_date = $product->get_date_on_sale_from() ? $product->get_date_on_sale_from()->date('Y-m-d H:i:s') : 'None';
      $sale_end_date = $product->get_date_on_sale_to() ? $product->get_date_on_sale_to()->date('Y-m-d H:i:s') : 'None';

      //   error_log("Product: {$product->get_name()}, Current Date: $current_date, Sale Start: $sale_start_date, Sale End: $sale_end_date");
   }
}


function sendemail_to_user($order)
{



   error_log("Order email sent+++");

   // Define the email template
   $template = 'emails/customer-processing-order.php';

   // Define the subject and email heading
   $subject = sprintf('Your CGG Order is now complete! 💚 - Order: %s', $order->get_id());
   $email_heading = sprintf('Your CGG Order is now complete! 💚 - Order: %s', $order->get_id());

   // Get the WooCommerce mailer instance
   $mailer = WC()->mailer();

   // Generate the email content
   $email_content = wc_get_template_html(
      $template,
      array(
         'email_heading' => $email_heading,
         'sent_to_admin' => false,
         'plain_text' => false,
         'order' => $order,
      )
   );

   // Send the email
   return wc_mail(
      $order->get_billing_email(), // Recipient's email
      $subject,                    // Subject
      $email_content,              // Email body
      array('Content-Type: text/html; charset=UTF-8') // Set the email content type to HTML
   );
}




function check_cart_after($order_id)
{


   global $wp, $wpdb;

   $current_user = wp_get_current_user();

   $order_id = intval(str_replace('checkout/order-received/', '', $wp->request));



   $order = wc_get_order($order_id);


   if (!$order) {
      error_log("Order $order_id not found.");
      return;
   }

   // Check if the order is already Processing
   if ($order->get_status() === 'processing') {
      error_log("Order $order_id is already Processing. Assigning tickets...");
      assign_tickets_to_user($order_id, $order); // Assign tickets immediately
   } else {
      // If the order is not yet Processing, wait or defer ticket assignment
      wait_for_order_processing($order_id, $order);
   }


   // // If order does not exist or is not paid, redirect to cart and stop execution.
   // if ((!$order || !$order->is_paid() ) && $order->get_payment_method() !== 'cashflow') {
   //    wp_redirect(wc_get_cart_url()); // Redirect to cart if payment failed
   //    exit;
   // }


   $orderData = $order->get_data();

   $orderItems = $order->get_items();

   $userEmail = $current_user->user_email;

   $order_user_id = $order->get_user_id();

   if (!$order_user_id)
      $order_user_id = $current_user->ID;

   $current_spending = get_user_meta($order_user_id, 'current_spending', true);

   if ($current_spending) {
      $cart_total_amount = $order->get_total();
      $total = (float) $current_spending + (float) $cart_total_amount;
      update_user_meta($order_user_id, 'current_spending', $total);
   } else {
      $cart_total_amount = $order->get_total();
      update_user_meta($order_user_id, 'current_spending', $cart_total_amount);
   }

   if (
      isset($orderData['billing']) && !empty($orderData['billing']) &&
      isset($orderData['billing']['email']) && !empty($orderData['billing']['email'])
   ) {

      $userEmail = $orderData['billing']['email'];
   }

   $productIds = [];

   foreach ($orderItems as $orderItem) {

      $productIds[] = $orderItem->get_product_id();
   }

   if (!$order || !$order->is_paid()) {
      //return;
   }

   $auth_token = $current_user->get("user_auth_token");

   if (empty($auth_token)) {

      $auth_token = wp_generate_password(64, false);

      $wpdb->query(
         $wpdb->prepare(
            "UPDATE $wpdb->users 
					SET user_auth_token = %s 
					WHERE ID = %d",
            $auth_token,
            $current_user->ID
         )
      );
   }

   $productIds = implode(',', array_map('intval', $productIds));

   $query = "SELECT id, category FROM {$wpdb->prefix}competitions WHERE competition_product_id IN ($productIds)";

   $com_result = $wpdb->get_results($query, ARRAY_A);

   $compCategories = [];

   $compIds = [];

   $instant_win_ids = [];

   error_log('com_result' . print_r($com_result, true));

   if (!empty($com_result)) {

      foreach ($com_result as $row) {
         $compCategories[] = $row['category'];
         $compIds[] = $row['id'];

         $id_placeholders = implode(', ', array_fill(0, count($compIds), '%s'));
      }

      $params = $compIds;

      $params[] = $order_id;

      $params[] = $current_user->ID;

      $instant_win = false;

      $query = $wpdb->prepare(
         "SELECT {$wpdb->prefix}comp_instant_prizes_tickets.*, {$wpdb->prefix}comp_instant_prizes.title, {$wpdb->prefix}competitions.prize_tickets,   {$wpdb->prefix}comp_instant_prizes.competition_prize ,
            {$wpdb->prefix}comp_instant_prizes.type,{$wpdb->prefix}comp_instant_prizes.value,{$wpdb->prefix}comp_instant_prizes.prize_value,{$wpdb->prefix}comp_instant_prizes.quantity, {$wpdb->prefix}comp_instant_prizes.prize_total_tickets,
            {$wpdb->prefix}comp_instant_prizes.image, {$wpdb->prefix}competitions.title as comp_title, {$wpdb->prefix}competition_tickets.order_id FROM `{$wpdb->prefix}comp_instant_prizes_tickets`
            INNER JOIN {$wpdb->prefix}competitions ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}comp_instant_prizes_tickets.competition_id
            INNER JOIN {$wpdb->prefix}competition_tickets ON {$wpdb->prefix}competition_tickets.competition_id = {$wpdb->prefix}comp_instant_prizes_tickets.competition_id
            INNER JOIN {$wpdb->prefix}comp_instant_prizes ON {$wpdb->prefix}comp_instant_prizes.id = {$wpdb->prefix}comp_instant_prizes_tickets.instant_id
            WHERE {$wpdb->prefix}competition_tickets.ticket_number = {$wpdb->prefix}comp_instant_prizes_tickets.ticket_number
            AND {$wpdb->prefix}competitions.enable_instant_wins = 1
            AND {$wpdb->prefix}competition_tickets.competition_id IN ($id_placeholders) 
            AND {$wpdb->prefix}competition_tickets.order_id = %d 
            AND {$wpdb->prefix}competition_tickets.is_purchased = 1
            AND {$wpdb->prefix}competition_tickets.user_id = %d
            AND {$wpdb->prefix}comp_instant_prizes_tickets.user_id IS NULL",
         $params
      );

      error_log('+++query++++' . print_r($query, true));

      $prize_results = $wpdb->get_results($query, ARRAY_A);

      error_log('+++prize_results++++' . print_r($prize_results, true));


      if (!empty($prize_results)) {

         $instant_win = true;



         foreach ($prize_results as $p_row) {


            error_log('+++p_row++++' . print_r($p_row, true));


            $instant_win_ids[] = $p_row['id'];

            if ($p_row['type'] == 'Tickets') {

               // error_log("First Level if user get ticekts " . print_r($p_row, true));
               // error_log("irst LevelOrder ID: " . $order_user_id);
               $InstatnWinData = assignTicketPrizeToUser($p_row, $order_user_id);

               // error_log("+++++++++++tthis is value ++++++++++++++++++ " . print_r($InstatnWinData, true));
               // error_log("++++need to merge++++++++++++++ " . print_r($instant_win_ids, true));
               foreach ($InstatnWinData as $value) {
                  $instant_win_ids[] = $value;
               }
               //   error_log("++++nnewwwwwwwwwwwe++++++++++++++ " . print_r($instant_win_ids, true));

               $subject = "You’re an instant winner! - Carp Gear Giveaways"; // this email template is used when sending tickets email
            } else {


               if ($p_row['type'] == 'Points') {

                  WC_Points_Rewards_Manager::increase_points($order_user_id, $p_row['value'], 'order-placed-instant-prize', null, $order_id);

                  $subject = "You’re an instant winner! - Carp Gear Giveaways";
               } else {

                  $subject = "You’re an instant winner! - Carp Gear Giveaways";
                  $subject = preg_replace('/\s+/', ' ', $subject); // Replace multiple spaces with a single space
               }
            }

            $mailSent = 0;

            $mailer = WC()->mailer();

            $content = get_custom_email_html($mailer, $p_row, $subject, $order_user_id);

            $headers = "Content-Type: text/html\r\n";

            $mailSent = $mailer->send($userEmail, $subject, $content, $headers);

            $updated_at = gmdate("Y-m-d H:i:s");

            $wpdb->query(
               $wpdb->prepare(
                  "UPDATE {$wpdb->prefix}comp_instant_prizes_tickets 
                      SET is_admin_declare_winner = 0, prize_value = %d , user_id = %d, mail_sent = %d, updated_at = %s  
                      WHERE id = %d",
                  $p_row['prize_value'],
                  $current_user->ID,
                  $mailSent,
                  $updated_at,
                  $p_row['id']
               )
            );

            do_action('instant_win_notification', array(
               'user_id' => $current_user->display_name,
               'comp_title' => $p_row['comp_title'],
               'image_url'  => $p_row['image'],

            ));
         }
      }
   }

   $merged_array = array_merge($instant_win_ids, $InstatnWinData);

   // error_log("Final  data InstatnWinData return from tickets " . print_r($instant_win_ids, true));      

   // error_log("Finale Data InstatnWinData return from tickets " . print_r($instant_win, true));

   $orderData = [
      'order_number' => $orderData['id'],
      'order_created' => $order->get_date_created(),
      'token' => $auth_token,
      'reset_cart_header' => true,
      'name' => $current_user->display_name,
      'email' => $current_user->user_email,
      'categories' => implode(",", array_unique($compCategories)),
      'comps' => implode(",", $compIds),
      'instant_winner' => $instant_win,
      'instant_wins' => !empty($instant_win_ids) ? implode(",", $instant_win_ids) : ""
   ];

   $orderData = base64_encode(json_encode($orderData));

   sendemail_to_user($order);

   WC()->session->__unset('check_comp_queries');
   WC()->cart->empty_cart();


   wp_redirect( FRONTEND_URL. 'confirmation?order=' . $orderData);
   exit;
}

function wait_for_order_processing($order_id, $order)
{
   $max_retries = 20; // Number of retries
   $retry_interval = 2; // Time in seconds to wait between retries



   for ($attempt = 0; $attempt < $max_retries; $attempt++) {
      // Refresh the order object to get the updated status
      $order = wc_get_order($order_id);
      //  error_log('++++++++$order->get_status()++++++++'.print_r($order->get_status(), true));

      if ($order->get_status() === 'processing'  || $order->get_status() === 'completed') {
         error_log("Order $order_id transitioned to Processing. Assigning tickets...");
         assign_tickets_to_user($order_id, $order); // Assign tickets when Processing
         return; // Exit once tickets are assigned
      }

      if ($attempt == 10) {
         error_log("Order $order_id transitioned to Processing 10th attempt. Assigning tickets...");
         assign_tickets_to_user($order_id, $order); // Assign tickets when Processing
         return; // Exit once tickets are assigned

      }

      // Log the waiting status
      error_log("Waiting for order $order_id to transition to Processing. Attempt $attempt...");
      sleep($retry_interval); // Wait for the next retry
   }

   // If order never transitions to Processing within retries
   error_log("Order $order_id did not transition to Processing within allowed time.");
}

// Hook to order-received template (usually tied to 'woocommerce_thankyou')
add_action('woocommerce_thankyou', 'check_cart_after', 10, 1);


function exclude_woocommerce_admin_script()
{
   // Get the current admin page URL
   $current_url = $_SERVER['REQUEST_URI'];

   // Define the target URLs where the script should be excluded
   $exclude_pages = [
      'page=woocommerce-points-and-rewards&tab=log',
      'page=woocommerce-points-and-rewards&tab=manage',
      'page=woocommerce-points-and-rewards&tab=settings',
      'page=woocommerce-points-and-rewards'
   ];

   // Check if the current URL matches any of the target URLs
   foreach ($exclude_pages as $page) {
      if (strpos($current_url, $page) !== false) {
         // Deregister the WooCommerce admin script
         wp_dequeue_script('woocommerce_admin');
         wp_deregister_script('woocommerce_admin');
         break;
      }
   }
}
add_action('admin_enqueue_scripts', 'exclude_woocommerce_admin_script', 100);

function dequeue_woocommerce_admin_script_in_admin($hook)
{
   // List of admin pages where we want to remove the script
   $excluded_pages = array(
      'index.php',         // Dashboard
      'update-core.php',   // Update Core page
   );

   if (in_array($hook, $excluded_pages)) {
      wp_dequeue_script('wc-reports');
      wp_deregister_script('wc-reports');
   }
}

add_action('admin_enqueue_scripts', 'dequeue_woocommerce_admin_script_in_admin', 100);

function enqueue_custom_scripts()
{
   wp_enqueue_script('jquery');
   wp_enqueue_script('custom-js', get_template_directory_uri() . '/custom.js', array('jquery'), null, true);

   
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');




// Filter attachment URLs to use S3
add_filter('wp_get_attachment_url', function($url, $post_id) {
    $uploads = wp_get_upload_dir();
    $local_base = $uploads['baseurl'];  // e.g. "https://your-site.com/wp-content/uploads"
    if (strpos($url, $local_base) === 0) {
        // Replace local base URL with S3 base URL
        $url = S3_UPLOADS_BASEURL . substr($url, strlen($local_base));
    }
    return $url;
}, 10, 2);

// (Optional: ensure thumbnails and other image sizes use S3 as well)
add_filter('wp_get_attachment_image_src', function($image) {
    if (!empty($image[0])) {
        $uploads = wp_get_upload_dir();
        $local_base = $uploads['baseurl'];
        if (strpos($image[0], $local_base) === 0) {
            $image[0] = S3_UPLOADS_BASEURL . substr($image[0], strlen($local_base));
        }
    }
    return $image;
}, 10);

// 2. Filter post content to rewrite any remaining local image URLs to S3
add_filter('the_content', function($content) {
    $uploads = wp_get_upload_dir();
    $local_base = $uploads['baseurl'];
    // Replace occurrences of local uploads URL with S3 uploads URL
    return str_replace($local_base, S3_UPLOADS_BASEURL, $content);
});


function enqueue_custom_checkout_script() {
   
       wp_enqueue_script(
           'custom-url-js',
           plugin_dir_url(__FILE__) . '../competitions/_inc/custom_url.js', // Adjust path to match plugin directory
           array('jquery'),
           filemtime(plugin_dir_path(__FILE__) . '../competitions/_inc/custom_url.js'), // Auto versioning
           true // Load in footer
       );

       // Pass PHP variables to JavaScript
       wp_localize_script('custom-url-js', 'wpData', array(
           'base_wp_url' => site_url() // or home_url()
       ));
   
}
add_action('wp_enqueue_scripts', 'enqueue_custom_checkout_script');



?>