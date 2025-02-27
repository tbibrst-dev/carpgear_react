jQuery.noConflict();
jQuery(document).ready(function () {

    jQuery('.navbar-toggler').click(function() {
        jQuery('.my-menu').toggle(); // Toggle the visibility of the menu
    });

    // Hide the menu when the close button is clicked
    jQuery('.close-btn').click(function() {
        jQuery('.my-menu').hide(); // Hide the menu
    });

    var swiper2 = new Swiper(".carp-top-bar .top-bar", {
        slidesPerView: 1,
        speed: 3000,
        spaceBetween: 10,
        freeMode: true,
        allowTouchMove: false,
        loop: true,
        autoplay: {
            delay: 0,
            disableOnInteraction: false,
        },
        breakpoints: {
            500: { slidesPerView: 1 },
            700: { slidesPerView: 1 },
            900: { slidesPerView: 2 },
            1200: { slidesPerView: 3 },
            1400: { slidesPerView: 3.5 },
        },

    });

    jQuery('.carp-top-bar .top-bar').hover(function () {
        swiper2.autoplay.stop();
    }, function () {
        swiper2.autoplay.start();
    });

    var swiper = new Swiper(".banner-Swiper", {
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });

    jQuery(".checkout-section-right-points").click(function () {
        jQuery(this).toggleClass("active");
    });

    jQuery('.pay-radio').click(function () {
        jQuery(this).toggleClass("active");
    });

   

    jQuery(".woocommerce-form-login").show();

    jQuery(".showlogin").on('click', function () {
        jQuery("#desktop_login").slideToggle(200);
        jQuery(".Account-sec").slideToggle(200);
        jQuery(".carp-login-check-one").slideToggle(200);
    });

    jQuery(".cgg_login").on("click", function (e) {

        e.preventDefault();


        let password = jQuery("#password").val();

        let username = jQuery("#username").val();

        if (username == "") {

            jQuery(".comp_checkout_notice").last().html(
                '<div class="wc-block-components-notice-banner is-error" role="alert">' +
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">' +
                '<path d="M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"></path>' +
                '</svg>' +
                '<div class="wc-block-components-notice-banner__content">' +
                '<strong>Error:</strong> Username is required.	</div>' +
                '</div>');
            return false;
        }

        if (password == "") {

            jQuery(".comp_checkout_notice").last().html(
                '<div class="wc-block-components-notice-banner is-error" role="alert">' +
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">' +
                '<path d="M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"></path>' +
                '</svg>' +
                '<div class="wc-block-components-notice-banner__content">' +
                '<strong>Error:</strong> The password field is empty.	</div>' +
                '</div>');
            return false;
        }

        var form = jQuery("<form></form>");

        // Add attributes to the form
        form.attr('method', 'post');
        form.addClass('woocommerce-form-login');

        // Find the container for login fields
        var loginContainer = jQuery('.checkout-section-login');

        // Clone the container with all its children
        var clonedContainer = loginContainer.clone(true);

        // Remove the original container
        //loginContainer.remove();

        // Append the cloned container (with all its fields) to the form
        form.append(clonedContainer);

        var loginInput = jQuery('<input/>', {
            type: 'hidden',
            name: 'login',
            value: 'Login'
        });

        // Append the login input field to the form
        form.append(loginInput);

        jQuery('body').append(form);

        // Submit the form
        form.submit();
    });


    if (jQuery("#account_username").length) {

        jQuery("#billing_email").on("focusout", function (e) {

            let bill_email = jQuery(this).val();

            jQuery("#account_username").val(bill_email);

            if (bill_email != '') {

                checkCreateAccount();
            }
        });

        jQuery("#account_password").on("focusout", function (e) {

            let newuserpwd = jQuery(this).val();

            if (newuserpwd != '') {

                checkCreateAccount();
            }
        });

    }

    function checkCreateAccount() {

        let newuserpwd = jQuery("#account_password").val();

        let newuseremail = jQuery("#account_username").val();

        if (newuserpwd != "" && newuseremail != "") {
            jQuery("#createaccount").attr("checked", true);
        }
    }

    const addEventListenerToParents = (parents) => {
        parents.forEach((parent) => {
            const id = parent.getAttribute("id");
            parent.addEventListener("click", () => addActiveClassInput(id, parents));
        });
    };

    const inputParents = document.querySelectorAll(
        ".checkout-section-right-points-onee"
    );

    const questionParents = document.querySelectorAll(".check-bait-one");

    questionParents.forEach((parent) => {

        let userAgent = window.navigator.userAgent.toLowerCase(),
            macosPlatforms = /(macintosh|macintel|macppc|mac68k|macos)/i;

        if (macosPlatforms.test(userAgent)) {

            parent.querySelector("label").style.lineHeight = '19px';
        }
    });

    addEventListenerToParents(inputParents);
    addEventListenerToParents(questionParents);

    const addActiveClassInput = (id, parents) => {
        parents.forEach((parent) => {
            const currentId = parent.getAttribute("id");
            const inputChild = parent.querySelector("input[type='radio']");

            if (currentId === id) {
                inputChild.checked = true;
                parent.classList.add("active");
            } else {
                inputChild.checked = false;
                parent.classList.remove("active");
            }
        });
    };

    jQuery("#billing_dob").datepicker({ format: "dd-mm-yyyy", autoclose: true })
        .on('change', function (e) {

            var dob = jQuery(this).val();

            var dobDate = moment(dob, "DD-MM-YYYY");

            var eighteenYearsAgo = moment().subtract(18, 'years');

            if (dobDate.isAfter(eighteenYearsAgo)) {

                jQuery("#billing_dob_field").removeClass("woocommerce-validated");

                jQuery("#billing_dob_field").addClass('woocommerce-invalid woocommerce-invalid-required-field');

                jQuery("#billing_dob").attr("style", "border-color: #a00;");

                jQuery(".comp_checkout_notice").last().html(
                    '<div class="wc-block-components-notice-banner is-error" role="alert">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">' +
                    '<path d="M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"></path>' +
                    '</svg>' +
                    '<div class="wc-block-components-notice-banner__content">' +
                    '<strong>Error:</strong> You must be over 18 years old to enter competitions.	</div>' +
                    '</div>');

            } else {
                jQuery("#billing_dob").removeAttr("style");
                jQuery(".comp_checkout_notice").last().html('');
                jQuery("#billing_dob_field").removeClass('woocommerce-invalid woocommerce-invalid-required-field');
            }

        });

    jQuery('#woo_points_pay').change(function () {
        if (jQuery(this).is(':checked')) {
            jQuery("[name='wc_points_rewards_apply_discount']").submit();
            jQuery(".checkout-section-right-points").addClass("d-none");
            jQuery(".use-point").addClass("d-none");
            jQuery(".checkout-section-right-points").addClass("active");
        } else {
            jQuery(".checkout-section-right-points").removeClass("d-none");
            jQuery(".use-point").removeClass("d-none");
            jQuery(".checkout-section-right-points").removeClass("active");
        }
    });

    jQuery(document.body).on("click", ".woocommerce-remove-coupon", function () {
        if (jQuery(".checkout-section-right-points").hasClass("active")) jQuery('#woo_points_pay').trigger("click");
    });


    jQuery("#custom_wc_coupon").on("click", function (e) {

        e.preventDefault();

        let couponCode = jQuery("#custom_coupon_code").val();

        if (couponCode == "") {

            jQuery(".comp_checkout_notice").last().html(
                '<div class="wc-block-components-notice-banner is-error" role="alert">' +
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true" focusable="false">' +
                '<path d="M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"></path>' +
                '</svg>' +
                '<div class="wc-block-components-notice-banner__content">' +
                '<strong>Error:</strong> Please enter a coupon code.	</div>' +
                '</div>');

            return false;
        }

        jQuery("#coupon_code").val(couponCode);

        jQuery("[name='apply_coupon']").click();

        return false;

        var form = jQuery("<form></form>");

        // Add attributes to the form
        form.attr('method', 'post');
        form.addClass('checkout_coupon woocommerce-form-coupon');

        // Find the container for login fields
        var couponContainer = jQuery('.checkout_coupon');

        // Clone the container with all its children
        var clonedContainer = couponContainer.clone(true);

        // Remove the original container
        //loginContainer.remove();

        // Append the cloned container (with all its fields) to the form
        form.append(clonedContainer);

        jQuery('body').append(form);

        // Submit the form
        form.submit();
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("wp-admin-bar-menu-toggle");
    if (toggleButton) {
        toggleButton.addEventListener("click", function () {
            console.log("Admin bar toggled!"); // Custom behavior
        });
    }
});
