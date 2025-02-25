<?php

require_once plugin_dir_path(__FILE__) . '_inc/fpdf/fpdf.php'; // Adjust path as needed



class ShippingLabelPDF extends FPDF
{
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));

        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c ',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }

    function DrawLabel($data)
    {
        // Set margins
        $this->SetMargins(10, 5, 10);

        // Title at the top
        $this->SetFont('Arial', 'B', 16);
        $this->SetXY(10, 1);  // Position it above the rounded rectangle
        $this->Cell(0, 10, "SHIPPING LABEL - " . strtoupper($data['competition']), 0, 1, 'C');

        // Add rounded rectangle container
        $this->RoundedRect(10, 10, 190, 120, 5);

        // SHIP TO Section
        $this->SetFont('Arial', 'B', 11);
        $this->SetXY(15, 15);
        $this->Cell(0, 8, 'SHIP TO:', 0, 1);

        // Name
        $this->SetFont('Arial', 'B', 14);
        $this->SetX(15);
        $this->Cell(0, 8, $data['name'], 0, 1);

        // Address
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(0, 0, 0);
        $this->SetX(15);
        $this->Cell(0, 8, $data['address'], 0, 1);
        $this->SetX(15);
        $this->Cell(0, 8, $data['email'], 0, 1);
        $this->SetX(15);
        $this->Cell(0, 8, $data['tel'], 0, 1);

        // Separator line
        $this->Line(15, $this->GetY() + 5, 195, $this->GetY() + 5);

        // ORDER DETAILS Section
        $this->SetFont('Arial', 'B', 11);
        $this->SetXY(15, $this->GetY() + 10);
        $this->Cell(0, 8, 'ORDER DETAILS:', 0, 1);

        // Order details with labels and values
        $this->SetFont('Arial', '', 11);
        $details = array(
            'Order #:' => $data['order_id'],
            'Product:' => $data['competition'],
            'Ticket No.:' => $data['ticket_number'],
            'Prize:' => $data['prize']
        );

        foreach ($details as $label => $value) {
            $this->SetX(15);
            $this->SetTextColor(100, 100, 100); // Gray color for labels
            $this->Cell(25, 8, $label, 0, 0);
            $this->SetTextColor(0, 0, 0); // Black color for values
            $this->Cell(0, 8, $value, 0, 1);
        }
    }
}

class Competitions_Admin
{

    private static $initiated = false;

    public static function init()
    {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        self::$initiated = true;
        add_action('admin_init', array('Competitions_Admin', 'admin_init'));
        add_action('admin_menu', array('Competitions_Admin', 'admin_menu'));
        add_action('admin_enqueue_scripts', array('Competitions_Admin', 'load_resources'));
        add_action('wp_ajax_create_competition_record', array('Competitions_Admin', 'create_competition_record'));
        add_action('wp_ajax_nopriv_create_competition_record', array('Competitions_Admin', 'create_competition_record'));
        add_action('wp_ajax_update_competition_record', array('Competitions_Admin', 'update_competition_record'));
        add_action('wp_ajax_nopriv_update_competition_record', array('Competitions_Admin', 'update_competition_record'));

        add_action('wp_ajax_update_competition_record_draft', array('Competitions_Admin', 'update_competition_record_draft'));
        add_action('wp_ajax_nopriv_update_competition_record_draft', array('Competitions_Admin', 'update_competition_record_draft'));

        add_action('wp_ajax_save_temp_competition_record', array('Competitions_Admin', 'save_temp_competition_record'));
        add_action('wp_ajax_nopriv_save_temp_competition_record', array('Competitions_Admin', 'save_temp_competition_record'));

        add_action('wp_ajax_save_temp_competition_record_draft', array('Competitions_Admin', 'save_temp_competition_record_draft'));
        add_action('wp_ajax_nopriv_save_temp_competition_record_draft', array('Competitions_Admin', 'save_temp_competition_record_draft'));

        add_action('wp_ajax_generate_ticket_numbers', array('Competitions_Admin', 'generate_ticket_numbers'));
        add_action('wp_ajax_nopriv_generate_ticket_numbers', array('Competitions_Admin', 'generate_ticket_numbers'));

        add_action('wp_ajax_check_generate_ticket_numbers', array('Competitions_Admin', 'check_generate_ticket_numbers'));
        add_action('wp_ajax_nopriv_check_generate_ticket_numbers', array('Competitions_Admin', 'check_generate_ticket_numbers'));

        add_action('wp_ajax_generate_temporary_ticket_numbers', array('Competitions_Admin', 'generate_temporary_ticket_numbers'));
        add_action('wp_ajax_nopriv_generate_temporary_ticket_numbers', array('Competitions_Admin', 'generate_temporary_ticket_numbers'));

        add_action('wp_ajax_update_global_settings', array('Competitions_Admin', 'update_global_settings'));
        add_action('wp_ajax_nopriv_update_global_settings', array('Competitions_Admin', 'update_global_settings'));

        add_action('wp_ajax_update_statistics_winner_prize', array('Competitions_Admin', 'update_statistics_winner_prize'));
        add_action('wp_ajax_nopriv_update_statistics_winner_prize', array('Competitions_Admin', 'update_statistics_winner_prize'));

        add_action('wp_ajax_update_statistics_charity_followrs', array('Competitions_Admin', 'update_statistics_charity_followrs'));
        add_action('wp_ajax_nopriv_update_statistics_charity_followrs', array('Competitions_Admin', 'update_statistics_charity_followrs'));

        add_action('wp_ajax_update_cometchat_pinned_message', array('Competitions_Admin', 'update_cometchat_pinned_message'));
        add_action('wp_ajax_nopriv_update_cometchat_pinned_message', array('Competitions_Admin', 'update_cometchat_pinned_message'));




        add_action('wp_ajax_save_global_question', array('Competitions_Admin', 'save_global_question'));
        add_action('wp_ajax_nopriv_save_global_question', array('Competitions_Admin', 'save_global_question'));

        add_action('wp_ajax_update_seo_settings', array('Competitions_Admin', 'update_seo_settings'));
        add_action('wp_ajax_nopriv_update_seo_settings', array('Competitions_Admin', 'update_seo_settings'));

        add_action('save_competition', array('Competitions_Admin', 'create_product_for_competition'));

        add_action('wp_ajax_make_competition_winner', array('Competitions_Admin', 'make_competition_winner'));
        add_action('wp_ajax_nopriv_make_competition_winner', array('Competitions_Admin', 'make_competition_winner'));

        add_action('wp_ajax_make_competition_reward_winner', array('Competitions_Admin', 'make_competition_reward_winner'));
        add_action('wp_ajax_nopriv_make_competition_reward_winner', array('Competitions_Admin', 'make_competition_reward_winner'));


        add_action('edit_user_profile', ['Competitions_Admin', 'add_user_profile_custom_fields'], 10);
        add_action('wp_ajax_update_lock_account', array('Competitions_Admin', 'update_lock_account_callback'));

        add_action("wp_ajax_get_all_list_ajax", array('Competitions_Admin', 'get_all_list_ajax'));

        add_action("wp_ajax_add_tickets_to_competition", array('Competitions_Admin', 'add_tickets_to_competition'));

        add_action("wp_ajax_save_slider_settings", array("Competitions_Admin", "save_slider_settings"));


        add_action("wp_ajax_mark_as_paid", array('Competitions_Admin', 'mark_as_paid'));
        add_action("wp_ajax_claimed_type", array('Competitions_Admin', 'claimed_type'));
        add_action("wp_ajax_mark_as_paid_prize", array('Competitions_Admin', 'mark_as_paid_prize'));
        add_action("wp_ajax_save_global_question_settings", array('Competitions_Admin', 'save_global_question_settings'));
        add_action("wp_ajax_mark_as_paid_unclaim", array('Competitions_Admin', 'mark_as_paid_unclaim'));
        add_action("wp_ajax_change_prize_title", array('Competitions_Admin', 'change_prize_title'));
        add_action("wp_ajax_validate_comp_id", array('Competitions_Admin', 'validate_comp_id'));

        error_log(admin_url('admin-ajax.php'));

    }

    public static function admin_init()
    {
        if (get_option('Activated_Competitions')) {
            delete_option('Activated_Competitions');
            // if ( ! headers_sent() ) {
            // 	$admin_url = self::get_page_url( 'init' );
            // 	wp_redirect( $admin_url );
            // }
        }
    }

    public static function admin_menu()
    {

        add_menu_page(
            'Competitions',
            'Competitions',
            'manage_options',
            'competitions_menu',
            array('Competitions_Admin', 'all_competitions'),
            'dashicons-admin-generic',
            2
        );

        add_submenu_page('competitions_menu', 'All Competitions', 'All Competitions', 'manage_options', 'competitions_menu');

        add_submenu_page(
            'competitions_menu',
            'Add Tickets',
            'Add Tickets',
            'manage_options',
            'add_tickets',
            array('Competitions_Admin', 'add_tickets')
        );

        add_submenu_page(
            'competitions_menu',
            'Instant Winners',
            'Instant Winners',
            'manage_options',
            'instant_win_menu',
            array('Competitions_Admin', 'instant_win_menu')
        );

        // add_submenu_page(
        //     'competitions_menu',
        //     'Popup Settings',
        //     'Popup Settings',
        //     'manage_options',
        //     'popup_settings',
        //     array('Competitions_Admin', 'popup_settings')
        // );

        add_submenu_page(
            'competitions_menu',
            'Limits & Lockouts',
            'Limits & Lockouts',
            'manage_options',
            'limits_lockouts',
            array('Competitions_Admin', 'limits_lockouts')
        );

        // add_submenu_page(
        //     'competitions_menu',
        //     'PDF Entry Lists',
        //     'PDF Entry Lists',
        //     'manage_options',
        //     'pdf_entry_lists',
        //     array('Competitions_Admin', 'pdf_entry_lists')
        // );

        add_submenu_page(
            'competitions_menu',
            'Winners',
            'Winners',
            'manage_options',
            //'winners',//
            'edit.php?post_type=winners',
            //array('Competitions_Admin', 'winners')
        );

        add_submenu_page(
            'competitions_menu',
            'SEO Page Settings',
            'SEO Page Settings',
            'manage_options',
            'seo-page-settings',
            array('Competitions_Admin', 'SEOSettings')
        );

        add_submenu_page(
            null,
            'Create Competition',
            'Create Competition',
            'manage_options',
            'create-competition',
            array('Competitions_Admin', 'create_competition')
        );

        add_submenu_page(
            null,
            'Edit Competition',
            'Edit Competition',
            'manage_options',
            'edit-competition',
            array('Competitions_Admin', 'edit_competition')
        );

        add_submenu_page(
            'competitions_menu',
            'Global Settings',
            'Global Settings',
            'manage_options',
            'global-settings',
            array('Competitions_Admin', 'global_settings')
        );

        add_submenu_page(
            null,
            'HP Slider',
            'HP Slider',
            'manage_options',
            'HPSlider',
            array('Competitions_Admin', 'HPSlider')
        );

        add_submenu_page(
            null,
            'Manage Static',
            'Manage Static',
            'manage_options',
            'manageStatic',
            array('Competitions_Admin', 'manageStatic')
        );

        add_submenu_page(
            null,
            'Manage Pinnned Message',
            'Manage Pinnned Message',
            'manage_options',
            'managePinnedMessage',
            array('Competitions_Admin', 'managePinnedMessage')
        );

        add_submenu_page(
            null,
            'Update Competitions',
            'Update Competitions',
            'manage_options',
            'update-Competitions',
            array('Competitions_Admin', 'updateDrawDatePassedCompetitions')
        );

        add_submenu_page(
            null,
            'Question Settings',
            'Question Settings',
            'manage_options',
            'question-settings',
            array('Competitions_Admin', 'global_question_settings')
        );

        add_submenu_page(
            null,
            'Create Competition',
            'Create Competition',
            'manage_options',
            'create-question',
            array('Competitions_Admin', 'create_question')
        );

        add_submenu_page(
            null,
            'Edit Question',
            'Edit Question',
            'manage_options',
            'edit-question',
            array('Competitions_Admin', 'edit_question')
        );

        add_submenu_page(
            null,
            'Entrants',
            'Entrants',
            'manage_options',
            'entrants',
            array('Competitions_Admin', 'entrants')
        );

        add_submenu_page(
            null,
            'Leaderboard',
            'Leaderboard',
            'manage_options',
            'leaderboard',
            array('Competitions_Admin', 'leaderboard')
        );

        add_submenu_page(
            null,
            'Reward Prizes',
            'Reward Prizes',
            'manage_options',
            'reward_prizes',
            array('Competitions_Admin', 'reward_prizes')
        );

        add_submenu_page(
            null,
            'Reward Prizes Entrants',
            'Reward Prizes Entrants',
            'manage_options',
            'reward_prizes_entrants',
            array('Competitions_Admin', 'reward_prizes_entrants')
        );

        add_submenu_page(
            null,
            'Instant Win',
            'Instant Win',
            'manage_options',
            'instant_wins',
            array('Competitions_Admin', 'instant_wins')
        );
    }

    public static function all_competitions()
    {

        Competitions::view('all-competitions');
    }


    public static function add_tickets()
    {

        Competitions::view('add-tickets');
    }

    public static function instant_win_menu()
    {

        Competitions::view('instant_win_menu');
    }

    public static function instant_wins()
    {

        Competitions::view('instant-wins');
    }

    public static function popup_settings()
    {

        Competitions::view('popup-settings');
    }


    public static function limits_lockouts()
    {

        Competitions::view('limits-lockouts');
    }


    public static function pdf_entry_lists()
    {

        Competitions::view('pdf-entry-lists');
    }

    public static function winners()
    {

        Competitions::view('winners');
    }


    public static function SEOSettings()
    {

        Competitions::view('seo-settings');
    }

    function wpCustomStyleSheet()
    {
        //first register sthe style sheet and then enqueue
        //wp_register_style( 'adminCustomStyle', get_bloginfo('stylesheet_directory') . '/adminCustomStyle.css', false, '1.0.0' );
        //wp_enqueue_style( 'adminCustomStyle' );

        wp_register_style('adminCompetitionStyle', get_bloginfo('stylesheet_directory') . '/admin_comp_style.css', false, '1.0.0');
        wp_enqueue_style('adminCompetitionStyle');
    }

    public static function load_resources($hook)
    {
        global $hook_suffix;
        // wp_register_script('custom_url.js', plugin_dir_url() . './custom_url.js');
        wp_register_script('custom_url.js', plugin_dir_url(__FILE__) . '_inc/custom_url.js', array('jquery'), self::get_asset_file_version('_inc/custom_url.js'));
        wp_enqueue_script('custom_url.js');




        if ($hook == 'user-edit.php') {
            wp_register_script('usercompetitions.js', plugin_dir_url(__FILE__) . '_inc/userCompetitions.js', array('jquery'), self::get_asset_file_version('_inc/competitions.js'));
            wp_enqueue_script('usercompetitions.js');
            wp_localize_script('usercompetitions.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        }

        if (
            $hook == 'post-new.php' || $hook == 'user-edit.php' || $hook == 'toplevel_page_gf_edit_forms' || $hook == 'forms_page_gf_new_form' || $hook == 'tools_page_crontrol_admin_manage_page' || $hook == 'toplevel_page_postman'
        )
            return true;

        if ("user-edit.php" == $hook || "woocommerce_page_wc-settings" == $hook || $hook == 'post-new.php' || $hook == 'post.php' || $hook == 'tools_page_crontrol_admin_manage_page')
            return true;

        wp_register_style('competitions-font-mozaic', plugin_dir_url(__FILE__) . '_inc/fonts/inter.css', array(), self::get_asset_file_version('_inc/fonts/inter.css'));
        wp_enqueue_style('competitions-font-mozaic');

        if ($hook == "competitions_page_limits_lockouts" || $hook == 'competitions_page_instant_win_menu') {

            wp_enqueue_style('twitter-bootstrap-css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css');
            wp_enqueue_style('dataTables-bootstrap5', 'https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css');

            $comp_admin_css_path = '_inc/competitions-admin.css';
            wp_register_style('competitions-admin', plugin_dir_url(__FILE__) . $comp_admin_css_path, array(), self::get_asset_file_version($comp_admin_css_path));
            wp_enqueue_style('competitions-admin');


            wp_enqueue_script('jquery-twitter-bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js', array());
            wp_enqueue_script('jquery-dataTables', 'https://cdn.datatables.net/2.0.8/js/dataTables.js', array());
            wp_enqueue_script('jquery-dataTables-bootstrap', 'https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js', array());

            wp_enqueue_script('jquery-min', 'https://code.jquery.com/jquery-3.6.0.min.js', array());

            wp_register_script('limitLock.js', plugin_dir_url(__FILE__) . '_inc/limitLock.js', array('jquery'), self::get_asset_file_version('_inc/limitLock.js'));

            wp_enqueue_script('limitLock.js');
            wp_localize_script('limitLock.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

            return true;
        }

        wp_register_style('bootstrap-competitions-admin', plugin_dir_url(__FILE__) . '_inc/bootstrap.min.css', array(), self::get_asset_file_version('_inc/bootstrap.min.css'));
        wp_enqueue_style('bootstrap-competitions-admin');

        $comp_admin_css_path = '_inc/competitions-admin.css';
        wp_register_style('competitions-admin', plugin_dir_url(__FILE__) . $comp_admin_css_path, array(), self::get_asset_file_version($comp_admin_css_path));
        wp_enqueue_style('competitions-admin');

        wp_register_script('bootstrap-bundle.js', plugin_dir_url(__FILE__) . '_inc/bootstrap.bundle.js', array(), self::get_asset_file_version('/_inc/bootstrap.bundle.js'));
        wp_enqueue_script('bootstrap-bundle.js');

        //wp_register_script('bootstrap-competitions-admin.js', plugin_dir_url(__FILE__) . '_inc/bootstrap.min.js', array(), self::get_asset_file_version('/_inc/bootstrap.min.js'));
        //wp_enqueue_script('bootstrap-competitions-admin.js');

        if ($hook != 'wpforms_page_wpforms-builder' && $hook != 'woocommerce_page_wc-settings') {
            wp_register_script('jquery-1.11.1.js', plugin_dir_url(__FILE__) . '_inc/jquery-1.11.1.js', array(), self::get_asset_file_version('/_inc/jquery-1.11.1.js'));
            wp_enqueue_script('jquery-1.11.1.js');
        }

        wp_register_script('ckeditor.js', plugin_dir_url(__FILE__) . '_inc/ckeditor/ckeditor.js', array(), self::get_asset_file_version('/_inc/ckeditor/ckeditor.js'));
        wp_enqueue_script('ckeditor.js');

        wp_register_script('ckfinder.js', plugin_dir_url(__FILE__) . '_inc/ckfinder/ckfinder.js', array(), self::get_asset_file_version('/_inc/ckfinder/ckfinder.js'));
        wp_enqueue_script('ckfinder.js');

        wp_register_script('papaparse.js', plugin_dir_url(__FILE__) . '_inc/papaparse.min.js', array(), self::get_asset_file_version('/_inc/papaparse.min.js'));
        wp_enqueue_script('papaparse.js');



        if ("admin_page_create-competition" == $hook || "admin_page_edit-competition" == $hook) {

            wp_enqueue_script('jquery-ui-core', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', array('jquery'), '1.12.1', true);
            wp_enqueue_script('jquery-ui-datepicker', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', array('jquery-ui-core'), '1.12.1', true);
            wp_enqueue_script('jquery-ui-timepicker', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js', array('jquery', 'jquery-ui-core'), '1.10.0', true);
            wp_enqueue_script('jquery-moment', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js', array('jquery', 'jquery-ui-core'), '1.10.0', true);

            wp_register_script('jquery-ui-js', 'https://code.jquery.com/ui/1.12.1/jquery-ui.min.js', array(), self::get_asset_file_version('/_inc/jquery-1.11.1.js'));
            wp_enqueue_script('jquery-ui-js');

            wp_enqueue_script('select-2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array('jquery', 'jquery-ui-core'), '1.10.0', true);

            wp_register_script('jquery.validate.js', plugin_dir_url(__FILE__) . '_inc/jquery.validate.js', array(), self::get_asset_file_version('/_inc/jquery.validate.js'));
            wp_enqueue_script('jquery.validate.js');

            wp_register_script('additional-methods.js', plugin_dir_url(__FILE__) . '_inc/additional-methods.js', array(), self::get_asset_file_version('/_inc/additional-methods.js'));
            wp_enqueue_script('additional-methods.js');

            wp_enqueue_style('jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
            wp_enqueue_style('jquery-timepicker-theme', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css');

            wp_enqueue_style('select-2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css');

            wp_register_script('competitions-admin', plugin_dir_url(__FILE__) . '_inc/competitions-admin.js', array(), self::get_asset_file_version('/_inc/competitions-admin.js'));
            wp_enqueue_script('competitions-admin');
            wp_localize_script('competitions-admin', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        }

        if ($hook == 'admin_page_create-question' || $hook == 'admin_page_edit-question' || $hook == 'admin_page_HPSlider' || $hook == 'admin_page_manage-static') {

            wp_register_script('jquery.validate.js', plugin_dir_url(__FILE__) . '_inc/jquery.validate.js', array(), self::get_asset_file_version('/_inc/jquery.validate.js'));
            wp_enqueue_script('jquery.validate.js');
        }

        if ($hook != 'wpforms_page_wpforms-builder' && $hook != 'woocommerce_page_wc-settings') {

            wp_register_script('competitions.js', plugin_dir_url(__FILE__) . '_inc/competitions.js', array('jquery'), self::get_asset_file_version('_inc/competitions.js'));

            wp_enqueue_script('competitions.js');
            wp_localize_script('competitions.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        }

        wp_enqueue_media();
    }
    public static function get_asset_file_version($relative_path)
    {

        $full_path = COMPETITIONS__PLUGIN_DIR . $relative_path;

        if (preg_match('/[a-z]/', COMPETITIONS_VERSION) && file_exists($full_path)) {
            return filemtime($full_path);
        }

        return COMPETITIONS_VERSION;
    }

    public static function create_competition()
    {

        Competitions::view('create-competition');
    }


    public static function edit_competition()
    {

        Competitions::view('edit-competition');
    }

    public static function global_settings()
    {

        Competitions::view('global-settings');
    }

    public static function HPSlider()
    {

        Competitions::view('HPSlider');
    }

    public static function manageStatic()
    {

        Competitions::view('manage-static');
    }

    public static function managePinnedMessage()
    {

        Competitions::view('manage-cometchat-pinned');
    }

    public static function global_question_settings()
    {

        Competitions::view('question-settings');
    }

    public static function create_question()
    {

        Competitions::view('create-question');
    }

    public static function edit_question()
    {

        Competitions::view('edit-question');
    }

    public static function entrants()
    {

        Competitions::view('entrants');
    }


    public static function leaderboard()
    {

        Competitions::view('leaderboard');
    }

    public static function reward_prizes()
    {

        Competitions::view('reward_prizes');
    }

    public static function reward_prizes_entrants()
    {

        Competitions::view('reward_prizes_entrants');
    }

    function create_competition_record()
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'competitions';

        $gallery_videos = $_REQUEST['gallery_video_urls'];

        $gallery_video_type = $_REQUEST['gallery_video_type'];

        $videoURLS = [];

        if (!empty($gallery_videos)) {

            foreach ($gallery_videos as $key => $gallery_video) {

                $videoURLS[$key][$gallery_video_type[$key]] = $gallery_video;
            }

            $videoURLS = json_encode($videoURLS);
        }

        $image_name = basename($_REQUEST['featured_image']);

        $time = current_time('mysql'); // Current time for directory structure
        $m = substr($time, 5, 2); // Extract the month from the current date

        $data = array(
            'title' => $_REQUEST['title'],
            'category' => $_REQUEST['category'],
            'via_mobile_app' => $_REQUEST['via_mobile_app'],
            'description' => $_REQUEST['description'],
            'status' => 'Draft',
            'image' => $_REQUEST['featured_image'],
            'images_thumb' => "/" . $m  . "/" . $image_name,
            'images_thumb_cat' => "/" . $m  . "/" . $image_name,
            'gallery_images' => $_REQUEST['gallery_image'],
            'is_draft' => 1,
            'promotional_messages' => $_REQUEST['promotional_messages'],
            'instant_win_only' => $_REQUEST['instant_win_only'],
            'gallery_videos' => $videoURLS,
            'slider_sorting' => $_REQUEST['sortedSelections']
        );

        $created = $wpdb->insert($table_name, $data);

        if ($created) {
            echo json_encode(['success' => true, 'message' => 'Competition created successfully!', 'record' => $wpdb->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create record.']);
        }

        wp_die();
    }

    function update_competition_record()
    {
        global $wpdb;



        $table_name = $wpdb->prefix . 'competitions';

        $temp_table_name = $wpdb->prefix . 'competitions_temp';

        $temp_tickets = $wpdb->prefix . 'competition_tickets_temp';

        $comp_tickets = $wpdb->prefix . 'competition_tickets';

        if (isset($_REQUEST['update_from_temp']) && $_REQUEST['update_from_temp'] == 1) {

            $temp_entry = $wpdb->get_row("SELECT * FROM " . $temp_table_name . " WHERE record = '" . $_REQUEST['record'] . "'", ARRAY_A);

            if (!empty($temp_entry)) {

                $instant_prizes = json_decode($temp_entry['instant_prizes'], true);

                $reward_prizes = json_decode($temp_entry['reward_prizes'], true);

                unset($temp_entry['record']);
                unset($temp_entry['id']);
                unset($temp_entry['instant_prizes']);
                unset($temp_entry['reward_prizes']);

                $temp_entry['is_draft'] = 0;

                $wpdb->update($table_name, $temp_entry, ['id' => $_REQUEST['record']]);

                $source_data = $wpdb->get_results("SELECT * FROM {$temp_tickets} where competition_id = '" . $_REQUEST['record'] . "'", ARRAY_A);

                if (!empty($source_data)) {

                    $sql = "INSERT INTO {$comp_tickets} (competition_id, ticket_number)
                    SELECT tt.competition_id, tt.ticket_number FROM {$temp_tickets} AS tt WHERE  NOT EXISTS (
                        select 1 from $comp_tickets as t where t.competition_id = tt.competition_id AND t.ticket_number = tt.ticket_number
                    ) and tt.competition_id = %d";

                    $wpdb->query($wpdb->prepare($sql, $_REQUEST['record']));

                    $wpdb->delete(
                        $temp_tickets,
                        array(
                            'competition_id' => $_REQUEST['record'],
                        )
                    );
                }

                $reward_table = $wpdb->prefix . 'comp_reward';

                $comp_tickets_purchased = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT count(*) as total_tickets FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %s and is_purchased = 1",
                        $_REQUEST['record']
                    )
                );

                if (!empty($reward_prizes)) { //&& $comp_tickets_purchased == 0

                    $wpdb->delete(
                        $reward_table,
                        array(
                            'competition_id' => $_REQUEST['record']
                        )
                    );


                    // error_log("reward_prizes: update_competition_record " . print_r($reward_prizes, true));

                    foreach ($reward_prizes as $reward_prize) {

                        $data = array(
                            'title' => $reward_prize['title'],
                            'type' => $reward_prize['type'],
                            'value' => $reward_prize['value'],
                            'web_order_reward' => $reward_prize['web_order_reward'] ? (int) $reward_prize['web_order_reward'] : 0,
                            'prize_value' => $reward_prize['prize_value'],
                            'prcnt_available' => $reward_prize['prcnt_available'],
                            'image' => $reward_prize['image'],
                            'competition_id' => $_REQUEST['record'],
                            'competition_prize' => $reward_prize['competition_prize'],
                            'prize_total_tickets' => $reward_prize['prize_total_tickets']
                        );

                        $created = $wpdb->insert($reward_table, $data);
                    }
                }

                $instant = $wpdb->prefix . 'comp_instant_prizes';

                $instant_ticket = $wpdb->prefix . 'comp_instant_prizes_tickets';

                if (!empty($instant_prizes) && $comp_tickets_purchased == 0) {

                    $wpdb->delete(
                        $instant,
                        array(
                            'competition_id' => $_REQUEST['record'],
                        )
                    );

                    $wpdb->delete(
                        $instant_ticket,
                        array(
                            'competition_id' => $_REQUEST['record'],
                        )
                    );

                    error_log("instant_prizes: update_competition_record " . print_r($instant_prizes, true));

                    foreach ($instant_prizes as $instant_prize) {

                        $data = array(
                            'title' => $instant_prize['title'],
                            'type' => $instant_prize['type'],
                            'value' => $instant_prize['value'],
                            'prize_value' => $instant_prize['prize_value'],
                            'web_order_instant' => $instant_prize['web_order_instant'] ? (int) $instant_prize['web_order_instant'] : 0,
                            'quantity' => $instant_prize['quantity'],
                            'image' => $instant_prize['image'],
                            'competition_id' => $_REQUEST['record'],
                            'competition_prize' => $instant_prize['competition_prize'],
                            'prize_total_tickets' => $instant_prize['prize_total_tickets'],
                            'show_description' => $instant_prize['show_description'],
                            'prize_description' => $instant_prize['prize_description']
                        );

                        $created = $wpdb->insert($instant, $data);

                        if ($created) {

                            $instant_prize_id = $wpdb->insert_id;

                            if (isset($instant_prize['tickets']) && $instant_prize['tickets'] > 0) {

                                $totalTickets = explode(",", $instant_prize['tickets']);

                                foreach ($totalTickets as $totalTicket) {

                                    $wpdb->insert($instant_ticket, [
                                        'competition_id' => $_REQUEST['record'],
                                        'instant_id' => $instant_prize_id,
                                        'ticket_number' => $totalTicket
                                    ]);
                                }
                            }
                        }
                    }
                }

                do_action('save_competition', $_REQUEST['record']);

                $wpdb->delete(
                    $temp_table_name,
                    array(
                        'record' => $_REQUEST['record']
                    )
                );

                wp_redirect(admin_url('/admin.php?page=competitions_menu'));
                exit;
            }
        }

        if ($_REQUEST['step'] == 'products') {

            $data = array(
                'status' => $_REQUEST['status'],
                'price_per_ticket' => $_REQUEST['price_per_ticket'],
                'cash' => $_REQUEST['cash'],
                'prize_value' => $_REQUEST['prize_value'],
                'total_sell_tickets' => $_REQUEST['total_sell_tickets'],
                'max_ticket_per_user' => $_REQUEST['max_ticket_per_user'],
                'quantity' => $_REQUEST['quantity'],
                'sale_price' => $_REQUEST['sale_price'] ? $_REQUEST['sale_price'] : null,
                'sale_start_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['sale_start_date']),
                'sale_end_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['sale_end_date']),
                'short_description' => $_REQUEST['short_description'],
                'draw_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['draw_date']),
                'draw_time' => $_REQUEST['draw_time'],
                'closing_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['closing_date']),
                'closing_time' => $_REQUEST['closing_time'],
                'live_draw' => $_REQUEST['live_draw'],
                'live_draw_info' => $_REQUEST['live_draw_info'],
                'hide_timer' => $_REQUEST['hide_timer'],
                'hide_ticket_count' => $_REQUEST['hide_ticket_count'],
                'disable_tickets' => $_REQUEST['disable_tickets'],
                'is_featured' => $_REQUEST['is_featured'],
                'prize_type' => $_REQUEST['prize_type'],
                'total_winners' => $_REQUEST['total_winners'],
                'points' => $_REQUEST['points'],
                'competitions_prize' => $_REQUEST['competitions_prize'],
                'prize_tickets' => $_REQUEST['prize_tickets'],
                'web_order' => $_REQUEST['webOrder'],
                'sale_start_time' => $_REQUEST['sale_price_start_time_hour'] . ':' . $_REQUEST['sale_price_start_time_minute'],
                'sale_end_time' => $_REQUEST['sale_price_end_time_hour'] . ':' . $_REQUEST['sale_price_end_time_minute'],
            );
        } elseif ($_REQUEST['step'] == 'question') {

            $data = array(
                'comp_question' => $_REQUEST['comp_question'],
                'question' => $_REQUEST['question'],
                'question_options' => json_encode(["answer1" => $_REQUEST['answer1'], "answer2" => $_REQUEST['answer2'], "answer3" => $_REQUEST['answer3']]),
                'correct_answer' => $_REQUEST['correct_answer']
            );
        } elseif ($_REQUEST['step'] == 'legals') {

            $data = array(
                'competition_rules' => $_REQUEST['competition_rules'],
                'faq' => $_REQUEST['faq']
            );
        } elseif ($_REQUEST['step'] == 'instant') {

            $data = array('enable_instant_wins' => $_REQUEST['enable_instant_wins']);

            if (isset($_REQUEST['enable_instant_wins']) && $_REQUEST['enable_instant_wins'] == 1) {

                Competitions_Admin::saveInstantPrizes();
            }
        } elseif ($_REQUEST['step'] == 'reward') {

            $data = array('enable_reward_wins' => $_REQUEST['enable_reward_wins'], 'is_draft' => 0);

            if (isset($_REQUEST['enable_reward_wins']) && $_REQUEST['enable_reward_wins'] == 1) {

                Competitions_Admin::saveRewards();
            }
        }


        $updated = $wpdb->update($table_name, $data, ['id' => $_REQUEST['record']]);

        do_action('save_competition', $_REQUEST['record']);

        //$updated = true;

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Competition updated successfully!', 'record' => $_REQUEST['record']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create record.']);
        }

        wp_die();
    }

    function update_competition_record_draft()
    {
        global $wpdb;



        $table_name = $wpdb->prefix . 'competitions';

        $temp_table_name = $wpdb->prefix . 'competitions_temp';

        $temp_tickets = $wpdb->prefix . 'competition_tickets_temp';

        $comp_tickets = $wpdb->prefix . 'competition_tickets';

        if (isset($_REQUEST['update_from_temp']) && $_REQUEST['update_from_temp'] == 1) {

            $temp_entry = $wpdb->get_row("SELECT * FROM " . $temp_table_name . " WHERE record = '" . $_REQUEST['record'] . "'", ARRAY_A);

            if (!empty($temp_entry)) {

                $instant_prizes = json_decode($temp_entry['instant_prizes'], true);

                $reward_prizes = json_decode($temp_entry['reward_prizes'], true);

                unset($temp_entry['record']);
                unset($temp_entry['id']);
                unset($temp_entry['instant_prizes']);
                unset($temp_entry['reward_prizes']);

                $temp_entry['is_draft'] = 1;

                $wpdb->update($table_name, $temp_entry, ['id' => $_REQUEST['record']]);

                $source_data = $wpdb->get_results("SELECT * FROM {$temp_tickets} where competition_id = '" . $_REQUEST['record'] . "'", ARRAY_A);

                if (!empty($source_data)) {

                    $sql = "INSERT INTO {$comp_tickets} (competition_id, ticket_number)
                    SELECT tt.competition_id, tt.ticket_number FROM {$temp_tickets} AS tt WHERE  NOT EXISTS (
                        select 1 from $comp_tickets as t where t.competition_id = tt.competition_id AND t.ticket_number = tt.ticket_number
                    ) and tt.competition_id = %d";

                    $wpdb->query($wpdb->prepare($sql, $_REQUEST['record']));

                    $wpdb->delete(
                        $temp_tickets,
                        array(
                            'competition_id' => $_REQUEST['record'],
                        )
                    );
                }

                $reward_table = $wpdb->prefix . 'comp_reward';

                $comp_tickets_purchased = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT count(*) as total_tickets FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %s and is_purchased = 1",
                        $_REQUEST['record']
                    )
                );

                if (!empty($reward_prizes)) { //&& $comp_tickets_purchased == 0

                    $wpdb->delete(
                        $reward_table,
                        array(
                            'competition_id' => $_REQUEST['record']
                        )
                    );


                    // error_log("reward_prizes: update_competition_record " . print_r($reward_prizes, true));

                    foreach ($reward_prizes as $reward_prize) {

                        $data = array(
                            'title' => $reward_prize['title'],
                            'type' => $reward_prize['type'],
                            'value' => $reward_prize['value'],
                            'web_order_reward' => $reward_prize['web_order_reward'] ? (int) $reward_prize['web_order_reward'] : 0,
                            'prize_value' => $reward_prize['prize_value'],
                            'prcnt_available' => $reward_prize['prcnt_available'],
                            'image' => $reward_prize['image'],
                            'competition_id' => $_REQUEST['record'],
                            'competition_prize' => $reward_prize['competition_prize'],
                            'prize_total_tickets' => $reward_prize['prize_total_tickets']
                        );

                        $created = $wpdb->insert($reward_table, $data);
                    }
                }

                $instant = $wpdb->prefix . 'comp_instant_prizes';

                $instant_ticket = $wpdb->prefix . 'comp_instant_prizes_tickets';

                if (!empty($instant_prizes) && $comp_tickets_purchased == 0) {

                    $wpdb->delete(
                        $instant,
                        array(
                            'competition_id' => $_REQUEST['record'],
                        )
                    );

                    $wpdb->delete(
                        $instant_ticket,
                        array(
                            'competition_id' => $_REQUEST['record'],
                        )
                    );

                    error_log("instant_prizes: update_competition_record " . print_r($instant_prizes, true));

                    foreach ($instant_prizes as $instant_prize) {

                        $data = array(
                            'title' => $instant_prize['title'],
                            'type' => $instant_prize['type'],
                            'value' => $instant_prize['value'],
                            'prize_value' => $instant_prize['prize_value'],
                            'web_order_instant' => $instant_prize['web_order_instant'] ? (int) $instant_prize['web_order_instant'] : 0,
                            'quantity' => $instant_prize['quantity'],
                            'image' => $instant_prize['image'],
                            'competition_id' => $_REQUEST['record'],
                            'competition_prize' => $instant_prize['competition_prize'],
                            'prize_total_tickets' => $instant_prize['prize_total_tickets'],
                            'show_description' => $instant_prize['show_description'],
                            'prize_description' => $instant_prize['prize_description']
                        );

                        $created = $wpdb->insert($instant, $data);

                        if ($created) {

                            $instant_prize_id = $wpdb->insert_id;

                            if (isset($instant_prize['tickets']) && $instant_prize['tickets'] > 0) {

                                $totalTickets = explode(",", $instant_prize['tickets']);

                                foreach ($totalTickets as $totalTicket) {

                                    $wpdb->insert($instant_ticket, [
                                        'competition_id' => $_REQUEST['record'],
                                        'instant_id' => $instant_prize_id,
                                        'ticket_number' => $totalTicket
                                    ]);
                                }
                            }
                        }
                    }
                }

                do_action('save_competition', $_REQUEST['record']);

                // $wpdb->delete(
                //     $temp_table_name,
                //     array(
                //         'record' => $_REQUEST['record']
                //     )
                // );

                // wp_redirect(admin_url('/admin.php?page=competitions_menu')); 
                exit;
            }
        }

        if ($_REQUEST['step'] == 'products') {

            $data = array(
                'status' => $_REQUEST['status'],
                'price_per_ticket' => $_REQUEST['price_per_ticket'],
                'cash' => $_REQUEST['cash'],
                'prize_value' => $_REQUEST['prize_value'],
                'total_sell_tickets' => $_REQUEST['total_sell_tickets'],
                'max_ticket_per_user' => $_REQUEST['max_ticket_per_user'],
                'quantity' => $_REQUEST['quantity'],
                'sale_price' => $_REQUEST['sale_price'] ? $_REQUEST['sale_price'] : null,
                'sale_start_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['sale_start_date']),
                'sale_end_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['sale_end_date']),
                'short_description' => $_REQUEST['short_description'],
                'draw_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['draw_date']),
                'draw_time' => $_REQUEST['draw_time'],
                'closing_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['closing_date']),
                'closing_time' => $_REQUEST['closing_time'],
                'live_draw' => $_REQUEST['live_draw'],
                'live_draw_info' => $_REQUEST['live_draw_info'],
                'hide_timer' => $_REQUEST['hide_timer'],
                'hide_ticket_count' => $_REQUEST['hide_ticket_count'],
                'disable_tickets' => $_REQUEST['disable_tickets'],
                'is_featured' => $_REQUEST['is_featured'],
                'prize_type' => $_REQUEST['prize_type'],
                'total_winners' => $_REQUEST['total_winners'],
                'points' => $_REQUEST['points'],
                'competitions_prize' => $_REQUEST['competitions_prize'],
                'prize_tickets' => $_REQUEST['prize_tickets'],
                'web_order' => $_REQUEST['webOrder'],
                'sale_start_time' => $_REQUEST['sale_price_start_time_hour'] . ':' . $_REQUEST['sale_price_start_time_minute'],
                'sale_end_time' => $_REQUEST['sale_price_end_time_hour'] . ':' . $_REQUEST['sale_price_end_time_minute'],
            );
        } elseif ($_REQUEST['step'] == 'question') {

            $data = array(
                'comp_question' => $_REQUEST['comp_question'],
                'question' => $_REQUEST['question'],
                'question_options' => json_encode(["answer1" => $_REQUEST['answer1'], "answer2" => $_REQUEST['answer2'], "answer3" => $_REQUEST['answer3']]),
                'correct_answer' => $_REQUEST['correct_answer']
            );
        } elseif ($_REQUEST['step'] == 'legals') {

            $data = array(
                'competition_rules' => $_REQUEST['competition_rules'],
                'faq' => $_REQUEST['faq']
            );
        } elseif ($_REQUEST['step'] == 'instant') {

            $data = array('enable_instant_wins' => $_REQUEST['enable_instant_wins']);

            if (isset($_REQUEST['enable_instant_wins']) && $_REQUEST['enable_instant_wins'] == 1) {

                Competitions_Admin::saveInstantPrizes();
            }
        } elseif ($_REQUEST['step'] == 'reward') {

            $data = array('enable_reward_wins' => $_REQUEST['enable_reward_wins'], 'is_draft' => 1);

            if (isset($_REQUEST['enable_reward_wins']) && $_REQUEST['enable_reward_wins'] == 1) {

                Competitions_Admin::saveRewards();
            }
        }


        $updated = $wpdb->update($table_name, $data, ['id' => $_REQUEST['record']]);

        do_action('save_competition', $_REQUEST['record']);

        //$updated = true;

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Competition updated successfully!', 'record' => $_REQUEST['record']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create record.']);
        }

        wp_die();
    }

    public static function convertIntoUTCFormat($custom_date)
    {

        $datetime = DateTime::createFromFormat('d/m/Y', $custom_date);

        if ($datetime) {

            return $datetime->format('Y-m-d');
        }
    }

    public static function saveInstantPrizes()
    {

        global $wpdb;

        $instant = $wpdb->prefix . 'comp_instant_prizes';

        $instant_ticket = $wpdb->prefix . 'comp_instant_prizes_tickets';

        if (isset($_REQUEST['total_prizes']) && $_REQUEST['total_prizes'] > 0) {

            $wpdb->delete(
                $instant,
                array(
                    'competition_id' => $_REQUEST['record'],
                )
            );

            $total_prizes = $_REQUEST['total_prizes'];

            // error_log("saveInstantPrizes: " . print_r($_REQUEST, true));


            for ($i = 1; $i <= $total_prizes; $i++) {

                $data = array(
                    'title' => $_REQUEST['title' . $i],
                    'type' => $_REQUEST['price_type' . $i],
                    'value' => $_REQUEST['cash_value' . $i],
                    'prize_value' => $_REQUEST['prize_value' . $i],
                    'quantity' => $_REQUEST['quantity' . $i],
                    'web_order_instant' => $_REQUEST['webOrderInstant' . $i] ? (int) $_REQUEST['webOrderInstant' . $i] : 0,
                    'image' => $_REQUEST['image' . $i],
                    'competition_id' => $_REQUEST['record'],
                    'competition_prize' => $_REQUEST['competition_prize' . $i],
                    'prize_total_tickets' => $_REQUEST['prize_total_tickets' . $i]
                );

                $created = $wpdb->insert($instant, $data);

                if ($created) {

                    $instant_prize_id = $wpdb->insert_id;

                    if (isset($_REQUEST['quantity' . $i]) && $_REQUEST['quantity' . $i] > 0) {

                        $totalTickets = $_REQUEST['quantity' . $i];

                        for ($j = 1; $j <= $totalTickets; $j++) {

                            $wpdb->insert($instant_ticket, [
                                'competition_id' => $_REQUEST['record'],
                                'instant_id' => $instant_prize_id,
                                'ticket_number' => $_REQUEST["ticket" . $i . "_" . $j]
                            ]);
                        }
                    }
                }
            }
        }
    }

    public static function saveRewards()
    {

        global $wpdb;

        $instant = $wpdb->prefix . 'comp_reward';

        if (isset($_REQUEST['total_reward']) && $_REQUEST['total_reward'] > 0) {

            $wpdb->delete(
                $instant,
                array(
                    'competition_id' => $_REQUEST['record']
                )
            );

            $total_prizes = $_REQUEST['total_reward'];
            // error_log("saveRewards: " . print_r($_REQUEST, true));


            for ($i = 1; $i <= $total_prizes; $i++) {

                $data = array(
                    'title' => $_REQUEST['title' . $i],
                    'type' => $_REQUEST['price_type' . $i],
                    'value' => $_REQUEST['cash_value' . $i],
                    'prize_value' => $_REQUEST['prize_value' . $i],
                    'prcnt_available' => $_REQUEST['prct_available' . $i],
                    'web_order_reward' => $_REQUEST['webOrderReward' . $i] ? (int) $_REQUEST['webOrderReward' . $i] : 0,
                    'image' => $_REQUEST['image' . $i],
                    'competition_id' => $_REQUEST['record'],
                    'competition_prize' => $_REQUEST['competition_prize' . $i],
                    'prize_total_tickets' => $_REQUEST['prize_total_tickets' . $i]
                );

                $created = $wpdb->insert($instant, $data);
            }
        }
    }

    public static function generate_temporary_ticket_numbers()
    {

        global $wpdb;

        $competition_id = $_REQUEST['record'];

        $com_tickets = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT count(*) as total_tickets FROM {$wpdb->prefix}competition_tickets_temp WHERE competition_id = %s",
                $competition_id
            )
        );

        $total_sell_tickets = $_REQUEST['total_sell_tickets'];

        if ($com_tickets > 0) {

            if ($com_tickets != $total_sell_tickets) {

                $wpdb->delete(
                    "{$wpdb->prefix}competition_tickets_temp",
                    array(
                        'competition_id' => $competition_id,
                    )
                );

                $com_tickets = self::generateCompetitionTempTickets($total_sell_tickets);
            }
        } else {

            $com_tickets = self::generateCompetitionTempTickets($total_sell_tickets);
        }

        if ($com_tickets) {

            // $limit = $_REQUEST['total_qty'];
            $limit = isset($_REQUEST['total_qty']) ? (int) $_REQUEST['total_qty'] : 0;


            $ticket_numbers = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT ticket_number FROM {$wpdb->prefix}competition_tickets_temp WHERE competition_id = %d ORDER BY RAND() < 0.01 LIMIT %d",
                    $competition_id,
                    $limit
                )
            );

            echo json_encode(['success' => true, 'data' => $ticket_numbers]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch record.']);
        }

        wp_die();
    }

    public static function generateCompetitionTempTickets($total_sell_tickets)
    {

        global $wpdb;

        $ticket_table = $wpdb->prefix . 'competition_tickets_temp';

        $numbers = range(1, $total_sell_tickets);

        self::fisherYatesShuffle($numbers);

        $numbers = array_values(array_unique($numbers));

        if (count($numbers) != $total_sell_tickets) {

            $numbers = range(1, $total_sell_tickets);

            shuffle($numbers);
        }

        $data = [];

        foreach ($numbers as $ticket) {

            $data[] = array(
                'competition_id' => $_REQUEST['record'],
                'ticket_number' => $ticket,
            );
        }

        $query = "INSERT INTO $ticket_table (competition_id, ticket_number) VALUES ";

        $placeholders = array();

        foreach ($data as $row) {
            $placeholders[] = $wpdb->prepare('(%s, %s)', $row['competition_id'], $row['ticket_number']);
        }

        $query .= implode(', ', $placeholders);

        $result = $wpdb->query($query);

        return true;
    }

    public static function generate_ticket_numbers()
    {

        global $wpdb;

        $competition_id = $_REQUEST['record'];

        $com_tickets = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT count(*) as total_tickets FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %s",
                $competition_id
            )
        );

        $total_sell_tickets = $_REQUEST['total_sell_tickets'];

        if ($com_tickets > 0) {

            if ($com_tickets != $total_sell_tickets) {

                $wpdb->delete(
                    "{$wpdb->prefix}competition_tickets",
                    array(
                        'competition_id' => $competition_id,
                    )
                );

                $com_tickets = self::generateCompetitionTickets($total_sell_tickets);
            }
        } else {

            $com_tickets = self::generateCompetitionTickets($total_sell_tickets);
        }

        if ($com_tickets) {

            $limit = $_REQUEST['total_qty'];

            $ticket_numbers = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT ticket_number FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %d ORDER BY RAND() < 0.01 LIMIT %d",
                    $competition_id,
                    $limit
                )
            );

            echo json_encode(['success' => true, 'data' => $ticket_numbers]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch record.']);
        }

        wp_die();
    }

    public static function generateCompetitionTickets($total_sell_tickets)
    {

        global $wpdb;

        $ticket_table = $wpdb->prefix . 'competition_tickets';

        $numbers = range(1, $total_sell_tickets);

        self::fisherYatesShuffle($numbers);

        $numbers = array_values(array_unique($numbers));

        if (count($numbers) != $total_sell_tickets) {

            $numbers = range(1, $total_sell_tickets);

            shuffle($numbers);
        }

        $data = [];

        foreach ($numbers as $ticket) {

            $data[] = array(
                'competition_id' => $_REQUEST['record'],
                'ticket_number' => $ticket,
            );
        }

        $query = "INSERT INTO $ticket_table (competition_id, ticket_number) VALUES ";

        $placeholders = array();

        foreach ($data as $row) {
            $placeholders[] = $wpdb->prepare('(%s, %s)', $row['competition_id'], $row['ticket_number']);
        }

        $query .= implode(', ', $placeholders);

        $wpdb->query($query);

        return true;
    }

    public static function fisherYatesShuffle(&$array)
    {
        $count = count($array);
        for ($i = $count - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            list($array[$i], $array[$j]) = array($array[$j], $array[$i]);
        }
    }

    public static function check_generate_ticket_numbers()
    {

        global $wpdb;

        $competition_id = $_REQUEST['record'];

        $com_tickets = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT count(*) as total_tickets FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %s",
                $competition_id
            )
        );

        $total_sell_tickets = $_REQUEST['total_sell_tickets'];

        if ($com_tickets > 0) {

            if ($com_tickets != $total_sell_tickets) {

                $wpdb->delete(
                    "{$wpdb->prefix}competition_tickets",
                    array(
                        'competition_id' => $competition_id,
                    )
                );

                $com_tickets = self::generateCompetitionTickets($total_sell_tickets);
            }
        } else {

            $com_tickets = self::generateCompetitionTickets($total_sell_tickets);
        }

        echo json_encode(['success' => true, 'message' => 'Create Tickets successfully']);

        wp_die();
    }

    public static function generate_ticket_numbers_non_sequential()
    {
        global $wpdb;

        $competition_id = $_REQUEST['record'];

        $com_tickets = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %s",
                $competition_id
            ),
            ARRAY_A
        );

        if (empty($com_tickets)) {
            $com_tickets = self::generateCompetitionTicketsNonSequential();
        }

        if ($com_tickets) {

            $limit = $_REQUEST['total_qty'];

            $ticket_numbers = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT ticket_number FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %d ORDER BY RAND() < 0.01 LIMIT %d",
                    $competition_id,
                    $limit
                )
            );

            echo json_encode(['success' => true, 'data' => $ticket_numbers]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch record.']);
        }

        wp_die();
    }

    public static function generateCompetitionTicketsNonSequential($total_sell_tickets = 0)
    {

        global $wpdb;

        $competition_id = $_REQUEST['record'];

        $ticket_table = $wpdb->prefix . 'competition_tickets';

        $created = false;

        if ($total_sell_tickets == 0) {

            $total_sell_tickets = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT total_sell_tickets FROM {$wpdb->prefix}competitions WHERE id = %s",
                    $competition_id
                )
            );
        }

        $regenerate_tickets = 0;

        if (!empty($total_sell_tickets)) {

            $uniqueTickets = self::generateRandomTicket($total_sell_tickets[0]);

            if (!empty($uniqueTickets)) {

                foreach ($uniqueTickets as $ticket) {

                    $data = array(
                        'competition_id' => $_REQUEST['record'],
                        'ticket_number' => $ticket,
                    );

                    $created = $wpdb->insert($ticket_table, $data);

                    if (!$created)
                        $regenerate_tickets += 1;
                }
            }
        }

        if ($regenerate_tickets > 0) {

            self::generate_ticket_numbers($regenerate_tickets);
        } else {

            return $created;
        }
    }

    public static function getRandomCharacters($characters, $length)
    {
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, strlen($characters) - 1);
            $result .= $characters[$index];
        }
        return $result;
    }

    public static function generateRandomCombinationTicket($totalCount)
    {
        $letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $numbers = "0123456789";

        $tickets = array();

        while (count($tickets) <= $totalCount) {

            $ticket = '';

            $numLetters = 0;

            $numNumbers = 0;

            for ($i = 0; $i < $totalCount; $i++) {
                $isLetter = mt_rand(0, 1); // Randomly choose between letter or number
                if ($isLetter && $numLetters < 4) {
                    $ticket .= self::getRandomCharacters($letters, 1); // Add one random letter
                    $numLetters++;
                } elseif (!$isLetter && $numNumbers < 6) {
                    $ticket .= self::getRandomCharacters($numbers, 1); // Add one random number
                    $numNumbers++;
                } else {
                    $i--; // If we cannot add a letter or number, repeat this iteration
                }
            }
            $tickets[$ticket] = true;
        }
        return array_keys($tickets);
    }


    public static function generateRandomTicket($totalCount)
    {

        $numbers = "0123456789";

        $tickets = [];

        while (count($tickets) < $totalCount) {

            $ticket = '';

            for ($i = 0; $i < 8; $i++) {

                $index = rand(0, strlen($numbers) - 1);

                $ticket .= $numbers[$index];
            }
            if (!in_array($ticket, $tickets)) {
                $tickets[] = $ticket;
            }
        }

        return $tickets;
    }

    public static function getCompetitionInstantPrizes($id)
    {

        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes WHERE competition_id = %s", $id);

        $instant_wins = $wpdb->get_results($query, ARRAY_A);

        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes_tickets WHERE competition_id = %s", $id);

        $prize_res = $wpdb->get_results($query, ARRAY_A);

        $prize_tickets = [];

        if (!empty($prize_res)) {

            foreach ($prize_res as $res) {

                $prize_tickets[$res['instant_id']][] = $res['ticket_number'];
            }
        }

        if (!empty($instant_wins)) {

            foreach ($instant_wins as $index => $instant_win) {

                $instant_wins[$index]['tickets'] = implode(",", $prize_tickets[$instant_win['id']]);
            }
        }

        return $instant_wins;
    }

    public static function getCompetitionRewardPrizes($id)
    {

        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_reward WHERE competition_id = %s", $id);

        $reward_wins = $wpdb->get_results($query, ARRAY_A);

        return $reward_wins;
    }

    function save_temp_competition_record()
    {



        global $wpdb;

        $table_name = $wpdb->prefix . 'competitions_temp';

        $main_table = $wpdb->prefix . 'competitions';

        $ticket_temp = $wpdb->prefix . 'competition_tickets_temp';

        $ticket_table = $wpdb->prefix . 'competition_tickets';

        if (
            isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit' &&
            isset($_REQUEST['record']) && $_REQUEST['record'] > 0
        ) {

            $temp_record = $wpdb->get_row("SELECT * FROM " . $table_name . " WHERE record = '" . $_REQUEST['record'] . "'");

            if (empty($temp_record)) {

                $entry = $wpdb->get_row("SELECT * FROM " . $main_table . " WHERE id = '" . $_REQUEST['record'] . "'", ARRAY_A);

                $entry['record'] = $_REQUEST['record'];

                unset($entry['created_at']);

                unset($entry['updated_at']);

                unset($entry['id']);

                $entry['instant_prizes'] = json_encode(self::getCompetitionInstantPrizes($_REQUEST['record']));

                $entry['reward_prizes'] = json_encode(self::getCompetitionRewardPrizes($_REQUEST['record']));

                $wpdb->insert($table_name, $entry);

                $comp_tickets_purchased = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT count(*) as total_tickets FROM $ticket_table WHERE competition_id = %s and is_purchased = 1",
                        $_REQUEST['record']
                    )
                );

                if ($entry['total_ticket_sold'] > 0 && $comp_tickets_purchased) {
                    //do nothing
                } else {

                    // create clone of tickets into temp_tickets
                    $source_data = $wpdb->get_results("SELECT competition_id,ticket_number FROM {$ticket_table} where competition_id = '" . $_REQUEST['record'] . "'", ARRAY_A);

                    if (!empty($source_data)) {

                        $query = "INSERT INTO $ticket_temp (competition_id, ticket_number) VALUES ";

                        $placeholders = array();

                        foreach ($source_data as $row) {
                            $placeholders[] = $wpdb->prepare('(%s, %s)', $row['competition_id'], $row['ticket_number']);
                        }

                        $query .= implode(', ', $placeholders);

                        $wpdb->query($query);
                    }
                }
            }


            if ($_REQUEST['step'] == 'details') {

                $gallery_videos = $_REQUEST['gallery_video_urls'];
                $gallery_video_type = $_REQUEST['gallery_video_type'];
                $gallery_video_thumb = $_REQUEST['gallery_video_thumb'];


                $videoURLS = [];

                // if (!empty($gallery_videos)) {

                //     foreach ($gallery_videos as $key => $gallery_video) {

                //         $videoURLS[$key][$gallery_video_type[$key]] = $gallery_video;
                //     }

                //     $videoURLS = json_encode($videoURLS);
                // }

                if (!empty($gallery_videos)) {
                    foreach ($gallery_videos as $key => $gallery_video) {
                        $videoURLS[$key] = [
                            $gallery_video_type[$key] => $gallery_video,
                            'thumb' => $gallery_video_thumb[$key]
                        ];
                    }
                    $videoURLS = json_encode($videoURLS);
                }

                $image_name = basename($_REQUEST['featured_image']);
                $time = current_time('mysql'); // Current time for directory structure
                $m = substr($time, 5, 2); // Extract the month from the current date
                $data = array(
                    'title' => $_REQUEST['title'],
                    'category' => $_REQUEST['category'],
                    'via_mobile_app' => $_REQUEST['via_mobile_app'],
                    'description' => $_REQUEST['description'],
                    'image' => $_REQUEST['featured_image'],
                    'images_thumb' =>  "/" . $m  . "/" . $image_name,
                    'images_thumb_cat' =>  "/" . $m  . "/" . $image_name,

                    'gallery_images' => $_REQUEST['gallery_image'],
                    'promotional_messages' => $_REQUEST['promotional_messages'],
                    'instant_win_only' => $_REQUEST['instant_win_only'],
                    'gallery_videos' => $videoURLS,
                    'slider_sorting' => $_REQUEST['sortedSelections']
                );
            }

            if ($_REQUEST['step'] == 'products') {

                $data = array(
                    'status' => $_REQUEST['status'],
                    'price_per_ticket' => $_REQUEST['price_per_ticket'],
                    'cash' => $_REQUEST['cash'],
                    'prize_value' => $_REQUEST['prize_value'],
                    'total_sell_tickets' => $_REQUEST['total_sell_tickets'],
                    'max_ticket_per_user' => $_REQUEST['max_ticket_per_user'],
                    'quantity' => $_REQUEST['quantity'],
                    'sale_price' => $_REQUEST['sale_price'] ? $_REQUEST['sale_price'] : null,
                    'sale_start_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['sale_start_date']),
                    'sale_end_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['sale_end_date']),
                    'short_description' => $_REQUEST['short_description'],
                    'draw_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['draw_date']),
                    'draw_time' => $_REQUEST['draw_time'],
                    'closing_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['closing_date']),
                    'closing_time' => $_REQUEST['closing_time'],
                    'live_draw' => $_REQUEST['live_draw'],
                    'live_draw_info' => $_REQUEST['live_draw_info'],
                    'hide_timer' => $_REQUEST['hide_timer'],
                    'hide_ticket_count' => $_REQUEST['hide_ticket_count'],
                    'disable_tickets' => $_REQUEST['disable_tickets'],
                    'is_featured' => $_REQUEST['is_featured'],
                    'prize_type' => $_REQUEST['prize_type'],
                    'total_winners' => $_REQUEST['total_winners'],
                    'points' => $_REQUEST['points'],
                    'competitions_prize' => $_REQUEST['competitions_prize'],
                    'prize_tickets' => $_REQUEST['prize_tickets'],
                    'web_order' => $_REQUEST['webOrder'],
                    'sale_start_time' => $_REQUEST['sale_price_start_time_hour'] . ':' . $_REQUEST['sale_price_start_time_minute'],
                    'sale_end_time' => $_REQUEST['sale_price_end_time_hour'] . ':' . $_REQUEST['sale_price_end_time_minute'],
                );
            }

            if ($_REQUEST['step'] == 'question') {

                $_REQUEST['save_original'];

                if ($_REQUEST['save_original'] === true) {

                    $data = $wpdb->get_row("select comp_question, question, question_options, correct_answer from " . $wpdb->prefix . "competitions where id = '" . $_REQUEST['record'] . "'", ARRAY_A);
                } else {

                    $data = array(
                        'comp_question' => $_REQUEST['comp_question'],
                        'question' => $_REQUEST['question'],
                        'question_options' => json_encode(["answer1" => $_REQUEST['answer1'], "answer2" => $_REQUEST['answer2'], "answer3" => $_REQUEST['answer3']]),
                        'correct_answer' => $_REQUEST['correct_answer']
                    );
                }
            }

            if ($_REQUEST['step'] == 'legals') {

                $data = array(
                    'competition_rules' => $_REQUEST['competition_rules'],
                    'faq' => $_REQUEST['faq']
                );
            }

            if ($_REQUEST['step'] == 'instant') {

                $instant_prizes = [];

                if ($_REQUEST['total_prizes'] > 0) {

                    $total_prizes = $_REQUEST['total_prizes'];

                    // error_log("save_temp_competition_record " . print_r($_REQUEST, true));


                    for ($i = 1; $i <= $total_prizes; $i++) {

                        $instant_prizes[$i] = array(
                            'title' => $_REQUEST['title' . $i],
                            'type' => $_REQUEST['price_type' . $i],
                            'value' => $_REQUEST['cash_value' . $i],
                            'quantity' => $_REQUEST['quantity' . $i],
                            'web_order_instant' => $_REQUEST['webOrderInstant' . $i] ? (int) $_REQUEST['webOrderInstant' . $i] : 0,
                            'prize_value' => $_REQUEST['prize_value' . $i],
                            'image' => $_REQUEST['image' . $i],
                            'competition_prize' => $_REQUEST['competition_prize' . $i],
                            'prize_total_tickets' => $_REQUEST['prize_total_tickets' . $i],
                            'show_description' => $_REQUEST['show_description' . $i],
                            'prize_description' => $_REQUEST['prize_description' . $i]
                        );

                        if (isset($_REQUEST['quantity' . $i]) && $_REQUEST['quantity' . $i] > 0) {

                            $totalTickets = $_REQUEST['quantity' . $i];

                            $prize_tickets = [];

                            for ($j = 1; $j <= $totalTickets; $j++) {

                                $prize_tickets[] = $_REQUEST["ticket" . $i . "_" . $j];
                            }

                            $instant_prizes[$i]['tickets'] = implode(",", $prize_tickets);
                        }
                    }
                }

                $data = array(
                    'enable_instant_wins' => $_REQUEST['enable_instant_wins'],
                    'instant_prizes' => json_encode($instant_prizes)
                );
            }

            if ($_REQUEST['step'] == 'reward') {

                $reward_prizes = [];

                if ($_REQUEST['enable_reward_wins'] > 0) {

                    $total_reward = $_REQUEST['total_reward'];

                    for ($i = 1; $i <= $total_reward; $i++) {

                        $reward_prizes[$i] = array(
                            'title' => $_REQUEST['title' . $i],
                            'type' => $_REQUEST['price_type' . $i],
                            'value' => $_REQUEST['cash_value' . $i],
                            'web_order_reward' => $_REQUEST['webOrderReward' . $i] ? (int) $_REQUEST['webOrderReward' . $i] : 0,
                            'prize_value' => $_REQUEST['prize_value' . $i],
                            'prcnt_available' => $_REQUEST['prct_available' . $i],
                            'image' => $_REQUEST['image' . $i],
                            'competition_prize' => $_REQUEST['competition_prize' . $i],
                            'prize_total_tickets' => $_REQUEST['prize_total_tickets' . $i],
                        );
                    }
                }

                $data = array(
                    'enable_reward_wins' => $_REQUEST['enable_reward_wins'],
                    'reward_prizes' => json_encode($reward_prizes)
                );
            }



            $updated = $wpdb->update($table_name, $data, ['record' => $_REQUEST['record']]);
        }

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Competition updated successfully!', 'record' => $_REQUEST['record'], 'saveoriginal' => $_REQUEST['save_original'] === true]);
        } else {
            echo json_encode(['success' => false, 'saveoriginal' => $_REQUEST['save_original'] === true, 'data' => $data]);
        }

        wp_die();
    }
    function save_temp_competition_record_draft()
    {



        global $wpdb;

        $table_name = $wpdb->prefix . 'competitions_temp';

        $main_table = $wpdb->prefix . 'competitions';

        $ticket_temp = $wpdb->prefix . 'competition_tickets_temp';

        $ticket_table = $wpdb->prefix . 'competition_tickets';

        if (
            isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'edit' &&
            isset($_REQUEST['record']) && $_REQUEST['record'] > 0
        ) {

            $temp_record = $wpdb->get_row("SELECT * FROM " . $table_name . " WHERE record = '" . $_REQUEST['record'] . "'");

            if (empty($temp_record)) {

                $entry = $wpdb->get_row("SELECT * FROM " . $main_table . " WHERE id = '" . $_REQUEST['record'] . "'", ARRAY_A);

                $entry['record'] = $_REQUEST['record'];

                unset($entry['created_at']);

                unset($entry['updated_at']);

                unset($entry['id']);

                $entry['instant_prizes'] = json_encode(self::getCompetitionInstantPrizes($_REQUEST['record']));

                $entry['reward_prizes'] = json_encode(self::getCompetitionRewardPrizes($_REQUEST['record']));

                $wpdb->insert($table_name, $entry);

                $comp_tickets_purchased = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT count(*) as total_tickets FROM $ticket_table WHERE competition_id = %s and is_purchased = 1",
                        $_REQUEST['record']
                    )
                );

                if ($entry['total_ticket_sold'] > 0 && $comp_tickets_purchased) {
                    //do nothing
                } else {

                    // create clone of tickets into temp_tickets
                    $source_data = $wpdb->get_results("SELECT competition_id,ticket_number FROM {$ticket_table} where competition_id = '" . $_REQUEST['record'] . "'", ARRAY_A);

                    if (!empty($source_data)) {

                        $query = "INSERT INTO $ticket_temp (competition_id, ticket_number) VALUES ";

                        $placeholders = array();

                        foreach ($source_data as $row) {
                            $placeholders[] = $wpdb->prepare('(%s, %s)', $row['competition_id'], $row['ticket_number']);
                        }

                        $query .= implode(', ', $placeholders);

                        $wpdb->query($query);
                    }
                }
            }


            if ($_REQUEST['step'] == 'details') {

                $gallery_videos = $_REQUEST['gallery_video_urls'];
                $gallery_video_type = $_REQUEST['gallery_video_type'];
                $gallery_video_thumb = $_REQUEST['gallery_video_thumb'];


                $videoURLS = [];

                // if (!empty($gallery_videos)) {

                //     foreach ($gallery_videos as $key => $gallery_video) {

                //         $videoURLS[$key][$gallery_video_type[$key]] = $gallery_video;
                //     }

                //     $videoURLS = json_encode($videoURLS);
                // }

                if (!empty($gallery_videos)) {
                    foreach ($gallery_videos as $key => $gallery_video) {
                        $videoURLS[$key] = [
                            $gallery_video_type[$key] => $gallery_video,
                            'thumb' => $gallery_video_thumb[$key]
                        ];
                    }
                    $videoURLS = json_encode($videoURLS);
                }

                $image_name = basename($_REQUEST['featured_image']);
                $time = current_time('mysql'); // Current time for directory structure
                $m = substr($time, 5, 2); // Extract the month from the current date
                $data = array(
                    'title' => $_REQUEST['title'],
                    'category' => $_REQUEST['category'],
                    'via_mobile_app' => $_REQUEST['via_mobile_app'],
                    'description' => $_REQUEST['description'],
                    'image' => $_REQUEST['featured_image'],
                    'images_thumb' =>  "/" . $m  . "/" . $image_name,
                    'images_thumb_cat' =>  "/" . $m  . "/" . $image_name,

                    'gallery_images' => $_REQUEST['gallery_image'],
                    'promotional_messages' => $_REQUEST['promotional_messages'],
                    'instant_win_only' => $_REQUEST['instant_win_only'],
                    'gallery_videos' => $videoURLS,
                    'slider_sorting' => $_REQUEST['sortedSelections']
                );
            }

            if ($_REQUEST['step'] == 'products') {

                $data = array(
                    'status' => $_REQUEST['status'],
                    'price_per_ticket' => $_REQUEST['price_per_ticket'],
                    'cash' => $_REQUEST['cash'],
                    'prize_value' => $_REQUEST['prize_value'],
                    'total_sell_tickets' => $_REQUEST['total_sell_tickets'],
                    'max_ticket_per_user' => $_REQUEST['max_ticket_per_user'],
                    'quantity' => $_REQUEST['quantity'],
                    'sale_price' => $_REQUEST['sale_price'] ? $_REQUEST['sale_price'] : null,
                    'sale_start_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['sale_start_date']),
                    'sale_end_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['sale_end_date']),
                    'short_description' => $_REQUEST['short_description'],
                    'draw_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['draw_date']),
                    'draw_time' => $_REQUEST['draw_time'],
                    'closing_date' => Competitions_Admin::convertIntoUTCFormat($_REQUEST['closing_date']),
                    'closing_time' => $_REQUEST['closing_time'],
                    'live_draw' => $_REQUEST['live_draw'],
                    'live_draw_info' => $_REQUEST['live_draw_info'],
                    'hide_timer' => $_REQUEST['hide_timer'],
                    'hide_ticket_count' => $_REQUEST['hide_ticket_count'],
                    'disable_tickets' => $_REQUEST['disable_tickets'],
                    'is_featured' => $_REQUEST['is_featured'],
                    'is_draft' => 1,
                    'prize_type' => $_REQUEST['prize_type'],
                    'total_winners' => $_REQUEST['total_winners'],
                    'points' => $_REQUEST['points'],
                    'competitions_prize' => $_REQUEST['competitions_prize'],
                    'prize_tickets' => $_REQUEST['prize_tickets'],
                    'web_order' => $_REQUEST['webOrder'],
                    'sale_start_time' => $_REQUEST['sale_price_start_time_hour'] . ':' . $_REQUEST['sale_price_start_time_minute'],
                    'sale_end_time' => $_REQUEST['sale_price_end_time_hour'] . ':' . $_REQUEST['sale_price_end_time_minute'],
                );
            }

            if ($_REQUEST['step'] == 'question') {

                $_REQUEST['save_original'];

                if ($_REQUEST['save_original'] === true) {

                    $data = $wpdb->get_row("select comp_question, question, question_options, correct_answer from " . $wpdb->prefix . "competitions where id = '" . $_REQUEST['record'] . "'", ARRAY_A);
                } else {

                    $data = array(
                        'comp_question' => $_REQUEST['comp_question'],
                        'question' => $_REQUEST['question'],
                        'question_options' => json_encode(["answer1" => $_REQUEST['answer1'], "answer2" => $_REQUEST['answer2'], "answer3" => $_REQUEST['answer3']]),
                        'correct_answer' => $_REQUEST['correct_answer']
                    );
                }
            }

            if ($_REQUEST['step'] == 'legals') {

                $data = array(
                    'competition_rules' => $_REQUEST['competition_rules'],
                    'faq' => $_REQUEST['faq']
                );
            }

            if ($_REQUEST['step'] == 'instant') {

                $instant_prizes = [];

                if ($_REQUEST['total_prizes'] > 0) {

                    $total_prizes = $_REQUEST['total_prizes'];

                    // error_log("save_temp_competition_record " . print_r($_REQUEST, true));


                    for ($i = 1; $i <= $total_prizes; $i++) {

                        $instant_prizes[$i] = array(
                            'title' => $_REQUEST['title' . $i],
                            'type' => $_REQUEST['price_type' . $i],
                            'value' => $_REQUEST['cash_value' . $i],
                            'quantity' => $_REQUEST['quantity' . $i],
                            'web_order_instant' => $_REQUEST['webOrderInstant' . $i] ? (int) $_REQUEST['webOrderInstant' . $i] : 0,
                            'prize_value' => $_REQUEST['prize_value' . $i],
                            'image' => $_REQUEST['image' . $i],
                            'competition_prize' => $_REQUEST['competition_prize' . $i],
                            'prize_total_tickets' => $_REQUEST['prize_total_tickets' . $i],
                            'show_description' => $_REQUEST['show_description' . $i],
                            'prize_description' => $_REQUEST['prize_description' . $i]
                        );

                        if (isset($_REQUEST['quantity' . $i]) && $_REQUEST['quantity' . $i] > 0) {

                            $totalTickets = $_REQUEST['quantity' . $i];

                            $prize_tickets = [];

                            for ($j = 1; $j <= $totalTickets; $j++) {

                                $prize_tickets[] = $_REQUEST["ticket" . $i . "_" . $j];
                            }

                            $instant_prizes[$i]['tickets'] = implode(",", $prize_tickets);
                        }
                    }
                }

                $data = array(
                    'enable_instant_wins' => $_REQUEST['enable_instant_wins'],
                    'instant_prizes' => json_encode($instant_prizes)
                );
            }

            if ($_REQUEST['step'] == 'reward') {

                $reward_prizes = [];

                if ($_REQUEST['enable_reward_wins'] > 0) {

                    $total_reward = $_REQUEST['total_reward'];

                    for ($i = 1; $i <= $total_reward; $i++) {

                        $reward_prizes[$i] = array(
                            'title' => $_REQUEST['title' . $i],
                            'type' => $_REQUEST['price_type' . $i],
                            'value' => $_REQUEST['cash_value' . $i],
                            'web_order_reward' => $_REQUEST['webOrderReward' . $i] ? (int) $_REQUEST['webOrderReward' . $i] : 0,
                            'prize_value' => $_REQUEST['prize_value' . $i],
                            'prcnt_available' => $_REQUEST['prct_available' . $i],
                            'image' => $_REQUEST['image' . $i],
                            'competition_prize' => $_REQUEST['competition_prize' . $i],
                            'prize_total_tickets' => $_REQUEST['prize_total_tickets' . $i],
                        );
                    }
                }

                $data = array(
                    'enable_reward_wins' => $_REQUEST['enable_reward_wins'],
                    'reward_prizes' => json_encode($reward_prizes),
                    'is_draft' => 1
                );
            }



            $updated = $wpdb->update($table_name, $data, ['record' => $_REQUEST['record']]);
        }

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Competition updated successfully!', 'record' => $_REQUEST['record'], 'saveoriginal' => $_REQUEST['save_original'] === true]);
        } else {
            echo json_encode(['success' => false, 'saveoriginal' => $_REQUEST['save_original'] === true, 'data' => $data]);
        }

        wp_die();
    }

    public static function updateDrawDatePassedCompetitions()
    {

        global $wpdb;

        $main_table = $wpdb->prefix . 'competitions';

        $today = date("Y-m-d");

        $wpdb->query($wpdb->prepare("UPDATE $main_table SET status = %s WHERE status = %s and draw_date < %s and is_draft = '0'", array('Closed', 'Open', $today)));
    }
    public static function updateDrawDatePassedCompetitionss()
    {

        global $wpdb;

        $main_table = $wpdb->prefix . 'competitions';

        $today = date("Y-m-d");

        $wpdb->query($wpdb->prepare("UPDATE $main_table SET status = %s WHERE status = %s and draw_date < %s and is_draft = '0'", array('Closed', 'Open', $today)));
    }

    public static function update_global_settings()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'global_settings';

        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
            echo json_encode(['success' => false, 'message' => 'Database table not found.']);
            wp_die();
        }

        // Fetch the current record if it exists
        $recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);

        // Sanitize and validate input data
        $data = array(
            'live_draw_info'     => $_REQUEST['live_draw_info'],
            'postal_entry_info'  => $_REQUEST['postal_entry_info'],
            'main_competition'   => $_REQUEST['main_competition'],
            'instant_wins_info'  => $_REQUEST['instant_wins_info'],
            'reward_prize_info'  => $_REQUEST['reward_prize_info'],
            'work_step_1'        => $_REQUEST['work_step_1'],
            'work_step_2'        => $_REQUEST['work_step_2'],
            'work_step_3'        => $_REQUEST['work_step_3'],
            'slider_speed'       => intval($_REQUEST['slider_speed']),
            'suggested_tickets'  => intval($_REQUEST['suggested_tickets']),
            'announcement'       => wp_kses_post($_REQUEST['announcement']),
            'competition_rules'  => wp_kses_post($_REQUEST['competition_rules']),
            'competition_faq'    => wp_kses_post($_REQUEST['competition_faq']),
            'frontend_scripts'   => wp_kses_post($_REQUEST['manageScripts']),
        );

        // Define format for each field (ensure the order matches $data array)
        $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s');

        // If no record exists, insert new settings, else update
        if (empty($recordData)) {
            $created = $wpdb->insert($table_name, $data, $format); // Use insert for new record
        } else {
            $where = ['id' => $recordData['id']];
            $created = $wpdb->update($table_name, $data, $where, $format); // Use update for existing record
        }

        // Return success or error response
        if ($created !== false) {
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Oops! Something went wrong.']);
        }

        wp_die(); // Required to end AJAX requests
    }



    public static function update_statistics_winner_prize()
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'global_settings';

        // Fetch the current record if it exists
        $recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);

        $winner_stat = str_replace(',', '', sanitize_text_field($_REQUEST['winner_stat']));
        $prizes_stat = str_replace(',', '', sanitize_text_field($_REQUEST['prizes_stat']));


        $data = array(
            'winner_stat' => $winner_stat,
            'prizes_stat' => $prizes_stat,

        );
        // Define format for each field
        $format = array('%d', '%d');
        // If no record exists, insert new settings, else update


        if (empty($recordData)) {
            $created = $wpdb->insert($table_name, $data, $format); // Use insert for new record
        } else {
            $where = ['id' => $recordData['id']];
            $created = $wpdb->update($table_name, $data, $where, $format, ['%d']); // Use update for existing record
        }

        // Return success or error response
        if ($created !== false) {
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Oops! Something went wrong.']);
        }

        wp_die(); // Required to end AJAX requests

    }

    public static function update_statistics_charity_followrs()
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'global_settings';

        // Fetch the current record if it exists
        $recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);


        $donated_stat = str_replace(',', '', sanitize_text_field($_REQUEST['donated_stat']));
        $followers_stat = str_replace(',', '', sanitize_text_field($_REQUEST['followers_stat']));

        $data = array(
            'donated_stat' => $donated_stat,
            'followers_stat' => $followers_stat,

        );

        $format = array('%d', '%d');
        // If no record exists, insert new settings, else update
        if (empty($recordData)) {
            $created = $wpdb->insert($table_name, $data, $format); // Use insert for new record
        } else {
            $where = ['id' => $recordData['id']];
            $created = $wpdb->update($table_name, $data, $where, $format, ['%d']); // Use update for existing record
        }

        // Return success or error response
        if ($created !== false) {
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Oops! Something went wrong.']);
        }

        wp_die(); // Required to end AJAX requests
    }


    // public static function update_cometchat_pinned_message()
    // {
    //     global $wpdb;

    //     // Get the table name with prefix
    //     $table_name = $wpdb->prefix . 'global_settings';

    //     // Fetch existing record
    //     $recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);

    //     // Sanitize incoming data
    //     $pinned_message = sanitize_text_field($_REQUEST['pinned_message']);
    //     $show_pinned_message = isset($_REQUEST['show_pinned_message']) ? intval($_REQUEST['show_pinned_message']) : 0;

    //     // Log sanitized values for debugging
    //     error_log('pinned_message: ' . print_r($pinned_message, true));
    //     error_log('show_pinned_message: ' . print_r($show_pinned_message, true));

    //     // Prepare data for insert or update
    //     $data = array(
    //         'pinnedMessage' => $pinned_message,
    //         'showpinnedMessage' => $show_pinned_message,
    //     );

    //     // Format for the database columns
    //     $format = array('%s', '%d');

    //     // If no record exists, insert new settings, else update
    //     if (empty($recordData)) {
    //         $created = $wpdb->insert($table_name, $data, $format); // Insert new record
    //     } else {
    //         $where = array('id' => $recordData['id']);
    //         $created = $wpdb->update($table_name, $data, $where, $format, array('%d')); // Update existing record
    //     }

    //     // Return success or error response
    //     if ($created !== false) {
    //         wp_send_json_success(array('message' => 'Settings saved successfully!'));
    //     } else {
    //         wp_send_json_error(array('message' => 'Oops! Something went wrong.'));
    //     }

    //     wp_die(); // Required to end AJAX requests
    // }

    public static function update_cometchat_pinned_message()
    {
        global $wpdb;

        // wp_send_json_success(array('message' => 'testing'));
        // die('here');
        // Get the table name with prefix
        $table_name = $wpdb->prefix . 'global_settings';

        // Fetch existing record
        $recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);

        // Define allowed HTML tags for the pinned message
        $allowed_html = array(
            'a' => array(
                'href' => array(),
                'title' => array(),
                'target' => array(),
            ),
            'strong' => array(),
            'em' => array(),
            'br' => array(),
        );

        // Sanitize incoming data
        $pinned_message = wp_kses($_REQUEST['pinned_message'], $allowed_html);
        $show_pinned_message = isset($_REQUEST['show_pinned_message']) ? intval($_REQUEST['show_pinned_message']) : 0;

        // Log sanitized values for debugging
        error_log('pinned_message: ' . print_r($pinned_message, true));
        error_log('show_pinned_message: ' . print_r($show_pinned_message, true));

        // Prepare data for insert or update
        $data = array(
            'pinnedMessage' => $pinned_message,
            'showpinnedMessage' => $show_pinned_message,
        );

        // Format for the database columns
        $format = array('%s', '%d');

        // If no record exists, insert new settings, else update
        if (empty($recordData)) {
            $created = $wpdb->insert($table_name, $data, $format); // Insert new record
        } else {
            $where = array('id' => $recordData['id']);
            $created = $wpdb->update($table_name, $data, $where, $format, array('%d')); // Update existing record
        }

        // Return success or error response
        if ($created !== false) {
            wp_send_json_success(array('message' => 'Settings saved successfully!'));
        } else {
            wp_send_json_error(array('message' => 'Oops! Something went wrong.'));
        }

        wp_die(); // Required to end AJAX requests
    }





    function update_seo_settings()
    {

        global $wpdb;

        $seo_settings = $wpdb->prefix . 'seo_settings';

        $created = false;

        if (isset($_REQUEST['total_seo_pages']) && $_REQUEST['total_seo_pages'] > 0) {

            $wpdb->query("delete from " . $seo_settings);

            $total_prizes = $_REQUEST['total_seo_pages'];

            for ($i = 1; $i <= $total_prizes; $i++) {

                $data = array(
                    'page' => $_REQUEST['page' . $i],
                    'page_title' => $_REQUEST['page_title' . $i],
                    'meta_title' => $_REQUEST['meta_title' . $i],
                    'meta_description' => $_REQUEST['meta_description' . $i],
                );

                $created = $wpdb->insert($seo_settings, $data);
            }
        }

        if ($created) {
            echo json_encode(['success' => true, 'message' => 'Settings saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Oops! something went wrong.']);
        }

        wp_die();
    }

    function create_product_for_competition($record)
    {

        global $wpdb;

        $main_table = $wpdb->prefix . 'competitions';

        $entry = $wpdb->get_row("SELECT * FROM " . $main_table . " WHERE id = '" . $record . "'", ARRAY_A);

        if (!empty($entry)) {

            if (isset($entry['competition_product_id']) && $entry['competition_product_id'] > 0) {

                $product_id = $entry['competition_product_id'];
            } else {

                $product_id = wp_insert_post(
                    array(
                        'post_title' => $entry['title'],
                        'post_content' => $entry['short_description'],
                        'post_status' => 'publish',
                        'post_type' => 'product',
                    )
                );

                // Check if the product creation was successful
                if ($product_id && !is_wp_error($product_id)) {

                    if ($product_id) {

                        $wpdb->update(
                            $main_table,
                            array('competition_product_id' => $product_id), // Update with the desired new value
                            array('id' => $record)
                        );
                    }
                }
            }

            update_post_meta($product_id, '_regular_price', $entry['price_per_ticket']);
            update_post_meta($product_id, '_price', $entry['price_per_ticket']);
            update_post_meta($product_id, '_visibility', 'visible');
            update_post_meta($product_id, '_stock_status', 'instock');
            update_post_meta($product_id, 'total_sales', '0');
            update_post_meta($product_id, '_downloadable', 'no');
            update_post_meta($product_id, '_virtual', 'no');
            update_post_meta($product_id, '_purchase_note', '');
            update_post_meta($product_id, '_featured', 'yes'); // Set product as featured
            update_post_meta($product_id, '_weight', '');
            update_post_meta($product_id, '_length', '');
            update_post_meta($product_id, '_width', '');
            update_post_meta($product_id, '_height', '');
            update_post_meta($product_id, '_sku', '');
            update_post_meta($product_id, '_product_attributes', array());
            update_post_meta($product_id, '_sale_price_dates_from', $entry['sale_start_date']);
            update_post_meta($product_id, '_sale_price_dates_to', $entry['sale_end_date']);
            update_post_meta($product_id, '_sale_price', $entry['sale_price']);
            update_post_meta($product_id, '_sold_individually', '');
            update_post_meta($product_id, '_manage_stock', 'yes');
            update_post_meta($product_id, '_backorders', 'no');
            update_post_meta($product_id, '_stock', $entry['total_sell_tickets']);

            // Set the featured image
            if (!empty($entry['image'])) {
                // Add featured image to the product
                $image_url = $entry['image'];
                $image_name = basename($image_url);
                $image_data = file_get_contents($image_url);
                $upload_dir = wp_upload_dir();
                $upload_path = $upload_dir['path'] . '/' . $image_name;
                file_put_contents($upload_path, $image_data);
                $wp_filetype = wp_check_filetype($image_name, null);
                //$attachment = array(
                //    'post_mime_type' => $wp_filetype['type'],
                //    'post_title' => sanitize_file_name($image_name),
                //    'post_content' => '',
                //    'post_status' => 'inherit'
                //);
                $attachment_id = attachment_url_to_postid($image_url);

                if ($attachment_id) {
                    // Update the attachment metadata if necessary (e.g., updating title, description, etc.)
                    $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_path);
                    wp_update_attachment_metadata($attachment_id, $attachment_data);
                    set_post_thumbnail($product_id, $attachment_id);
                    // You can update post meta or other fields here as needed
                    update_post_meta($product_id, '_custom_image_id', $attachment_id);
                } else {
                    // If the attachment does not exist, insert a new one
                    $attachment = [
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => sanitize_file_name($image_name),
                        'post_content'   => '',
                        'post_status'    => 'inherit',
                    ];

                    // Insert the new attachment
                    $attach_id = wp_insert_attachment($attachment, $upload_path, $product_id);

                    // Generate and update attachment metadata (thumbnails, sizes, etc.)
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    $attach_data = wp_generate_attachment_metadata($attach_id, $upload_path);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    set_post_thumbnail($product_id, $attach_id); // Set the attached image as featured image

                    // Save attachment ID in post meta for future use
                    update_post_meta($product_id, '_custom_image_id', $attach_id);
                }
            }

            if ($entry['comp_question'] == 1) {

                $question_id = $entry['question_id'];
                $question = $entry['question'];
                $question_options = $entry['question_options'];
                $correct_answer = $entry['correct_answer'];
                $question_options = json_decode($question_options, true);
                if ($question_id > 0) {

                    $existing_question = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "global_questions WHERE id = %d", $question_id));

                    if (!empty($existing_question)) {


                        if (
                            $existing_question->question === $question &&
                            $existing_question->answer1 === $question_options['answer1'] &&
                            $existing_question->answer2 === $question_options['answer2'] &&
                            $existing_question->answer3 === $question_options['answer3'] &&
                            $existing_question->correct_option === $correct_answer
                        ) {

                            // Do nothing

                        } else {
                            $wpdb->update(
                                $wpdb->prefix . 'global_questions',
                                array(
                                    'question' => $question,
                                    'options' => $entry['question_options'],
                                    'answer1' => $question_options['answer1'],
                                    'answer2' => $question_options['answer2'],
                                    'answer3' => $question_options['answer3'],
                                    'correct_option' => $correct_answer
                                ),
                                array('id' => $existing_question->id)
                            );
                        }
                    }
                } else {

                    $wpdb->insert(
                        $wpdb->prefix . 'global_questions',
                        array(
                            'question' => $question,
                            'options' => $entry['question_options'],
                            'answer1' => $question_options['answer1'],
                            'answer2' => $question_options['answer2'],
                            'answer3' => $question_options['answer3'],
                            'correct_option' => $correct_answer,
                            'type' => 'competition'
                        )
                    );

                    $wpdb->update($main_table, array('question_id' => $wpdb->insert_id), array('id' => $entry['id']));
                }
            }
        }
    }

    public static function save_global_question()
    {
        global $wpdb;

        if (isset($_POST['mode']) && $_POST['mode'] == 'create') {

            $wpdb->insert(
                $wpdb->prefix . 'global_questions',
                array(
                    'question' => $_POST['question'],
                    'options' => json_encode(['answer1' => $_POST['answer1'], "answer2" => $_POST['answer2'], "answer3" => $_POST['answer3']]),
                    'answer1' => $_POST['answer1'],
                    'answer2' => $_POST['answer2'],
                    'answer3' => $_POST['answer3'],
                    'correct_option' => $_POST[$_POST['correct_option']],
                    'type' => 'global'
                )
            );
        }

        if (isset($_POST['mode']) && $_POST['mode'] == 'edit') {

            $wpdb->update(
                $wpdb->prefix . 'global_questions',
                array(
                    'question' => $_POST['question'],
                    'options' => json_encode(['answer1' => $_POST['answer1'], "answer2" => $_POST['answer2'], "answer3" => $_POST['answer3']]),
                    'answer1' => $_POST['answer1'],
                    'answer2' => $_POST['answer2'],
                    'answer3' => $_POST['answer3'],
                    'correct_option' => $_POST[$_POST['correct_option']],
                    'type' => 'global'
                ),
                ['id' => $_POST['id']]
            );
        }
        wp_redirect(admin_url('/admin.php?page=question-settings'));
        exit;
    }

    public static function make_competition_winner()
    {

        global $wpdb;

        $comp_info = $wpdb->get_row("select {$wpdb->prefix}competitions.*, {$wpdb->prefix}competition_tickets.ticket_number, 
        {$wpdb->prefix}competition_tickets.user_id, {$wpdb->prefix}competition_tickets.order_id from {$wpdb->prefix}competitions 
        inner join  {$wpdb->prefix}competition_tickets on  {$wpdb->prefix}competition_tickets.competition_id = {$wpdb->prefix}competitions.id
        where {$wpdb->prefix}competition_tickets.id = " . $_REQUEST['id'] . " and {$wpdb->prefix}competitions.id = " . $_REQUEST['competetion_id'], ARRAY_A);

        if (!empty($comp_info)) {

            $total_winners = $comp_info['total_winners'];

            $table_name = $wpdb->prefix . 'competition_winners';

            $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE competition_id = %d", $_REQUEST['competetion_id']);

            $count = $wpdb->get_var($query);

            if ($count >= $total_winners) {

                echo json_encode(['success' => false, 'message' => 'All prizes have been allocated; you cannot add any more winners']);

                die();
            }

            $sql_query = "INSERT INTO {$wpdb->prefix}competition_winners 
            (competition_id, user_id, ticket_number, ticket_id, order_id, created_at, modified_at)
            SELECT 
                ct.competition_id,
                ct.user_id,
                ct.ticket_number,
                ct.id AS ticket_id,
                ct.order_id,
                UTC_TIMESTAMP() AS created_at,
                UTC_TIMESTAMP() AS modified_at
            FROM 
                {$wpdb->prefix}competition_tickets AS ct
            WHERE 
                ct.id = " . $_REQUEST['id'] . " and ct.competition_id = " . $_REQUEST['competetion_id'];

            $created = $wpdb->query($sql_query);

            $count++;

            $winning_record_id = $wpdb->insert_id;

            $user_info = get_userdata($comp_info['user_id']);

            $post_title = $user_info->display_name . " " . $comp_info['title'];

            $existing_posts = get_posts(
                array(
                    'title' => $post_title,
                    'post_type' => 'winners',
                    'post_status' => 'any',
                    'numberposts' => 1,
                )
            );

            //if (empty($existing_posts)) {
            $post_data = array(
                'post_title' => $post_title,
                'post_content' => html_entity_decode(stripslashes($comp_info['description']), ENT_QUOTES, 'UTF-8'),
                'post_status' => 'draft',
                'post_author' => 1,
                'post_type' => 'winners',
                'meta_input' => array(
                    'customer_name' => $user_info->display_name,
                    'customer_county' => get_user_meta($comp_info['user_id'], 'billing_state', true),
                    'competition_name' => $comp_info['title'],
                    'ticket_number' => $comp_info['ticket_number'],
                ),
            );

            $post_id = wp_insert_post($post_data);

            $mail_sent = self::sendMainCompetitionWinningEmailNotification($comp_info, $winning_record_id, $user_info->user_email);

            $admin_winner = 0;

            if ($comp_info['prize_type'] == 'Points') {
                $admin_winner = 1;

                WC_Points_Rewards_Manager::increase_points($comp_info['user_id'], $comp_info['points'], 'main-competition-point-prize', null, $comp_info['order_id']);
            } else if ($comp_info['prize_type'] == 'Tickets') {

                $admin_winner = 1;

                $mail_sent = self::assignTicketsToUser($comp_info);
            }



            $wpdb->update(
                "{$wpdb->prefix}competition_winners",
                [
                    'post_id' => $post_id,
                    'prize_type' => $comp_info['prize_type'],
                    'mail_sent' => $mail_sent,
                    'is_admin_declare_winner' => $admin_winner

                ],
                ['id' => $winning_record_id]
            );

            if ($comp_info['prize_type'] == 'Prize') {
                $wpdb->update(
                    "{$wpdb->prefix}competition_winners",
                    [
                        'prize_value' => $comp_info['prize_value']
                    ],
                    ['id' => $winning_record_id]
                );
            }

            if ($count == $total_winners) {

                self::getEmailNotificationtoNonWinningUsers($comp_info);
            }

            echo json_encode(['success' => true, 'message' => 'Winners have been successfully selected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Oops! something went wrong.']);
        }

        wp_die();
    }

    public static function sendMainCompetitionWinningEmailNotification($competition, $prize_id, $user_email)
    {


        // error_log("competition Assigning prize to user: " . print_r($competition, true));
        // error_log("prize_id Assigning prize to user: " . print_r($prize_id, true));

        $subject = "You’ve won the main prize! - Carp Gear Giveaways";

        $mailer = WC()->mailer();

        if ($competition['prize_type'] == 'Points') {

            $template = 'emails/main-winner-points-email.php';


            $value = $competition['points'];
        } else if ($competition['prize_type'] == 'Prize') {

            $template = 'emails/main-winner-prize-email.php';

            $value = $competition['cash'];
        } elseif ($competition['prize_type'] == 'Tickets') {
            $template = 'emails/main-winner-ticket-email.php';

            $value = $competition['ticket'];
        }

        $content = wc_get_template_html(
            $template,
            array(
                'email_heading' => $subject,
                'sent_to_admin' => false,
                'plain_text' => false,
                'email' => $mailer,
                'title' => "Cash Prize",
                'value' => $value,
                'type' => $competition['prize_type'],
                'image' => $competition['image'],
                'comp_title' => $competition['title'],
                'ticket_number' => $competition['ticket_number'],
                'prize_id' => $prize_id,
                'competition_id' => $competition['id'],
                'order' => $competition['order_id']
            )
        );

        $headers = "Content-Type: text/html\r\n";

        return $mailer->send($user_email, $subject, $content, $headers);
    }

    public static function assignTicketsToUser($comp)
    {
        global $wpdb;

        $user_id = $comp['user_id'];

        $entry = $wpdb->get_row("SELECT comp.*, COUNT(t.id) AS total_ticket_sold, SUM(CASE WHEN t.user_id = $user_id THEN 1 ELSE 0 END) AS
        total_ticket_sold_by_user FROM {$wpdb->prefix}competitions comp
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1
        WHERE comp.id = '" . $comp['competitions_prize'] . "'", ARRAY_A);

        $user = get_userdata($comp['user_id']);

        $user_meta = get_user_meta($user->ID);


        // error_log("assignTicketPrizeToUser Assigning prize to user: " . print_r($comp, true));
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

        $order->add_product(get_product($entry['competition_product_id']), $comp['prize_tickets']);

        $order->set_address($billing_address, 'billing');

        $order->calculate_totals();

        $order->update_status("wc-admin-comp-win", 'Competition Ticket Prize Winner', TRUE);

        $purchase_date = date("Y-m-d");

        $extra_tickets = 0;

        $total_ticket_sold_by_user = $entry['total_ticket_sold_by_user'];
        $price_per_ticket = $entry['price_per_ticket'];


        $prize_total_tickets = $comp['prize_tickets'];

        $qty = 0;

        $tickets_left = $entry['max_ticket_per_user'] - $total_ticket_sold_by_user;

        $sendNotification = false;

        $user_points = 0;


        // if ($total_ticket_sold_by_user > 0 && $total_ticket_sold_by_user == $entry['max_ticket_per_user']) {

        //     $price_per_ticket = $entry['price_per_ticket'];

        //     $extra_tickets = $comp['prize_tickets'];

        //     $user_points = WC_Points_Rewards_Manager::calculate_points($extra_tickets * $price_per_ticket);

        //     $sendNotification = true;

        //     WC_Points_Rewards_Manager::increase_points($user_id, $user_points, 'manually-add-points-on-competition-winning', null, $order_id);
        // } else if ($comp['prize_tickets'] > $tickets_left) {

        //     $qty = $tickets_left;

        //     $extra_tickets = $comp['prize_tickets'] - $tickets_left;

        //     $price_per_ticket = $entry['price_per_ticket'];

        //     $user_points = WC_Points_Rewards_Manager::calculate_points($extra_tickets * $price_per_ticket);

        //     $sendNotification = true;

        //     WC_Points_Rewards_Manager::increase_points($user_id, $user_points, 'manually-add-points-on-competition-winning', null, $order_id);
        // } else if ($comp['prize_tickets'] > $entry['max_ticket_per_user']) {

        //     $qty = $entry['max_ticket_per_user'];

        //     $extra_tickets = $comp['prize_tickets'] - $entry['max_ticket_per_user'];

        //     $price_per_ticket = $entry['price_per_ticket'];

        //     $user_points = WC_Points_Rewards_Manager::calculate_points($extra_tickets * $price_per_ticket);

        //     $sendNotification = true;

        //     WC_Points_Rewards_Manager::increase_points($user_id, $user_points, 'manually-add-points-on-competition-winning', null, $order_id);
        // } else {

        //     $qty = $comp['prize_tickets'];
        // }


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

            if (!empty($prize_results)) {

                foreach ($prize_results as $p_row) {

                    $is_admin_declare_winner = 0;

                    if ($p_row['type'] == 'Points') {

                        WC_Points_Rewards_Manager::increase_points($user_id, $p_row['value'], 'order-placed-instant-prize', null, $order->id);

                        $subject = "You’re an instant winner! - Carp Gear Giveaways";

                        $is_admin_declare_winner = 1;
                    } else {

                        $subject = "You’re an instant winner! - Carp Gear Giveaways";
                    }

                    $mailSent = 0;

                    $mailer = WC()->mailer();

                    $content = get_custom_email_html($mailer, $p_row, $subject);

                    $headers = "Content-Type: text/html\r\n";

                    $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);

                    $updated_at = gmdate("Y-m-d H:i:s");

                    // $wpdb->query(
                    //     $wpdb->prepare(
                    //         "UPDATE {$wpdb->prefix}comp_instant_prizes_tickets 
                    //   SET is_admin_declare_winner = %d, user_id = %d, mail_sent = %d, updated_at = %s  
                    //   WHERE id = %d",
                    //         $is_admin_declare_winner,
                    //         $user_id,
                    //         $mailSent,
                    //         $updated_at,
                    //         $p_row['id']
                    //     )
                    // );

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

                    do_action('instant_win_notification', array(
                        'user_id'    => $user_id,
                        'comp_title' => $p_row['title'],
                        'image_url'  => wp_get_attachment_url($p_row['image']),
                    ));
                }
            }
        }

        // $mailer = WC()->mailer();

        // $email_data = [
        //     'points' => $user_points,
        //     'comp_title' => $entry['title'],
        //     'total_tickets' => $comp['prize_tickets'],
        //     'main_comp' => $comp,
        //     'add_additional_info' => $sendNotification
        // ];

        // $headers = "Content-Type: text/html\r\n";

        // $content = self::get_competition_main_winner_point_addition_email_content($mailer, $email_data, "You’ve won the main prize! - Carp Gear Giveaways");

        // return $mailer->send($user->user_email, "You’ve won the main prize! - Carp Gear Giveaways", $content, $headers);
        return true;
    }
    public static function assignTicketsToUserRewardWin($comp)
    {
        global $wpdb;

        $user_id = $comp['reward_owner'];

        $queryReward = $wpdb->prepare(
            "SELECT comp.*, COUNT(t.id) AS total_ticket_sold, SUM(CASE WHEN t.user_id = %d THEN 1 ELSE 0 END) AS total_ticket_sold_by_user
             FROM {$wpdb->prefix}competitions comp
             LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1
             WHERE comp.id = %s",
            $user_id,
            $comp['competition_prize']
        );
        // error_log("queryReward: " . print_r($queryReward, true));

        $entry = $wpdb->get_row($queryReward, ARRAY_A);

        $user = get_userdata($comp['reward_owner']);

        $user_meta = get_user_meta($user->ID);


        // error_log("assignTicketPrizeToUser Assigning prize to user: " . print_r($comp, true));
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

        $order->add_product(get_product($entry['competition_product_id']), $comp['prize_tickets']);

        $order->set_address($billing_address, 'billing');

        $order->calculate_totals();

        $order->update_status("wc-admin-comp-win", 'Competition Ticket Prize Winner', TRUE);

        $purchase_date = date("Y-m-d");

        $extra_tickets = 0;

        $total_ticket_sold_by_user = $entry['total_ticket_sold_by_user'];
        $price_per_ticket = $entry['price_per_ticket'];


        $prize_total_tickets = $comp['prize_total_tickets'];

        $qty = 0;

        $tickets_left = $entry['max_ticket_per_user'] - $total_ticket_sold_by_user;

        $sendNotification = false;

        $user_points = 0;


        // if ($total_ticket_sold_by_user > 0 && $total_ticket_sold_by_user == $entry['max_ticket_per_user']) {

        //     $price_per_ticket = $entry['price_per_ticket'];

        //     $extra_tickets = $comp['prize_tickets'];

        //     $user_points = WC_Points_Rewards_Manager::calculate_points($extra_tickets * $price_per_ticket);

        //     $sendNotification = true;

        //     WC_Points_Rewards_Manager::increase_points($user_id, $user_points, 'manually-add-points-on-competition-winning', null, $order_id);
        // } else if ($comp['prize_tickets'] > $tickets_left) {

        //     $qty = $tickets_left;

        //     $extra_tickets = $comp['prize_tickets'] - $tickets_left;

        //     $price_per_ticket = $entry['price_per_ticket'];

        //     $user_points = WC_Points_Rewards_Manager::calculate_points($extra_tickets * $price_per_ticket);

        //     $sendNotification = true;

        //     WC_Points_Rewards_Manager::increase_points($user_id, $user_points, 'manually-add-points-on-competition-winning', null, $order_id);
        // } else if ($comp['prize_tickets'] > $entry['max_ticket_per_user']) {

        //     $qty = $entry['max_ticket_per_user'];

        //     $extra_tickets = $comp['prize_tickets'] - $entry['max_ticket_per_user'];

        //     $price_per_ticket = $entry['price_per_ticket'];

        //     $user_points = WC_Points_Rewards_Manager::calculate_points($extra_tickets * $price_per_ticket);

        //     $sendNotification = true;

        //     WC_Points_Rewards_Manager::increase_points($user_id, $user_points, 'manually-add-points-on-competition-winning', null, $order_id);
        // } else {

        //     $qty = $comp['prize_tickets'];
        // }


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

            $subject = "You’ve won a reward prize! - Carp Gear Giveaways";
            $mailer = WC()->mailer();
            $content = self::get_reward_email_html($mailer, $email_data, $subject);
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

            if (!empty($prize_results)) {

                foreach ($prize_results as $p_row) {

                    $is_admin_declare_winner = 0;

                    if ($p_row['type'] == 'Points') {

                        WC_Points_Rewards_Manager::increase_points($user_id, $p_row['value'], 'order-placed-instant-prize', null, $order->id);

                        $subject = "You’re an instant winner! - Carp Gear Giveaways";

                        $is_admin_declare_winner = 1;
                    } else {

                        $subject = "You’re an instant winner! - Carp Gear Giveaways";
                    }

                    $mailSent = 0;

                    $mailer = WC()->mailer();

                    $content = self::get_reward_email_html($mailer, $p_row, $subject);

                    $headers = "Content-Type: text/html\r\n";

                    $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);

                    $updated_at = gmdate("Y-m-d H:i:s");

                    // $wpdb->query(
                    //     $wpdb->prepare(
                    //         "UPDATE {$wpdb->prefix}comp_instant_prizes_tickets 
                    //   SET is_admin_declare_winner = %d, user_id = %d, mail_sent = %d, updated_at = %s  
                    //   WHERE id = %d",
                    //         $is_admin_declare_winner,
                    //         $user_id,
                    //         $mailSent,
                    //         $updated_at,
                    //         $p_row['id']
                    //     )
                    // );

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

                    do_action('instant_win_notification', array(
                        'user_id'    => $user_id,
                        'comp_title' => $p_row['title'],
                        'image_url'  => wp_get_attachment_url($p_row['image']),
                    ));
                }
            }
        }

        // $mailer = WC()->mailer();

        // $email_data = [
        //     'points' => $user_points,
        //     'comp_title' => $entry['title'],
        //     'total_tickets' => $comp['prize_tickets'],
        //     'main_comp' => $comp,
        //     'add_additional_info' => $sendNotification
        // ];

        // $headers = "Content-Type: text/html\r\n";

        // $content = self::get_competition_main_winner_point_addition_email_content($mailer, $email_data, "You’ve won the main prize! - Carp Gear Giveaways");

        // return $mailer->send($user->user_email, "You’ve won the main prize! - Carp Gear Giveaways", $content, $headers);
        return true;
    }

    public static function get_competition_main_winner_point_addition_email_content($mailer, $email_data, $email_heading)
    {

        $template = 'emails/competition-main-winner-tickets-points-email.php';

        return wc_get_template_html(
            $template,
            array(
                'email_heading' => $email_heading,
                'sent_to_admin' => false,
                'plain_text' => false,
                'email' => $mailer,
                'points' => $email_data['points'],
                'comp_title' => $email_data['comp_title'],
                'total_tickets' => $email_data['total_tickets'],
                'add_additional_info' => !empty($email_data['add_additional_info']) ? 1 : 0,
                'image' => $email_data['main_comp']['image'],
                'main_comp_title' => $email_data['main_comp']['title'],
                'ticket_number' => $email_data['main_comp']['ticket_number'],
            )
        );
    }

    // public static function getEmailNotificationtoNonWinningUsers($competition)
    // {

    //     global $wpdb;

    //     $comp_users = $wpdb->get_results("SELECT DISTINCT user_id FROM {$wpdb->prefix}competition_tickets 
    //     WHERE competition_id = " . $competition['id'] . " and is_purchased = 1 and user_id > 0 GROUP by user_id", ARRAY_A);

    //     $winning_users = $wpdb->get_results("SELECT DISTINCT user_id FROM {$wpdb->prefix}competition_winners 
    //     WHERE competition_id = " . $competition['id'] . " GROUP BY user_id", ARRAY_A);

    //     $winning_user_ids = array_map(function ($user) {
    //         return $user['user_id'];
    //     }, $winning_users);

    //     $comp_users_ids = array_map(function ($user) {
    //         return $user['user_id'];
    //     }, $comp_users);

    //     $filtered_user_ids = array_diff($comp_users_ids, $winning_user_ids);

    //     $args = [
    //         'include' => $filtered_user_ids,
    //         'fields' => ['ID', 'user_email', 'display_name']
    //     ];

    //     $user_query = new WP_User_Query($args);

    //     $users = $user_query->get_results();

    //     if (!empty($users)) {

    //         $template = 'emails/admin-thank-you-note.php';

    //         $subject = "Thank You for Your Participation! - Carp Gear Giveaways";

    //         foreach ($users as $user) {

    //             $mailer = WC()->mailer();

    //             $content = wc_get_template_html(
    //                 $template,
    //                 array(
    //                     'email_heading' => $subject,
    //                     'sent_to_admin' => false,
    //                     'plain_text' => false,
    //                     'email' => $mailer,
    //                     'user_name' => $user->display_name
    //                 )
    //             );

    //             $headers = "Content-Type: text/html\r\n";

    //             $mailer->send($user->user_email, $subject, $content, $headers);
    //         }
    //     }
    // }


    public static function getEmailNotificationtoNonWinningUsers($competition)
    {
        global $wpdb;

        // Get all users who purchased tickets for the competition
        $comp_users = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT user_id FROM {$wpdb->prefix}competition_tickets 
            WHERE competition_id = %d AND is_purchased = 1 AND user_id > 0 GROUP BY user_id", intval($competition['id'])), ARRAY_A);

        // Get all winning users
        $winning_users = $wpdb->get_results($wpdb->prepare("SELECT DISTINCT user_id FROM {$wpdb->prefix}competition_winners 
            WHERE competition_id = %d GROUP BY user_id", intval($competition['id'])), ARRAY_A);

        // Extract user IDs from both results
        $winning_user_ids = array_map(function ($user) {
            return $user['user_id'];
        }, $winning_users);

        $comp_users_ids = array_map(function ($user) {
            return $user['user_id'];
        }, $comp_users);

        // Filter out winning users to get only non-winning users
        $filtered_user_ids = array_diff($comp_users_ids, $winning_user_ids);

        // Fetch details of non-winning users who haven't received the email yet for this competition
        if (!empty($filtered_user_ids)) {
            $placeholders = implode(',', array_fill(0, count($filtered_user_ids), '%d'));

            // Check the `wp_competition_winners` table for users who haven't been sent the email yet for this competition (`mail_sent = 0`)
            $unsent_users = $wpdb->get_results($wpdb->prepare(
                "
                SELECT user_id FROM {$wpdb->prefix}competition_winners 
                WHERE competition_id = %d AND mail_sent = 0 AND user_id IN ($placeholders)",
                array_merge([intval($competition['id'])], $filtered_user_ids)
            ), ARRAY_A);

            // Extract user IDs who have not been sent the email yet
            $unsent_user_ids = array_map(function ($user) {
                return $user['user_id'];
            }, $unsent_users);

            // If there are users who haven't received emails
            if (!empty($unsent_user_ids)) {
                $args = [
                    'include' => $unsent_user_ids,
                    'fields' => ['ID', 'user_email', 'display_name']
                ];

                $user_query = new WP_User_Query($args);
                $users = $user_query->get_results();

                if (!empty($users)) {
                    $template = 'emails/admin-thank-you-note.php';
                    $subject = "Thank You for Your Participation! - Carp Gear Giveaways";

                    foreach ($users as $user) {
                        // Send the email
                        $mailer = WC()->mailer();
                        $content = wc_get_template_html(
                            $template,
                            array(
                                'email_heading' => $subject,
                                'sent_to_admin' => false,
                                'plain_text' => false,
                                'email' => $mailer,
                                'user_name' => $user->display_name
                            )
                        );

                        $headers = "Content-Type: text/html\r\n";
                        $mailer->send($user->user_email, $subject, $content, $headers);

                        // Update `mail_sent` to 1 in the `wp_competition_winners` table for this competition
                        $wpdb->update(
                            "{$wpdb->prefix}competition_winners",
                            array('mail_sent' => 1),  // Set mail_sent to 1
                            array(
                                'competition_id' => intval($competition['id']),
                                'user_id' => intval($user->ID)
                            ),
                            array('%d'),
                            array('%d', '%d')
                        );
                    }
                }
            }
        }
    }

    public static function make_competition_reward_winner()
    {
        global $wpdb;



        $sql_query = "INSERT INTO {$wpdb->prefix}comp_reward_winner 
        (competition_id, user_id, ticket_number, ticket_id, created_at, reward_id)
        SELECT 
            ct.competition_id,
            ct.user_id,
            ct.ticket_number,
            ct.id AS ticket_id,
            UTC_TIMESTAMP() AS created_at,
            " . $_REQUEST['reward_id'] . " as reward_id
        FROM 
            {$wpdb->prefix}competition_tickets AS ct
        WHERE 
            ct.id = " . $_REQUEST['id'] . " and ct.competition_id = " . $_REQUEST['competetion_id'];

        $created = $wpdb->query($sql_query);

        $winning_record_id = $wpdb->insert_id;

        $reward_info = $wpdb->get_row("select {$wpdb->prefix}comp_reward.*, {$wpdb->prefix}competitions.title as comp_title, {$wpdb->prefix}competition_tickets.ticket_number, {$wpdb->prefix}competition_tickets.order_id, 
        {$wpdb->prefix}competition_tickets.user_id as reward_owner, {$wpdb->prefix}competitions.description from {$wpdb->prefix}comp_reward 
        inner join  {$wpdb->prefix}competition_tickets on  {$wpdb->prefix}competition_tickets.competition_id = {$wpdb->prefix}comp_reward.competition_id
        INNER JOIN {$wpdb->prefix}competitions ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}comp_reward.competition_id
        where {$wpdb->prefix}comp_reward.id = " . $_REQUEST['reward_id'] . " and {$wpdb->prefix}competition_tickets.id = " . $_REQUEST['id'] . " and {$wpdb->prefix}comp_reward.competition_id = " . $_REQUEST['competetion_id'], ARRAY_A);

        // Send Email Notification to reward winner

        if (!empty($reward_info)) {
            // error_log('reward_info value: ' . print_r($reward_info, true));

            $user_info = get_userdata($reward_info['reward_owner']);

            $subject = "You’ve won a reward prize! - Carp Gear Giveaways";

            if ($reward_info['type'] == 'Points') {

                WC_Points_Rewards_Manager::increase_points($reward_info['reward_owner'], $reward_info['value'], 'order-placed-reward-prize', null, $reward_info['order_id']);
            } else if ($reward_info['type'] == 'Tickets') {

                $admin_winner = 1;

                $mail_sent = self::assignTicketsToUserRewardWin($reward_info);

                if ($created) {
                    echo json_encode(['success' => true, 'message' => 'Reward Winner has been successfully selected.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Oops! something went wrong.']);
                }

                wp_die();
            } else   if ($reward_info['type'] == "Prize") {
                $wpdb->update("{$wpdb->prefix}comp_reward_winner", ['prize_value' => $reward_info['prize_value']], ['id' => $winning_record_id]);
            }


            $mailSent = 0;

            $mailer = WC()->mailer();

            $content = self::get_reward_email_html($mailer, $reward_info, $subject);

            $headers = "Content-Type: text/html\r\n";

            $mailSent = $mailer->send($user_info->user_email, $subject, $content, $headers);

            // Add Draft Winner POST

            $post_title = $user_info->display_name . " " . $reward_info['comp_title'];

            $existing_posts = get_posts(
                array(
                    'title' => $post_title,
                    'post_type' => 'winners',
                    'post_status' => 'any',
                    'numberposts' => 1,
                )
            );

            //if (empty($existing_posts)) {
            $post_data = array(
                'post_title' => $post_title,
                'post_content' => html_entity_decode(stripslashes($reward_info['description']), ENT_QUOTES, 'UTF-8'),
                'post_status' => 'draft',
                'post_author' => 1,
                'post_type' => 'winners',
                'meta_input' => array(
                    'customer_name' => $user_info->display_name,
                    'customer_county' => get_user_meta($reward_info['reward_owner'], 'billing_state', true),
                    'competition_name' => $reward_info['comp_title'],
                    'ticket_number' => $reward_info['ticket_number'],
                ),
            );

            $post_id = wp_insert_post($post_data);

            if (!is_wp_error($post_id)) {

                $wpdb->update("{$wpdb->prefix}comp_reward_winner", ['post_id' => $post_id, "mail_sent" => $mailSent], ['id' => $winning_record_id]);
            }


            //}
        }

        if ($created) {
            echo json_encode(['success' => true, 'message' => 'Reward Winner has been successfully selected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Oops! something went wrong.']);
        }

        wp_die();
    }

    public static function get_reward_email_html($mailer, $email_data, $email_heading = false)
    {
        if ($email_data['type'] == 'Points') {
            $template = 'emails/reward-win-points-email.php';
        } elseif ($email_data['type'] == 'PointsAllocation') {
            $template = 'emails/reward-win-points-allocation-email.php';
        } else {
            $template = 'emails/reward-win-prize-email.php';
        }

        return wc_get_template_html(
            $template,
            array(
                'email_heading' => $email_heading,
                'sent_to_admin' => false,
                'plain_text' => false,
                'email' => $mailer,
                'title' => $email_data['title'],
                'type' => $email_data['type'],
                'value' => $email_data['value'],
                'image' => $email_data['image'],
                'comp_title' => $email_data['comp_title'],
                'ticket_number' => $email_data['ticket_number']
            )
        );
    }

    public static function autoUpdateSpendingLimit()
    {

        $current_date = date('Y-m-d H:i:s');

        $users = get_users(array('fields' => array('ID')));

        foreach ($users as $user) {

            $user_meta = get_user_meta($user->ID);

            $current_spending = isset($user_meta['current_spending']) ? $user_meta['current_spending'] : "";

            if ($current_spending > 0) {

                $limit_created = $user_meta['limit_created'][0];

                $limit_renewal = $user_meta['limit_renewal'][0];

                if (strtotime($current_date) > strtotime($limit_renewal) && strtotime($limit_renewal) > strtotime(($limit_created))) {

                    update_user_meta($user->ID, 'current_spending', 0);
                }
            }
        }
    }

    public static function autoUnlockUsers()
    {

        $current_date = date('Y-m-d H:i:s');

        $users = get_users(array('fields' => array('ID')));

        foreach ($users as $user) {

            $user_meta = get_user_meta($user->ID);

            $account_lock = isset($user_meta['lock_account']) ? $user_meta['lock_account'][0] : "";

            if ($account_lock == 1) {

                $lockout_date = $user_meta['lockout_date'][0];

                $locking_date = $user_meta['locking_date'][0];

                if (strtotime($current_date) > strtotime($lockout_date) && strtotime($lockout_date) > strtotime(($locking_date))) {

                    update_user_meta($user->ID, 'lock_account', 0);
                }
            }
        }
    }

    public static function competitionRewardPrizeLevelReachedNotification()
    {

        global $wpdb;

        $competitions = $wpdb->get_results("select {$wpdb->prefix}competitions.*, COUNT(t.id) AS total_ticket_sold from {$wpdb->prefix}competitions
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON {$wpdb->prefix}competitions.id = t.competition_id AND t.is_purchased = 1 
        where enable_reward_wins = 1 and status = 'Open' and is_draft = 0 GROUP by t.competition_id", ARRAY_A);

        if (!empty($competitions)) {

            $args = array(
                'role' => 'administrator',
            );

            $users = get_users($args);

            foreach ($competitions as $competition) {

                $competition_sold_prcnt = ($competition['total_ticket_sold'] / $competition['total_sell_tickets']) * 100;

                $records = $wpdb->get_results("SELECT {$wpdb->prefix}comp_reward.* FROM {$wpdb->prefix}comp_reward 
                WHERE competition_id = " . $competition['id'], ARRAY_A);

                if (!empty($records)) {

                    foreach ($records as $index => $reward_record) {

                        if ($index === array_key_last($records)) {
                            $limit = "";
                        } else {
                            $limit = ceil($competition['total_sell_tickets'] * ($reward_record['prcnt_available'] / 100.0));
                        }

                        if ($reward_record['prcnt_available'] <= $competition_sold_prcnt) {

                            $subject = "Reward " . $reward_record['title'] . " unlocked! - Carp Gear Giveaways";

                            $reward_link = admin_url('admin.php?page=reward_prizes_entrants&id=' . $competition['id'] . '&reward=' . $reward_record['id'] . '&limit=' . $limit);

                            $mailSent = 0;

                            $mailer = WC()->mailer();

                            $reward_data = $reward_record;

                            $reward_data['comp_title'] = $competition['title'];

                            $reward_data['reward_link'] = $reward_link;

                            $content = self::get_reward_price_level_reached_html($mailer, $reward_data, $subject);

                            $headers = "Content-Type: text/html\r\n";

                            foreach ($users as $user) {

                                $sql = "select * from {$wpdb->prefix}comp_email_notification 
                                where type='reward' and competition_id = %d and reward_id	= %d  and user_id = %d";

                                $is_notify = $wpdb->get_row($wpdb->prepare($sql, $competition['id'], $reward_record['id'], $user->ID));

                                if (empty($is_notify)) {

                                    $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);

                                    $wpdb->insert(
                                        $wpdb->prefix . "comp_email_notification",
                                        array(
                                            'competition_id' => $competition['id'],
                                            'user_id' => $user->ID,
                                            'reward_id' => $reward_record['id'],
                                            'mail_sent' => $mailSent,
                                            'type' => 'reward'
                                        )

                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function competitionSoldOutNotification()
    {

        global $wpdb;

        $competitions = $wpdb->get_results("select {$wpdb->prefix}competitions.*, COUNT(t.id) AS total_ticket_sold from {$wpdb->prefix}competitions
        INNER JOIN {$wpdb->prefix}competition_tickets t ON {$wpdb->prefix}competitions.id = t.competition_id AND t.is_purchased = 1 
        where enable_reward_wins = 1 and status = 'Open' and is_draft = 0 GROUP by t.competition_id HAVING total_ticket_sold = total_sell_tickets", ARRAY_A);

        // Create a DateTime object with the current time in UTC
        $datetime = new DateTime('now', new DateTimeZone('UTC'));

        // Set the time zone to Europe/London
        $datetime->setTimezone(new DateTimeZone('Europe/London'));

        $current_date = $datetime->format('Y-m-d');  // Get current date (e.g., 2025-01-31)

        // Format and print the time in HH:mm format
        $time_uk = $datetime->format('H:i');

        // Prepare your query
        $query = $wpdb->prepare("
        SELECT c.*, COUNT(t.id) AS total_ticket_sold 
        FROM {$wpdb->prefix}competitions c
        LEFT JOIN {$wpdb->prefix}competition_tickets t 
            ON c.id = t.competition_id 
            AND t.is_purchased = 1 
        WHERE c.status = 'Open' 
            AND c.is_draft = 0 
            AND (
                c.closing_date < %s
                OR (c.closing_date = %s AND c.closing_time <= %s)
                OR (SELECT COUNT(id) FROM {$wpdb->prefix}competition_tickets WHERE competition_id = c.id AND is_purchased = 1) = c.total_sell_tickets
            )
        GROUP BY c.id
        ", $current_date, $current_date, $time_uk);

        $competitionssssss = $wpdb->get_results($query, ARRAY_A);

        // error_log("++++++++++++++++++++++++++++++++++++++++++++++" . print_r($query, true));

        if (!empty($competitionssssss)) {
            foreach ($competitionssssss as $competition) {
                $wpdb->update($wpdb->prefix . "competitions", ['category' => 'finished_and_sold_out', 'status' => 'Finished'], ['id' => $competition['id']]);
            }
        }


        if (!empty($competitions)) {

            $args = array(
                'role' => 'administrator',
            );

            $users = get_users($args);

            foreach ($competitions as $competition) {

                $wpdb->update($wpdb->prefix . "competitions", ['category' => 'finished_and_sold_out', 'status' => 'Finished'], ['id' => $competition['id']]);

                $subject = "Competition sold out! - Carp Gear Giveaways";

                $comp_link = admin_url('admin.php?page=entrants&id=' . $competition['id']);

                $mailSent = 0;

                $mailer = WC()->mailer();

                $competition['comp_link'] = $comp_link;

                $content = self::get_competition_sold_html($mailer, $competition, $subject);

                $headers = "Content-Type: text/html\r\n";

                foreach ($users as $user) {

                    $sql = "select * from {$wpdb->prefix}comp_email_notification where type='competition' and competition_id = %d and user_id = %d";

                    $is_notify = $wpdb->get_row($wpdb->prepare($sql, $competition['id'], $user->ID));

                    if (empty($is_notify)) {

                        $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);

                        $wpdb->insert(
                            $wpdb->prefix . "comp_email_notification",
                            array(
                                'competition_id' => $competition['id'],
                                'user_id' => $user->ID,
                                'mail_sent' => $mailSent,
                                'type' => 'competition'
                            )

                        );
                    }
                }
            }
        }
    }

    public static function get_reward_price_level_reached_html($mailer, $email_data, $email_heading = false)
    {

        $template = 'emails/reward-price-reached-email.php';

        return wc_get_template_html(
            $template,
            array(
                'email_heading' => $email_heading,
                'sent_to_admin' => false,
                'plain_text' => false,
                'email' => $mailer,
                'title' => $email_data['title'],
                'type' => $email_data['type'],
                'value' => $email_data['value'],
                'image' => $email_data['image'],
                'comp_title' => $email_data['comp_title'],
                'ticket_number' => $email_data['ticket_number'],
                'reward_link' => $email_data['reward_link']
            )
        );
    }

    public static function get_competition_sold_html($mailer, $email_data, $email_heading = false)
    {

        $template = 'emails/competition-sold-out-email.php';

        return wc_get_template_html(
            $template,
            array(
                'email_heading' => $email_heading,
                'sent_to_admin' => false,
                'plain_text' => false,
                'email' => $mailer,
                'image' => $email_data['image'],
                'comp_title' => $email_data['title'],
                'comp_link' => $email_data['comp_link']
            )
        );
    }

    public static function add_user_profile_custom_fields($user)
    {
        $wallet_balance = get_user_meta($user->ID, 'wallet_balance', true);
        $wallet_lock = get_user_meta($user->ID, 'wallet_lock', true);
        $lock_account = get_user_meta($user->ID, 'lock_account', true);
        $limit_value = get_user_meta($user->ID, 'limit_value', true);
        $symbol = get_woocommerce_currency_symbol();
?>
        <style>
            .wallet_btn {
                display: flex !important;
                align-items: center;
            }
        </style>
        <h3><?php _e('Wallet Management', 'default'); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="wallet_balance"><?php _e('Current Wallet Balance'); ?></label></th>
                <td>
                    <p class="description"><?php echo $wallet_balance ? $symbol . $wallet_balance : $symbol . '0'; ?></p>
                </td>
            </tr>
            <tr>
                <th><?php _e('Lock/Unlock'); ?></th>
                <td>
                    <?php if ($wallet_lock == 1) { ?>
                        <button type="button" id="wallet_unlock" class="button dashicons-before dashicons-unlock wallet_btn">
                            <?php _e('Unlock', 'default'); ?>
                        </button>
                    <?php } else { ?>
                        <button type="button" id="wallet_lock" class="button dashicons-before dashicons-lock wallet_btn">
                            <?php _e('Lock', 'default'); ?>
                        </button>
                    <?php } ?>
                </td>
            </tr>
        </table>

        <h3><?php _e('Responsibility Controls', 'default'); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="limit_value"><?php _e('Spend Limit'); ?></label></th>
                <td>
                    <?php if ($limit_value == null || $limit_value == '' || $limit_value == 0) { ?>
                        <p class="description"><?php _e("No account spend limit set.", 'default'); ?></p>
                    <?php } else if ($limit_value) { ?>
                        <p class="description"><?php _e($symbol . $limit_value, 'default'); ?></p>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th><label for="lock_account"><?php _e('Account Lock'); ?></label></th>
                <td>
                    <?php if ($lock_account == 1) { ?>
                        <p id="unlock-account-description" class="description" style="font-weight: 700;">
                            <?php _e('Account is permanently locked.', 'default'); ?>
                        </p>
                        <div id="unlock-account-div" class="notice notice-success update-nag inline" style="display: none;">
                            Account lock removed.
                        </div>
                        <button type="button" id="unlock_account" value="0" class="button" style="margin-top: 7px !important;"
                            data-user-id="<?php echo $user->ID; ?>" data-lock-action="0">
                            <?php _e('Remove Lock', 'default'); ?>
                        </button>
                    <?php } else { ?>
                        <p id="lock-account-description" class="description"><?php _e('Account not locked.', 'default'); ?></p>
                        <div id="lock-account-div" class="notice notice-success update-nag inline" style="display: none;">
                            Account permanently locked.
                        </div>
                        <button type="button" id="lock_account" value="1" class="button" style="margin-top: 7px !important;"
                            data-user-id="<?php echo $user->ID; ?>" data-lock-action="1">
                            <?php _e('Lock Account', 'default'); ?>
                        </button>
                    <?php } ?>
                </td>
            </tr>
        </table>
<?php
    }

    public static function update_lock_account_callback()
    {

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized action');
        }

        $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
        $lock_action = isset($_POST['lock_action']) ? intval($_POST['lock_action']) : 0;

        update_user_meta($user_id, 'lock_account', $lock_action);

        if ($lock_action == 1) {

            update_user_meta($user_id, 'locking_period', 'Permanantly');
        } else {
            update_user_meta($user_id, 'locking_period', '');
        }
        $date = new DateTime('2222-12-31');
        update_user_meta($user_id, 'lockout_date',  $date->format('Y-m-d'));
        update_user_meta($user_id, 'locking_date', date("Y-m-d"));

        wp_send_json_success('User updated successfully');

        wp_die();
    }

    public static function get_all_list_ajax()
    {

        global $wpdb;
        global $wc_points_rewards;

        // print_r($wc_points_rewards);


        if (isset($_POST['mode']) && !empty($_POST['mode'])) {

            if ($_POST['mode'] == 'limits') {


                $limit = isset($_POST['length']) ? intval($_POST['length']) : 25;
                $offset = isset($_POST['start']) ? intval($_POST['start']) : 0;

                $args = array(
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'limit_value',
                            'value' => '',
                            'compare' => '!='
                        ),
                        array(
                            'key' => 'limit_duration',
                            'value' => '',
                            'compare' => '!='
                        )
                    )
                );

                $users = get_users($args);
                $total_users_count = count($users);

                //$total_users = count_users();
                //$total_users_count = $total_users['total_users'];

                $query_args = array(
                    'number' => $limit,
                    'offset' => $offset,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'limit_value',
                            'value' => '',
                            'compare' => '!='
                        ),
                        array(
                            'key' => 'limit_duration',
                            'value' => '',
                            'compare' => '!='
                        )
                    )
                );

                $user_query = new WP_User_Query($query_args);

                $users = $user_query->get_results();

                $data = array();

                foreach ($users as $user) {

                    $user_meta = get_user_meta($user->ID);

                    $current_spending = isset($user_meta['current_spending']['0']) ? $user_meta['current_spending']['0'] : 0;

                    if (empty($user_meta['limit_value'][0]))
                        $user_meta['limit_value'][0] = 0;

                    $limit_balance = $user_meta['limit_value'][0] - $current_spending;

                    $limit_created = isset($user_meta['limit_created'][0]) ? $user_meta['limit_created'][0] : '';

                    $limit_renewal = isset($user_meta['limit_renewal'][0]) ? $user_meta['limit_renewal'][0] : '';

                    if (!empty($limit_created))
                        $limit_created = self::format_date_with_ordinal($limit_created);
                    if (!empty($limit_renewal))
                        $limit_renewal = self::format_date_with_ordinal($limit_renewal);

                    $symbol = get_woocommerce_currency_symbol();

                    $url = admin_url("user-edit.php?user_id=" . $user->ID);

                    $user_id = '<a class="link_text" href="' . $url . '">' . $user->ID . '</a>';

                    $data[] = array(
                        'user_id' => $user_id,
                        'user_email' => $user->user_email,
                        'user_name' => $user->display_name,
                        'limit_duration' => isset($user_meta['limit_duration'][0]) ? $user_meta['limit_duration'][0] : '',
                        'limit_value' => isset($user_meta['limit_value'][0]) ? $symbol . number_format($user_meta['limit_value'][0], 2, '.', '') : '',
                        'limit_balance' => $symbol . number_format($limit_balance, 2, '.', ''),
                        'limit_created' => $limit_created,
                        'limit_renewal' => $limit_renewal,
                    );
                }

                wp_send_json(
                    array(
                        'data' => $data,
                        'draw' => intval($_POST['draw']),
                        'recordsTotal' => $total_users_count,
                        'recordsFiltered' => $user_query->get_total(),
                    )
                );
            }

            if ($_POST['mode'] == 'locks') {

                $limit = isset($_POST['length']) ? intval($_POST['length']) : 25;
                $offset = isset($_POST['start']) ? intval($_POST['start']) : 0;

                $args = array(
                    'meta_query' => [
                        array(
                            'key' => 'locking_period',
                            'value' => '',
                            'compare' => '!='
                        )
                    ]
                );

                $users = get_users($args);
                $total_users_count = count($users);

                // $total_users = count_users();
                // $total_users_count = $total_users['total_users'];

                $query_args = array(
                    'number' => $limit,
                    'offset' => $offset,
                    'meta_query' => [
                        array(
                            'key' => 'locking_period',
                            'value' => '',
                            'compare' => '!='
                        )
                    ]
                );

                $user_query = new WP_User_Query($query_args);

                $users = $user_query->get_results();

                $data = array();

                foreach ($users as $user) {

                    $user_meta = get_user_meta($user->ID);

                    $lock_created = isset($user_meta['locking_date'][0]) ? $user_meta['locking_date'][0] : '';

                    $lock_expires = isset($user_meta['lockout_date'][0]) ? $user_meta['lockout_date'][0] : '';

                    if (!empty($lock_created))
                        $lock_created = self::format_date_with_ordinal($lock_created);
                    if (!empty($lock_expires))
                        $lock_expires = self::format_date_with_ordinal($lock_expires);

                    $symbol = get_woocommerce_currency_symbol();

                    $url = admin_url("user-edit.php?user_id=" . $user->ID);

                    $user_id = '<a class="link_text" href="' . $url . '">' . $user->ID . '</a>';

                    $data[] = array(
                        'user' => $user_id,
                        'user_email' => $user->user_email,
                        'user_name' => $user->display_name,
                        'locking_date' => $lock_created,
                        'lockout_date' => $lock_expires,
                    );
                }

                wp_send_json(
                    array(
                        'data' => $data,
                        'draw' => intval($_POST['draw']),
                        'recordsTotal' => $total_users_count,
                        'recordsFiltered' => $user_query->get_total(),
                    )
                );
            }

            if ($_POST['mode'] == 'unpaid') {
                $limit = isset($_POST['length']) ? intval($_POST['length']) : 50;
                $offset = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $search = isset($_POST['search_value']) ? sanitize_text_field($_POST['search_value']) : "";


                // Initialize search query part
                $search_query_instant = '';
                $search_query_reward = '';
                $search_query_winner = '';


                // Prepare search query part

                if (!empty($search)) {
                    $search_query_instant = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s  OR ipt.edited_title_instant LIKE %s OR ip.title LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }
                if (!empty($search)) {
                    $search_query_reward = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s OR rw.edited_title_reward LIKE %s  OR r.title LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }
                if (!empty($search)) {
                    $search_query_winner = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s  OR cw.edited_title  LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }

                // Count queries
                $count_query1 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 0
                    AND ip.type = 'Prize'
                    $search_query_instant
                ";

                $count_query2 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 0
                    AND r.type = 'Prize'
                    $search_query_reward
                ";

                $count_query3 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.is_admin_declare_winner = 0
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Prize'
                    $search_query_winner
                    ";

                // Execute count queries
                $count1 = $wpdb->get_var($wpdb->prepare($count_query1));
                $count2 = $wpdb->get_var($wpdb->prepare($count_query2));
                $count3 = $wpdb->get_var($wpdb->prepare($count_query3));

                // Total count
                $totalCount = $count1 + $count2 + $count3;

                // Data query
                $query = $wpdb->prepare(
                    "
                    SELECT ipt.ticket_number,ipt.id, ip.title AS prize_title, ipt.edited_title_instant AS edited_title, ip.type, ip.web_order_instant AS webOrder , c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email , u.id AS user_id ,'instant' AS ctype 
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 0
                    AND ip.type = 'Prize'
                    $search_query_instant

                    UNION

                    SELECT cw.ticket_number,cw.id, cw.prize_type AS prize_title, cw.edited_title AS edited_title ,cw.prize_type, c.web_order AS webOrder , c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id ,'cash' AS ctype 
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.is_admin_declare_winner = 0
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Prize'
                    $search_query_winner
                    
                    UNION
                    
                    SELECT rw.ticket_number,rw.id, rw.edited_title_reward AS edited_title,r.title AS prize_title, r.type, r.web_order_reward AS webOrder, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email, u.id AS user_id ,'reward' AS ctype 
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 0
                    AND r.type = 'Prize'
                    $search_query_reward
                    LIMIT %d OFFSET %d
                    ",
                    $limit,
                    $offset
                );

                // Execute data query
                $prize_results = $wpdb->get_results($query, ARRAY_A);


                // echo "<pre>";
                // print_r($prize_results);
                // echo "<pre>";

                $data = [];
                $users = [];

                if (!empty($prize_results)) {
                    foreach ($prize_results as $prize_result) {
                        if (!isset($users[$prize_result['user_id']])) {
                            $users[$prize_result['user_id']] = get_user_meta($prize_result['user_id']);
                        }

                        $user_data = $users[$prize_result['user_id']];
                        $billing_address = $user_data['billing_address_1']['0'];

                        if (!empty($user_data['billing_address_2']['0']))
                            $billing_address .= " " . $user_data['billing_address_2']['0'];
                        if (!empty($user_data['billing_city']['0']))
                            $billing_address .= " " . $user_data['billing_city']['0'];
                        if (!empty($user_data['billing_state']['0']))
                            $billing_address .= " " . $user_data['billing_state']['0'];
                        if (!empty($user_data['billing_postcode']['0']))
                            $billing_address .= " " . $user_data['billing_postcode']['0'];
                        if (!empty($user_data['billing_country']['0']))
                            $billing_address .= " " . $user_data['billing_country']['0'];

                        $url = admin_url("user-edit.php?user_id=" . $prize_result['user_id']);
                        $user_id = '<a class="link_text" href="' . $url . '">' . $prize_result['display_name'] . '</a>';

                        $order_id = "";
                        if (!empty($prize_result['order_id'])) {
                            $order_url = admin_url('post.php?post=' . $prize_result['order_id'] . '&action=edit');
                            $order_id = '<a class="link_text" href="' . $order_url . '">' . $prize_result['order_id'] . '</a>';
                        }

                        $data[] = [
                            'id' => $prize_result['id'],
                            'ticket_number' => ($prize_result['webOrder'] == 1 ?
                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe-americas" viewBox="0 0 16 16">
                                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484q-.121.12-.242.234c-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.78-.39.952-.171 1.227.182.078.099.163.208.273.318.609.304.662-.132.723-.633.039-.322.081-.671.277-.867.434-.434 1.265-.791 2.028-1.12.712-.306 1.365-.587 1.579-.88A7 7 0 1 1 2.04 4.327Z"/>
                                    </svg> '
                                : ''
                            ) . $prize_result['ticket_number'] .
                                "<a class='mark-paid' href='#' data-url='#' data-id='" . $prize_result['id'] . "'  data-set='" . $prize_result['ctype'] . "'>Mark as paid</a>",
                            'order_id' => $order_id,
                            'title' => ($prize_result['edited_title'] && $prize_result['edited_title'] != '' ?  $prize_result['edited_title'] : $prize_result['prize_title']) . '<span class="edit-prize-title"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                          </svg> </span>',
                            'user_name' => $user_id,
                            'user_email' => $prize_result['user_email'],
                            'phone' => $user_data['billing_phone'][0],
                            'user_address' => trim($billing_address),
                            'comp_title' => $prize_result['comp_title']
                        ];
                    }
                }

                wp_send_json([
                    'data' => $data,
                    'draw' => intval($_POST['draw']),
                    'recordsTotal' => intval($totalCount),
                    'recordsFiltered' => intval($totalCount)
                ]);
            }


            if ($_POST['mode'] == 'paid') {
                $limit = isset($_POST['length']) ? intval($_POST['length']) : 50;
                $offset = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $search = isset($_POST['search_value']) ? sanitize_text_field($_POST['search_value']) : "";

                // Prepare search query part
                // Initialize search query part
                $search_query_instant = '';
                $search_query_reward = '';
                $search_query_winner = '';


                if (!empty($search)) {
                    $search_query_instant = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s  OR ipt.edited_title_instant LIKE %s OR ip.title LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }
                if (!empty($search)) {
                    $search_query_reward = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s OR rw.edited_title_reward LIKE %s  OR r.title LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }
                if (!empty($search)) {
                    $search_query_winner = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s  OR cw.edited_title  LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }

                // Count queries
                $count_query1 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 2
                    AND ip.type = 'Prize'
                    $search_query_instant
                ";

                $count_query2 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 2
                    AND r.type = 'Prize'
                    $search_query_reward
                ";


                $count_query3 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.is_admin_declare_winner = 2
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Prize'
                    $search_query_winner
                    ";

                // Execute count queries
                $count1 = $wpdb->get_var($wpdb->prepare($count_query1));
                $count2 = $wpdb->get_var($wpdb->prepare($count_query2));
                $count3 = $wpdb->get_var($wpdb->prepare($count_query3));

                // Total count
                $totalCount = $count1 + $count2 + $count3;

                // Data query
                $query = $wpdb->prepare(
                    "
                    SELECT ipt.ticket_number, ipt.id, ip.title AS prize_title, ipt.edited_title_instant AS edited_title, ip.type, ip.web_order_instant AS webOrder, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id, 'instant' AS ctype , ipt.claimed_as
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 2
                    AND ip.type = 'Prize'
                    $search_query_instant

                    UNION

                    SELECT cw.ticket_number,cw.id, c.title AS prize_title , cw.edited_title AS edited_title ,cw.prize_type, c.web_order AS webOrder ,c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id ,'cash' AS ctype , cw.claimed_as
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.is_admin_declare_winner = 2
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Prize'
                    $search_query_winner
                    
                    UNION
                    
                    SELECT rw.ticket_number,rw.id,  r.title AS prize_title, rw.edited_title_reward AS edited_title, r.type,r.web_order_reward AS webOrder, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id,'reward' AS ctype , rw.claimed_as
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 2
                    AND r.type = 'Prize'
                    $search_query_reward
                    LIMIT %d OFFSET %d
                    ",
                    $limit,
                    $offset
                );

                // Execute data query
                $prize_results = $wpdb->get_results($query, ARRAY_A);

                $data = [];
                $users = [];

                if (!empty($prize_results)) {
                    foreach ($prize_results as $prize_result) {
                        if (!isset($users[$prize_result['user_id']])) {
                            $users[$prize_result['user_id']] = get_user_meta($prize_result['user_id']);
                        }

                        $user_data = $users[$prize_result['user_id']];
                        $billing_address = $user_data['billing_address_1']['0'];

                        if (!empty($user_data['billing_address_2']['0']))
                            $billing_address .= " " . $user_data['billing_address_2']['0'];
                        if (!empty($user_data['billing_city']['0']))
                            $billing_address .= " " . $user_data['billing_city']['0'];
                        if (!empty($user_data['billing_state']['0']))
                            $billing_address .= " " . $user_data['billing_state']['0'];
                        if (!empty($user_data['billing_postcode']['0']))
                            $billing_address .= " " . $user_data['billing_postcode']['0'];
                        if (!empty($user_data['billing_country']['0']))
                            $billing_address .= " " . $user_data['billing_country']['0'];

                        $url = admin_url("user-edit.php?user_id=" . $prize_result['user_id']);
                        $user_id = '<a class="link_text" href="' . $url . '">' . $prize_result['display_name'] . '</a>';

                        $order_id = "";
                        if (!empty($prize_result['order_id'])) {
                            $order_url = admin_url('post.php?post=' . $prize_result['order_id'] . '&action=edit');
                            $order_id = '<a class="link_text" href="' . $order_url . '">' . $prize_result['order_id'] . '</a>';
                        }

                        $data[] = [
                            'id' => $prize_result['id'],
                            'ticket_number' => ($prize_result['webOrder'] == 1 ?
                                '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe-americas" viewBox="0 0 16 16">
                                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484q-.121.12-.242.234c-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.78-.39.952-.171 1.227.182.078.099.163.208.273.318.609.304.662-.132.723-.633.039-.322.081-.671.277-.867.434-.434 1.265-.791 2.028-1.12.712-.306 1.365-.587 1.579-.88A7 7 0 1 1 2.04 4.327Z"/>
                            </svg> '
                                : ''
                            ) . $prize_result['ticket_number'] . "<a class='mark-paid' href='#' data-url='#' data-id='" . $prize_result['id'] . "'  data-set='" . $prize_result['ctype'] . "'></a>",
                            'order_id' => $order_id,
                            'title' => ($prize_result['edited_title'] && $prize_result['edited_title'] != '' ?  $prize_result['edited_title'] : $prize_result['prize_title']) . '<span class="edit-prize-title"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                          </svg> </span>',
                            'claimed' => $prize_result['claimed_as'],
                            'user_name' => $prize_result['display_name'],
                            'user_email' => $prize_result['user_email'],
                            'phone' => $user_data['billing_phone'][0],
                            'user_address' => trim($billing_address),
                            'comp_title' => $prize_result['comp_title']
                        ];
                    }
                }

                wp_send_json([
                    'data' => $data,
                    'draw' => intval($_POST['draw']),
                    'recordsTotal' => intval($totalCount),
                    'recordsFiltered' => intval($totalCount)
                ]);
            }


            if ($_POST['mode'] == 'claimed') {
                $limit = isset($_POST['length']) ? intval($_POST['length']) : 50;
                $offset = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $search = isset($_POST['search_value']) ? sanitize_text_field($_POST['search_value']) : "";
                $filter = isset($_POST['filter']) ? sanitize_text_field($_POST['filter']) : "";


                // Initialize search query part
                $search_query_instant = '';
                $search_query_reward = '';
                $search_query_winner = '';


                if (!empty($search)) {
                    $search_query_instant = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s  OR ipt.edited_title_instant LIKE %s OR ip.title LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }
                if (!empty($search)) {
                    $search_query_reward = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s OR rw.edited_title_reward LIKE %s  OR r.title LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }
                if (!empty($search)) {
                    $search_query_winner = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s  OR cw.edited_title  LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }

                if ($filter == 1) {

                    $count_query3 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    INNER JOIN {$wpdb->prefix}claim_prize_data AS cpd  ON cw.competition_id = cpd.competition_id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.is_admin_declare_winner = 1
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Prize'
                    $search_query_winner
                    ";

                    // Execute count queries
                    $count1 = $wpdb->get_var($wpdb->prepare($count_query1));
                    $count2 = $wpdb->get_var($wpdb->prepare($count_query2));
                    $count3 = $wpdb->get_var($wpdb->prepare($count_query3));

                    // Total count
                    $totalCount = $count3;

                    $query = $wpdb->prepare(
                        "
                    

                    SELECT cw.ticket_number,cw.id, cw.edited_title AS edited_title ,cw.prize_type AS prize_title, cw.prize_type, c.web_order AS webOrder, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id ,'Cash' AS ctype ,cpd.address_line1, cpd.address_line2,cpd.city,cpd.state,cpd.code,cpd.order_id , 'cash' AS mode ,  c.id AS main_comp_id
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    INNER JOIN {$wpdb->prefix}claim_prize_data AS cpd  ON cw.competition_id = cpd.competition_id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.is_admin_declare_winner = 1
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Prize'
                    $search_query_winner

                    LIMIT %d OFFSET %d
                    ",
                        $limit,
                        $offset
                    );

                    // Execute data query
                    $prize_results = $wpdb->get_results($query, ARRAY_A);




                    $data = [];
                    $users = [];

                    if (!empty($prize_results)) {
                        foreach ($prize_results as $prize_result) {
                            if (!isset($users[$prize_result['user_id']])) {
                                $users[$prize_result['user_id']] = get_user_meta($prize_result['user_id']);
                            }

                            $user_data = $users[$prize_result['user_id']];
                            $billing_address = $user_data['billing_address_1']['0'];

                            if (!empty($user_data['billing_address_2']['0']))
                                $billing_address .= " " . $user_data['billing_address_2']['0'];
                            if (!empty($user_data['billing_city']['0']))
                                $billing_address .= " " . $user_data['billing_city']['0'];
                            if (!empty($user_data['billing_state']['0']))
                                $billing_address .= " " . $user_data['billing_state']['0'];
                            if (!empty($user_data['billing_postcode']['0']))
                                $billing_address .= " " . $user_data['billing_postcode']['0'];
                            if (!empty($user_data['billing_country']['0']))
                                $billing_address .= " " . $user_data['billing_country']['0'];

                            $url = admin_url("user-edit.php?user_id=" . $prize_result['user_id']);
                            $user_id = '<a class="link_text" href="' . $url . '">' . $prize_result['display_name'] . '</a>';

                            $order_id = "";
                            // $prize_result['order_id'] = 1140;
                            if (!empty($prize_result['order_id'])) {
                                $order_url = admin_url('post.php?post=' . $prize_result['order_id'] . '&action=edit');
                                $order_id = '<a class="link_text" href="' . $order_url . '">' . $prize_result['order_id'] . '</a>';
                            }



                            // Prepare the SQL query with placeholders for values
                            $query_claimed_address = $wpdb->prepare(
                                "
                                    SELECT * 
                                    FROM {$wpdb->prefix}claim_prize_data 
                                    WHERE user_id = %d 
                                    AND competition_id = %d 
                                    AND ticket_number = %d
                                    ",
                                $prize_result['user_id'],
                                $prize_result['main_comp_id'],
                                $prize_result['ticket_number']
                            );

                            // Execute the query
                            $query_claimed_address_results = $wpdb->get_results($query_claimed_address, ARRAY_A);

                            // Log the query result for debugging
                            // error_log('query_claimed_address query: ' . print_r($query_claimed_address, true));
                            // error_log('query_claimed_address_results result: ' . print_r($query_claimed_address_results, true));

                            if (!empty($query_claimed_address_results)) {
                                $billing_address = "";

                                if (!empty($query_claimed_address_results[0]['address_line1']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['address_line1'];
                                if (!empty($query_claimed_address_results[0]['address_line2']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['address_line2'];
                                if (!empty($query_claimed_address_results[0]['city']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['city'];
                                if (!empty($query_claimed_address_results[0]['state']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['state'];
                                if (!empty($query_claimed_address_results[0]['code']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['code'];
                            }



                            $data[] = [
                                'id' => '<input type="checkbox" class="user-checkbox" data-id="' . $prize_result['order_id'] . '" data-compid="' . $prize_result['main_comp_id'] . '" />',
                                'ticket_number' => ($prize_result['webOrder'] == 1 ?
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe-americas" viewBox="0 0 16 16">
                                    <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484q-.121.12-.242.234c-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.78-.39.952-.171 1.227.182.078.099.163.208.273.318.609.304.662-.132.723-.633.039-.322.081-.671.277-.867.434-.434 1.265-.791 2.028-1.12.712-.306 1.365-.587 1.579-.88A7 7 0 1 1 2.04 4.327Z"/>
                                </svg> '
                                    : ''
                                ) . $prize_result['ticket_number'] . "<a class='mark-paid' href='#' data-url='#' data-id='" . $prize_result['id'] . "'  data-set='" . $prize_result['mode'] . "'></a>",
                                'order_id' => $order_id,
                                'title' => ($prize_result['edited_title'] && $prize_result['edited_title'] != '' ?  $prize_result['edited_title'] : $prize_result['prize_title']) . '<span class="edit-prize-title"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                              </svg> </span>',
                                'type' => $prize_result['ctype'],
                                'user_name' => $prize_result['display_name'],
                                'user_email' => $prize_result['user_email'],
                                'phone' => $user_data['billing_phone'][0],
                                'user_address' => $prize_result['address_line1'],
                                'comp_title' => $prize_result['comp_title']
                            ];
                        }
                    }

                    wp_send_json([
                        'data' => $data,
                        'draw' => intval($_POST['draw']),
                        'recordsTotal' => intval($totalCount),
                        'recordsFiltered' => intval($totalCount)
                    ]);
                } elseif ($filter == 2) {
                    $count_query1 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 1
                    AND ip.type = 'Prize'
                    $search_query_instant
                    ";

                    $count_query2 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 1
                    AND r.type = 'Prize'
                    $search_query_reward
                    ";

                    $count1 = $wpdb->get_var($wpdb->prepare($count_query1));
                    $count2 = $wpdb->get_var($wpdb->prepare($count_query2));

                    $totalCount = $count1 + $count2;

                    $query = $wpdb->prepare(
                        "
                    SELECT ipt.ticket_number,ipt.id, ipt.edited_title_instant AS edited_title, ip.title AS prize_title, ip.type, ip.web_order_instant AS webOrder , c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id,'Prize' AS ctype  , 'instant' AS mode , c.id AS main_comp_id
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 1
                    AND ip.type = 'Prize'
                    $search_query_instant
                    
                    UNION
                    
                    SELECT rw.ticket_number,rw.id,  rw.edited_title_reward AS edited_title, r.title AS prize_title, r.type, r.web_order_reward AS webOrder, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id ,'Prize' AS ctype , 'reward' AS mode , c.id AS main_comp_id
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 1
                    AND r.type = 'Prize'
                    $search_query_reward                    

                    LIMIT %d OFFSET %d
                    ",
                        $limit,
                        $offset
                    );

                    // Execute data query
                    $prize_results = $wpdb->get_results($query, ARRAY_A);

                    // print_r($prize_results);

                    $data = [];
                    $users = [];

                    if (!empty($prize_results)) {
                        foreach ($prize_results as $prize_result) {
                            if (!isset($users[$prize_result['user_id']])) {
                                $users[$prize_result['user_id']] = get_user_meta($prize_result['user_id']);
                            }

                            $user_data = $users[$prize_result['user_id']];
                            $billing_address = $user_data['billing_address_1']['0'];

                            if (!empty($user_data['billing_address_2']['0']))
                                $billing_address .= " " . $user_data['billing_address_2']['0'];
                            if (!empty($user_data['billing_city']['0']))
                                $billing_address .= " " . $user_data['billing_city']['0'];
                            if (!empty($user_data['billing_state']['0']))
                                $billing_address .= " " . $user_data['billing_state']['0'];
                            if (!empty($user_data['billing_postcode']['0']))
                                $billing_address .= " " . $user_data['billing_postcode']['0'];
                            if (!empty($user_data['billing_country']['0']))
                                $billing_address .= " " . $user_data['billing_country']['0'];

                            $url = admin_url("user-edit.php?user_id=" . $prize_result['user_id']);
                            $user_id = '<a class="link_text" href="' . $url . '">' . $prize_result['display_name'] . '</a>';

                            $order_id = "";
                            // $prize_result['order_id'] = 1140;
                            if (!empty($prize_result['order_id'])) {
                                $order_url = admin_url('post.php?post=' . $prize_result['order_id'] . '&action=edit');
                                $order_id = '<a class="link_text" href="' . $order_url . '">' . $prize_result['order_id'] . '</a>';
                            }

                            // Prepare the SQL query with placeholders for values
                            $query_claimed_address = $wpdb->prepare(
                                "
                                    SELECT * 
                                    FROM {$wpdb->prefix}claim_prize_data 
                                    WHERE user_id = %d 
                                    AND competition_id = %d 
                                    AND ticket_number = %d
                                    ",
                                $prize_result['user_id'],
                                $prize_result['main_comp_id'],
                                $prize_result['ticket_number']
                            );

                            // Execute the query
                            $query_claimed_address_results = $wpdb->get_results($query_claimed_address, ARRAY_A);

                            // Log the query result for debugging
                            // error_log('query_claimed_address query: ' . print_r($query_claimed_address, true));
                            // error_log('query_claimed_address_results result: ' . print_r($query_claimed_address_results, true));

                            if (!empty($query_claimed_address_results)) {
                                $billing_address = "";

                                if (!empty($query_claimed_address_results[0]['address_line1']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['address_line1'];
                                if (!empty($query_claimed_address_results[0]['address_line2']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['address_line2'];
                                if (!empty($query_claimed_address_results[0]['city']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['city'];
                                if (!empty($query_claimed_address_results[0]['state']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['state'];
                                if (!empty($query_claimed_address_results[0]['code']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['code'];
                            }

                            $data[] = [
                                'id' => '<input type="checkbox" class="user-checkbox" data-id="' . $prize_result['order_id'] . '" data-compid="' . $prize_result['main_comp_id'] . '" />',
                                'ticket_number' => ($prize_result['webOrder'] == 1 ?
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe-americas" viewBox="0 0 16 16">
                                    <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484q-.121.12-.242.234c-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.78-.39.952-.171 1.227.182.078.099.163.208.273.318.609.304.662-.132.723-.633.039-.322.081-.671.277-.867.434-.434 1.265-.791 2.028-1.12.712-.306 1.365-.587 1.579-.88A7 7 0 1 1 2.04 4.327Z"/>
                                </svg> '
                                    : ''
                                ) . $prize_result['ticket_number'] . "<a class='mark-paid' href='#' data-url='#' data-id='" . $prize_result['id'] . "'  data-set='" . $prize_result['mode'] . "'></a>",
                                'order_id' => $order_id,
                                'title' => ($prize_result['edited_title'] && $prize_result['edited_title'] != '' ?  $prize_result['edited_title'] : $prize_result['prize_title']) . '<span class="edit-prize-title"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                              </svg> </span>',
                                'type' => $prize_result['ctype'],
                                'user_name' => $prize_result['display_name'],
                                'user_email' => $prize_result['user_email'],
                                'phone' => $user_data['billing_phone'][0],
                                'user_address' => trim($billing_address),
                                'comp_title' => $prize_result['comp_title']
                            ];
                        }
                    }

                    wp_send_json([
                        'data' => $data,
                        'draw' => intval($_POST['draw']),
                        'recordsTotal' => intval($totalCount),
                        'recordsFiltered' => intval($totalCount)
                    ]);
                } else {

                    $count_query1 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 1
                    AND ip.type = 'Prize'
                    $search_query_instant
                    ";

                    $count_query2 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 1
                    AND r.type = 'Prize'
                    $search_query_reward
                    ";

                    $count_query3 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.is_admin_declare_winner = 1
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Prize'
                    $search_query_winner
                    ";

                    // Execute count queries
                    $count1 = $wpdb->get_var($wpdb->prepare($count_query1));
                    $count2 = $wpdb->get_var($wpdb->prepare($count_query2));
                    $count3 = $wpdb->get_var($wpdb->prepare($count_query3));

                    // Total count
                    $totalCount = $count1 + $count2 + $count3;

                    // Data query
                    $query = $wpdb->prepare(
                        "
                    SELECT ipt.ticket_number,ipt.id as main_id, ipt.edited_title_instant AS edited_title, ip.title AS prize_title, ip.type, ip.web_order_instant AS webOrder  , c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id, ip.type AS ctype, 'instant' AS mode , c.id AS main_comp_id ,ipt.claimed_as
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 1
                    AND ip.type = 'Prize'
                    $search_query_instant
                    
                    UNION
                    
                    SELECT rw.ticket_number,rw.id as main_id, rw.edited_title_reward AS edited_title, r.title AS prize_title, r.type, r.web_order_reward AS webOrder, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id , r.type AS ctype , 'reward' AS mode , c.id AS main_comp_id,rw.claimed_as
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 1
                    AND r.type = 'Prize'
                    $search_query_reward


                    UNION

                    SELECT cw.ticket_number,cw.id as main_id, cw.edited_title AS edited_title ,cw.prize_type AS prize_title, cw.prize_type,   c.web_order AS webOrder,  c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id , cw.prize_type AS ctype , 'cash' AS mode , c.id AS main_comp_id, cw.claimed_as
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.is_admin_declare_winner = 1
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Prize'
                    $search_query_winner

                    LIMIT %d OFFSET %d
                    ",
                        $limit,
                        $offset
                    );

                    // Execute data query
                    $prize_results = $wpdb->get_results($query, ARRAY_A);

                    // error_log('prize_results query: ' . print_r($prize_results, true));


                    // print_r($prize_results);

                    $data = [];
                    $users = [];

                    if (!empty($prize_results)) {
                        foreach ($prize_results as $prize_result) {
                            if (!isset($users[$prize_result['user_id']])) {
                                $users[$prize_result['user_id']] = get_user_meta($prize_result['user_id']);
                            }

                            $user_data = $users[$prize_result['user_id']];
                            $billing_address = $user_data['billing_address_1']['0'];

                            if (!empty($user_data['billing_address_2']['0']))
                                $billing_address .= " " . $user_data['billing_address_2']['0'];
                            if (!empty($user_data['billing_city']['0']))
                                $billing_address .= " " . $user_data['billing_city']['0'];
                            if (!empty($user_data['billing_state']['0']))
                                $billing_address .= " " . $user_data['billing_state']['0'];
                            if (!empty($user_data['billing_postcode']['0']))
                                $billing_address .= " " . $user_data['billing_postcode']['0'];
                            if (!empty($user_data['billing_country']['0']))
                                $billing_address .= " " . $user_data['billing_country']['0'];

                            $url = admin_url("user-edit.php?user_id=" . $prize_result['user_id']);
                            $user_id = '<a class="link_text" href="' . $url . '">' . $prize_result['display_name'] . '</a>';

                            $order_id = "";

                            // $prize_result['order_id'] = 1140;

                            if (!empty($prize_result['order_id'])) {
                                $order_url = admin_url('post.php?post=' . $prize_result['order_id'] . '&action=edit');
                                $order_id = '<a class="link_text" href="' . $order_url . '">' . $prize_result['order_id'] . '</a>';
                            }



                            // Prepare the SQL query with placeholders for values
                            $query_claimed_address = $wpdb->prepare(
                                "
                                    SELECT * 
                                    FROM {$wpdb->prefix}claim_prize_data 
                                    WHERE user_id = %d 
                                    AND competition_id = %d 
                                    AND ticket_number = %d
                                    ",
                                $prize_result['user_id'],
                                $prize_result['main_comp_id'],
                                $prize_result['ticket_number']
                            );

                            // Execute the query
                            $query_claimed_address_results = $wpdb->get_results($query_claimed_address, ARRAY_A);

                            // Log the query result for debugging
                            // error_log('query_claimed_address query: ' . print_r($prize_result, true));
                            // error_log('query_claimed_address_results result: ' . print_r($query_claimed_address_results, true));

                            if (!empty($query_claimed_address_results)) {
                                $billing_address = "";

                                if (!empty($query_claimed_address_results[0]['address_line1']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['address_line1'];
                                if (!empty($query_claimed_address_results[0]['address_line2']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['address_line2'];
                                if (!empty($query_claimed_address_results[0]['city']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['city'];
                                if (!empty($query_claimed_address_results[0]['state']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['state'];
                                if (!empty($query_claimed_address_results[0]['code']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['code'];
                                if (!empty($query_claimed_address_results[0]['country']))
                                    $billing_address .= " " . $query_claimed_address_results[0]['country'];
                            }



                            // Prepare the SQL query with placeholders for values
                            $query_zempler = $wpdb->prepare(
                                "
                                    SELECT * 
                                    FROM {$wpdb->prefix}zempler_payments 
                                    WHERE comptetion_id = %d 
                                    AND prize_id = %d 
                                    
                                    ",

                                $prize_result['main_comp_id'],
                                $prize_result['main_id']
                            );

                            // Execute the query
                            $query_zempler_data = $wpdb->get_results($query_zempler, ARRAY_A);


                            $data[] = [
                                'id' => '<input type="checkbox" class="user-checkbox" data-id="' . $prize_result['order_id'] . '"   data-mainid="' . $prize_result['main_id'] . '"  data-compid="' . $prize_result['main_comp_id'] . '"/>',
                                'ticket_number' => ($prize_result['webOrder'] == 1 ?
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe-americas" viewBox="0 0 16 16">
                                    <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0M2.04 4.326c.325 1.329 2.532 2.54 3.717 3.19.48.263.793.434.743.484q-.121.12-.242.234c-.416.396-.787.749-.758 1.266.035.634.618.824 1.214 1.017.577.188 1.168.38 1.286.983.082.417-.075.988-.22 1.52-.215.782-.406 1.48.22 1.48 1.5-.5 3.798-3.186 4-5 .138-1.243-2-2-3.5-2.5-.478-.16-.755.081-.99.284-.172.15-.322.279-.51.216-.445-.148-2.5-2-1.5-2.5.78-.39.952-.171 1.227.182.078.099.163.208.273.318.609.304.662-.132.723-.633.039-.322.081-.671.277-.867.434-.434 1.265-.791 2.028-1.12.712-.306 1.365-.587 1.579-.88A7 7 0 1 1 2.04 4.327Z"/>
                                </svg> '
                                    : ''
                                ) . $prize_result['ticket_number'] . "<a class='mark-paid' href='#' data-url='#' data-id='" . $prize_result['main_id'] . "'  data-set='" . $prize_result['mode'] . "'></a>",
                                'order_id' => $order_id,
                                'title' => ($prize_result['edited_title'] && $prize_result['edited_title'] != '' ?  $prize_result['edited_title'] : $prize_result['prize_title']) . '<span class="edit-prize-title"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                              </svg> </span>',
                                'type' => $prize_result['ctype'],
                                'claimed' => $prize_result['claimed_as'],
                                'user_name' => $prize_result['display_name'],
                                'user_email' => $prize_result['user_email'],
                                'phone' => $user_data['billing_phone'][0],
                                'user_address' => trim($billing_address),
                                'comp_title' => $prize_result['comp_title']
                            ];
                        }
                    }

                    wp_send_json([
                        'data' => $data,
                        'draw' => intval($_POST['draw']),
                        'recordsTotal' => intval($totalCount),
                        'recordsFiltered' => intval($totalCount)
                    ]);
                }

                // Count queries

            }

            if ($_POST['mode'] == 'points-cred') {
                $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
                $offset = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $search = isset($_POST['search_value']) ? sanitize_text_field($_POST['search_value']) : "";

                // Prepare the search query
                $search_query = '';
                if (!empty($search)) {
                    $search_query = $wpdb->prepare(
                        " AND (wc_points_rewards_user_points_log.ticket_number LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }

                // Count query
                $count_query = $wpdb->prepare(
                    "
                    SELECT COUNT(*) as total
                    FROM {$wpdb->prefix}wc_points_rewards_user_points_log  
                    WHERE type NOT IN ('order-redeem', 'admin-adjustment', 'order-placed')                
                     
                    
                    "
                );

                // Execute count query
                $totalCount = $wpdb->get_var($count_query);

                // Data query
                $data_query = $wpdb->prepare(
                    "
                   SELECT 
                        {$wpdb->prefix}wc_points_rewards_user_points_log.*                       
                    FROM {$wpdb->prefix}wc_points_rewards_user_points_log                    
                     WHERE type NOT IN ('order-redeem', 'admin-adjustment', 'order-placed')
                    ORDER BY date DESC                  
                    LIMIT %d OFFSET %d
                    ",
                    $limit,
                    $offset
                );

                // Execute data query
                $prize_results = $wpdb->get_results($data_query, ARRAY_A);

                // echo"<pre>";

                // print_r($prize_results);
                // echo"</pre>";

                $data = [];
                $users = [];

                if (!empty($prize_results)) {
                    foreach ($prize_results as $prize_result) {
                        if (!isset($users[$prize_result['user_id']])) {
                            $users[$prize_result['user_id']] = get_user_meta($prize_result['user_id']);
                        }

                        $user_data = $users[$prize_result['user_id']];

                        // print_r($user_data);

                        $billing_address = $user_data['billing_address_1']['0'];

                        if (!empty($user_data['billing_address_2']['0']))
                            $billing_address .= " " . $user_data['billing_address_2']['0'];
                        if (!empty($user_data['billing_city']['0']))
                            $billing_address .= " " . $user_data['billing_city']['0'];
                        if (!empty($user_data['billing_state']['0']))
                            $billing_address .= " " . $user_data['billing_state']['0'];
                        if (!empty($user_data['billing_postcode']['0']))
                            $billing_address .= " " . $user_data['billing_postcode']['0'];
                        if (!empty($user_data['billing_country']['0']))
                            $billing_address .= " " . $user_data['billing_country']['0'];

                        $url = admin_url("user-edit.php?user_id=" . $prize_result['user_id']);
                        $user_id = '<a class="link_text" href="' . $url . '">' . $prize_result['display_name'] . '</a>';

                        $order_id = "";
                        if (!empty($prize_result['order_id'])) {
                            $order_url = admin_url('post.php?post=' . $prize_result['order_id'] . '&action=edit');
                            $order_id = '<a class="link_text" href="' . $order_url . '">' . $prize_result['order_id'] . '</a>';
                        }

                        if (!empty($user_data['billing_email']['0']))
                            $billing_email = " " . $user_data['billing_email']['0'];

                        if (!empty($user_data['first_name']['0']))
                            $first_name = $user_data['first_name']['0'];

                        if (!empty($user_data['last_name']['0']))
                            $last_name = $user_data['last_name']['0'];

                        $data[] = [
                            'id' => $prize_result['id'],
                            'ticket_number' => $prize_result['ticket_number'],
                            'order_id' => $order_id,
                            'title' => $prize_result['points'] . " " . 'Points',
                            'user_name' => $first_name . " " . $last_name,
                            'user_email' => $billing_email,
                            'phone' => $user_data['billing_phone'][0],
                            'user_address' => trim($billing_address),
                            'comp_title' => $prize_result['comp_title']
                        ];
                    }
                }

                wp_send_json([
                    'data' => $data,
                    'draw' => intval($_POST['draw']),
                    'recordsTotal' => intval($totalCount),
                    'recordsFiltered' => intval($totalCount)
                ]);
            }


            if ($_POST['mode'] == 'ticket-cred') {
                $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
                $offset = isset($_POST['start']) ? intval($_POST['start']) : 0;
                $search = isset($_POST['search_value']) ? sanitize_text_field($_POST['search_value']) : "";

                // Prepare the search query
                $search_query = '';
                if (!empty($search)) {
                    $search_query = $wpdb->prepare(
                        " AND (ct.ticket_number LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%',
                        '%' . $wpdb->esc_like($search) . '%'
                    );
                }

                // Count query
                //                $count_query = $wpdb->prepare(
                //                    "
                //                    SELECT COUNT(*) as total
                //                    FROM {$wpdb->prefix}competition_winners
                //                    INNER JOIN {$wpdb->prefix}competitions ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_winners.competition_id
                //                    INNER JOIN {$wpdb->prefix}users ON {$wpdb->prefix}competition_winners.user_id = {$wpdb->prefix}users.id
                //                    WHERE {$wpdb->prefix}competition_winners.prize_type = 'Tickets'
                //                    $search_query
                //                    "
                //                );
                //
                //                // Execute count query
                //                $totalCount = $wpdb->get_var($count_query);
                //
                //                // Data query
                //                $data_query = $wpdb->prepare(
                //                    "
                //                   SELECT 
                //                        {$wpdb->prefix}competition_winners.*, 
                //                        {$wpdb->prefix}competitions.title AS comp_title,
                //                        {$wpdb->prefix}users.display_name, 
                //                        {$wpdb->prefix}users.user_email, 
                //                        {$wpdb->prefix}users.id 
                //                    FROM {$wpdb->prefix}competition_winners
                //                    INNER JOIN {$wpdb->prefix}competitions ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_winners.competition_id
                //                    INNER JOIN {$wpdb->prefix}users ON {$wpdb->prefix}competition_winners.user_id = {$wpdb->prefix}users.id
                //                    WHERE {$wpdb->prefix}competition_winners.prize_type = 'Tickets'
                //                    $search_query
                //                    ORDER BY {$wpdb->prefix}competition_winners.created_at ASC
                //                    LIMIT %d OFFSET %d
                //                    ",
                //                    $limit,
                //                    $offset
                //                );

                $count_query1 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ip.type = 'Tickets'
                    $search_query
                ";

                $count_query2 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND r.type = 'Tickets'
                    $search_query
                ";

                $count_query3 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Tickets'
                    $search_query
                    ";

                // Execute count queries
                $count1 = $wpdb->get_var($wpdb->prepare($count_query1));
                $count2 = $wpdb->get_var($wpdb->prepare($count_query2));
                $count3 = $wpdb->get_var($wpdb->prepare($count_query3));

                // Total count
                $totalCount = $count1 + $count2 + $count3;

                // Data query
                $query = $wpdb->prepare(
                    "
                    SELECT ipt.ticket_number,ipt.id, ip.title AS prize_title, ip.type, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email , u.id AS user_id ,'instant' AS ctype 
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ip.type = 'Tickets'
                    $search_query

                    UNION

                    SELECT cw.ticket_number,cw.id, cw.prize_type AS prize_title, cw.prize_type, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id ,'cash' AS ctype 
                    FROM {$wpdb->prefix}competition_winners AS cw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = cw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = cw.competition_id                    
                    INNER JOIN {$wpdb->prefix}users AS u ON cw.user_id = u.id
                    WHERE ct.ticket_number = cw.ticket_number                    
                    AND ct.is_purchased = 1
                    AND cw.user_id > 0
                    AND cw.prize_type = 'Tickets'
                    $search_query
                    
                    UNION
                    
                    SELECT rw.ticket_number,rw.id, r.title AS prize_title, r.type, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email, u.id AS user_id ,'reward' AS ctype 
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND r.type = 'Tickets'
                    $search_query
                    LIMIT %d OFFSET %d
                    ",
                    $limit,
                    $offset
                );





                // Execute data query
                $prize_results = $wpdb->get_results($query, ARRAY_A);

                $data = [];
                $users = [];

                if (!empty($prize_results)) {
                    foreach ($prize_results as $prize_result) {
                        if (!isset($users[$prize_result['user_id']])) {
                            $users[$prize_result['user_id']] = get_user_meta($prize_result['user_id']);
                        }

                        $user_data = $users[$prize_result['user_id']];
                        $billing_address = $user_data['billing_address_1']['0'];

                        if (!empty($user_data['billing_address_2']['0']))
                            $billing_address .= " " . $user_data['billing_address_2']['0'];
                        if (!empty($user_data['billing_city']['0']))
                            $billing_address .= " " . $user_data['billing_city']['0'];
                        if (!empty($user_data['billing_state']['0']))
                            $billing_address .= " " . $user_data['billing_state']['0'];
                        if (!empty($user_data['billing_postcode']['0']))
                            $billing_address .= " " . $user_data['billing_postcode']['0'];
                        if (!empty($user_data['billing_country']['0']))
                            $billing_address .= " " . $user_data['billing_country']['0'];

                        $url = admin_url("user-edit.php?user_id=" . $prize_result['user_id']);
                        $user_id = '<a class="link_text" href="' . $url . '">' . $prize_result['display_name'] . '</a>';

                        $order_id = "";
                        if (!empty($prize_result['order_id'])) {
                            $order_url = admin_url('post.php?post=' . $prize_result['order_id'] . '&action=edit');
                            $order_id = '<a class="link_text" href="' . $order_url . '">' . $prize_result['order_id'] . '</a>';
                        }

                        $data[] = [
                            'id' => $prize_result['id'],
                            'ticket_number' => $prize_result['ticket_number'],
                            'order_id' => $order_id,
                            'title' => 'Ticket',
                            'user_name' => $prize_result['display_name'],
                            'user_email' => $prize_result['user_email'],
                            'phone' => $user_data['billing_phone'][0],
                            'user_address' => trim($billing_address),
                            'comp_title' => $prize_result['comp_title']
                        ];
                    }
                }

                wp_send_json([
                    'data' => $data,
                    'draw' => intval($_POST['draw']),
                    'recordsTotal' => intval($totalCount),
                    'recordsFiltered' => intval($totalCount)
                ]);
            }
        }
    }


    // function generate_and_save_pdf($users_data)
    // {
    //     // Define the upload directory
    //     $upload_dir = wp_upload_dir();
    //     $folder = 'shipping_labels'; // Define your custom folder name
    //     $target_dir = $upload_dir['basedir'] . '/' . $folder;

    //     // Create the directory if it doesn't exist
    //     if (!file_exists($target_dir)) {
    //         mkdir($target_dir, 0755, true);
    //     }

    //     // Create a new PDF document
    //     $pdf = new FPDF();

    //     // Loop through the users data array to generate content for each user
    //     foreach ($users_data as $user_data) {
    //         // Add a new page for each user
    //         $pdf->AddPage();

    //         // Set up fonts for the page
    //         $pdf->SetFont('Arial', 'B', 16);

    //         // ---------- Ship to Section (Shipping Address) ----------
    //         $pdf->Cell(0, 10, 'Ship to:', 0, 1, 'L');  // Section Title
    //         $pdf->SetFont('Arial', '', 12);

    //         // Draw a border around the shipping address section
    //         $pdf->Rect(10, 20, 190, 60); // Position x, y, width, height for the border around the shipping address
    //         $pdf->SetXY(15, 25);  // Set position for the address text inside the border

    //         // Shipping Information (With proper line breaks)
    //         if (isset($user_data['shipping_info'])) {
    //             $pdf->MultiCell(0, 10, str_replace('\n', "\n", $user_data['shipping_info']));
    //         }

    //         // Line break between sections
    //         $pdf->Ln(15);

    //         // ---------- Product Information Section ----------
    //         $pdf->SetFont('Arial', 'B', 16);
    //         $pdf->Cell(0, 10, 'Product Information:', 0, 1, 'L');  // Section Title
    //         $pdf->SetFont('Arial', '', 12);

    //         // Draw a border around the product information section
    //         $pdf->Rect(10, 90, 190, 60);  // Position x, y, width, height for the border around the product information
    //         $pdf->SetXY(15, 95);  // Set position for the product info text inside the border

    //         // Product Information (With proper line breaks)
    //         if (isset($user_data['product_info'])) {
    //             $pdf->MultiCell(0, 10, str_replace('\n', "\n", $user_data['product_info']));
    //         }

    //         // Line break after each user's info (optional)
    //         $pdf->Ln(10);
    //     }

    //     // Create a dynamic filename with timestamp
    //     $timestamp = date('Ymd_His'); // Format: YYYYMMDD_HHMMSS
    //     $filename = 'shipping_labels_' . $timestamp; // Example: shipping_labels_20231023_153045

    //     // Save the PDF to the specified directory
    //     $pdf_file_path = $target_dir . '/' . $filename . '.pdf';
    //     $pdf->Output($pdf_file_path, 'F');

    //     // Return the URI of the generated PDF
    //     return $upload_dir['baseurl'] . '/' . $folder . '/' . $filename . '.pdf';
    // }


    function generate_and_save_pdf($users_data)
    {
        $upload_dir = wp_upload_dir();
        $folder = 'shipping_labels';
        $target_dir = $upload_dir['basedir'] . '/' . $folder;

        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
        }

        $pdf = new ShippingLabelPDF();

        foreach ($users_data as $user_data) {
            $pdf->AddPage();
            $pdf->DrawLabel($user_data);
        }

        $timestamp = date('Ymd_His');
        $filename = 'shipping_labels_' . $timestamp;
        $pdf_file_path = $target_dir . '/' . $filename . '.pdf';

        try {
            $pdf->Output($pdf_file_path, 'F');
            return $upload_dir['baseurl'] . '/' . $folder . '/' . $filename . '.pdf';
        } catch (Exception $e) {
            error_log('PDF Generation Error: ' . $e->getMessage());
            return false;
        }
    }


    public static function mark_as_paid()
    {
        global $wpdb;
        $selected_rows = isset($_POST['selected_data']) ? json_decode(stripslashes($_POST['selected_data']), true) : [];
        if (empty($selected_rows)) {
            wp_send_json_error(['message' => 'No rows selected']);
            return;
        }

        // error_log("mark_as_paid " . print_r($selected_rows, true));

        foreach ($selected_rows as $row) {
            $table_name = '';
            $email = $row['email'];
            $id = intval($row['mainid']); // Sanitize the ID

            $ticket_number = "";
            $comp_name = "";
            $intant_title = "";
            $instant_image = "";
            $user_email = '';

            // Determine the table to update based on the 'table' field
            if ($row['table'] === 'instant') {
                $table_name = "{$wpdb->prefix}comp_instant_prizes_tickets";

                $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes_tickets WHERE id = %s", $id);
                $instant_wins = $wpdb->get_results($query, ARRAY_A);

                $query2 = $wpdb->prepare("SELECT title FROM " . $wpdb->prefix . "competitions WHERE id = %s", $instant_wins[0]['competition_id']);
                $competition = $wpdb->get_results($query2, ARRAY_A);

                $query3 = $wpdb->prepare("SELECT image , title AS instant_title  FROM " . $wpdb->prefix . "comp_instant_prizes WHERE id = %s", $instant_wins[0]['instant_id']);
                $instant_wins_details = $wpdb->get_results($query3, ARRAY_A);



                $ticket_number = $instant_wins[0]['ticket_number'];
                $comp_name = $competition[0]['title'];
                $intant_title = $instant_wins_details[0]['instant_title'];
                $instant_image = $instant_wins_details[0]['image'];
            } elseif ($row['table'] === 'RewardWin') {
                $table_name = "{$wpdb->prefix}comp_reward_winner";
            } elseif ($row['table'] === 'cash') {
                $table_name = "{$wpdb->prefix}competition_winners";

                $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "competition_winners WHERE id = %s", $id);
                $main = $wpdb->get_results($query, ARRAY_A);

                $query2 = $wpdb->prepare("SELECT title , image FROM " . $wpdb->prefix . "competitions WHERE id = %s", $main[0]['competition_id']);
                $competition = $wpdb->get_results($query2, ARRAY_A);

                $comp_name = $competition[0]['title'];
                $ticket_number = $main[0]['ticket_number'];
                $intant_title = $competition[0]['instant_title'];
                $instant_image = $competition[0]['image'];
            }

            // If a valid table is found, update the record
            if (!empty($table_name)) {

                $result = $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE $table_name 
                         SET is_admin_declare_winner = %d
                         WHERE id = %d",
                        2, // Value to update
                        $id // ID to target
                    )
                );


                // error_log("table+++++++++++" .  print_r($result, true));
                if ($email) {
                    // Email subject
                    $subject = " Well done Legend! Your prize is on the way";

                    // Get WooCommerce mailer instance
                    $mailer = WC()->mailer();

                    // Prepare email variables based on prize type
                    $template = 'emails/win-web-order-email.php';

                    // Generate email content
                    $content = wc_get_template_html(
                        $template,
                        array(
                            'email_heading' => $subject,
                            'sent_to_admin' => false,
                            'plain_text' => false,
                            'email' => $mailer,
                            'title' => $intant_title,
                            'value' => "Cash Prize",
                            'type' => "Cash Prize",
                            'image' => $instant_image,
                            'comp_title' => $comp_name,
                            'ticket_number' =>  $ticket_number,
                            'prize_id' => "Cash Prize",
                            'competition_id' => "Cash Prize",
                            'order' => "Cash Prize",
                        )
                    );

                    // Email headers
                    $headers = "Content-Type: text/html\r\n";

                    // Send email
                    // $user_email = $competition['winner_email']; // Ensure this variable is defined and holds the winner's email
                    // Ensure this variable is defined and holds the winner's email        
                    $mailer->send($email, $subject, $content, $headers);
                }

                // Optional: Check the result for debugging or further processing
                if (false === $result) {
                    error_log("Failed to update ID $id in table $table_name: " . $wpdb->last_error);
                } elseif (0 === $result) {
                    error_log("No rows updated for ID $id in table $table_name.");
                } else {
                    error_log("Successfully updated ID $id in table $table_name.");
                }
            } else {
                error_log("Invalid table '{$row['table']}' provided for row ID $id.");
            }
        }



        // if (!empty($ids)) {
        //     // Prepare the placeholders for the SQL query
        //     $placeholders = implode(',', array_fill(0, count($ids), '%d'));

        //     // Update the records in the database
        //     $wpdb->query(
        //         $wpdb->prepare(
        //             "UPDATE {$wpdb->prefix}competition_winners
        //              SET is_admin_declare_winner = %d
        //              WHERE id IN ($placeholders)",
        //             array_merge([2], $ids)
        //         )
        //     );
        // }

        $total_users_count = 1;





        wp_send_json(
            array(
                'draw' => intval($_POST['draw']),
                'recordsTotal' => $total_users_count,
                'recordsFiltered' => $total_users_count,
            )
        );
    }

    public static function change_prize_title()
    {

        global $wpdb;
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $text = isset($_POST['text']) ? $_POST['text'] : '';
        $ticketNumber = isset($_POST['ticketNumber']) ? $_POST['ticketNumber'] : '';

        if ($id) {
            // error_log("change_prize_title: " . print_r($id, true));
            // error_log("type: " . print_r($type, true));
            // error_log("text: " . print_r($text, true));

            // Update based on type
            if ($type === 'instant') {
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}comp_instant_prizes_tickets 
                     SET edited_title_instant = %s  
                     WHERE id = %d",
                        $text,
                        $id
                    )
                );
            }

            if ($type === 'reward') {
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}comp_reward_winner 
                     SET edited_title_reward = %s 
                     WHERE id = %d",
                        $text,
                        $id
                    )
                );
            }

            if ($type === 'cash') {
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}competition_winners 
                     SET edited_title = %s
                     WHERE id = %d",
                        $text,
                        $id
                    )
                );
            }
        }

        wp_send_json(array(
            'status' => 200,
            'message' => "Data saved Successfully"
        ));
    }


    public static function mark_as_paid_prize()
    {

        global $wpdb;
        $selected_rows = isset($_POST['selected_data']) ? json_decode(stripslashes($_POST['selected_data']), true) : [];
        if (empty($selected_rows)) {
            wp_send_json_error(['message' => 'No rows selected']);
            return;
        }
        $users_data = array();

        // error_log('Order Object: ' . print_r($selected_rows, true));

        foreach ($selected_rows as $row) {
            $users_data[] = array(
                'name' => $row['winner'],
                'address' => $row['address'],
                'email' => $row['email'],
                'tel' => $row['tel'],
                'competition' => $row['competitionName'],
                'ticket_number' => $row['ticketNumber'],
                'prize' => $row['prize'],
                'type' => $row['type'],
                'order_id' => $row['orderId']
            );
        }

        $user = get_user_by('email', $row['email']);

        if ($user) {
            // User found, get user ID
            $user_id = $user->ID;
        }

        $order_id = $row['orderId'];
        $ticketNumber = $row['ticketNumber'];
        $compid = $row['compid'];
        // Query to get data for a specific user and order
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM wp_claim_prize_data WHERE user_id = %d  AND ticket_number = %d AND competition_id = %d",
                $user_id,
                $ticketNumber,
                $compid
            )
        );

        if (!empty($results)) {
            $order = wc_get_order($row['orderId']);
            // error_log('Order Object results: ' . print_r($results, true));
            // error_log('Order Object: ' . print_r($order, true));
            // error_log('user_id: ' . print_r($user_id, true));

            // Update billing address
            $order->set_billing_address_1($results[0]->address_line1);
            $order->set_billing_address_2($results[0]->address_line2);
            $order->set_billing_city($results[0]->city);
            $order->set_billing_state($results[0]->state);
            $order->set_billing_postcode($results[0]->code);
            $order->set_billing_country('GB');

            // Update shipping address
            $order->set_shipping_address_1($results[0]->address_line1);
            $order->set_shipping_address_2($results[0]->address_line2);
            $order->set_shipping_city($results[0]->city);
            $order->set_shipping_state($results[0]->state);
            $order->set_shipping_postcode($results[0]->code);
            $order->set_shipping_country('GB');

            // Save changes
            $order->save();
        } else {
            error_log('No results found for the query.');
        }




        wp_send_json(array(
            'printers' => admin_url('post.php?post=' . $row['orderId'] . '&action=edit&printshippinglabels=yes'),
        ));
    }

    public static function mark_as_paid_unclaim()
    {
        global $wpdb;
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $text = isset($_POST['text']) ? $_POST['text'] : '';
        $ticketNumber = isset($_POST['ticket']) ? $_POST['ticket'] : '';
        $number = intval($ticketNumber);
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $image = "";
        $intant_title = "";
        $comp_name = "";

        if ($id) {


            // Update based on type
            if ($type === 'instant') {

                $result = $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}comp_instant_prizes_tickets  
                         SET is_admin_declare_winner = %d
                         WHERE id = %d",
                        2, // Value to update
                        $id // ID to target
                    )
                );

                $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes_tickets WHERE id = %s", $id);
                $instant_wins = $wpdb->get_results($query, ARRAY_A);


                $query3 = $wpdb->prepare("SELECT image , title AS instant_title  FROM " . $wpdb->prefix . "comp_instant_prizes WHERE id = %s", $instant_wins[0]['instant_id']);
                $instant_wins_details = $wpdb->get_results($query3, ARRAY_A);

                $query2 = $wpdb->prepare("SELECT title FROM " . $wpdb->prefix . "competitions WHERE id = %s", $instant_wins[0]['competition_id']);
                $competition = $wpdb->get_results($query2, ARRAY_A);

                // error_log('++++++++++++instant_wins_details' . print_r($instant_wins_details, true));
                // error_log('++++++++++++competition' . print_r($competition, true));

                $image = $instant_wins_details[0]['image'];
                $intant_title = $instant_wins_details[0]['instant_title'];
                $comp_name = $competition[0]['title'];
            }

            if ($type === 'reward') {


                $result = $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}comp_reward_winner  
                         SET is_admin_declare_winner = %d
                         WHERE id = %d",
                        2, // Value to update
                        $id // ID to target
                    )
                );
            }

            if ($type === 'cash') {


                $result = $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}competition_winners  
                         SET is_admin_declare_winner = %d
                         WHERE id = %d",
                        2, // Value to update
                        $id // ID to target
                    )
                );
            }
            $subject = " Well done Legend! Your prize is on the way";

            // Get WooCommerce mailer instance
            $mailer = WC()->mailer();

            // Prepare email variables based on prize type
            $template = 'emails/win-web-order-email.php';

            // Generate email content
            $content = wc_get_template_html(
                $template,
                array(
                    'email_heading' => $subject,
                    'sent_to_admin' => false,
                    'plain_text' => false,
                    'email' => $mailer,
                    'title' => $intant_title,
                    'value' => "Cash Prize",
                    'type' => "Cash Prize",
                    'image' => $image,
                    'comp_title' => $comp_name,
                    'ticket_number' =>  $number,
                    'prize_id' => "Cash Prize",
                    'competition_id' => "Cash Prize",
                    'order' => "Cash Prize",
                )
            );

            // Email headers
            $headers = "Content-Type: text/html\r\n";

            // Send email
            // $user_email = $competition['winner_email']; // Ensure this variable is defined and holds the winner's email
            // Ensure this variable is defined and holds the winner's email        
            $mailer->send($email, $subject, $content, $headers);
        }

        wp_send_json(array(
            'status' => 200,
            'message' => "Data saved Successfully"
        ));
    }




    public static function validate_comp_id()
    {
        global $wpdb;

        $comp_id = isset($_POST['comp_id']) ? $_POST['comp_id'] : 0;

        // error_log('comp id++++++++++++++++++++++++++++++++' . print_r($_POST, true));
        // error_log('comp id post++++++++++++++++++++++++++++++++' . print_r($_POST['comp_id'], true));

        if ($comp_id > 0) {
            $result = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}competitions WHERE id = %d", $comp_id));
            // Return the result to JavaScript
            if ($result > 0) {
                wp_send_json_success(['exists' => true]); // Comp Id exists
            } else {
                wp_send_json_success(['exists' => false]); // Comp Id doesn't exist
            }
        } else {
            wp_send_json_error(); // Invalid Comp Id
        }
    }

    public static function claimed_type()
    {
        global $wpdb;

        $limit = isset($_POST['length']) ? intval($_POST['length']) : 50;
        $offset = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $search = isset($_POST['search_value']) ? sanitize_text_field($_POST['search_value']) : "";

        // Prepare search query part
        $search_query = '';
        if (!empty($search)) {
            $search_query = $wpdb->prepare(
                " AND (ct.ticket_number LIKE %s OR u.display_name LIKE %s OR u.user_email LIKE %s OR c.title LIKE %s)",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }

        // Count queries
        $count_query1 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 1
                    AND ip.type = 'Prize'
                    $search_query
                ";

        $count_query2 = "
                    SELECT COUNT(*) as count 
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 1
                    AND r.type = 'Prize'
                    $search_query
                ";

        // Execute count queries
        $count1 = $wpdb->get_var($wpdb->prepare($count_query1));
        $count2 = $wpdb->get_var($wpdb->prepare($count_query2));

        // Total count
        $totalCount = $count1 + $count2;

        // Data query
        $query = $wpdb->prepare(
            "
                    SELECT ipt.ticket_number,ipt.id, ip.title AS prize_title, ip.type, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id
                    FROM {$wpdb->prefix}comp_instant_prizes_tickets AS ipt
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = ipt.competition_id
                    INNER JOIN {$wpdb->prefix}comp_instant_prizes AS ip ON ip.id = ipt.instant_id
                    INNER JOIN {$wpdb->prefix}users AS u ON ipt.user_id = u.id
                    WHERE ct.ticket_number = ipt.ticket_number
                    AND c.enable_instant_wins = 1
                    AND ct.is_purchased = 1
                    AND ipt.user_id > 0
                    AND ipt.is_admin_declare_winner = 1
                    AND ip.type = 'Prize'
                    $search_query
                    
                    UNION
                    
                    SELECT rw.ticket_number,rw.id, r.title AS prize_title, r.type, c.title AS comp_title, 
                    ct.order_id, u.display_name, u.user_email,u.id AS user_id
                    FROM {$wpdb->prefix}comp_reward_winner AS rw
                    INNER JOIN {$wpdb->prefix}competitions AS c ON c.id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}competition_tickets AS ct ON ct.competition_id = rw.competition_id
                    INNER JOIN {$wpdb->prefix}comp_reward AS r ON r.id = rw.reward_id
                    INNER JOIN {$wpdb->prefix}users AS u ON rw.user_id = u.id
                    WHERE ct.ticket_number = rw.ticket_number
                    AND c.enable_reward_wins = 1
                    AND ct.is_purchased = 1
                    AND rw.user_id > 0
                    AND rw.is_admin_declare_winner = 1
                    AND r.type = 'Prize'
                    $search_query
                    LIMIT %d OFFSET %d
                    ",
            $limit,
            $offset
        );

        // Execute data query
        $prize_results = $wpdb->get_results($query, ARRAY_A);

        // print_r($prize_results);

        $data = [];
        $users = [];

        if (!empty($prize_results)) {
            foreach ($prize_results as $prize_result) {
                if (!isset($users[$prize_result['user_id']])) {
                    $users[$prize_result['user_id']] = get_user_meta($prize_result['user_id']);
                }

                $user_data = $users[$prize_result['user_id']];
                $billing_address = $user_data['billing_address_1']['0'];

                if (!empty($user_data['billing_address_2']['0']))
                    $billing_address .= " " . $user_data['billing_address_2']['0'];
                if (!empty($user_data['billing_city']['0']))
                    $billing_address .= " " . $user_data['billing_city']['0'];
                if (!empty($user_data['billing_state']['0']))
                    $billing_address .= " " . $user_data['billing_state']['0'];
                if (!empty($user_data['billing_postcode']['0']))
                    $billing_address .= " " . $user_data['billing_postcode']['0'];
                if (!empty($user_data['billing_country']['0']))
                    $billing_address .= " " . $user_data['billing_country']['0'];

                $url = admin_url("user-edit.php?user_id=" . $prize_result['user_id']);
                $user_id = '<a class="link_text" href="' . $url . '">' . $prize_result['display_name'] . '</a>';

                $order_id = "";
                if (!empty($prize_result['order_id'])) {
                    $order_url = admin_url('post.php?post=' . $prize_result['order_id'] . '&action=edit');
                    $order_id = '<a class="link_text" href="' . $order_url . '">' . $prize_result['order_id'] . '</a>';
                }

                $data[] = [
                    'id' => '<input type="checkbox" class="user-checkbox" data-id="' . $prize_result['id'] . '" />',
                    'ticket_number' => $prize_result['ticket_number'],
                    'order_id' => $order_id,
                    'title' => $prize_result['prize_title'],
                    'user_name' => $prize_result['display_name'],
                    'user_email' => $prize_result['user_email'],
                    'phone' => $user_data['billing_phone'][0],
                    'user_address' => trim($billing_address),
                    'comp_title' => $prize_result['comp_title']
                ];
            }
        }

        wp_send_json([
            'data' => $data,
            'draw' => intval($_POST['draw']),
            'recordsTotal' => intval($totalCount),
            'recordsFiltered' => intval($totalCount)
        ]);
    }

    public static function format_date_with_ordinal($date_string)
    {
        $date = new DateTime($date_string);

        $day = $date->format('j');
        $month = $date->format('F');
        $year = $date->format('Y');
        $time = $date->format('g:i A');

        if (!in_array(($day % 100), array(11, 12, 13))) {
            switch ($day % 10) {
                case 1:
                    $day_suffix = 'st';
                    break;
                case 2:
                    $day_suffix = 'nd';
                    break;
                case 3:
                    $day_suffix = 'rd';
                    break;
                default:
                    $day_suffix = 'th';
                    break;
            }
        } else {
            $day_suffix = 'th';
        }

        return $day . $day_suffix . ' ' . $month . ' ' . $year . ' ' . $time;
    }

    public static function add_tickets_to_competition()
    {

        global $wpdb;


        $entry = $wpdb->get_row("SELECT * FROM  {$wpdb->prefix}competitions  WHERE id = '" . $_REQUEST['competition_id'] . "'", ARRAY_A);

        $user = get_userdata($_REQUEST['user']);

        $user_meta = get_user_meta($user->ID);


        if ($_REQUEST['ticketsToAdd'] == 0) {


            $pointstoadd = $_REQUEST['pointsToAdd'] * 100;
            WC_Points_Rewards_Manager::increase_points($_REQUEST['user'],  $pointstoadd, 'order-placed-instant-prize', null, null);

            $subject = "You’re an instant winner! - Carp Gear Giveaways";
            $mailer = WC()->mailer();

            $email_data = [
                'title' => 'Over allocation is going to be assigned to the account as points',
                'type' => 'PointsAllocation',
                'comp_title' => $entry['title'],
                'ticket_number' => 'N/A',
                'instant_id' => 'Points',
                'competition_id' => $entry['id'],
                'order_id' => 'Points',

            ];

            $content = get_custom_email_html($mailer, $email_data, $subject);

            $headers = "Content-Type: text/html\r\n";

            $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);

            wp_send_json(
                array(
                    'success' => true,
                    'message' => 'Points added successfully'
                )
            );
        } else {


            $totalPointsTicekts = $_REQUEST['ticketsToAdd'] * 100;
            $totalTicektstoadd = $_REQUEST['ticketsToAdd'];

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
                    'customer_id' => $_REQUEST['user'],
                )
            );

            $order_id = $order->get_id();

            $order->add_product(get_product($entry['competition_product_id']), $_REQUEST['qty']);

            $order->set_address($billing_address, 'billing');

            $order->calculate_totals();

            $order->update_status("wc-admin-assigned", 'Manually added order', TRUE);

            $purchase_date = date("Y-m-d");



            $query = $wpdb->prepare(
                "UPDATE {$wpdb->prefix}competition_tickets AS tickets 
            INNER JOIN ( SELECT id FROM {$wpdb->prefix}competition_tickets WHERE competition_id IN 
             ( SELECT id FROM {$wpdb->prefix}competitions WHERE id = %d ) 
          and is_purchased <> 1 and user_id IS NULL ORDER BY RAND() LIMIT %d ) 
          AS subquery ON tickets.id = subquery.id SET tickets.is_purchased = 1, 
          tickets.user_id = %d, tickets.purchased_on = %s, tickets.order_id = %d",
                $_REQUEST['competition_id'],
                $_REQUEST['ticketsToAdd'],
                $_REQUEST['user'],
                $purchase_date,
                $order_id
            );

            $wpdb->query($query);

            $params = [$_REQUEST['competition_id']];

            $params[] = $order_id;

            $params[] = $_REQUEST['user'];

            $query = $wpdb->prepare(
                "SELECT {$wpdb->prefix}comp_instant_prizes_tickets.*, {$wpdb->prefix}comp_instant_prizes.title, {$wpdb->prefix}competition_tickets.order_id,
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

            // error_log('++++++++++++++++++++++' . print_r($prize_results, true));


            if (!empty($prize_results)) {

                foreach ($prize_results as $p_row) {

                    if ($p_row['type'] == 'Points') {

                        WC_Points_Rewards_Manager::increase_points($_REQUEST['user'], $p_row['value'], 'order-placed-instant-prize', null, $order->id);

                        $subject = "You’re an instant winner! - Carp Gear Giveaways";
                    } else {

                        $subject = "You’re an instant winner! - Carp Gear Giveaways";
                    }

                    $mailSent = 0;

                    $mailer = WC()->mailer();

                    $content = get_custom_email_html($mailer, $p_row, $subject);

                    $headers = "Content-Type: text/html\r\n";

                    $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);

                    $updated_at = gmdate("Y-m-d H:i:s");

                    $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE {$wpdb->prefix}comp_instant_prizes_tickets 
                      SET user_id = %d, mail_sent = %d, updated_at = %s  
                      WHERE id = %d",
                            $_REQUEST['user'],
                            $mailSent,
                            $updated_at,
                            $p_row['id']
                        )
                    );
                }
            }

            if ($totalPointsTicekts > 0) {
                WC_Points_Rewards_Manager::increase_points($_REQUEST['user'],  $totalPointsTicekts, 'order-placed-instant-prize', null, null);

                $subject = "You’re an instant winner! - Carp Gear Giveaways";
                $mailer = WC()->mailer();

                $email_data = [
                    'title' => 'Over allocation is going to be assigned to the account as points',
                    'type' => 'PointsAllocation',
                    'comp_title' => $entry['title'],
                    'ticket_number' => 'N/A',
                    'instant_id' => 'Points',
                    'competition_id' => $entry['id'],
                    'order_id' => $order_id,

                ];

                $content = get_custom_email_html($mailer, $email_data, $subject);

                $headers = "Content-Type: text/html\r\n";

                $mailSent = $mailer->send($user->user_email, $subject, $content, $headers);
            }

            wp_send_json(
                array(
                    'success' => true,
                    'message' => 'Tickets added successfully'
                )
            );
        }
    }

    public static function get_url_content_type($url)
    {

        $headers = get_headers($url, 1);

        if (isset($headers["Content-Type"])) {
            return $headers["Content-Type"];
        }

        return false;
    }

    public static function is_image_or_video($url)
    {
        $content_type = self::get_url_content_type($url);

        if ($content_type) {
            if (strpos($content_type, 'image/') !== false) {
                return 'image';
            } elseif (strpos($content_type, 'video/') !== false) {
                return 'video';
            } else {
                return 'other';
            }
        }

        return 'unknown';
    }

    public static function save_slider_settings()
    {

        global $wpdb;

        if (isset($_REQUEST['total_slides']) && !empty($_REQUEST['total_slides'])) {

            $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}homepage_sliders");

            $total_slides = $_REQUEST['total_slides'];

            for ($i = 1; $i <= $total_slides; $i++) {

                $data = array(
                    'slider_title' => $_REQUEST['slider_title' . $i],
                    'sub_title' => $_REQUEST['sub_title' . $i],
                    'link' => $_REQUEST['link' . $i],
                    'btn_text' => $_REQUEST['btn_text' . $i],
                    'desktop_image' => $_REQUEST['desktop_image' . $i],
                    'mobile_image' => $_REQUEST['mobile_image' . $i],
                );

                $wpdb->insert("{$wpdb->prefix}homepage_sliders", $data);
            }
        }

        $table_name = $wpdb->prefix . 'global_settings';

        $recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);

        $data = [];

        $data['slider_height_desktop'] = $_REQUEST['slider_height_desktop'];
        $data['slider_height_tablet'] = $_REQUEST['slider_height_tablet'];
        $data['slider_height_mobile'] = $_REQUEST['slider_height_mobile'];
        $data['slider_speed'] = $_REQUEST['slider_speed'];

        $format = array('%s', '%s', '%s', '%s', '%d');

        if (empty($recordData)) {

            $wpdb->create($table_name, $data, $format);
        } else {

            $wpdb->update($table_name, $data, ["id" => $recordData['id']]);
        }

        echo json_encode(['success' => true, 'message' => 'Settings saved successfully!']);

        wp_die();
    }

    public static function save_global_question_settings()
    {
        global $wpdb;

        if (isset($_REQUEST['show_question']) && !empty($_REQUEST['show_question'])) {

            $wpdb->update("{$wpdb->prefix}global_settings", ["show_question" => "1"], ["id" => 2]);
        } else {

            $wpdb->update("{$wpdb->prefix}global_settings", ["show_question" => "0"], ["id" => 2]);
        }

        echo json_encode(['success' => true, 'message' => ""]);
    }
}




add_action('admin_head', 'my_custom_fonts'); // admin_head is a hook my_custom_fonts is a function we are adding it to the hook

function my_custom_fonts()
{
    if (isset($_REQUEST['printshippinglabels']) && $_REQUEST['printshippinglabels'] == 'yes') {
        echo '<style>
			#adminmenumain,#wpadminbar,#woocommerce-embedded-root,.wrap .wp-heading-inline+.page-title-action,#screen-meta-links{
				display:none;
			}
			#wpcontent{
				margin-left:0px;
			}
			html.wp-toolbar{
				padding-top:0px;
			}
		</style>';
    }
    if (isset($_REQUEST['saferedirect']) && $_REQUEST['saferedirect'] == 'yes') {
        // echo "<script>
        // 	setTimeout(function() {
        // 		window.close();
        // 	}, 2000);
        // </script>";

        echo '<script type="text/javascript">
        (function() {
            // Fetch data from localStorage
            const customData = localStorage.getItem("competitionData"); // Replace with your key
            if (customData) {
                // Send AJAX request to update the custom table
                jQuery.ajax({
                    url: ajaxurl, // Ensure this is the correct WordPress AJAX endpoint
                    type: "POST",
                    data: {
                        action: "update_custom_table", // Custom action for updating the table
                        custom_data: customData
                    },
                    success: function(response) {
                        if (response.success) {
                            // After success, perform the redirect
                            setTimeout(function() {
                                if (window.opener) {
                                    window.opener.location.reload();
                                }
                                //  if (window.opener && window.opener.reloadAllTables) {
                                //         window.opener.reloadAllTables(); // This will reload all tables in the main window
                                //     }
                                window.close();
                            }, 2000);
                        } else {
                            alert("Failed to update custom table: " + response.message);
                        }
                    },
                    error: function(error) {
                        console.error("AJAX Error:", error);
                    }
                });
            }
        })();
      </script>';
    }
}

add_action('woocommerce_update_order', 'wp_kama_woocommerce_update_order_action', 10, 2);

/**
 * Function for `woocommerce_update_order` action-hook.
 * 
 * @param  $order_id 
 * @param  $order    
 *
 * @return void
 */
function wp_kama_woocommerce_update_order_action($order_id, $order)
{
    $referer = $_SERVER['HTTP_REFERER'];
    $printShippingLabels = null;
    // Parse the URL to get query parameters
    parse_str(parse_url($referer, PHP_URL_QUERY), $queryParams);

    if (isset($queryParams['printshippinglabels']) && $queryParams['printshippinglabels'] == 'yes') {
        if (is_admin()) {
            // Specify the redirect URL

            /* Save Stats */

            add_filter('wp_redirect', function ($redirect_url) use ($order_id) {
                return admin_url('post.php?post=' . $order_id . '&action=edit&printshippinglabels=yes&saferedirect=yes&message=1');
            });
        }
    }
}


// Register AJAX action for logged-in users
add_action('wp_ajax_update_custom_table', 'update_custom_table_function');

// Optional: For non-logged-in users (if needed)
add_action('wp_ajax_nopriv_update_custom_table', 'update_custom_table_function');

/**
 * Function to update the custom table with data from localStorage
 */
function update_custom_table_function()
{
    if (isset($_POST['custom_data'])) {
        global $wpdb;

        // Decode the JSON data into an array (since it's sent as JSON string)
        $custom_data = json_decode(stripslashes($_POST['custom_data']), true); // true converts it to an associative array

        if (json_last_error() === JSON_ERROR_NONE) { // Ensure no decoding errors
            error_log("First Level if user get custom_data " . print_r($custom_data, true));

            // Access the first element of the array
            $data = $custom_data[0]; // Accessing the first element

            // Log individual fields for debugging
            // error_log("Table: " . $data['table']);
            // error_log("ID: " . $data['id']);

            // print_r($custom_data);

            // print_r($data);
            // exit;



            if ($data['table'] == 'instant') {
                // Sanitize individual fields if necessary
                $id = intval($data['mainid']); // Ensure it's an integer
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}comp_instant_prizes_tickets
                         SET is_admin_declare_winner = %d
                         WHERE id = %d",
                        2, // The value for is_admin_declare_winner
                        $id // The ID from the current custom_data
                    )
                );
            } else if ($data['table'] == 'reward') {
                $id = intval($data['mainid']); // Sanitize ID
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}comp_reward_winner
                         SET is_admin_declare_winner = %d
                         WHERE id = %d",
                        2, // The value for is_admin_declare_winner
                        $id // The ID from the current row
                    )
                );
            } else if ($data['table'] == 'cash') {
                $id = intval($data['mainid']); // Sanitize ID
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}competition_winners
                         SET is_admin_declare_winner = %d
                         WHERE id = %d",
                        2, // The value for is_admin_declare_winner
                        $id // The ID from the current row
                    )
                );
            }

            wp_send_json_success(array('message' => 'Custom table updated successfully.'));
        } else {
            wp_send_json_error(array('message' => 'Invalid JSON data.'));
        }
    }

    wp_die(); // Terminate the request
}


function update_payment_status()
{
    global $wpdb;

    $query = $wpdb->prepare(

        "SELECT * 
        FROM {$wpdb->prefix}competition_winners
        Where status ='Pending'"

    );

    $prize_results = $wpdb->get_results($query, ARRAY_A);
}
