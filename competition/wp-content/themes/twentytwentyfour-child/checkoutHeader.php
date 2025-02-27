<?php

global $current_user, $wp, $wpdb;

//$items_count = ($_REQUEST['item_count']) ? $_REQUEST['item_count'] : 0;
$frontend_url = FRONTEND_URL;
$frontend_s3_url = FRONTEND_S3_URL;

$items_count = WC()->cart->get_cart_contents_count();

$currentUser = wp_get_current_user();

$myAccount = "";

if (is_user_logged_in()) {
    $myAccount = "my_account";
}

$recordData = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}global_settings LIMIT 1", ARRAY_A);

$announcement = '';

if (!empty($recordData['announcement']))
    $announcement = html_entity_decode(stripslashes($recordData['announcement']), ENT_QUOTES, 'UTF-8');

?>
<section class="carp-top-bar">
    <div class="autoplay-section-all animation-top-line">
        <div class="store-notice">
            <div class="ticker-wrap">
                <div class="ticker" id="marquee">
                    <?php
                    $itemCount = 9; // Only render one set of items
                    for ($i = 0; $i < $itemCount; $i++) {
                        echo '<div class="top-bar-txt">';
                        echo "<h4>";
                        echo $announcement;
                        echo "</h4>";
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- top-bar-end -->


<!-- header-section-start -->
<section class="carp-header">
    <div class="container-fluid">
        <div class="car-header-all">
            <nav class="navbar navbar-expand-lg navbar-light ">
                <div class="container-fluid">
                    <a class="navbar-brand" href="<?= $frontend_url ?>">
                        <img src="<?= $frontend_s3_url ?>/images/CGG-Logo-High-Res.png" alt="" width="219px" height="70px">
                    </a>
                    <div class="head-item">
                        <div class="mobile-show">
                            <button type="button" class="notification <?php echo htmlspecialchars($myAccount); ?>" id="notification"
                                onclick="window.location.href='<?php echo $frontend_url; ?>account';">

                                <span class="cart-main-num">
                                    <svg width="24" height="28" viewBox="0 0 24 28" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M14.3333 16.2989H9.66665C7.19215 16.3017 4.81979 17.2859 3.07005 19.0356C1.32032 20.7854 0.336092 23.1577 0.333313 25.6322C0.333153 25.8796 0.411609 26.1206 0.557353 26.3204C0.703097 26.5203 0.908586 26.6686 1.14415 26.7441C4.68713 27.6607 8.34189 28.0719 12 27.9656C15.6581 28.0719 19.3128 27.6607 22.8558 26.7441C23.0914 26.6686 23.2969 26.5203 23.4426 26.3204C23.5884 26.1206 23.6668 25.8796 23.6666 25.6322C23.6639 23.1577 22.6796 20.7854 20.9299 19.0356C19.1802 17.2859 16.8078 16.3017 14.3333 16.2989Z"
                                            fill="white" />
                                        <path
                                            d="M12 13.9656C15.6166 13.9656 18.4166 9.91723 18.4166 6.43473C18.4166 4.73293 17.7406 3.10082 16.5372 1.89746C15.3339 0.694106 13.7018 0.0180664 12 0.0180664C10.2982 0.0180664 8.66607 0.694106 7.46271 1.89746C6.25935 3.10082 5.58331 4.73293 5.58331 6.43473C5.58331 9.91723 8.38331 13.9656 12 13.9656Z"
                                            fill="white" />
                                    </svg>
                                    <div class="cart-number d-none">
                                        <?php echo $items_count; ?>
                                    </div>
                                </span>
                            </button>
                        </div>
                        <button class="navbar-toggler" type="button"
                            data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <!-- open the header menu popup -->
                        <div class="my-menu">
                            <div class="responsive-menu">
                                <button class="close-btn">×</button>
                                <ul>
                                    <li class="nav-item main-menu">
                                        <a class="nav-link" href="<?= $frontend_url ?>">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= $frontend_url ?>competitions/all">Comps</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= $frontend_url ?>competitions/instant_win_comps">INSTANTLY WIN</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= $frontend_url ?>results">Results</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= $frontend_url ?>winners_list">Winners</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= $frontend_url ?>contact">Contact</a>
                                    </li>
                                </ul>
                                <div class="mobile-copt-txt">
                                    <h2>Download OuR app</h2>
                                </div>
                                <div class="copy-right-left">
                                    <div class="copy-right-left-one">
                                        <a href="https://apps.apple.com/us/app/carp-gear-giveaways/id1513020494" target="_blank">
                                            <img src="<?= $frontend_s3_url ?>/images/single-comp-top.svg" alt=""> </a>
                                        <a href="https://play.google.com/store/apps/details?id=co.uk.carpgeargiveaways.app&pli=1" target="_blank">
                                            <img src="<?= $frontend_s3_url ?>/images/single-comp-top-1.svg" alt=""></a>
                                    </div>

                                </div>
                                <div class="copy-right-right"><a href="#" title="payment"> <img src="<?= $frontend_s3_url ?>/images/Payment-IconsFooter3x.png" alt="Payment-Icons" width="211px" height="26px"></a></div>
                            </div>
                        </div>


                    </div>
                    <div class="collapse navbar-collapse" id="navbarScroll">
                        <ul class="navbar-nav me-auto my-2 my-lg-0 navbar-nav-scroll">
                            <li class="nav-item main-menu">
                                <a class="nav-link" href="<?= $frontend_url ?>">Home</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="<?= $frontend_url ?>competitions/all">Comps</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link"
                                    href="<?= $frontend_url ?>competitions/instant_win_comps">INSTANTLY WIN</a>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link" href="<?= $frontend_url ?>results">Results</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $frontend_url ?>winners_list">Winners</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $frontend_url ?>contact">Contact</a>
                            </li>
                        </ul>

                        <div class="header-shop-btns">
                            <button type="button" class="notification resp-hidden <?php echo $myAccount; ?>"
                                id="notification-click">
                                <span class="cart-main-num">
                                    <svg width="24" height="28" viewBox="0 0 24 28" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M14.3333 16.2989H9.66665C7.19215 16.3017 4.81979 17.2859 3.07005 19.0356C1.32032 20.7854 0.336092 23.1577 0.333313 25.6322C0.333153 25.8796 0.411609 26.1206 0.557353 26.3204C0.703097 26.5203 0.908586 26.6686 1.14415 26.7441C4.68713 27.6607 8.34189 28.0719 12 27.9656C15.6581 28.0719 19.3128 27.6607 22.8558 26.7441C23.0914 26.6686 23.2969 26.5203 23.4426 26.3204C23.5884 26.1206 23.6668 25.8796 23.6666 25.6322C23.6639 23.1577 22.6796 20.7854 20.9299 19.0356C19.1802 17.2859 16.8078 16.3017 14.3333 16.2989Z"
                                            fill="white" />
                                        <path
                                            d="M12 13.9656C15.6166 13.9656 18.4166 9.91723 18.4166 6.43473C18.4166 4.73293 17.7406 3.10082 16.5372 1.89746C15.3339 0.694106 13.7018 0.0180664 12 0.0180664C10.2982 0.0180664 8.66607 0.694106 7.46271 1.89746C6.25935 3.10082 5.58331 4.73293 5.58331 6.43473C5.58331 9.91723 8.38331 13.9656 12 13.9656Z"
                                            fill="white" />
                                    </svg>
                                    <div class="cart-number-comp d-none">
                                        0
                                    </div>
                                </span>
                            </button>
                            <div class="top-basket-pop">
                                <!-- Button trigger modal -->
                                <button type="button" class="notification" id="cart_page">
                                    <span class="cart-items">
                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M26.8336 18.6668H3.50025L5.25025 16.3335H22.1669C22.6697 16.3335 23.1142 16.0127 23.2729 15.5355L26.7729 5.0355C26.8931 4.67967 26.8324 4.28884 26.6131 3.98434C26.3937 3.67984 26.0414 3.50017 25.6669 3.50017H5.14991L1.99175 0.342003C1.53558 -0.114164 0.798246 -0.114164 0.342079 0.342003C-0.114087 0.79817 -0.114087 1.5355 0.342079 1.99167L3.50025 5.14984V14.7772L0.233579 19.1335C-0.0324205 19.487 -0.0744205 19.9595 0.122746 20.355C0.321079 20.7505 0.724746 21.0002 1.16691 21.0002H26.8336C27.4787 21.0002 28.0002 20.4775 28.0002 19.8335C28.0002 19.1895 27.4787 18.6668 26.8336 18.6668Z"
                                                fill="white" />
                                            <path
                                                d="M4.66683 28.0001C5.95549 28.0001 7.00016 26.9554 7.00016 25.6668C7.00016 24.3781 5.95549 23.3334 4.66683 23.3334C3.37816 23.3334 2.3335 24.3781 2.3335 25.6668C2.3335 26.9554 3.37816 28.0001 4.66683 28.0001Z"
                                                fill="white" />
                                            <path
                                                d="M23.3335 28.0001C24.6222 28.0001 25.6668 26.9554 25.6668 25.6668C25.6668 24.3781 24.6222 23.3334 23.3335 23.3334C22.0449 23.3334 21.0002 24.3781 21.0002 25.6668C21.0002 26.9554 22.0449 28.0001 23.3335 28.0001Z"
                                                fill="white" />
                                        </svg>
                                        <div class="cart-item-num"><?php echo $items_count; ?></div>
                                    </span>
                                </button>


                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</section>
<!-- header-section-end -->

<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script>

</script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const marquee = document.getElementById('marquee');
        let clone = marquee.innerHTML;

        // Clone the announcement for continuous scrolling
        marquee.innerHTML += clone;

        // Set marquee speed
        let marqueeSpeed = 0.45; // Adjust speed here (pixels per frame)
        let marqueeOffset = 0;

        function animateMarquee() {
            marqueeOffset -= marqueeSpeed;

            // Reset position when the first clone has fully scrolled out
            if (Math.abs(marqueeOffset) >= marquee.scrollWidth / 2) {
                marqueeOffset = 0;
            }

            marquee.style.transform = `translateX(${marqueeOffset}px)`;
            requestAnimationFrame(animateMarquee);
        }

        animateMarquee();
    });


    var frontend_url = "<?= $frontend_url ?>";
    document.querySelector("#notification-click").onclick = function () {
        if (this.classList.contains("my_account")) {
            location.href = frontend_url+"account";
        } else {
            location.href = frontend_url+"auth/login";
        }
    };

    document.getElementById("cart_page").onclick = function () {
        location.href = frontend_url+"cart";
    };
</script>