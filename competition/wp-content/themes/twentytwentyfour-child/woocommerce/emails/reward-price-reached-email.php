<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$email_heading = "";
do_action('woocommerce_email_header', $email_heading, $email);
$symbol = get_woocommerce_currency_symbol();
?>
<div style="width: 100%; max-width: 450px; margin: auto;">
    <div style="display: block; gap: 24px;">

        <div class="instant-win-email-content-heading" style="width: 100%; max-width: 450px; margin: auto;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                style="margin: auto; text-align: center;">
                <tr>
                  
                  

                    <td style="padding: 0;">
                        <h1
                            style="color: #fff; font-size: 30px; text-transform: uppercase; text-align: center; margin: 0; font-weight: 900; line-height: 39.6px;">
                            <img src="<?php echo bloginfo('stylesheet_directory') . '/images/Frame 151.png'; ?>"
                                style="height: 25px;vertical-align: middle;margin-right:0px;margin-bottom: 5px;" />
                                <span>Reward Level Reached</span>
                        </h1>
                    </td>
                </tr>
            </table>
        </div>

        <p
            style="font-family: 'Roboto', sans-serif;text-align: center; color: #fff !important; font-size: 16px; font-weight: normal; line-height: 22.35px;">
            Reward Prize Level: <?php echo round($prcnt_available); ?>%
        </p>


        <!-- content -->
        <div style="margin-top:24px;">
            <!--todo competition title -->
            <p
                style="margin-bottom:0px;text-align: center;text-transform: uppercase;font-family: 'Roboto', sans-serif;font-size: 12px;font-weight: 400;line-height: 24px;color: #FFFFFF;opacity: 50%;">
                Competition
            </p>
            <p class="competition-title"
                style="margin-top:0px;text-transform: uppercase;font-family: 'Roboto', sans-serif;font-size: 15px;font-weight: 600;line-height: 18px;text-align: center;color: #fff;">
                <?php printf(esc_html($comp_title)); ?>
            </p>
            <!--todo competition title -->
        </div>

        <div class="instant-win-prize-details-container"
            style="max-width: 450px;  padding: 12px; background: #202323; border-radius: 8px; border: 1px solid #FFFFFF1A; margin-top: 20px; background-color: #FFFFFF1A;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                style="width: 100%; height: 100%;">
                <tr>
                    <td style="width: 100px; padding: 0;">
                        <img src="<?php printf(esc_html($image)); ?>" alt=""
                            style="width: 100px;  border-radius: 4px; display: block;" />
                    </td>
                    <td style="padding-left: 15px; vertical-align: middle;">
                        <h1
                            style="font-family: 'Roboto', sans-serif; font-weight: 900; line-height: 19.8px; text-align: left; color: #FFFFFF; margin: 0; font-size: 18px;">
                            <?php printf(esc_html($title)); ?>
                        </h1>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <?php
    $reward_link = FRONTEND_URL. "competition/details/". $title."-" .$comp_id;
    ?>

    <p>
        <a href="<?php echo $reward_link; ?>"
            style="display:block;text-align: center;font-family: 'Roboto', sans-serif;max-width: 450px;max-height: 60px;text-decoration:none;padding: 20px 16px;border-radius: 4px;border: none;outline: none;background-color: #ffbb41;text-transform: uppercase;color: #0f1010;line-height: 24px;font-size: 18px;font-weight: 900;margin-top: 10px;">
            View Competition</a>
    </p>
</div>