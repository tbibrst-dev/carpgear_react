$(document).ready(function () {
    jQuery(document).on('click', '.delete_comp', function (e) {
        $("#deleteModal").modal("show");
        $("#deleteModal").find('.delete_competition').attr('href', $(this).data("url"));

    });

    jQuery(document).on('click', '.make_winners', function (e) {
        let winnerName = $(e.currentTarget).closest(".comp_winner").find(".text-content").text();
        $("#makeWinnerModal").find("#winner_name").text(winnerName);
        $("#makeWinnerModal").find("#winning_id").val($(e.currentTarget).data("id"));
        $("#makeWinnerModal").modal("show");
    });

    jQuery(".make_winner_comp").on("click", function () {

        let data = {
            action: 'make_competition_winner',
            id: $("#makeWinnerModal").find("#winning_id").val(),
            competetion_id: $("#makeWinnerModal").find("#main_competition_id").val(),
        };

        $(".show_loader").removeClass("d-none");

        $("#makeWinnerModal").modal("hide");

        jQuery.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: data,
            success: function (response) {
                $(".show_loader").addClass("d-none");
                response = JSON.parse(response);
                if (response.success) {
                    jQuery('#wpbody-content').prepend('<div class="notice notice-success update-nag inline"><p>' + response.message + '</p></div>');
                    location.reload();
                } else {
                    jQuery('#wpbody-content').prepend('<div class="notice notice-error update-nag inline"><p>' + response.message + '</p></div>');

                }
            },
        });

    });

    jQuery(".make_reward_winner").on("click", function () {

        let data = {
            action: 'make_competition_reward_winner',
            id: $("#makeWinnerModal").find("#winning_id").val(),
            competetion_id: $("#makeWinnerModal").find("#main_competition_id").val(),
            reward_id: $("#makeWinnerModal").find("#reward_id").val(),
        };

        $(".show_loader").removeClass("d-none");

        $("#makeWinnerModal").modal("hide");

        jQuery.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: data,
            success: function (response) {
                $(".show_loader").addClass("d-none");
                location.reload();
            },
        });

    });

    var itemRow = 1;

    if ($(".seo_main_content").length > 0) var itemRow = $(".seo_main_content").find(".itemRow").length;

    if ($(".hpslider_content").length > 0) var itemRow = $(".sliders_content").find(".itemRow").length;

    if ($(".global_content_statstics").length > 0) {

        $(".manageStaticWinners").on("click", function (e) {
            let data = {
                action: 'update_statistics_winner_prize',
            };

            data.winner_stat = $("[name='winner_stat']").val();
            data.prizes_stat = $("[name='prizes_stat']").val();
            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: data,
                success: function (response) {
                    console.log(response);

                    alert('Records Saved successfully');
                    location.reload();
                },
            });
           


        })


        $(".manageStaticCharity").on("click", function (e) {
            let data = {
                action: 'update_statistics_charity_followrs',
            };

            data.donated_stat = $("[name='donated_stat']").val();
            data.followers_stat = $("[name='followers_stat']").val();

            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: data,
                success: function (response) {
                    console.log(response);
                    alert('Records Saved successfully');
                    location.reload();
                },
            });

            
        })

        $(".managePinnedMessage").on("click", function () {
            let data = {
                action: 'update_cometchat_pinned_message',
                pinned_message: $("[name='pinnedMessage']").val(),
                show_pinned_message: $("#showpinnedMessage").is(":checked") ? 1 : 0,
            };


            console.log('data++++',data);
        
            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajax_url, // Ensure this is set in your WordPress enqueue
                data: data,
                success: function (response) {
                    console.log(response);
                    alert('Records Saved successfully');
                    location.reload();
                },
                error: function (error) {
                    console.error("Error saving pinned message:", error);
                    alert('An error occurred. Please try again.');
                },
            });
        });
        


    }


    if ($(".global_content").length > 0) {

        CKEDITOR.tools.enableHtml5Elements(document);
        CKEDITOR.config.height = 150;
        CKEDITOR.config.width = 'auto';
        CKFinder.setupCKEditor();

        CKEDITOR.config.toolbar = [
            { name: 'document', items: ['Source'] },
            { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            { name: 'basicstyles', items: ['Bold', 'Italic', '|', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
            { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
            { name: 'insert', items: ['Image', 'HorizontalRule', 'PageBreak'] },
        ];

        CKEDITOR.replace('live_draw_info');

        CKEDITOR.replace('postal_entry_info');

        CKEDITOR.replace('main_competition');

        CKEDITOR.replace('instant_wins_info');

        CKEDITOR.replace('reward_prize_info');

        CKEDITOR.replace('competition_rules');

        CKEDITOR.replace('competition_faq');
        CKEDITOR.replace('pinnedMessage');

        CKEDITOR.replace('work_step_1');
        CKEDITOR.replace('work_step_2');
        CKEDITOR.replace('work_step_3');

        CKEDITOR.replace('announcement');

        $(".save_global_settings").on("click", function (e) {

            let data = {
                action: 'update_global_settings',
            };

            data.live_draw_info = CKEDITOR.instances.live_draw_info.getData();
            data.postal_entry_info = CKEDITOR.instances.postal_entry_info.getData();
            data.main_competition = CKEDITOR.instances.main_competition.getData();
            data.instant_wins_info = CKEDITOR.instances.instant_wins_info.getData();
            data.reward_prize_info = CKEDITOR.instances.reward_prize_info.getData();
            data.work_step_1 = CKEDITOR.instances.work_step_1.getData();
            data.work_step_2 = CKEDITOR.instances.work_step_2.getData();
            data.work_step_3 = CKEDITOR.instances.work_step_3.getData();
            data.slider_speed = $("[name='slider_speed']").val();
            data.suggested_tickets = $("[name='suggested_tickets']").val();
            data.announcement = CKEDITOR.instances.announcement.getData();
            data.competition_rules = CKEDITOR.instances.competition_rules.getData();
            data.competition_faq = CKEDITOR.instances.competition_faq.getData();

            // data.winner_stat = $("[name='winner_stat']").val();
            // data.prizes_stat = $("[name='prizes_stat']").val();
            // data.donated_stat = $("[name='donated_stat']").val();
            // data.followers_stat = $("[name='followers_stat']").val();
            data.manageScripts = $("[name='manageScripts']").val();


            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: data,
                success: function (response) {
                    location.reload();
                },
            });
        });




    }

    if ($(".seo_content").length > 0) {

        checkLineItemRow();

        $("#add_more").on("click", function () {

            let newRow = $(".seo_main_content").find(".itemRowclone").clone(true);

            newRow.removeClass("itemRowclone").addClass("itemRow").removeClass("d-none");

            var newRowNum = getLineItemNextRowNumber();

            updateRowNumberForRow(newRow, newRowNum);

            console.log(newRow);

            newRow.appendTo($(".seo_main_content"));

            checkLineItemRow();

        });

        $(".seo_main_content").on('click', '.delete_item_row', function (e) {
            var element = jQuery(e.currentTarget);
            element.closest('.itemRow').remove();
            checkLineItemRow();
        });


        $(".save_seo_settings").on("click", function (e) {

            updateItemElementByOrder();

            let data = {
                action: 'update_seo_settings',
            };

            $("#total_seo_pages").val($(".seo_main_content").find(".itemRow").length);

            let formData = $("#seo_settings").serializeArray();

            $(formData).each(function (index, field) {
                data[field.name] = field.value;
            });

            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: data,
                success: function (response) {
                    location.reload();
                },
            });
        });

    }

    if ($(".question_content").length > 0) {

        if ($("#savQuestionContent").length > 0) {

            $("#savQuestionContent").validate({
                ignore: [],
                rules: {
                    'question': {
                        required: function (element) {
                            return true;
                        }
                    },
                    'answer1': {
                        required: function (element) {
                            return true;
                        }
                    },
                    'answer2': {
                        required: function (element) {
                            return true;
                        }
                    },
                    'answer3': {
                        required: function (element) {
                            return true;
                        }
                    }
                }
            });


            $("#savQuestionContent").on("submit", function (e) {

                if ($("#savQuestionContent").valid()) {

                    if ($('.correct-answer:checked').length == 0) {

                        if (!$("#answer1").closest("div").find("#correct-answer-error").length) {
                            $("#answer1").closest("div").append('<label id="correct-answer-error" class="error" for="correct_answer">At least one answer is required to be selected as correct</label>');
                        } else {
                            $("#answer1").closest("div").find("#correct-answer-error").text('At least one answer is required to be selected as correct').show();
                        }

                        e.preventDefault();
                    }
                }
            });

            $('.correct-answer', $("#savQuestionContent")).on('change', function () {
                $('.correct-answer').not(this).prop('disabled', this.checked);
                $('.form-control.correct-answer').not($(this).closest('.mb-3').find('.form-control.correct-answer')).prop('disabled', this.checked);
                $(".slider_label").removeClass("selected_answer");
                if (this.checked) {
                    $(this).closest('.form-switch').find(".slider_label").addClass("selected_answer");
                }
            });
        }

        if ($("#globalQuestionSetting").length) {


            $('#globalQuestionSetting').on('click', function () {

                let show_question = 0;

                if ($("#globalQuestionSetting").prop('checked') == true) show_question = 1;

                if (show_question) {
                    $("#question-status-div-enabled").removeClass('hide');
                } else {
                    $("#question-status-div-disabled").removeClass('hide');

                }

                let params = {
                    action: "save_global_question_settings",
                    "show_question": show_question
                };

                jQuery.ajax({
                    type: "POST",
                    url: ajax_object.ajax_url,
                    data: params,
                    success: function (response) {
                        $("#question-status-div-enabled").addClass('hide');
                        $("#question-status-div-disabled").addClass('hide');


                    }
                });

            });

        }
    }

    if ($("#dropdownExportBtn").length) {

        $(".dropdown").on("click", ".dropdown-item", function (e) {
            e.preventDefault();
            let url = window.location.href;
            url += '&' + $(this).data("type") + '=1&download_csv=1';
            window.location.href = url;
        });
    }


    if ($("#dropdownExportBtnInstant").length) {

        $(".dropdown").on("click", ".dropdown-item-instant", function (e) {
            e.preventDefault();
            let url = window.location.href;
            url += '&' + $(this).data("type") + '=1&download_csv_instant=1';
            window.location.href = url;
        });
    }


    function getLineItemNextRowNumber() {

        return ++itemRow;
    }

    function updateRowNumberForRow(lineItemRow, expectedSequenceNumber, currentSequenceNumber) {

        if (typeof currentSequenceNumber == 'undefined') {
            currentSequenceNumber = 0;
        }

        let idFields = new Array('page', 'page_title', 'meta_title', 'meta_description');

        let expectedRowId = 'row' + expectedSequenceNumber;

        for (let idIndex in idFields) {
            let elementId = idFields[idIndex];
            let actualElementId = elementId + currentSequenceNumber;
            let expectedElementId = elementId + expectedSequenceNumber;
            lineItemRow.find('#' + actualElementId).attr('id', expectedElementId)
                .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);
        }

        lineItemRow.attr('id', expectedRowId).attr('data-row', expectedSequenceNumber);

        lineItemRow.find('input.rowNumber').val(expectedSequenceNumber);

        return lineItemRow;
    }

    function checkLineItemRow() {

        let numRows = $(".seo_main_content").find(".itemRow").length;

        if (numRows > 1) {
            showItemsDeleteIcon();
        } else {
            hideItemsDeleteIcon();
        }

    }

    function showItemsDeleteIcon() {

        $(".seo_main_content").find('.deleteRow').show();
    }

    function hideItemsDeleteIcon() {

        $(".seo_main_content").find('.deleteRow').hide();
    }

    function updateItemElementByOrder() {

        let lineItems = $(".seo_main_content").find(".itemRow");

        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var expectedRowIndex = (index + 1);
            var expectedRowId = 'row' + expectedRowIndex;
            var actualRowId = lineItemRow.attr('id');
            if (expectedRowId != actualRowId) {
                var actualIdComponents = actualRowId.split('row');
                updateRowNumberForRow(lineItemRow, expectedRowIndex, actualIdComponents[1]);
            }
        });
    }

    if ($(".competitions_content").length > 0) {

        $(".add_tickets").on("click", function (e) {

            let currentElem = $(e.currentTarget);

            let compData = currentElem.data();

            let newValue = $(this).closest("div").find(".add_tickets_input").val();

            let InputValue = parseInt(newValue);
            let TicketToAdd = 0;
            let PointsToAdd = 0;
            let RemainingTickets = compData.remaining ? compData.remaining : 0;
            let MaxTicketsPerPerson = compData.maxTicketsPerPerson;
            let AlreadyHaveTickets = compData.purchased;


            // if no ticket add in input
            if (InputValue == '' || InputValue == 0) {
                alert("Please add some value to add tickets");
                return true;
            }

            //if competiton is closed or finished
            if (compData.status == 'Closed' || compData.status == 'Finished') {
                alert("Oops! You cannot buy for " + compData.status + " Competition");
                return true;
            }


            //if reamining tickets not availbale
            if (RemainingTickets == 0) {
                alert("Oops! It seems all tickets are sold for this competition. We will credit your account with points for each ticket over the limit.");
                TicketToAdd = 0;
                PointsToAdd = InputValue;
            }

            //if reamining tickets are available greater then added tickets
            if (RemainingTickets >= InputValue) {

                if (AlreadyHaveTickets >= MaxTicketsPerPerson) {
                    alert("Oops! It seems you've already bought " + compData.purchased + " tickets for this competition. We will credit your account with points for each ticket over the limit.");
                    PointsToAdd = InputValue;
                    TicketToAdd = 0;
                }

                let ticketsCanbeAdd = MaxTicketsPerPerson - AlreadyHaveTickets;
                if (ticketsCanbeAdd == 0) {
                    alert("Oops! It seems you've already bought " + compData.purchased + " tickets for this competition. We will credit your account with points for each ticket over the limit.");
                    PointsToAdd = InputValue;
                    TicketToAdd = 0;
                }


            }

            //if reamining tickets are less then available  added tickets
            if (RemainingTickets < InputValue && RemainingTickets > 0) {

                if (AlreadyHaveTickets >= MaxTicketsPerPerson) {
                    alert("Oops! It seems you've already bought " + compData.purchased + " tickets for this competition. We will credit your account with points for each ticket over the limit.");
                    PointsToAdd = InputValue;
                    TicketToAdd = 0;
                }

                let ticketsCanbeAdd = MaxTicketsPerPerson - AlreadyHaveTickets;
                if (ticketsCanbeAdd == 0) {
                    alert("Oops! It seems you've already bought " + compData.purchased + " tickets for this competition. We will credit your account with points for each ticket over the limit.");
                    PointsToAdd = InputValue;
                    TicketToAdd = 0;
                } else {

                    if (RemainingTickets >= ticketsCanbeAdd) {
                        TicketToAdd = ticketsCanbeAdd;
                        PointsToAdd = MaxTicketsPerPerson - TicketToAdd;
                        alert("Oops! It seems you've already bought " + compData.purchased + " tickets for this competition. We will credit your account with points for each ticket over the limit.");
                    } else {

                        TicketToAdd = ticketsCanbeAdd - RemainingTickets;
                        PointsToAdd = (MaxTicketsPerPerson - TicketToAdd) + RemainingTickets;
                        alert("Oops! It seems you've already bought " + compData.purchased + " tickets for this competition. We will credit your account with points for each ticket over the limit.");
                    }



                }


            }
            let params = {
                competition_id: compData.id,
                ticketsToAdd: TicketToAdd,
                pointsToAdd: PointsToAdd,
                user: compData.user,
                action: "add_tickets_to_competition"
            };

            console.log('params++++', params)

            $(".show_loader").removeClass("d-none");

            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: params,
                success: function (response) {
                    $(".show_loader").addClass("d-none");
                    location.reload();
                },
            });

        });

    }

    if ($(".hpslider_content").length > 0) {

        checkSliderLineItemRow();

        $("#add_slide").on("click", function (e) {

            e.preventDefault();

            let newRow = $(".sliders_content").find(".itemRowCloneCopy").clone(true);

            newRow.removeClass("itemRowCloneCopy").addClass("itemRow").removeClass("d-none");

            newRow.find(".wp_media_preview .desktop_image").addClass("wp_media_url");

            newRow.find(".wp_media_preview .mobile_image").addClass("wp_media_url");

            let newRowNum = getLineItemNextRowNumber();

            updateRowNumberForSliderRow(newRow, newRowNum);

            newRow.find(".slider_title").attr('required', true);
            newRow.find(".link").attr('required', true);
            newRow.find(".btn_text").attr('required', true);
            newRow.find(".wp_media_url").attr('required', true);

            newRow.appendTo($(".sliders_content"));

            checkSliderLineItemRow();

            registerSliderWPMediaFrame(newRow);

        });

        $(".sliders_content").on('click', '.delete_item_row', function (e) {
            let element = jQuery(e.currentTarget);
            element.closest('.itemRow').remove();
            checkSliderLineItemRow();
        });

        $(".sliders_content").find(".itemRow").each(function (index, elem) {

            registerSliderWPMediaFrame($(elem));

        });

        jQuery.validator.addClassRules({
            slider_title: {
                required: true
            },
            link: {
                required: true
            },
            btn_text: {
                required: true
            },
            wp_media_url: {
                required: true
            }
        });


        $("#slider_settings").validate(
            { ignore: ":hidden:not(.wp_media_url)" }
        );

        $(".save_hp_slider_settings").on("click", function (e) {

            e.preventDefault();

            if (!$("#slider_settings").valid()) {

                return true;
            }

            updateSliderItemElementByOrder();

            let data = {
                action: 'save_slider_settings',
            };

            $("#total_slides").val($(".sliders_content").find(".itemRow").length);

            let formData = $("#slider_settings").serializeArray();

            $(formData).each(function (index, field) {
                data[field.name] = field.value;
            });

            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: data,
                success: function (response) {
                    location.reload();
                },
            });
        });

        function updateRowNumberForSliderRow(lineItemRow, expectedSequenceNumber, currentSequenceNumber) {

            if (typeof currentSequenceNumber == 'undefined') {
                currentSequenceNumber = 0;
            }

            let idFields = new Array('slider_title', 'sub_title', 'link', 'btn_text', 'desktop_image', 'mobile_image');

            let expectedRowId = 'row' + expectedSequenceNumber;

            for (let idIndex in idFields) {

                let elementId = idFields[idIndex];

                let actualElementId = elementId + currentSequenceNumber;

                let expectedElementId = elementId + expectedSequenceNumber;

                lineItemRow.find('#' + actualElementId).attr('id', expectedElementId)
                    .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);

                if (elementId == 'desktop_image') {
                    lineItemRow.find(".desktop-image-container").find('.desktop_image').attr('name', expectedElementId);
                }

                if (elementId == 'mobile_image') {
                    lineItemRow.find(".mobile-image-container").find('.mobile_image').attr('name', expectedElementId);
                }
            }

            lineItemRow.attr('id', expectedRowId).attr('data-row', expectedSequenceNumber);

            lineItemRow.find('input.rowNumber').val(expectedSequenceNumber);

            return lineItemRow;
        }

        function checkSliderLineItemRow() {

            let numRows = $(".sliders_content").find(".itemRow").length;

            if (numRows > 1) {
                showSliderItemsDeleteIcon();
            } else {
                hideSliderItemsDeleteIcon();
            }

        }

        function showSliderItemsDeleteIcon() {

            $(".sliders_content").find('.deleteRow').show();
        }

        function hideSliderItemsDeleteIcon() {

            $(".sliders_content").find('.deleteRow').hide();
        }

        function updateSliderItemElementByOrder() {

            let lineItems = $(".sliders_content").find(".itemRow");

            lineItems.each(function (index, domElement) {
                var lineItemRow = jQuery(domElement);
                var expectedRowIndex = (index + 1);
                var expectedRowId = 'row' + expectedRowIndex;
                var actualRowId = lineItemRow.attr('id');
                if (expectedRowId != actualRowId) {
                    var actualIdComponents = actualRowId.split('row');
                    updateRowNumberForSliderRow(lineItemRow, expectedRowIndex, actualIdComponents[1]);
                }
            });
        }

        function registerSliderWPMediaFrame(lineItemRow) {

            lineItemRow.find(".wp_media_frame").on("click", function (e) {

                e.preventDefault();

                let currentElem = $(e.currentTarget);

                var frame = wp.media({
                    title: 'Select or Upload Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                });

                frame.on('select', function () {
                    var attachment = frame.state().get('selection').first().toJSON();
                    currentElem.addClass("d-none");
                    currentElem.closest(".wp_media_container").find(".btn-upload").addClass("d-none");
                    currentElem.closest(".wp_media_container").find(".wp_media_preview").removeClass("d-none");
                    currentElem.closest(".wp_media_container").find(".wp_media_preview .img-content").html('<img src="' + attachment.url + '" alt="" width="150px" height="150px">');
                    currentElem.closest(".wp_media_container").find(".wp_media_url").val(attachment.url);
                    currentElem.closest(".wp_media_container").find(".wp_media_url").valid();
                });
                frame.open();
            });

        }

        jQuery(".remove_desktop_image, .remove_mobile_image").on("click", function (e) {

            e.preventDefault();

            let currentElem = $(e.currentTarget);

            currentElem.closest(".wp_media_preview").addClass("d-none");

            currentElem.closest(".wp_media_container").find(".wp_media_preview .img-content").html("");

            currentElem.closest(".wp_media_container").find(".wp_media_url").val("");

            currentElem.closest(".wp_media_container").find(".btn-upload").removeClass("d-none");

            currentElem.closest(".wp_media_container").find(".wp_media_frame").removeClass("d-none");
        });

    }

    const inputs = document.querySelectorAll('.number_statics_stats');

    inputs.forEach(function (input) {
        input.addEventListener('input', function (e) {
            // Remove any non-numeric characters (except commas)
            let value = e.target.value.replace(/[^\d]/g, '');

            // Format the number with commas
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            e.target.value = value;
        });
    });


});