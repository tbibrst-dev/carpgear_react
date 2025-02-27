<html>

<body>
    <?php

    //exit;
    
    require_once ('wp-load.php');

  
    
    require_once ("wp-content/plugins/competitions/class.competitions.php");

    global $wpdb;

    

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
