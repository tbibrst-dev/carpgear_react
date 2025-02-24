<html>

<body>
    <?php

    //exit;
    
    require_once ('wp-load.php');

    // $url = "https://cggprelive.co.uk/competition/index.php/wp-json/wp/v2/winners?_embed&type=winners&status=publish&filter[orderby]=date&order=desc&&filter[posts_per_page]=5";
    
    // $response = wp_remote_get($url);
    
    // if (is_array($response) && !is_wp_error($response)) {
    //     $headers = $response['headers']; // array of http header lines
    //     $body = json_decode($response['body'], true); // use the content
    // }
    
    // echo "<pre>";
    // print_r($body);
    // echo "</pre>";
    // exit;
    
    require_once ("wp-content/plugins/competitions/class.competitions.php");

    global $wpdb;

    //Competitions_Admin::competitionRewardPrizeLevelReachedNotification();
    
    //exit;
    
    /*$body = [
        "input_1_1" => "Team",
        "input_1_2" => "Dev",
        "input_2" => "testing@gmail.com",
        "input_3" => "9080980991"
    ];

    $form_id = 1;
    $input_values = array('form_id' => 1);
    $input_values['1.3'] = 'Team';
    $input_values['1.6'] = 'Dev';
    $input_values['2'] = 'testing@gmail.com';
    $input_values['4'] = '9080980991';
    $input_values['3'] = 'This is a test content';
    //$input_values['gform_save'] = true;
    
    $result = GFAPI::add_entry($input_values, $form_id);
    if (!is_wp_error($result)) {
        $resume_token = rgar($result, 'resume_token');
        $resume_message = rgar($result, 'confirmation_message');
    }
    echo '<pre>';
    print_r($result);
    echo '</pre>';
    exit;

    $response = wp_remote_post(
        'https://cggprelive.co.uk/competition/wp-json/gf/v2/forms/1/submissions',
        array(
            'body' => json_encode($body),
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        )
    );

    //$response = wp_remote_retrieve_body($response);
    
    echo '<pre>';
    print_r($result);
    echo '</pre>';
    exit;


    require_once ("wp-content/plugins/competitions/class.competitions.php");

    global $wpdb;

    Competitions_Admin::competitionSoldOutNotification();

    exit;

    $args = array(
        'role' => 'administrator',
    );
    $users = get_users($args);

    foreach ($users as $user) {
        echo $user->user_email;
    }
    echo '<pre>';
    print_r($users);
    echo '</pre>';
    exit;

    //echo date("Y-m-d");
    
    exit;

    $users = get_users(array('fields' => array('ID')));

    foreach ($users as $user) {

        echo $user->ID;
        if ($user->ID == '1' || $user->ID == '9' || $user->ID == '6' || $user->ID == '5' || $user->ID == '7')
            continue;
        $userMeta = get_user_meta($user->ID);

        if (!isset($userMeta['limit_value']))
            update_user_meta($user->ID, 'limit_value', false);
        if (!isset($userMeta['limit_duration']))
            update_user_meta($user->ID, 'limit_duration', false);
        if (!isset($userMeta['lockout_period']))
            update_user_meta($user->ID, 'lockout_period', false);
        if (!isset($userMeta['current_spending']))
            update_user_meta($user->ID, 'current_spending', false);
        if (!isset($userMeta['lock_account']))
            update_user_meta($user->ID, 'lock_account', false);
        if (!isset($userMeta['locking_period']))
            update_user_meta($user->ID, 'locking_period', false);
    }
    exit;*/

    $mailSent = 0;

    $mailer = WC()->mailer();

    $headers = "Content-Type: text/html\r\n";

    $subject = "Test Email Content";

    function get_custom_email_html1($mailer, $email_heading, $email_data)
    {
        if ($email_data['type'] == 'Points') {
            $template = 'emails/instant-win-points-email.php';
        } else {
            $template = 'emails/instant-win-prize-email.php';
        }

        $template = 'emails/instant-win-prize-email.php';

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
                'image' => $email_data['image'],
                'comp_title' => $email_data['comp_title'],
                'ticket_number' => $email_data['ticket_number']
            )
        );
    }


    $id_placeholders = "10";

    $query = $wpdb->prepare(
        "SELECT {$wpdb->prefix}comp_instant_prizes_tickets.*, {$wpdb->prefix}comp_instant_prizes.title,
    {$wpdb->prefix}comp_instant_prizes.type,{$wpdb->prefix}comp_instant_prizes.value,{$wpdb->prefix}comp_instant_prizes.quantity,
    {$wpdb->prefix}comp_instant_prizes.image, {$wpdb->prefix}competitions.title as comp_title FROM `{$wpdb->prefix}comp_instant_prizes_tickets`
    INNER JOIN {$wpdb->prefix}competitions ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}comp_instant_prizes_tickets.competition_id
    INNER JOIN {$wpdb->prefix}competition_tickets ON ({$wpdb->prefix}competition_tickets.competition_id = {$wpdb->prefix}comp_instant_prizes_tickets.competition_id)
    INNER JOIN {$wpdb->prefix}comp_instant_prizes ON {$wpdb->prefix}comp_instant_prizes.id = {$wpdb->prefix}comp_instant_prizes_tickets.instant_id
    WHERE {$wpdb->prefix}competition_tickets.ticket_number = {$wpdb->prefix}comp_instant_prizes_tickets.ticket_number
    AND {$wpdb->prefix}competitions.enable_instant_wins = 1
    AND {$wpdb->prefix}comp_instant_prizes_tickets.competition_id IN ($id_placeholders)
    AND {$wpdb->prefix}competition_tickets.is_purchased = 1
    AND {$wpdb->prefix}competition_tickets.user_id = 2
    AND {$wpdb->prefix}comp_instant_prizes_tickets.user_id = 2",
        $params
    );

    $prize_results = $wpdb->get_results($query, ARRAY_A);

    if (!empty($prize_results)) {

        $instant_win = true;

        foreach ($prize_results as $p_row) {

            $content = get_custom_email_html1($mailer, $subject, $p_row);

            print_r($content);
            //exit;
    
            $userEmail = "teamdevbrst24@gmail.com";

            echo $mailSent = $mailer->send($userEmail, $subject, $content, $headers);

            $userEmail = "yogita.sharma@brihaspatitech.com";

            echo $mailSent = $mailer->send($userEmail, $subject, $content, $headers);

            exit;

        }
    }
