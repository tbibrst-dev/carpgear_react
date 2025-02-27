<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$email_heading = "";
do_action('woocommerce_email_header', $email_heading, $email);
?>
<link href="https://development.brstdev.com/competition/fonts/MozaicGEO.css" rel="stylesheet" />
<div style="width: 100%; max-width: 450px; margin: auto;">
    <div style="display: block; gap: 24px;">

        <div class="instant-win-email-content-heading" style="width: 100%; max-width: 450px; margin: auto;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                style="margin: auto; text-align: center;">
                <tr>
                    <td style="padding: 0;">
                        <h1
                            style="color: #fff; font-size: 36px; text-transform: uppercase; text-align: center; margin: 0; font-weight: 900; line-height: 39.6px;">
                            <img src="<?php echo bloginfo('stylesheet_directory') . '/images/Frame 151.png'; ?>"
                                style="height: 25px; vertical-align: unset; margin-right:0px;" />
                            Reward Prize
                        </h1>
                    </td>
                </tr>
            </table>
        </div>
        <p
            style="font-family: 'Roboto', sans-serif;text-align: center; color: #fff !important; font-size: 16px; font-weight: 100; line-height: 22.35px;">
            You’ve won a Reward Prize!
        </p>

        <div style="margin-top:24px;">
            <p
                style="margin-bottom:0px;text-align: center;text-transform: uppercase;font-family: 'Roboto', sans-serif;font-size: 12px;font-weight: 400;line-height: 24px;color: #FFFFFF;opacity: 50%;">
                Competition
            </p>
            <p class="competition-title"
                style="margin-top:0px;text-transform: uppercase;font-family: 'Roboto', sans-serif;font-size: 15px;font-weight: 600;line-height: 18px;text-align: center;color: #fff;">
                <?php printf(esc_html($comp_title)); ?>
            </p>
        </div>

        <div class="instant-win-prize-details-container"
            style="max-width: 450px; padding: 12px; background: #202323; border-radius: 8px; border: 1px solid #FFFFFF1A; margin-top: 20px; background-color: #FFFFFF1A;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                style="width: 100%; height: 100%;">
                <tr>
                    <td style="width: 100px; padding: 0;">
                        <img src="<?php printf(esc_html($image)); ?>" alt=""
                            style="width: 100px;  border-radius: 4px; display: block;" />
                    </td>
                    <td style="padding-left: 15px; vertical-align: middle;">
                        <h1
                            style="color: #FFFFFF; margin: 0 0 10px 0; font-size: 18px; font-family: 'Roboto', sans-serif; font-weight: 900; line-height: 19.8px;">
                            <?php printf(esc_html($title)); ?>
                        </h1>
                        <div
                            style="opacity: 50%; display: inline-block; padding: 8px 8px 4px; border-radius: 4px; background-color: #333838; border: 1px solid #282b2b;">
                            <p
                                style="font-family: 'Roboto', sans-serif; font-size: 12px; font-weight: 400; line-height: 13.2px; color: #FFFFFF; margin: 0; text-transform: uppercase; opacity: 75%;">
                                TICKET NUMBER: <?php printf(esc_html($ticket_number)); ?>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <?php
    $claimURL = FRONTEND_URL."claim/prize?competition_name=$comp_title&competition_type=reward&prize_name=$title&prize_id=$prize_id&competition_id=$competition_id&order=$order&ticket_number=$ticket_number";
    ?>
    <p>
        <a href="<?php echo $claimURL; ?>"
            style="display:block;text-align: center;font-family: 'Roboto', sans-serif;max-width: 450px;max-height: 60px;text-decoration:none;padding: 20px 16px;border-radius: 4px;border: none;outline: none;background-color: #ffbb41;text-transform: uppercase;color: #0f1010;line-height: 24px;font-size: 18px;font-weight: 900;margin-top: 10px;">
            Claim your prize</a>
    </p>
</div>