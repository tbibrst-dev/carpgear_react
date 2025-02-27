<?php

global $current_user, $wp, $wpdb;

$items_count = WC()->cart->get_cart_contents_count();



$frontend_url = FRONTEND_URL;
$frontend_s3_url = FRONTEND_S3_URL;

?>
<div class="footer-section">
    <div class="container">
        <div class="image-contact-field-comp">
            <div class="form-footer">
                <h2>Sign up to our mailing list</h2>
                <p>Sign up to our mailing list to get the latest news and offers.</p>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 form-input-contact">
                        <div class="mb-3 newslet">
                            <input type="text" class="form-control" id="sub_name" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 form-input-contact">
                        <div class="mb-3 newslet">
                            <input type="email" class="form-control" id="sub_email" placeholder="Email">
                        </div>
                    </div>
                    <div class="col-sm-12 sub">
                        <div class="contact-subscribe">
                            <button type="button" class="btn">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- copy right section start here -->
<div class="copy-right">
    <div class="container">
        <div class="mobile-copt-txt">
            <h2>Carp Gear Giveaway is better with our IOS / Android app</h2>
        </div>
        <div class="copy-right-all">
            <div class="row copy-right-all-align">
                <div class="col-lg-6">
                    <div class="copy-right-left">
                        <div class="copy-right-left-one">
                            <a href="https://apps.apple.com/us/app/carp-gear-giveaways/id1513020494" target="_blank">
                                <img src="<?= $frontend_s3_url ?>/images/single-comp-top.svg" alt=""> </a>
                            <a href="https://play.google.com/store/apps/details?id=co.uk.carpgeargiveaways.app&pli=1" target="_blank">
                                <img src="<?= $frontend_s3_url ?>/images/single-comp-top-1.svg" alt=""></a>
                        </div>
                        <div class="copy-right-left-two">
                            <!-- <div class="copy-right-left-social">
                                <a href="#">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M18.2437 2.25H21.5531L14.325 10.5094L22.8281 21.75H16.1719L10.9547 14.9344L4.99216 21.75H1.6781L9.40779 12.9141L1.25623 2.25H8.08123L12.7922 8.47969L18.2437 2.25ZM17.0812 19.7719H18.914L7.08279 4.125H5.11404L17.0812 19.7719Z"
                                            fill="white" />
                                    </svg>
                                </a>
                            </div> -->
                            <div class="copy-right-left-social">
                                <a
                                    href="https://www.facebook.com/carpgeargiveaways/"
                                    target="_blank">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M23.2916 12C23.2916 5.57812 18.0885 0.375 11.6666 0.375C5.24475 0.375 0.041626 5.57812 0.041626 12C0.041626 17.8022 4.29272 22.6116 9.85022 23.4844V15.3605H6.89709V12H9.85022V9.43875C9.85022 6.52547 11.5846 4.91625 14.241 4.91625C15.5132 4.91625 16.8435 5.14313 16.8435 5.14313V8.0025H15.3773C13.9335 8.0025 13.483 8.89875 13.483 9.81797V12H16.7071L16.1915 15.3605H13.483V23.4844C19.0405 22.6116 23.2916 17.8022 23.2916 12Z"
                                            fill="white" />
                                    </svg>
                                </a>
                            </div>
                            <div class="copy-right-left-social">
                                <a
                                    href="https://www.instagram.com/carpgeargiveaways/"
                                    target="_blank">
                                    <svg width="25" height="26" viewBox="0 0 25 26" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12.3386 6.8108C8.93146 6.8108 6.18324 9.57173 6.18324 12.9947C6.18324 16.4176 8.93146 19.1785 12.3386 19.1785C15.7457 19.1785 18.494 16.4176 18.494 12.9947C18.494 9.57173 15.7457 6.8108 12.3386 6.8108ZM12.3386 17.015C10.1368 17.015 8.33681 15.212 8.33681 12.9947C8.33681 10.7773 10.1315 8.97434 12.3386 8.97434C14.5457 8.97434 16.3404 10.7773 16.3404 12.9947C16.3404 15.212 14.5404 17.015 12.3386 17.015ZM20.1815 6.55785C20.1815 7.35976 19.5386 8.00021 18.7457 8.00021C17.9475 8.00021 17.31 7.35437 17.31 6.55785C17.31 5.76132 17.9529 5.11548 18.7457 5.11548C19.5386 5.11548 20.1815 5.76132 20.1815 6.55785ZM24.2582 8.02173C24.1672 6.08962 23.7279 4.37816 22.319 2.96809C20.9154 1.55802 19.2118 1.1167 17.2886 1.01982C15.3065 0.906803 9.36538 0.906803 7.38324 1.01982C5.46538 1.11132 3.76181 1.55264 2.35288 2.96271C0.943956 4.37278 0.510027 6.08423 0.413599 8.01635C0.301099 10.0077 0.301099 15.9762 0.413599 17.9676C0.50467 19.8997 0.943956 21.6111 2.35288 23.0212C3.76181 24.4313 5.46003 24.8726 7.38324 24.9695C9.36538 25.0825 15.3065 25.0825 17.2886 24.9695C19.2118 24.878 20.9154 24.4367 22.319 23.0212C23.7225 21.6111 24.1618 19.8997 24.2582 17.9676C24.3707 15.9762 24.3707 10.0131 24.2582 8.02173ZM21.6975 20.1042C21.2797 21.1591 20.4707 21.9717 19.4154 22.3969C17.835 23.0266 14.085 22.8813 12.3386 22.8813C10.5922 22.8813 6.83681 23.0212 5.26181 22.3969C4.21181 21.9771 3.40288 21.1644 2.97967 20.1042C2.35288 18.5165 2.49753 14.7492 2.49753 12.9947C2.49753 11.2401 2.35824 7.46739 2.97967 5.8851C3.39753 4.83024 4.20646 4.01757 5.26181 3.59239C6.84217 2.96271 10.5922 3.10802 12.3386 3.10802C14.085 3.10802 17.8404 2.96809 19.4154 3.59239C20.4654 4.01219 21.2743 4.82486 21.6975 5.8851C22.3243 7.47278 22.1797 11.2401 22.1797 12.9947C22.1797 14.7492 22.3243 18.5219 21.6975 20.1042Z"
                                            fill="white" />
                                    </svg>
                                </a>
                            </div>
                            <div class="copy-right-left-social">
                                <a
                                    href="https://www.tiktok.com/@carpgeargiveawaysltd"
                                    target="_blank">
                                    <svg width="24" height="26" viewBox="0 0 24 26" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M24 10.6622C21.6408 10.6675 19.3395 9.97015 17.4211 8.66852V17.7464C17.4205 19.4278 16.8783 21.0688 15.8671 22.4502C14.856 23.8316 13.424 24.8874 11.7627 25.4766C10.1013 26.0657 8.28986 26.16 6.57045 25.7469C4.85104 25.3338 3.30566 24.433 2.14095 23.1649C0.976234 21.8969 0.247702 20.3219 0.0527683 18.6508C-0.142166 16.9796 0.20579 15.2919 1.05011 13.8133C1.89443 12.3346 3.19486 11.1356 4.77753 10.3764C6.36019 9.61725 8.14965 9.33417 9.90663 9.56504V14.1309C9.10263 13.8912 8.23929 13.8984 7.43988 14.1516C6.64047 14.4047 5.94589 14.8908 5.45531 15.5404C4.96474 16.19 4.70326 16.97 4.70823 17.7689C4.71319 18.5677 4.98434 19.3447 5.48295 19.9888C5.98157 20.6329 6.68215 21.1112 7.48464 21.3554C8.28714 21.5996 9.15052 21.5972 9.95148 21.3485C10.7524 21.0998 11.45 20.6176 11.9446 19.9707C12.4392 19.3238 12.7055 18.5454 12.7055 17.7464V0H17.4211C17.4179 0.377471 17.4512 0.754428 17.5208 1.12611C17.6847 1.95582 18.0254 2.74512 18.5221 3.44574C19.0188 4.14635 19.6611 4.74355 20.4097 5.20081C21.4747 5.86829 22.7233 6.22406 24 6.2238V10.6622Z"
                                            fill="white" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="copy-right-right">
                        <a href="#" title="payment">
                            <img src="<?= $frontend_s3_url ?>/images/Payment-Icons%201.svg" alt="Payment-Icons">
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- copy right section end here -->

<div class="privacy-section-footer">
    <div class="container">
        <ul>
            <li><a href="<?= $frontend_url ?>legal-terms?tab=1" title="Competition Terms & Conditions">Competition Terms & Conditions</a></li>
            <li><a href="<?= $frontend_url ?>legal-terms?tab=2" title="Website Terms of Use">Website Terms of Use</a></li>
            <li><a href="<?= $frontend_url ?>legal-terms?tab=3" title="Privacy Policy & Cookie Policy">Privacy Policy & Cookie Policy</a></li>
            <li><a href="<?= $frontend_url ?>free-postal-route" title="Free Postal Route">Free Postal Route</a></li>
            <li><a href="<?= $frontend_url ?>faq" title="FAQ">FAQ</a></li>
            <li><a href="<?= $frontend_url ?>contact" title="Contact">Contact</a></li>

        </ul>
        <h6>Copyright ©Carp Gear Giveaways- Registered Company Number 12385280 <br>Trademarked - UK00003485092</h6>
        <h6 class="images-found">Images Found On This Website are Copyright Protected! Do NOT Download or Use For
            Commercial Use!</h6>
        <!-- <h3><a href="#">Carp Fishing Gear</a><span> From</span><a href="#">The Tackle Lounge</a></h3> -->
    </div>
</div>

<div class="bottom-nav">
    <div class="bottom-nav-all">
        <div class="bottom-nav-responsive-three">
            <a href="<?= $frontend_url ?>results">
                <button type="button" class="bottom-nav-res">
                    <svg width="33" height="19" viewBox="0 0 33 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M32.5 6.77472V3.13009C32.5 1.69059 31.2899 0.523682 29.7973 0.523682H3.20263C1.71006 0.523621 0.5 1.69053 0.5 3.13003V6.79901C1.87231 6.9891 2.92838 8.12418 2.92838 9.50016C2.92838 10.8761 1.87231 12.0113 0.5 12.2009V15.8704C0.5 17.3094 1.71006 18.4763 3.20263 18.4763H29.7973C31.2898 18.4763 32.4999 17.3094 32.4999 15.8704V12.2252C30.9983 12.1575 29.8016 10.9652 29.8016 9.50022C29.8017 8.03529 30.9984 6.84301 32.5 6.77472ZM8.09469 16.8319H7.16488V14.4934H8.09469V16.8319ZM8.09469 12.7234H7.16488V10.3849H8.09469V12.7234ZM8.09469 8.615H7.16488V6.27601H8.09469V8.615ZM8.09469 4.5066H7.16488V2.16809H8.09469V4.5066Z"
                            fill="white" />
                    </svg>
                </button>
            </a>
        </div>
        <div class="bottom-nav-responsive-three">
            <a href="<?= $frontend_url ?>competitions/instant_win_comps">
                <button type="button" class="bottom-nav-res">
                    <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.83334 19.1667L19.1667 4.5V13.8333H27.1667L13.8333 28.5V19.1667H5.83334Z"
                            fill="white" stroke="white" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>
            </a>
        </div>
        <div class="bottom-nav-responsive-three">
            <a href="<?= $frontend_url ?>cart">
                <button type="button" class="bottom-nav-res">
                    <svg width="29" height="29" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M27.3336 19.1668H4.00025L5.75025 16.8335H22.6669C23.1697 16.8335 23.6142 16.5127 23.7729 16.0355L27.2729 5.5355C27.3931 5.17967 27.3324 4.78884 27.1131 4.48434C26.8937 4.17984 26.5414 4.00017 26.1669 4.00017H5.64991L2.49175 0.842003C2.03558 0.385836 1.29825 0.385836 0.84208 0.842003C0.385913 1.29817 0.385913 2.0355 0.84208 2.49167L4.00025 5.64984V15.2772L0.73358 19.6335C0.467579 19.987 0.425579 20.4595 0.622746 20.855C0.821079 21.2505 1.22475 21.5002 1.66691 21.5002H27.3336C27.9787 21.5002 28.5002 20.9775 28.5002 20.3335C28.5002 19.6895 27.9787 19.1668 27.3336 19.1668Z"
                            fill="white" />
                        <path
                            d="M5.16683 28.5001C6.45549 28.5001 7.50016 27.4554 7.50016 26.1668C7.50016 24.8781 6.45549 23.8334 5.16683 23.8334C3.87816 23.8334 2.8335 24.8781 2.8335 26.1668C2.8335 27.4554 3.87816 28.5001 5.16683 28.5001Z"
                            fill="white" />
                        <path
                            d="M23.8335 28.5001C25.1222 28.5001 26.1668 27.4554 26.1668 26.1668C26.1668 24.8781 25.1222 23.8334 23.8335 23.8334C22.5449 23.8334 21.5002 24.8781 21.5002 26.1668C21.5002 27.4554 22.5449 28.5001 23.8335 28.5001Z"
                            fill="white" />
                    </svg>
                    <span class="bottom-add-cart"><?php echo $items_count; ?></span>
                </button>
            </a>
        </div>
    </div>
</div>
<?php
wp_footer();
$auth_token = WC()->session->get('AUTH_TOKEN_KEY', false);
if (!empty($auth_token)):
?>
    <script>
        let token = "<?php echo base64_decode($auth_token); ?>";
        let latestNonce = "<?php echo esc_js(wp_create_nonce('wc_store_api')); ?>";
        let latestNonceTime = "<?php echo esc_js(time()); ?>";

        localStorage.setItem("AUTH_TOKEN_KEY", btoa(token));
        localStorage.setItem("NONCE", latestNonce);
    </script>
<?php
    WC()->session->__unset('AUTH_TOKEN_KEY');
endif;
?>