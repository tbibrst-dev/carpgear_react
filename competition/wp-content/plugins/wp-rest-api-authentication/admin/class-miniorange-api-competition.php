<?php

class Miniorange_API_Competition
{



    public static function getSoldOutFinishedCompetitions($data)
    {
        global $wpdb;

        // Get the current time in UK timezone (UTC or Europe/London)
        $current_time = gmdate('Y-m-d H:i:s');

        // Combine close date & close time, and draw date & draw time for proper comparisons
        $query = "SELECT comp.*, 
                         COUNT(t.id) AS total_ticket_sold, 
                         CONCAT(comp.closing_date, ' ', comp.closing_time) AS close_datetime, 
                         CONCAT(comp.draw_date, ' ', comp.draw_time) AS draw_datetime 
                  FROM {$wpdb->prefix}competitions comp 
                  LEFT JOIN {$wpdb->prefix}competition_tickets t 
                  ON comp.id = t.competition_id AND t.is_purchased = 1 
                  WHERE comp.is_draft = '0' 
                  GROUP BY comp.id
                  HAVING 
                      (total_ticket_sold >= comp.total_sell_tickets  AND draw_datetime >= '$current_time') 
                      OR (close_datetime <= '$current_time' AND draw_datetime >= '$current_time')";

        // print_r( $query );

        $results = $wpdb->get_results($query);

        $response = array(
            'success' => true,
            'data' => $results
        );

        wp_send_json($response, 200);
    }



    public static function getInstantWinsCompetitions()
    {

        global $wpdb;

        $main_table = $wpdb->prefix . 'competitions';

        $ticket_table = $wpdb->prefix . 'competition_tickets';

        $entry = $wpdb->get_results("SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM $main_table comp 
        LEFT JOIN $ticket_table t ON comp.id = t.competition_id AND t.is_purchased = 1 
        WHERE comp.is_draft = '0' and comp.category = 'instant_win_comps'  and comp.draw_date >= CURDATE() GROUP BY comp.id", ARRAY_A);

        //$response = rest_ensure_response($entry);

        $response = array(
            'success' => 'true',
            'data' => $entry
        );

        wp_send_json($response, 200);

        //echo wp_json_encode( $entry );

        //exit;

    }

    public static function getCompetitions($data)
    {

        global $wpdb;

        $limit = isset($data['limit']) ? absint($data['limit']) : 10;
        $page = isset($data['page']) ? absint($data['page']) : 1;
        $status = isset($data['status']) ? sanitize_text_field($data['status']) : '';
        $category = isset($data['category']) ? sanitize_text_field($data['category']) : '';

        $query = "SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->prefix}competitions comp 
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
        WHERE comp.is_draft = '0' ";

        $where_clause_added = false;


        // Handle the 'finished_and_sold_out' category logic
        if ($status == 'Finished') {
            $query .= "AND (comp.draw_date >= CURDATE() OR comp.draw_date <= CURDATE() AND comp.draw_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)) ";
            $where_clause_added = true;
        } else {
            $query .= "AND comp.draw_date >= CURDATE() ";
            $where_clause_added = true;
        }

        if (!empty($status)) {
            $query .= "AND comp.status = %s ";
            $where_clause_added = true;
        }

        if (!empty($category)) {
            $query .= "AND comp.category = %s ";
            $where_clause_added = true;
        }

        $query .= "GROUP BY comp.id ";

        if (isset($data['order_by'])) {

            $order = isset($data['order']) ? $data['order'] : 'DESC';

            $query .= "ORDER BY comp." . $data['order_by'] . " " . $order;
        }

        $offset = ($page - 1) * $limit;

        $query .= " LIMIT %d, %d";

        $prepared_query_args = array();

        if (!empty($status)) {
            $prepared_query_args[] = $status;
        }

        if (!empty($category)) {
            $prepared_query_args[] = $category;
        }

        $prepared_query_args[] = $offset;
        $prepared_query_args[] = $limit;

        $prepared_query = $wpdb->prepare($query, $prepared_query_args);

        $results = $wpdb->get_results($prepared_query, ARRAY_A);

        if (!empty($results)) {

            $response = array(
                'success' => 'true',
                'data' => $results
            );
        } else {

            $response = array(
                'success' => false,
                'data' => [],
                'message' => 'No Data Found'
            );
        }

        wp_send_json($response, 200);
    }




    // public static function getDrawnNextCompetitions($data)
    // {

    //     global $wpdb;

    //     $category = ''; //isset($data['category']) ? sanitize_text_field($data['category']) : '';

    //     // $limit = isset($data['limit']) ? absint($data['limit']) : 10;
    //     $limit = 15;

    //     $page = isset($data['page']) ? absint($data['page']) : 1;

    //     $status = 'Open';

    //     //$query = "SELECT * FROM {$wpdb->prefix}competitions WHERE is_draft = '0' and draw_date > %s ";

    //     $query = "SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->prefix}competitions comp 
    //     LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
    //     WHERE comp.is_draft = '0' and comp.draw_date > %s ";


    //     if (!empty($status)) {
    //         $query .= "AND comp.status = %s ";
    //     }

    //     if (!empty($category)) {
    //         $query .= "AND comp.category = %s ";
    //     }

    //     $query .= "GROUP BY comp.id ";

    //     if (isset($data['order_by'])) {

    //         $order = isset($data['order']) ? $data['order'] : 'DESC';

    //         $query .= "ORDER BY comp." . $data['order_by'] . " " . $order;
    //     }

    //     $offset = ($page - 1) * $limit;

    //     $query .= " LIMIT %d, %d";

    //     $today = date("Y-m-d");

    //     $prepared_query_args = array($today);

    //     if (!empty($status)) {
    //         $prepared_query_args[] = $status;
    //     }

    //     if (!empty($category)) {
    //         $prepared_query_args[] = $category;
    //     }

    //     $prepared_query_args[] = $offset;
    //     $prepared_query_args[] = $limit;



    //     $prepared_query = $wpdb->prepare($query, $prepared_query_args);

    //     error_log("Draw next query++" . print_r($prepared_query, true));

    //     $results = $wpdb->get_results($prepared_query, ARRAY_A);

    //     $response = array(
    //         'success' => 'true',
    //         'data' => $results
    //     );

    //     wp_send_json($response, 200);
    // }
    public static function getDrawnNextCompetitions($data)
    {
        global $wpdb;
    
        $category = ''; // isset($data['category']) ? sanitize_text_field($data['category']) : '';
        $limit = 15;
        $status = 'Open';
        $page = isset($data['page']) ? absint($data['page']) : 1;
    
        // Get current UK time
        $currentTimeUK = new DateTime('now', new DateTimeZone('Europe/London'));
        $currentTimeFormatted = $currentTimeUK->format('Y-m-d H:i:s');
    
        // 48-hour cutoff time
        $cutoffTimeUK = clone $currentTimeUK;
        $cutoffTimeUK->modify('+48 hours');
        $cutoffTimeFormatted = $cutoffTimeUK->format('Y-m-d H:i:s');
    
        // Query to fetch competitions within 48 hours
        $query = "SELECT comp.*, COUNT(t.id) AS total_ticket_sold 
            FROM {$wpdb->prefix}competitions comp
            LEFT JOIN {$wpdb->prefix}competition_tickets t 
            ON comp.id = t.competition_id AND t.is_purchased = 1
            WHERE comp.is_draft = '0' 
            AND TIMESTAMP(comp.draw_date, comp.draw_time) 
                BETWEEN %s AND %s ";
    
        if (!empty($status)) {
            $query .= "AND comp.status = %s ";
        }
    
        if (!empty($category)) {
            $query .= "AND comp.category = %s ";
        }
    
        $query .= "GROUP BY comp.id ";
    
        if (isset($data['order_by'])) {
            $order = isset($data['order']) ? esc_sql($data['order']) : 'DESC';
            $query .= "ORDER BY comp." . esc_sql($data['order_by']) . " " . $order . " ";
        }
    
        $offset = ($page - 1) * $limit;
        $query .= "LIMIT %d OFFSET %d";
    
        // Prepare query parameters
        $prepared_query_args = [$currentTimeFormatted, $cutoffTimeFormatted];
    
        if (!empty($status)) {
            $prepared_query_args[] = $status;
        }
    
        if (!empty($category)) {
            $prepared_query_args[] = $category;
        }
    
        $prepared_query_args[] = $limit;
        $prepared_query_args[] = $offset;
    
        $prepared_query = $wpdb->prepare($query, $prepared_query_args);
    
        error_log("Draw next query++" . print_r($prepared_query, true));
    
        $results = $wpdb->get_results($prepared_query, ARRAY_A);
    
        $response = [
            'success' => true,
            'data' => !empty($results) ? $results : []
        ];
    
        wp_send_json($response, 200);
    }
    


    public static function getFeaturedCompetitions($data)
    {

        global $wpdb;

        $limit = isset($data['limit']) ? absint($data['limit']) : 10;

        //$query = "SELECT * FROM {$wpdb->prefix}competitions WHERE is_draft = '0' and is_featured = 1 ";

        $query = "SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->prefix}competitions comp 
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
        WHERE comp.is_draft = '0' and comp.is_featured = 1 and comp.status = 'Open' GROUP BY comp.id ";

        if (isset($data['order_by'])) {

            $order = isset($data['order']) ? $data['order'] : 'DESC';

            $query .= "ORDER BY comp." . $data['order_by'] . " " . $order;
        }

        $query .= " LIMIT %d";

        $prepared_query_args = [$limit];

        $prepared_query = $wpdb->prepare($query, $prepared_query_args);

        $results = $wpdb->get_results($prepared_query, ARRAY_A);

        $response = array(
            'success' => 'true',
            'data' => $results
        );

        wp_send_json($response, 200);
    }

    public static function getCompetitionDetail($data)
    {

        global $wpdb;

        $query = "SELECT * FROM {$wpdb->prefix}competitions WHERE id = %d";

        $prepared_query_args = [$data['id']];

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
        if (!empty($result['slider_sorting']))
            $result['slider_sorting'] = stripslashes($result['slider_sorting']);

        $comp_tickets_purchased = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT count(*) as total_tickets FROM {$wpdb->prefix}competition_tickets WHERE competition_id = %s and is_purchased = 1",
                $data['id']
            )
        );

        $result['total_ticket_sold'] = $comp_tickets_purchased;

        $result['competition_sold_prcnt'] = ($comp_tickets_purchased / $result['total_sell_tickets']) * 100;

        $query = $wpdb->prepare("SELECT reward.*, CASE
        WHEN reward.user_id IS NOT NULL THEN u.display_name ELSE NULL END AS full_name  
        FROM " . $wpdb->prefix . "comp_reward reward 
        LEFT JOIN " . $wpdb->prefix . "users u ON reward.user_id = u.id WHERE competition_id = %s", $data['id']);

        $reward_wins = $wpdb->get_results($query, ARRAY_A);

        if (!empty($reward_wins)) {

            foreach ($reward_wins as $reward_index => $reward_win) {

                $reward_win['prcnt_available'] = round($reward_win['prcnt_available']);

                $reward_wins[$reward_index]['prcnt_available'] = $reward_win['prcnt_available'];

                $reward_wins[$reward_index]['reward_open'] = ($reward_win['prcnt_available'] <= $result['competition_sold_prcnt']) ? true : false;
            }
        }

        $result['reward_wins'] = $reward_wins;

        $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes WHERE competition_id = %s", $data['id']);

        $instant_wins = $wpdb->get_results($query, ARRAY_A);

        $result['instant_wins'] = [];
        foreach ($instant_wins as $win) {
            // Decode the HTML for the competition rules
            $win['prize_description'] = self::decode_html($win['prize_description']);

            // Optionally, you can add any additional processing here

            // Add the processed win to the result array
            $result['instant_wins'][] = $win;
        }


        // $result['instant_wins'] = $instant_wins;


        $query = $wpdb->prepare("SELECT instant.*, CASE
        WHEN instant.user_id IS NOT NULL THEN u.display_name ELSE NULL END AS full_name  
        FROM " . $wpdb->prefix . "comp_instant_prizes_tickets instant 
        LEFT JOIN " . $wpdb->prefix . "users u ON instant.user_id = u.id 
        WHERE competition_id = %s", $data['id']);

        $instant_wins_tickets = $wpdb->get_results($query, ARRAY_A);

        if (!empty($instant_wins_tickets) && is_array($instant_wins_tickets)) {
            // Sort the array by 'ticket_number' in ascending order
            usort($instant_wins_tickets, function ($a, $b) {
                return (int)$a['ticket_number'] - (int)$b['ticket_number'];
            });

            $result['instant_wins_tickets'] = $instant_wins_tickets;
        } else {
            // Log error if data retrieval failed or is not in the expected format
            error_log('Error: instant_wins_tickets data is empty or not an array.');
            $result['instant_wins_tickets'] = [];
        }


        $response = array(
            'success' => 'true',
            'data' => $result
        );

        wp_send_json($response, 200);
    }

    public static function decode_html($content)
    {

        return html_entity_decode(stripslashes($content), ENT_QUOTES, 'UTF-8');
    }

    public static function getGlobalSettings($data)
    {

        global $wpdb;

        $query = "SELECT * FROM {$wpdb->prefix}global_settings";

        $recordData = $wpdb->get_row($query, ARRAY_A);

        if (!empty($recordData['live_draw_info']))
            $recordData['live_draw_info'] = html_entity_decode(stripslashes($recordData['live_draw_info']), ENT_QUOTES, 'UTF-8');
        if (!empty($recordData['work_step_1']))
            $recordData['work_step_1'] = html_entity_decode(stripslashes($recordData['work_step_1']), ENT_QUOTES, 'UTF-8');
        if (!empty($recordData['work_step_2']))
            $recordData['work_step_2'] = html_entity_decode(stripslashes($recordData['work_step_2']), ENT_QUOTES, 'UTF-8');
        if (!empty($recordData['work_step_3']))
            $recordData['work_step_3'] = html_entity_decode(stripslashes($recordData['work_step_3']), ENT_QUOTES, 'UTF-8');

        if (!empty($recordData['reward_prize_info']))
            $recordData['reward_prize_info'] = html_entity_decode(stripslashes($recordData['reward_prize_info']), ENT_QUOTES, 'UTF-8');
        if (!empty($recordData['instant_wins_info']))
            $recordData['instant_wins_info'] = html_entity_decode(stripslashes($recordData['instant_wins_info']), ENT_QUOTES, 'UTF-8');
        if (!empty($recordData['main_competition']))
            $recordData['main_competition'] = html_entity_decode(stripslashes($recordData['main_competition']), ENT_QUOTES, 'UTF-8');
        if (!empty($recordData['postal_entry_info']))
            $recordData['postal_entry_info'] = html_entity_decode(stripslashes($recordData['postal_entry_info']), ENT_QUOTES, 'UTF-8');

        if (!empty($recordData['announcement']))
            $recordData['announcement'] = html_entity_decode(stripslashes($recordData['announcement']), ENT_QUOTES, 'UTF-8');

        if (!empty($recordData['frontend_scripts']))
            $recordData['frontend_scripts'] = html_entity_decode(stripslashes($recordData['frontend_scripts']), ENT_QUOTES, 'UTF-8');

        $response = array(
            'success' => 'true',
            'data' => $recordData
        );

        wp_send_json($response, 200);
    }

    public static function getSEOPageSettings($data)
    {

        global $wpdb;

        $page = isset($data['page']) ? sanitize_text_field($data['page']) : '';

        $query = "select * from " . $wpdb->prefix . 'seo_settings';

        if (!empty($page)) {
            $query .= " WHERE page = %s ";
        }

        $prepared_query_args = array();

        if (!empty($page)) {
            $prepared_query_args[] = $page;
        }

        $prepared_query = $wpdb->prepare($query, $prepared_query_args);

        $results = $wpdb->get_results($prepared_query, ARRAY_A);

        $response = array(
            'success' => 'true',
            'data' => $results
        );

        wp_send_json($response, 200);
    }

    public static function getOtherCompetitionsBackup($data)
    {

        global $wpdb;

        $category = isset($data['category']) ? sanitize_text_field($data['category']) : '';

        $limit = isset($data['limit']) ? absint($data['limit']) : 10;

        $page = isset($data['page']) ? absint($data['page']) : 1;

        $current_comp = isset($data['id']) ? absint($data['id']) : '';

        $status = 'Open';

        //$query = "SELECT * FROM {$wpdb->prefix}competitions WHERE is_draft = '0' and draw_date > %s ";

        $query = "SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->prefix}competitions comp 
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
        WHERE comp.is_draft = '0' ";


        if (!empty($status)) {
            $query .= "AND comp.status = %s ";
        }

        if (!empty($category)) {
            $query .= "AND comp.category = %s ";
        }

        if (!empty($current_comp)) {
            $query .= "AND comp.id <> %d ";
        }

        $query .= "GROUP BY comp.id ";

        if (isset($data['order_by'])) {

            $order = isset($data['order']) ? $data['order'] : 'DESC';

            $query .= "ORDER BY comp." . $data['order_by'] . " " . $order;
        }

        $offset = ($page - 1) * $limit;

        $query .= " LIMIT %d, %d";

        $prepared_query_args = array();

        if (!empty($status)) {
            $prepared_query_args[] = $status;
        }

        if (!empty($category)) {
            $prepared_query_args[] = $category;
        }

        if (!empty($current_comp)) {
            $prepared_query_args[] = $current_comp;
        }

        $prepared_query_args[] = $offset;
        $prepared_query_args[] = $limit;

        $prepared_query = $wpdb->prepare($query, $prepared_query_args);

        $results = $wpdb->get_results($prepared_query, ARRAY_A);

        $response = array(
            'success' => 'true',
            'data' => $results
        );

        wp_send_json($response, 200);
    }

    public static function getOtherCompetitions($data)
    {

        global $wpdb;

        $category = isset($data['category']) ? sanitize_text_field($data['category']) : '';

        $limit = isset($data['limit']) ? absint($data['limit']) : 10;

        $page = isset($data['page']) ? absint($data['page']) : 1;

        $current_comp = isset($data['id']) ? absint($data['id']) : '';

        $categories = isset($data['categories']) ? sanitize_text_field($data['categories']) : '';

        $current_comps = isset($data['ids']) ? sanitize_text_field($data['ids']) : '';

        $status = 'Open';

        //$query = "SELECT * FROM {$wpdb->prefix}competitions WHERE is_draft = '0' and draw_date > %s ";

        $query = "SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->prefix}competitions comp 
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
        WHERE comp.is_draft = '0' AND comp.draw_date >= CURDATE() ";

        if (!empty($status)) {
            $query .= "AND comp.status = %s ";
        }

        // if (!empty($category)) {
        //     $query .= "AND comp.category = %s ";
        // }

        if (!empty($current_comp)) {
            $query .= "AND comp.id <> %d ";
        }

        // if (!empty($categories)) {
        //     $categories_array = explode(',', $categories);
        //     $placeholders = implode(', ', array_fill(0, count($categories_array), '%s'));
        //     $query .= "AND comp.category IN ($placeholders) ";
        // }

        if (!empty($current_comps)) {
            $comps_array = explode(',', $current_comps);
            $id_placeholders = implode(', ', array_fill(0, count($comps_array), '%s'));
            $query .= "AND comp.id NOT IN ($id_placeholders) ";
        }

        $query .= "GROUP BY comp.id ";

        if (isset($data['order_by'])) {

            $order = isset($data['order']) ? $data['order'] : 'DESC';

            $query .= "ORDER BY comp." . $data['order_by'] . " " . $order;
        }

        $offset = ($page - 1) * $limit;

        $query .= " LIMIT %d, %d";

        $prepared_query_args = array();

        if (!empty($status)) {
            $prepared_query_args[] = $status;
        }

        if (!empty($category)) {
            $prepared_query_args[] = $category;
        }

        if (!empty($current_comp)) {
            $prepared_query_args[] = $current_comp;
        }

        if (!empty($categories)) {
            $prepared_query_args = array_merge($prepared_query_args, explode(',', $categories));
        }

        if (!empty($current_comps)) {
            $prepared_query_args = array_merge($prepared_query_args, explode(',', $current_comps));
        }

        $prepared_query_args[] = $offset;
        $prepared_query_args[] = $limit;

        $prepared_query = $wpdb->prepare($query, $prepared_query_args);

        $results = $wpdb->get_results($prepared_query, ARRAY_A);

        $response = array(
            'success' => 'true',
            'data' => $results
        );

        wp_send_json($response, 200);
    }

    public static function subscribeMailing($data)
    {

        global $wpdb;

        $email = isset($data['email']) ? sanitize_text_field($data['email']) : '';

        if (!empty($email)) {

            $query = "SELECT * FROM {$wpdb->prefix}subscribe_mailing WHERE email = %s";

            $prepared_query_args = [$email];

            $prepared_query = $wpdb->prepare($query, $prepared_query_args);

            $entry = $wpdb->get_row($prepared_query, ARRAY_A);

            if (!empty($entry)) {

                $response = array(
                    'success' => 'false',
                    'message' => 'Already subscribed. Please use different email.'
                );

                wp_send_json($response, 200);
            } else {

                $table_name = $wpdb->prefix . 'subscribe_mailing';

                $name = isset($data['name']) ? sanitize_text_field($data['name']) : '';

                $table_data = array(
                    'email' => $data['email']
                );

                if (!empty($name)) {
                    $table_data['name'] = $data['name'];
                }

                $wpdb->insert($table_name, $table_data);

                $response = array(
                    'success' => 'true',
                    'message' => 'Subscribed successfully'
                );

                wp_send_json($response, 200);
            }
        }

        $response = array(
            'success' => 'false',
            'message' => 'Email can not be empty'
        );

        wp_send_json($response, 400);
    }

    public static function checkUserCompetitionInstantPrizes($data)
    {

        global $wpdb;

        $token = $data['token'];

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $user = $wpdb->get_row($query, ARRAY_A);

        $competitions = $data['competitions'];
        error_log("competitions Data " . print_r($competitions, true));


        $instant_wins = $data['instant_wins'];
        error_log("instant_wins Data " . print_r($instant_wins, true));


        $id_placeholders = implode(', ', array_fill(0, count(explode(",", $competitions)), '%s'));

        $instant_win_ids = implode(', ', array_fill(0, count(explode(",", $instant_wins)), '%s'));

        $params = explode(",", $competitions);

        $params[] = $user['ID'];

        $params = array_merge($params, explode(",", $instant_wins));

        $query = $wpdb->prepare(
            "SELECT {$wpdb->prefix}comp_instant_prizes_tickets.ticket_number, {$wpdb->prefix}comp_instant_prizes_tickets.id,{$wpdb->prefix}comp_instant_prizes_tickets.user_id,
        {$wpdb->prefix}comp_instant_prizes.title,  {$wpdb->prefix}comp_instant_prizes_tickets.instant_id,
        {$wpdb->prefix}comp_instant_prizes.type, {$wpdb->prefix}comp_instant_prizes.value, 
        {$wpdb->prefix}comp_instant_prizes.quantity, {$wpdb->prefix}comp_instant_prizes.image,
        {$wpdb->prefix}competitions.title as competition_name, {$wpdb->prefix}competitions.id as competition_id FROM `{$wpdb->prefix}comp_instant_prizes_tickets`
            INNER JOIN {$wpdb->prefix}competitions ON {$wpdb->prefix}competitions.id = {$wpdb->prefix}comp_instant_prizes_tickets.competition_id
            INNER JOIN {$wpdb->prefix}competition_tickets ON ({$wpdb->prefix}competition_tickets.competition_id = {$wpdb->prefix}comp_instant_prizes_tickets.competition_id)
            INNER JOIN {$wpdb->prefix}comp_instant_prizes ON {$wpdb->prefix}comp_instant_prizes.id = {$wpdb->prefix}comp_instant_prizes_tickets.instant_id
            WHERE {$wpdb->prefix}competition_tickets.ticket_number = {$wpdb->prefix}comp_instant_prizes_tickets.ticket_number
            AND {$wpdb->prefix}competitions.enable_instant_wins = 1
            -- AND {$wpdb->prefix}comp_instant_prizes_tickets.competition_id IN ($id_placeholders)
            AND {$wpdb->prefix}competition_tickets.is_purchased = 1
            AND {$wpdb->prefix}competition_tickets.user_id = %d
            AND {$wpdb->prefix}comp_instant_prizes_tickets.id IN ($instant_win_ids)",
            $params
        );
        error_log("results results Data " . print_r($query, true));

        $results = $wpdb->get_results($query, ARRAY_A);
        error_log("results Data " . print_r($results, true));

        if (!empty($results)) {

            $response = array(
                'success' => 'true',
                'data' => $results,
                'won_instant' => true
            );
        } else {

            $response = array(
                'success' => 'true',
                'data' => [],
                'won_instant' => false
            );
        }

        wp_send_json($response, 200);
    }

    public static function getUserPurchasedCompetitions($data)
    {

        global $wpdb;

        $token = $data['token'];

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $user = $wpdb->get_row($query, ARRAY_A);

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT count(*) as total_tickets,competition_id  FROM {$wpdb->prefix}competition_tickets 
                WHERE user_id = %d and is_purchased = 1 GROUP BY competition_id",
                $user['ID']
            ),
            ARRAY_A
        );

        if (!empty($results)) {

            $response = array(
                'success' => true,
                'data' => $results
            );
        } else {

            $response = array(
                'success' => false,
                'data' => []
            );
        }

        wp_send_json($response, 200);
    }

    public static function getUserCompetitions($data)
    {


        global $wpdb;

        $token = $data['token'];

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $user = $wpdb->get_row($query, ARRAY_A);



        if (empty($user)) {
            wp_send_json(['success' => false, 'error' => 'Invalid Token'], 401);
        }

        $draw_date = isset($data['draw_date']) ? sanitize_text_field($data['draw_date']) : '';

        $limit = isset($data['limit']) ? absint($data['limit']) : 10;

        $page = isset($data['page']) ? absint($data['page']) : 1;

        $status = isset($data['status']) ? sanitize_text_field($data['status']) : '';

        $type = isset($data['type']) ? sanitize_text_field($data['type']) : 'upcoming';




        $query = "SELECT {$wpdb->prefix}competitions.*, {$wpdb->prefix}competition_tickets.ticket_number, 
        {$wpdb->prefix}competition_tickets.order_id FROM {$wpdb->prefix}competitions
        inner join {$wpdb->prefix}competition_tickets on {$wpdb->prefix}competition_tickets.competition_id = {$wpdb->prefix}competitions.id 
        WHERE {$wpdb->prefix}competition_tickets.user_id = %d AND {$wpdb->prefix}competition_tickets.is_purchased = 1 
        AND {$wpdb->prefix}competitions.is_draft = '0'";

        if (!empty($status)) {
            $query .= "AND {$wpdb->prefix}competitions.status = %s ";
        }




        if (!empty($draw_date)) {

            if ($type == 'upcoming') {
                $query .= "AND {$wpdb->prefix}competitions.draw_date >= %s ";
            } elseif ($type == 'drawn') {
                $query .= "AND {$wpdb->prefix}competitions.draw_date < %s ";
            }
        }

        $query .= " ORDER BY {$wpdb->prefix}competitions.draw_date";

        $prepared_query_args = array($user['ID']);

        if (!empty($status)) {
            $prepared_query_args[] = $status;
        }

        if (!empty($draw_date)) {
            $prepared_query_args[] = $draw_date;
        }

        $prepared_query = $wpdb->prepare($query, $prepared_query_args);

        $results = $wpdb->get_results($prepared_query, ARRAY_A);




        if (!empty($results)) {

            // $allComps = [];

            // foreach ($results as $competition) {

            //     $order = $competition['order_id'];

            //     $order = wc_get_order($competition['order_id']);

            //     if ($order->get_status() == 'cancelled')
            //         continue;

            //     if (!isset($allComps[$competition['id']])) {
            //         $allComps[$competition['id']] = $competition;
            //         $allComps[$competition['id']]['tickets'] = [];
            //     }

            //     $allComps[$competition['id']]['order_status'] = $order->get_status();

            //     if ($order->has_status('completed')) {
            //         $allComps[$competition['id']]['tickets'][] = $competition['ticket_number'];
            //     }
            // }
            $allComps = [];

            foreach ($results as $competition) {
                $orderId = $competition['order_id'];
                $order = wc_get_order($orderId);

                // Log error if order is not found
                if (!$order) {
                    error_log('Order not found for ID: ' . $orderId);
                    continue;
                }

                // Skip if the order is cancelled
                if ($order->get_status() == 'cancelled') {
                    continue;
                }

                // Initialize competition entry if not set
                if (!isset($allComps[$competition['id']])) {
                    $allComps[$competition['id']] = $competition;
                    $allComps[$competition['id']]['tickets'] = [];
                }

                // Update order status
                $allComps[$competition['id']]['order_status'] = $order->get_status();
                $allComps[$competition['id']]['transaction'] = $order->get_transaction_id();
                $allComps[$competition['id']]['get_payment_method'] =  $order->get_payment_method();

                // Add ticket number if the order is completed
                // if ($order->has_status('completed') || $order->get_transaction_id() || $order->get_payment_method() != 'code' ) {
                //     $allComps[$competition['id']]['tickets'][] = $competition['ticket_number'];
                // }


                if ($order->get_payment_method() != 'code' ||  !$order->has_status('failed') || $order->has_status('wc-admin-comp-win')) {
                    $allComps[$competition['id']]['tickets'][] = $competition['ticket_number'];
                }
            }


            if ($type == 'won') {
                global $wpdb;

                $userComps = array_keys($allComps);

                $competitionIDs = implode(",", array_fill(0, count($userComps), '%d'));

                $query = $wpdb->prepare(
                    "SELECT
                        rw.user_id,
                        rw.ticket_id,
                        rw.competition_id,
                        rw.ticket_number,
                        'reward' AS win_type,
                        rw.reward_id AS win_id,
                        reward_info.title as prize,
                        reward_info.type as prize_type,
                        reward_info.image as prize_image,
                        reward_info.value as cash_value,
                        rw.is_admin_declare_winner as prize_claim,
                        rw.edited_title_reward as edited_title

                    FROM
                        `wp_comp_reward_winner` rw
                        INNER JOIN wp_comp_reward reward_info ON (reward_info.id = rw.reward_id AND rw.competition_id = reward_info.competition_id)
                    WHERE
                        rw.user_id = %d AND rw.competition_id IN ($competitionIDs)
                    
                    UNION

                    SELECT
                        wc.user_id,
                        wc.ticket_number AS ticket_id,
                        wc.competition_id,
                        wc.ticket_number,
                        'main' AS win_type,
                        wc.id AS win_id,
                        wp_competitions.title as prize,
                        wc.prize_type,
                        wp_competitions.image as prize_image,
                        wp_competitions.cash as cash_value,
                        wc.is_admin_declare_winner as prize_claim,
                        wc.edited_title as edited_title

                    FROM
                        wp_competition_winners wc
                        INNER JOIN wp_competitions ON (wc.competition_id = wp_competitions.id)
                    WHERE
                        wc.user_id = %d AND wc.competition_id IN ($competitionIDs)

                    UNION    
    
                    SELECT
                        iw.user_id,
                        iw.ticket_number AS ticket_id,
                        iw.competition_id,
                        iw.ticket_number,
                        'instant' AS win_type,
                        iw.instant_id AS win_id,
                        instant_info.title as prize,
                        instant_info.type as prize_type,
                        instant_info.image as prize_image,
                        instant_info.value as cash_value,
                        iw.is_admin_declare_winner as prize_claim,
                        iw.edited_title_instant as edited_title

                    FROM
                        wp_comp_instant_prizes_tickets iw
                        INNER JOIN wp_comp_instant_prizes instant_info ON (instant_info.id = iw.instant_id AND iw.competition_id = instant_info.competition_id)
                    WHERE
                        iw.user_id = %d AND iw.competition_id IN ($competitionIDs)",
                    array_merge([$user['ID']], $userComps, [$user['ID']], $userComps, [$user['ID']], $userComps)
                );

                // echo"<pre>";
                // print_r($query);
                // echo"</pre>";


                $w_results = $wpdb->get_results($query, ARRAY_A);

                // wp_send_json($w_results, 200);

                // die();


                $wonComps = [];

                if (!empty($w_results)) {
                    foreach ($w_results as $w_result) {

                        // if ($allComps[$w_result['competition_id']]['order_status'] == 'completed') {

                        if (!isset($wonComps[$w_result['competition_id']]))
                            $wonComps[$w_result['competition_id']] = $allComps[$w_result['competition_id']];


                        $wonComps[$w_result['competition_id']]['won'][] = $w_result;
                        // }
                    }
                }

                $allComps = $wonComps;
            }



            $allComps = array_values($allComps);

            $response = array(
                'success' => true,
                'data' => $allComps,

            );
        } else {

            $response = array(
                'success' => false,
                'data' => []
            );
        }

        wp_send_json($response, 200);
    }

    public static function getUserInfoByToken($data)
    {

        global $wpdb;

        $token = $data['token'];

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $user = $wpdb->get_row($query, ARRAY_A);

        error_log('checklogindata' . print_r($user, true));


        $allowedValues = [
            'nickname',
            'first_name',
            'last_name',
            'description',
            'billing_first_name',
            'billing_last_name',
            'billing_address_1',
            'billing_city',
            'billing_state',
            'billing_postcode',
            'billing_country',
            'billing_email',
            'billing_company',
            'billing_address_2',
            'billing_phone',
            'comchatid',
            'account_number',
            'sort_code'
        ];

        $comchatid = "";

        // Check if comchatid is empty
        if (empty($user['comchatid'])) {
            // Generate a new CometChat user ID (username + timestamp)
            $comchatid = $user['user_login'] . '_' . time();
            // Sanitize $comchatid to remove special characters
            $comchatid = preg_replace('/[^a-zA-Z0-9_]/', '', $comchatid);

            // Optionally, ensure the resulting string is not empty
            if (empty($comchatid)) {
                $comchatid = 'default_' . time();
            }

            // Create a new CometChat user using WordPress HTTP API (wp_remote_post)
            $cometchat_response = wp_remote_post('https://263121674dc8214a.api-EU.cometchat.io/v3/users', array(
                'body'    => wp_json_encode(array(
                    'uid'      => $comchatid, // Use the generated CometChat ID
                    'name'     => $user['display_name'], // Display name from WordPress
                    'metadata' => array(
                        '@private' => array(
                            'email' => $user['user_email'] // Email from WordPress
                        )
                    )
                )),
                'headers' => array(
                    'accept'        => 'application/json',
                    'apikey'        => '0667e4d462fcb9414846d323ef572bbeea036392', // Replace with your CometChat API Key
                    'content-type'  => 'application/json',
                ),
                'timeout' => 15,
            ));


            // Check for errors in the response
            if (is_wp_error($cometchat_response)) {

                wp_send_json_error(['message' => 'Error creating CometChat user: ' . $cometchat_response->get_error_message()], 500);
                return;
            }

            // If successful, update the user's comchatid in the WordPress database
            $response_code = wp_remote_retrieve_response_code($cometchat_response);
            if ($response_code == 200 || $response_code == 201) {
                // Update the comchatid in the database
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->users} SET comchatid = %s WHERE ID = %d",
                        $comchatid,
                        $user['ID']
                    )
                );
            }
            // } else {
            //     // Handle failed response from CometChat
            //     wp_send_json_error(['message' => 'Failed to create CometChat user.'], 500);
            //     return;
            // }
        } else {
            // If comchatid exists, use it
            $comchatid = $user['comchatid'];
        }


        $group_guid = 'cometchat-guid-1';
        $cometchat_url = "https://263121674dc8214a.api-eu.cometchat.io/v3/groups/{$group_guid}/members";

        // Prepare the payload
        $payload = array(
            'participants' => array($comchatid),
        );

        error_log('payload for adding in group' . print_r($payload, true));

        // Send API request
        $response = wp_remote_post($cometchat_url, array(
            'body'    => wp_json_encode($payload),
            'headers' => array(
                'accept'        => 'application/json',
                'apikey'        => '0667e4d462fcb9414846d323ef572bbeea036392', // Replace with your CometChat API Key
                'content-type'  => 'application/json',
            ),
            'timeout' => 15, // Timeout in seconds
        ));

        $userMeta = get_user_meta($user['ID']);

        $user_id = $user['ID'];
        $meta_key = 'wp_user_level';
        $meta_value = 0;

        // Check if the meta key exists
        $existing_meta = get_user_meta($user_id, $meta_key, true);
        if ($existing_meta == '') { // Returns an empty string if meta does not exist
            // Meta key does not exist, so add it
            add_user_meta($user_id, $meta_key, $meta_value, true);
        }

        $userdata = [];

        foreach ($userMeta as $metaField => $meta_value) {

            if (in_array($metaField, $allowedValues)) {
                $userdata[$metaField] = $meta_value[0];
            }
        }

        $userdata['email'] = $user['user_email'];
        $userdata['name'] = $user['display_name'];
        $userdata['account_number'] = $user['account_number'];
        $userdata['sort_code'] = $user['sort_code'];
        $userdata['token'] = $token;
        $userdata['comchatid'] = $comchatid ? $comchatid : "cometchat-uid-3";


        $userdata['limit_value'] = isset($userMeta['limit_value']) ? $userMeta['limit_value'][0] : 0;
        $userdata['limit_duration'] = isset($userMeta['limit_duration']) ? $userMeta['limit_duration'][0] : '';
        $userdata['lockout_period'] = isset($userMeta['lockout_period']) ? $userMeta['lockout_period'][0] : '';
        $userdata['current_spending'] = isset($userMeta['current_spending']) ? $userMeta['current_spending'][0] : 0;
        $userdata['lock_account'] = isset($userMeta['lock_account']) ? $userMeta['lock_account'][0] : 0;
        $userdata['locking_period'] = isset($userMeta['locking_period']) ? $userMeta['locking_period'][0] : '';
        $userdata['locking_date'] = isset($userMeta['locking_date']) ? $userMeta['locking_date'][0] : '';

        $response = array(
            'success' => true,
            'data' => $userdata
        );

        wp_send_json($response, 200);
    }

    public static function updateUserInfoByToken($data)
    {

        global $wpdb;

        $token = $data['token'];

        if (empty($token)) {
            wp_send_json(['success' => false, 'error' => 'Token is missing'], 401);
        }

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $user = $wpdb->get_row($query, ARRAY_A);

        if (empty($user)) {
            wp_send_json(['success' => false, 'error' => 'Invalid Token'], 401);
        }

        if (!empty($data['limit_value']))
            update_user_meta($user['ID'], 'limit_value', $data['limit_value']);
        if (!empty($data['limit_duration']))
            update_user_meta($user['ID'], 'limit_duration', $data['limit_duration']);
        if (!empty($data['lockout_period']))
            update_user_meta($user['ID'], 'lockout_period', $data['lockout_period']);
        if (!empty($data['current_spending']))
            update_user_meta($user['ID'], 'current_spending', $data['current_spending']);

        if (!empty($data['locking_period'])) {
            update_user_meta($user['ID'], 'locking_period', $data['locking_period']);
            update_user_meta($user['ID'], 'lock_account', true);
            update_user_meta($user['ID'], 'locking_date', date("Y-m-d H:i:s"));
            $lockout_date = self::getLockExpireDate($data['locking_period'], date("Y-m-d H:i:s"));
            update_user_meta($user['ID'], 'lockout_date', $lockout_date);
        }

        if (!empty($data['limit_duration'])) {
            update_user_meta($user['ID'], 'limit_created', date("Y-m-d H:i:s"));
            $renewal_date = self::getLimitRenewalDate($data['limit_duration'], date("Y-m-d H:i:s"));
            update_user_meta($user['ID'], 'limit_renewal', $renewal_date);
        }

        $allowedValues = [
            'nickname',
            'first_name',
            'last_name',
            'description',
            'billing_first_name',
            'billing_last_name',
            'billing_address_1',
            'billing_city',
            'billing_state',
            'billing_postcode',
            'billing_country',
            'billing_email',
            'billing_company',
            'billing_address_2',
            'billing_phone'
        ];

        foreach ($allowedValues as $meta_key) {

            if (isset($data[$meta_key]) && !empty($data[$meta_key]))
                update_user_meta($user['ID'], $meta_key, $data[$meta_key]);
        }

        if (!empty($data['first_name']) && !empty($data['first_name'])) {

            $outcome = trim(get_user_meta($user['ID'], 'first_name', true) . " " . get_user_meta($user['ID'], 'last_name', true));
            if (!empty($outcome) && ($user['display_name'] != $outcome)) {
                wp_update_user(array('ID' => $user['ID'], 'display_name' => $outcome));
            }
        }

        if (!empty($data['email'])) {
            $args = array(
                'ID' => $data['email'],
                'user_email' => esc_attr($data['email'])
            );
            wp_update_user($args);
        }


        $response = array(
            'success' => true,
            'message' => 'User Update Successfully.',
            'data' => self::getUserInfoByToken(['token' => $token])
        );

        wp_send_json($response, 200);
    }

    public static function getLockExpireDate($locking_period, $date_locking)
    {

        $lockout_date = "";

        switch ($locking_period) {
            case "1 Week":
                $lockout_date = date('Y-m-d H:i:s', strtotime("+1 week", strtotime($date_locking)));
                break;
            case "2 Weeks":
                $lockout_date = date('Y-m-d H:i:s', strtotime("+2 week", strtotime($date_locking)));
                break;
            case "4 Weeks":
                $lockout_date = date('Y-m-d H:i:s', strtotime("+4 week", strtotime($date_locking)));
                break;
            case "12 Weeks":
                $lockout_date = date('Y-m-d H:i:s', strtotime("12 week", strtotime($date_locking)));
                break;
            case "1 Year":
                $lockout_date = date('Y-m-d H:i:s', strtotime("+1 year", strtotime($date_locking)));
                break;
            default:
                $lockout_date = "2222-12-31";
        }

        return $lockout_date;
    }

    public static function getLimitRenewalDate($duration, $created_at)
    {

        $renewalDate = "";

        switch ($duration) {
            case "Per Day":
                $renewalDate = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($created_at)));
                break;
            case "Per Week":
                $renewalDate = date('Y-m-d H:i:s', strtotime('+1 week', strtotime($created_at)));
                break;
            case "Per Month":
                $renewalDate = date('Y-m-d H:i:s', strtotime('+1 month', strtotime($created_at)));
                break;
            case "Per Year":
                $renewalDate = date('Y-m-d H:i:s', strtotime("+1 year", strtotime($created_at)));
                break;
        }

        return $renewalDate;
    }

    public static function getUserPoints($data)
    {
        global $wpdb;

        $token = $data['token'];

        if (empty($token)) {
            wp_send_json(['success' => false, 'error' => 'Token is missing'], 401);
        }

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $user = $wpdb->get_row($query, ARRAY_A);

        if (empty($user)) {
            wp_send_json(['success' => false, 'error' => 'Invalid Token'], 401);
        }

        $points = WC_Points_Rewards_Manager::get_users_points($user['ID']);

        $pointLogs = WC_Points_Rewards_Points_Log::get_points_log_entries(['user' => $user['ID']]);

        $response = array(
            'success' => true,
            'points' => $points,
            'logs' => $pointLogs
        );

        wp_send_json($response, 200);
    }

    public static function updateProfileDetails($data)
    {
        global $wpdb;

        $token = $data['token'];

        if (empty($token)) {
            wp_send_json(['success' => false, 'error' => 'Token is missing'], 401);
        }

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $current_user = $wpdb->get_row($query);

        if (empty($current_user)) {
            wp_send_json(['success' => false, 'error' => 'Invalid Token'], 401);
        }

        $user_id = $current_user->ID;

        $account_first_name = !empty($data['account_first_name']) ? wc_clean(wp_unslash($data['account_first_name'])) : '';
        $account_last_name = !empty($data['account_last_name']) ? wc_clean(wp_unslash($data['account_last_name'])) : '';
        $account_display_name = !empty($data['account_display_name']) ? wc_clean(wp_unslash($data['account_display_name'])) : '';
        $account_email = !empty($data['account_email']) ? wc_clean(wp_unslash($data['account_email'])) : '';
        $pass_cur = !empty($data['password_current']) ? $data['password_current'] : '';
        $pass1 = !empty($data['password_1']) ? $data['password_1'] : '';
        $pass2 = !empty($data['password_2']) ? $data['password_2'] : '';
        $save_pass = true;

        $current_first_name = get_user_meta($user_id, 'first_name', true);
        $current_last_name = get_user_meta($user_id, 'last_name', true);
        $current_email = $current_user->user_email;

        $user = new stdClass();
        $user->ID = $user_id;
        $user->first_name = $account_first_name;
        $user->last_name = $account_last_name;
        $user->display_name = $account_display_name;

        if (is_email($account_display_name)) {
            wp_send_json(['success' => false, 'error' => __('Display name cannot be changed to email address due to privacy concern.', 'woocommerce')], 400);
        }

        $required_fields = apply_filters(
            'woocommerce_save_account_details_required_fields',
            array(
                'account_first_name' => __('First name', 'woocommerce'),
                'account_last_name' => __('Last name', 'woocommerce'),
                'account_display_name' => __('Display name', 'woocommerce'),
                'account_email' => __('Email address', 'woocommerce'),
            )
        );

        foreach ($required_fields as $field_key => $field_name) {
            if (empty($data[$field_key])) {
                wp_send_json(['success' => false, 'error' => sprintf(__('%s is a required field.', 'woocommerce'), '<strong>' . esc_html($field_name) . '</strong>')], 400);
            }
        }

        if ($account_email) {
            $account_email = sanitize_email($account_email);
            if (!is_email($account_email)) {
                wp_send_json(['success' => false, 'error' => __('Please provide a valid email address.', 'woocommerce')], 400);
            } elseif (email_exists($account_email) && $account_email !== $current_user->user_email) {
                wp_send_json(['success' => false, 'error' => __('This email address is already registered.', 'woocommerce')], 400);
            }
            $user->user_email = $account_email;
        }

        if (!empty($pass_cur) && empty($pass1) && empty($pass2)) {
            wp_send_json(['success' => false, 'error' => __('Please fill out all password fields.', 'woocommerce')], 400);
            $save_pass = false;
        } elseif (!empty($pass1) && empty($pass_cur)) {
            wp_send_json(['success' => false, 'error' => __('Please enter your current password.', 'woocommerce')], 400);
            $save_pass = false;
        } elseif (!empty($pass1) && empty($pass2)) {
            wp_send_json(['success' => false, 'error' => __('Please re-enter your password.', 'woocommerce')], 400);
            $save_pass = false;
        } elseif ((!empty($pass1) || !empty($pass2)) && $pass1 !== $pass2) {
            wp_send_json(['success' => false, 'error' => __('New passwords do not match.', 'woocommerce')], 400);
            $save_pass = false;
        } elseif (!empty($pass1) && !wp_check_password($pass_cur, $current_user->user_pass, $current_user->ID)) {
            wp_send_json(['success' => false, 'error' => __('Your current password is incorrect.', 'woocommerce')], 400);
            $save_pass = false;
        }

        if ($pass1 && $save_pass) {
            $user->user_pass = $pass1;
        }

        // Allow plugins to return their own errors.

        wp_update_user($user);

        // Update customer object to keep data in sync.
        $customer = new WC_Customer($user->ID);

        if ($customer) {
            // Keep billing data in sync if data changed.
            if (is_email($user->user_email) && $current_email !== $user->user_email) {
                $customer->set_billing_email($user->user_email);
            }

            if ($current_first_name !== $user->first_name) {
                $customer->set_billing_first_name($user->first_name);
            }

            if ($current_last_name !== $user->last_name) {
                $customer->set_billing_last_name($user->last_name);
            }

            $customer->save();
        }

        $response = array(
            'success' => true,
            'message' => 'User Update Successfully.',
            'data' => self::getUserInfoByToken(['token' => $token])
        );

        wp_send_json($response, 200);
    }

    public static function getUserOrders($data)
    {

        global $wpdb;

        $token = $data['token'];

        $page = isset($data['page']) ? $data['page'] : false;

        $paginate = false;

        if (empty($token)) {
            wp_send_json(['success' => false, 'error' => 'Token is missing'], 401);
        }

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $current_user = $wpdb->get_row($query);

        if (empty($current_user)) {
            wp_send_json(['success' => false, 'error' => 'Invalid Token'], 401);
        }

        $user_id = $current_user->ID;

        $all_statuses = wc_get_order_statuses();

        unset($all_statuses['wc-admin-assigned']);
        unset($all_statuses['wc-cancelled']);

        $statuses = array_keys($all_statuses);

        $customer_orders = wc_get_orders(
            apply_filters(
                'woocommerce_my_account_my_orders_query',
                array(
                    'customer' => $user_id,
                    'return' => 'ids',
                    'limit' => -1,
                    'status' => $statuses,
                    //'page' => $current_page,
                    //'paginate' => $paginate,
                )
            )
        );

        $orders = [];

        if (!empty($customer_orders)) {

            foreach ($customer_orders as $customer_order) {

                $order = wc_get_order($customer_order); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

                $item_count = $order->get_item_count();

                $orders[] = [
                    "order_id_" => $customer_order,
                    "order_number" => $order->get_order_number(),
                    'order_date' => wc_format_datetime($order->get_date_created()),
                    "order_status" => wc_get_order_status_name($order->get_status()),
                    "order_total_formatted" => sprintf(
                        _n(
                            '%1$s for %2$s item',
                            '%1$s for %2$s items',
                            $item_count,
                            'woocommerce'
                        ),
                        $order->get_formatted_order_total(),
                        $item_count
                    ),
                    "item_count" => $item_count,
                    "order_total" => $order->get_formatted_order_total()

                ];
            }
        }

        $response = array(
            'success' => true,
            'data' => $orders
        );

        wp_send_json($response, 200);
    }

    public static function getOrderDetailById($data)
    {

        global $wpdb;

        $token = $data['token'];

        if (empty($token)) {
            wp_send_json(['success' => false, 'error' => 'Token is missing'], 401);
        }

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $current_user = $wpdb->get_row($query);

        if (empty($current_user)) {
            wp_send_json(['success' => false, 'error' => 'Invalid Token'], 401);
        }

        $user_id = $current_user->ID;

        $customer_order = $data['id'];

        $order = wc_get_order($customer_order);

        $orderData = $order_api_info = [];

        if (!empty($order)) {

            $orderData = $order->get_data();

            $lineItems = $orderData['line_items'];

            $order_api_info['id'] = $orderData['id'];
            $order_api_info['status'] = $orderData['status'];
            $order_api_info['date_created'] = $orderData['date_created'];
            $order_api_info['shipping'] = $orderData['shipping'];
            $order_api_info['billing'] = $orderData['billing'];
            $order_api_info['payment_method'] = $orderData['payment_method'];
            $order_api_info['payment_method_title'] = $orderData['payment_method_title'];
            $order_api_info['number'] = $orderData['number'];
            $order_api_info['discount_total'] = $orderData['discount_total'];
            $order_api_info['total'] = $orderData['total'];
            $order_api_info['product'] = [];

            foreach ($lineItems as $lineItem) {

                $comp_tickets = [];


                $query = $wpdb->prepare("SELECT {$wpdb->prefix}competition_tickets.ticket_number, {$wpdb->prefix}competitions.* FROM {$wpdb->prefix}competition_tickets 
                    INNER JOIN {$wpdb->prefix}competitions on {$wpdb->prefix}competitions.id = {$wpdb->prefix}competition_tickets.competition_id 
                    WHERE {$wpdb->prefix}competition_tickets.user_id = %d and {$wpdb->prefix}competition_tickets.order_id = %d
                    and {$wpdb->prefix}competitions.competition_product_id = %d and {$wpdb->prefix}competition_tickets.is_purchased = 1", $user_id, $customer_order, $lineItem['product_id']);

                $records = $wpdb->get_results($query, ARRAY_A);

                if ($orderData['status'] == 'completed') {
                    foreach ($records as $record) {
                        $comp_tickets[] = $record['ticket_number'];
                    }
                }


                $lineItemData = $lineItem->get_data();

                $order_api_info['product'][] = [
                    "title" => $records[0]['title'],
                    "draw_date" => $records[0]['draw_date'],
                    "draw_time" => $records[0]['draw_time'],
                    "quantity" => $lineItemData['quantity'],
                    "price" => $lineItemData['subtotal'],
                    "tickets" => $comp_tickets,
                ];
            }
        }

        $response = array(
            'success' => true,
            'data' => $order_api_info
        );

        wp_send_json($response, 200);
    }

    public static function getCompetitionInstantWins($data)
    {

        global $wpdb;

        $page = isset($data['page']) ? absint($data['page']) : 1;

        $records_per_page = 7;

        $current_page = $page;

        $results = [];

        do {

            $page_start_day = $current_page * $records_per_page - 1;

            $page_end_day = $current_page * $records_per_page - $records_per_page;

            $start_date = date('Y-m-d', strtotime("-$page_start_day days"));

            $end_date = ($current_page == 1) ? date('Y-m-d') : date('Y-m-d', strtotime("-$page_end_day days"));

            $query = "SELECT
                     {$wpdb->prefix}comp_instant_prizes_tickets.id,
                     {$wpdb->prefix}comp_instant_prizes_tickets.ticket_number,
                     {$wpdb->prefix}competitions.title,
                     {$wpdb->prefix}users.display_name,
                     {$wpdb->prefix}comp_instant_prizes_tickets.updated_at,
                     {$wpdb->prefix}comp_instant_prizes.title AS instant_title,
                     {$wpdb->prefix}comp_instant_prizes_tickets.edited_title_instant
                     FROM {$wpdb->prefix}comp_instant_prizes_tickets
                     INNER JOIN
                     {$wpdb->prefix}competitions ON {$wpdb->prefix}comp_instant_prizes_tickets.competition_id = {$wpdb->prefix}competitions.id
                     INNER JOIN
                     {$wpdb->prefix}comp_instant_prizes ON {$wpdb->prefix}comp_instant_prizes_tickets.instant_id = {$wpdb->prefix}comp_instant_prizes.id
                     INNER JOIN
                     {$wpdb->prefix}users ON {$wpdb->prefix}comp_instant_prizes_tickets.user_id = {$wpdb->prefix}users.ID
                     WHERE DATE({$wpdb->prefix}comp_instant_prizes_tickets.updated_at) BETWEEN '$start_date' AND '$end_date'
                     ORDER BY {$wpdb->prefix}comp_instant_prizes_tickets.updated_at DESC";

            $results = $wpdb->get_results($wpdb->prepare($query), ARRAY_A);

            if (!empty($results)) {
                break;
            } else {
                $current_page++;
            }
        } while ($current_page <= $page + 1);

        $list = [];

        foreach ($results as $info) {

            $date = date('Y-m-d', strtotime($info['updated_at']));

            if (!isset($list[$date])) {

                $db_date = new DateTime($date);

                $list[$date] = [
                    'winning_date' => $db_date->format('D, d M Y'),
                    'winners' => []
                ];
            }

            $list[$date]['winners'][] = [
                "id" => $info['id'],
                "ticket_number" => $info['ticket_number'],
                "user" => $info['display_name'],
                "competition_title" => $info['title'],
                "instant_title" => $info['instant_title'],
                "edited_title_instant" => $info['edited_title_instant']
            ];
        }

        $list = array_values($list);

        if (!empty($results)) {
            $response = array(
                'success' => true,
                'data' => $list,
                'page' => $current_page
            );
        } else {
            $response = array(
                'success' => false,
                'data' => []
            );
        }

        wp_send_json($response, 200);
    }

    public static function getCompetitionWinners($data, $retry = 0)
    {
        global $wpdb;

        $page = isset($data['page']) ? absint($data['page']) : 1;

        $page_start_day = $page * 7 - 1;
        $page_end_day = $page * 7 - 7;
        $start_date = date('Y-m-d', strtotime("-$page_start_day days"));
        $end_date = ($page == 1) ? date('Y-m-d') : date('Y-m-d', strtotime("-$page_end_day days"));

        $query = "SELECT
        {$wpdb->prefix}competition_winners.id,
        {$wpdb->prefix}competition_winners.ticket_number,
        {$wpdb->prefix}competitions.title,
        {$wpdb->prefix}users.display_name,
        {$wpdb->prefix}competition_winners.modified_at,
        {$wpdb->prefix}competition_winners.edited_title,
        {$wpdb->prefix}competitions.title AS prize_title

        FROM {$wpdb->prefix}competition_winners
        INNER JOIN
        {$wpdb->prefix}competitions ON {$wpdb->prefix}competition_winners.competition_id = {$wpdb->prefix}competitions.id
        INNER JOIN
        {$wpdb->prefix}users ON {$wpdb->prefix}competition_winners.user_id = {$wpdb->prefix}users.ID
        WHERE DATE({$wpdb->prefix}competition_winners.modified_at) BETWEEN '$start_date' AND '$end_date'";

        $query .= " ORDER BY {$wpdb->prefix}competition_winners.modified_at DESC";

        $results = $wpdb->get_results($wpdb->prepare($query), ARRAY_A);

        $list = [];

        if (!empty($results)) {
            foreach ($results as $info) {

                $date = date('Y-m-d', strtotime($info['modified_at']));

                if (!isset($list[$date])) {

                    $db_date = new DateTime($date);

                    $list[$date] = [
                        'winning_date' => $db_date->format('D, d M Y'),
                        'list' => []
                    ];
                }

                $list[$date]['list'][] = [
                    "id" => $info['id'],
                    "ticket_number" => $info['ticket_number'],
                    "competition_title" => $info['title'],
                    "display_name" => $info['display_name'],
                    "edit_title" => $info['edited_title'],
                    "prize_title" => $info['prize_title'],

                ];
            }
        }

        $query = "SELECT
        {$wpdb->prefix}comp_reward_winner.id,
        {$wpdb->prefix}comp_reward_winner.ticket_number,
        {$wpdb->prefix}competitions.title,
        {$wpdb->prefix}users.display_name,
        {$wpdb->prefix}comp_reward_winner.created_at, 
        {$wpdb->prefix}comp_reward_winner.edited_title_reward, 
        {$wpdb->prefix}comp_reward.title AS prize_title
        FROM {$wpdb->prefix}comp_reward_winner
        INNER JOIN
        {$wpdb->prefix}competitions ON {$wpdb->prefix}comp_reward_winner.competition_id = {$wpdb->prefix}competitions.id
        INNER JOIN
        {$wpdb->prefix}comp_reward ON {$wpdb->prefix}comp_reward_winner.reward_id = {$wpdb->prefix}comp_reward.id
        INNER JOIN
        {$wpdb->prefix}users ON {$wpdb->prefix}comp_reward_winner.user_id = {$wpdb->prefix}users.ID
        WHERE DATE({$wpdb->prefix}comp_reward_winner.created_at) BETWEEN '$start_date' AND '$end_date'";

        $query .= " ORDER BY {$wpdb->prefix}comp_reward_winner.created_at DESC";

        $reward_list = $wpdb->get_results($wpdb->prepare($query), ARRAY_A);



        if (!empty($reward_list)) {
            foreach ($reward_list as $info) {
                $date = date('Y-m-d', strtotime($info['created_at']));

                if (!isset($list[$date])) {
                    $db_date = new DateTime($date);
                    $list[$date] = [
                        'winning_date' => $db_date->format('D, d M Y'),
                        'list' => []
                    ];
                }

                $list[$date]['list'][] = [
                    "id" => $info['id'],
                    "ticket_number" => $info['ticket_number'],
                    "competition_title" => $info['title'],
                    "display_name" => $info['display_name'],
                    "edit_title" => $info['edited_title_reward'],
                    "prize_title" => $info['prize_title'],

                ];
            }
        }

        if (empty($results) && empty($reward_list)) {

            if ($retry < 5) {
                return self::getCompetitionWinners(['page' => $page + 1], $retry + 1);
            }
        }

        $list = array_values($list);




        if (!empty($results) || !empty($reward_list)) {
            $response = array(
                'success' => true,
                'data' => $list,
                'page' => $page
            );
        } else {
            $response = array(
                'success' => false,
                'data' => [],
                'page' => $page
            );
        }

        wp_send_json($response, 200);
    }


    public static function getRecentWinners($data)
    {
        global $wpdb;

        $query = "SELECT
        {$wpdb->prefix}competition_winners.id,
        {$wpdb->prefix}competition_winners.user_id,
        {$wpdb->prefix}competition_winners.ticket_number,
        {$wpdb->prefix}competitions.title,
        {$wpdb->prefix}competitions.image,
        {$wpdb->prefix}users.display_name,
        {$wpdb->prefix}competition_winners.modified_at
        FROM {$wpdb->prefix}competition_winners
        INNER JOIN {$wpdb->prefix}competitions ON {$wpdb->prefix}competition_winners.competition_id = {$wpdb->prefix}competitions.id
        INNER JOIN {$wpdb->prefix}users ON {$wpdb->prefix}competition_winners.user_id = {$wpdb->prefix}users.ID
        WHERE ({$wpdb->prefix}competition_winners.user_id IS NOT NULL AND {$wpdb->prefix}competition_winners.user_id != '') 
        AND ({$wpdb->prefix}competition_winners.modified_at IS NOT NULL AND {$wpdb->prefix}competition_winners.modified_at != '') 
        ORDER BY {$wpdb->prefix}competition_winners.modified_at DESC LIMIT 4";

        $results = $wpdb->get_results($wpdb->prepare($query), ARRAY_A);

        $list = [];

        foreach ($results as $info) {

            $user_id = $info['user_id'];

            $user_city = get_user_meta($user_id, 'billing_city', true);

            $list[] = [
                "id" => $info['id'],
                "ticket_number" => $info['ticket_number'],
                "title" => $info['title'],
                "display_name" => $info['display_name'],
                "image" => $info['image'],
                "modified_at" => $info['modified_at'],
                "city" => $user_city,
            ];
        }

        if (!empty($results)) {
            $response = array(
                'success' => true,
                'data' => $list
            );
        } else {
            $response = array(
                'success' => false,
                'data' => []
            );
        }

        wp_send_json($response, 200);
    }

    public static function createGFEntry($data)
    {

        $form_id = 1;
        $input_values = array('form_id' => 1);
        $input_values['1.3'] = $data['first_name'];
        $input_values['1.6'] = $data['last_name'];
        $input_values['2'] = $data['email'];
        $input_values['4'] = $data['phone'];
        $input_values['3'] = $data['message'];

        $entry_id = GFAPI::add_entry($input_values, $form_id);

        if (is_array($entry_id)) {

            $response = array(
                'success' => false,
                'message' => 'Oops! Something went wrong.',
                'error' => $entry_id
            );
        } else {

            $entry = GFAPI::get_entry($entry_id);

            $form = GFAPI::get_form($form_id);

            GFAPI::send_notifications($form, $entry);

            $response = array(
                'success' => true,
                'message' => 'Thank you for contacting us! We will get in touch with you shortly.'
            );
        }

        wp_send_json($response, 200);
    }

    public static function getSingularCompetitions($data)
    {

        global $wpdb;

        $limit = isset($data['limit']) ? absint($data['limit']) : 10;

        $page = isset($data['page']) ? absint($data['page']) : 1;

        $status = 'Open';

        $query = "SELECT comp.*, COUNT(t.id) AS total_ticket_sold FROM {$wpdb->prefix}competitions comp 
        LEFT JOIN {$wpdb->prefix}competition_tickets t ON comp.id = t.competition_id AND t.is_purchased = 1 
        WHERE comp.is_draft = '0' and comp.instant_win_only = 0 and  comp.draw_date >= CURDATE() and comp.category <> 'instant_win_comps'";


        if (!empty($status)) {
            $query .= "AND comp.status = %s ";
        }

        $query .= "GROUP BY comp.id ";

        if (isset($data['order_by'])) {

            $order = isset($data['order']) ? $data['order'] : 'DESC';

            $query .= "ORDER BY comp." . $data['order_by'] . " " . $order;
        }

        $offset = ($page - 1) * $limit;

        $query .= " LIMIT %d, %d";

        $prepared_query_args = array();

        if (!empty($status)) {
            $prepared_query_args[] = $status;
        }

        $prepared_query_args[] = $offset;
        $prepared_query_args[] = $limit;

        $prepared_query = $wpdb->prepare($query, $prepared_query_args);

        $results = $wpdb->get_results($prepared_query, ARRAY_A);

        $response = array(
            'success' => 'true',
            'data' => $results
        );

        wp_send_json($response, 200);
    }

    public static function checkClaimPrizeForm($data)
    {

        global $wpdb;

        $token = $data['token'];

        if (empty($token)) {
            wp_send_json(['success' => false, 'error' => 'Token is missing'], 401);
        }

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        $current_user = $wpdb->get_row($query);

        if (empty($current_user)) {
            wp_send_json(['success' => false, 'error' => 'Invalid Token'], 401);
        }

        $user_id = $current_user->ID;

        error_log('currentuser data' . print_r($current_user, true));

        $response = array(
            'success' => 'false',
            'message' => 'invalid'
        );

        if (isset($data['competition_type'])) {

            $competition_type = $data['competition_type'];

            $order_id = $data['order'];

            if (!empty($order_id)) {

                $order = wc_get_order($order_id);

                if (!empty($order)) {

                    $order_user_id = $order->get_user_id();

                    if ($competition_type == 'reward') {

                        $query = $wpdb->prepare(
                            "select * from {$wpdb->prefix}comp_reward_winner where competition_id = %d and 
                            reward_id = %d and user_id = %d and ticket_number = %d and is_admin_declare_winner = 0",
                            array(
                                $data['competition_id'],
                                $data['prize_id'],
                                $order_user_id,
                                $data['ticket_number']
                            )
                        );

                        $prize_data = $wpdb->get_row($query, ARRAY_A);

                        if (!empty($prize_data)) {

                            $response = array(
                                'success' => 'true',
                                'message' => 'valid'
                            );
                        }
                    }

                    if ($competition_type == 'instant') {

                        $query = $wpdb->prepare(
                            "select * from {$wpdb->prefix}comp_instant_prizes_tickets where competition_id = %d and 
                        instant_id = %d and user_id = %d and ticket_number = %d and is_admin_declare_winner = 0",
                            array(
                                $data['competition_id'],
                                $data['prize_id'],
                                $order_user_id,
                                $data['ticket_number']
                            )
                        );

                        $prize_data = $wpdb->get_row($query, ARRAY_A);

                        if (!empty($prize_data)) {

                            $response = array(
                                'success' => 'true',
                                'message' => 'valid'
                            );
                        }
                    }

                    if ($competition_type == 'main') {

                        $query = $wpdb->prepare(
                            "select * from {$wpdb->prefix}competition_winners where competition_id = %d  and user_id = %d and ticket_number = %d and is_admin_declare_winner = 0",
                            array(
                                $data['competition_id'],
                                $order_user_id,
                                $data['ticket_number']
                            )
                        );
                        $prize_data = $wpdb->get_row($query, ARRAY_A);

                        if (!empty($prize_data)) {

                            $response = array(
                                'success' => 'true',
                                'message' => 'valid'
                            );
                        }
                    }
                }
            }
        }

        if (
            (empty($current_user->account_number) || $current_user->account_number == '0') &&
            (empty($current_user->sort_code) || $current_user->sort_code == '0')
        ) {
            $response = array(
                'success' => 'false',
                'message' => 'accountDetailsMissing'
            );
        }


        wp_send_json($response, 200);
    }
    public static function getHomePageSlider($data)
    {

        global $wpdb;

        $query = "SELECT * FROM {$wpdb->prefix}homepage_sliders";

        $sliderData = $wpdb->get_results($query, ARRAY_A);

        $response = array(
            'success' => 'true',
            'data' => $sliderData
        );

        wp_send_json($response, 200);
    }

    public static function getALLWinnersAndPrizeValues()
    {

        global $wpdb;

        $mainwinnercount = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}competition_winners WHERE is_admin_declare_winner > 0");
        $instantcount = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}comp_instant_prizes_tickets  WHERE user_id IS NOT NULL");
        $rewardwincount = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}comp_reward_winner WHERE user_id IS NOT NULL");

        $total_prize_value_main = $wpdb->get_var("SELECT SUM(CAST(prize_value AS DECIMAL(10, 2))) FROM {$wpdb->prefix}competition_winners WHERE is_admin_declare_winner > 0");
        $total_prize_value_instant = $wpdb->get_var("SELECT SUM(CAST(prize_value AS DECIMAL(10, 2))) FROM {$wpdb->prefix}comp_instant_prizes_tickets WHERE user_id IS NOT NULL");
        $total_prize_value_reward = $wpdb->get_var("SELECT SUM(CAST(prize_value AS DECIMAL(10, 2))) FROM {$wpdb->prefix}comp_reward_winner WHERE user_id IS NOT NULL");



        $totalWinner =  $mainwinnercount +  $instantcount +  $rewardwincount;
        $totalPrizeValue =  $total_prize_value_main +  $total_prize_value_instant +  $total_prize_value_reward;

        $response = array(
            'success' => 'true',
            'totalWinner' => $totalWinner,
            'totalPrizeValue' => $totalPrizeValue,
            'total_prize_value_main' => $total_prize_value_main,
            'total_prize_value_instant' => $total_prize_value_instant,
            'total_prize_value_reward' => $total_prize_value_reward
        );

        wp_send_json($response, 200);
    }

    public static function getPinnedMessageData()
    {

        global $wpdb;

        $data = $wpdb->get_row("SELECT pinnedMessage, showPinnedMessage FROM {$wpdb->prefix}global_settings where id = 2", ARRAY_A);

        $showPinnedMessage = isset($data['showPinnedMessage']) ? $data['showPinnedMessage'] : null;
        $pinnedMessageText = isset($data['pinnedMessage']) ? $data['pinnedMessage'] : null;

        $response = array(
            'success' => true,
            'showPinnedMessage' => $showPinnedMessage,
            'pinnedMessageText' => $pinnedMessageText,
        );

        wp_send_json($response, 200);
    }

    public static function getBankDetails($data)
    {
        global $wpdb;

        $token = $data['token'];
        $account = isset($data['account_number']) ? $data['account_number'] : '';
        $sort = isset($data['sort_code']) ? $data['sort_code'] : '';
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_auth_token = %s", $token);

        error_log('array++++++++++++++++account', print_r($data['account_number'], true));
        error_log('array++++++++++++++++sort', print_r($data['sort_code'], true));

        $user = $wpdb->get_row($query, ARRAY_A);
        if (empty($user)) {
            wp_send_json(['success' => false, 'error' => 'Invalid Token'], 401);
        }


        $id = $user['ID'];

        if (empty($account)) {
            wp_send_json(['success' => false, 'error' => 'Please enter value in Account Number and try again after sometime.'], 400);
        } else if (empty($sort)) {

            // wp_send_json(['success' => false, 'error' => 'Please enter your sort code and try again after sometime.'], 400);
            wp_send_json(['success' => false, 'error' => __('Please enter your sort code and try again after sometime', 'woocommerce')], 400);
        }


        error_log('++++++++++++++++account', print_r($account, true));
        error_log('++++++++++++++++sort', print_r($sort, true));


        if ($account && $sort) {

            $url = "http://api.addressy.com/BankAccountValidation/Batch/Validate/v1.00/xmla.ws?";
            $url .= "&Key=ZH81-MH87-EG49-JD72";
            $url .= "&AccountNumbers=" . urlencode($account);
            $url .= "&SortCodes=" . urlencode($sort);

            //Make the request to Postcode Anywhere and parse the XML returned
            $file = simplexml_load_file($url);

            error_log('++++++++++++' . print_r($file, true));
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

                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$wpdb->users} SET account_number = %d ,  sort_code = %s  WHERE ID = %d",
                        $account,
                        $sort,
                        $id
                    )
                );
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
}
