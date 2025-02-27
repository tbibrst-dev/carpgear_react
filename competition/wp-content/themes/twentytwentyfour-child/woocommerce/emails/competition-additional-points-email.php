<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$email_heading = "";
do_action('woocommerce_email_header', $email_heading, $email);
?>
<div style="width: 100%; max-width: 450px; margin: auto;">
    <div style="display: block; gap: 24px;">

        <div class="instant-win-email-content-heading" style="width: 100%;
    max-width: 450px;
    margin: auto;">
            <h1
                style="line-height: 100%;color: #fff; font-size: 36px; text-transform: uppercase; text-align: center;margin: 0;font-weight: 900;">
                Competition Entry
            </h1>
        </div>
        <p
            style="font-family: 'Roboto', sans-serif;text-align: center; color: #fff !important; font-size: 16px; font-weight: 100; line-height: 22.35px;">
            We wanted to let you know that recent entries into the <?php echo $comp_title; ?> competition have exceeded
            the maximum allowed per user. To ensure fair play for all participants, we've automatically converted the
            extra
            entries into <?php echo $points; ?> points and added them to your account.
            <br />
            <br />

            Thank you for your understanding, and good luck in the competition!
        </p>
    </div>
</div>