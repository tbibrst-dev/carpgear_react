<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$email_heading = "";
do_action('woocommerce_email_header', $email_heading, $email);
?>
<div style="width: 100%; max-width: 450px; margin: auto;">
    <div style="display: block; gap: 24px;">

        <p
            style="font-family: 'Roboto', sans-serif;text-align: left; color: #fff !important; font-size: 16px; font-weight: 100; line-height: 22.35px;">
            Dear <?php echo $user_name; ?>,</p>
        <p
            style="font-family: 'Roboto', sans-serif;text-align: left; color: #fff !important; font-size: 16px; font-weight: 100; line-height: 22.35px;">
            Thank you for entering our recent competition! We were thrilled to see so many fantastic entries and
            appreciate the time and effort you put into your submission.</p>
        <p
            style="font-family: 'Roboto', sans-serif;text-align: left; color: #fff !important; font-size: 16px; font-weight: 100; line-height: 22.35px;">
            While you were not one of the winners this time, your participation means a lot to us. We encourage you to
            keep an eye out for future competitions and opportunities to showcase your talents.</p>
        <p
            style="font-family: 'Roboto', sans-serif;text-align: left; color: #fff !important; font-size: 16px; font-weight: 100; line-height: 22.35px;">
            We genuinely appreciate your support and hope to see you in our upcoming events. Your enthusiasm is what
            makes our community so special.</p>
        <p
            style="font-family: 'Roboto', sans-serif;text-align: left; color: #fff !important; font-size: 16px; font-weight: 100; line-height: 22.35px;">
            Thank you once again for participating, and we look forward to your future entries!
        </p>

    </div>

</div>