<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$email_heading = "";
do_action('woocommerce_email_header', $email_heading, $email);
?>
<div style="width: 100%; max-width: 450px; margin: auto;">
    <div style="display: block; gap: 24px;">

        <!-- due to font not load change with from 278px to 300px -->
        <h1
            style="text-transform: uppercase;width: 300px;height: 65px;max-width: 300px; margin: 0px auto 25px; font-family: 'Roboto', sans-serif; font-size: 30px; font-weight: 900; line-height: 39.6px; text-align: center; color: #FFFFFF;">
            A competition Has sold out
        </h1>

        <div class="instant-win-prize-details-container"
            style="max-width: 450px; height: 123px; padding: 12px; background: #202323; border-radius: 8px; border: 1px solid #FFFFFF1A; margin-top: 20px; background-color: #FFFFFF1A;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0"
                style="width: 100%; height: 100%;">
                <tr>
                    <td style="width: 100px; padding: 0;">
                        <img src="<?php printf(esc_html($image)); ?>" alt=""
                            style="width: 100px; height: 123px; border-radius: 4px; display: block;" />
                    </td>
                    <td style="padding-left: 15px; vertical-align: middle;">
                        <h1
                            style="margin: 0; color: #FFFFFF; font-size: 18px; font-family: 'Roboto', sans-serif; font-weight: 600; line-height: 19.8px;">
                            <?php printf(esc_html($comp_title)); ?>
                        </h1>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <p>
        <a href="<?php echo $comp_link; ?>"
            style="display:block;text-align: center;font-family: 'Roboto', sans-serif;max-width: 450px;max-height: 60px;text-decoration:none;padding: 20px 16px;border-radius: 4px;border: none;outline: none;background-color: #ffbb41;text-transform: uppercase;color: #0f1010;line-height: 24px;font-size: 18px;font-weight: 900;margin-top: 10px;">
            View Competition</a>
    </p>
</div>