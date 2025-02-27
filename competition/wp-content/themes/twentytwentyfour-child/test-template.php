<?php /* Template Name: Test Template */ ?>

<?php
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

                    $subject = "Reward " . $reward_record['title'] . " unlocked! - " . get_bloginfo('name');

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
?>