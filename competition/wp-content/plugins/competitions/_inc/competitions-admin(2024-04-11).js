/*CKEDITOR.config.toolbar = [
    { name: 'document', items: ['Source'] },
    { name: 'styles', items: ['Format'] },
    { name: 'basicstyles', items: ['Bold', 'Italic'] },
    { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
    // { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
    { name: 'insert', items: ['EasyImageUpload', 'HorizontalRule', 'PageBreak'] },

];*/

CKEDITOR.tools.enableHtml5Elements(document);
CKEDITOR.config.height = 450;
CKEDITOR.config.width = 'auto';
//CKEDITOR.config.extraPlugins = 'easyimage';
CKFinder.setupCKEditor();

$(document).ready(function () {

    var competitionEditor = {};

    var selectedGalleryImageUrls = [];

    var currentActiveTab = false;

    CKEDITOR.replace('description_editor');
    competitionEditor.description_editor = CKEDITOR.instances.description_editor;

    CKEDITOR.replace('live_draw_info');
    competitionEditor.live_draw_info = CKEDITOR.instances.live_draw_info;


    CKEDITOR.replace('rule_editor');
    competitionEditor.rule_editor = CKEDITOR.instances.rule_editor;

    CKEDITOR.replace('faq_editor');
    competitionEditor.faq_editor = CKEDITOR.instances.faq_editor;

    /**
     * CKEDITOR.replace('description_editor',{
        filebrowserBrowseUrl: 'https://development.brstdev.com/competition/wp-content/plugins/competitions/_inc/ckfinder/ckfinder.html',
        filebrowserImageBrowseUrl: 'https://development.brstdev.com/competition/wp-content/plugins/competitions/_inc/ckfinder/ckfinder.html?type=Images',
        filebrowserUploadUrl: 'https://development.brstdev.com/competition/wp-content/plugins/competitions/_inc/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl: 'https://development.brstdev.com/competition/wp-content/plugins/competitions/_inc/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images'
    });
     */
    // competitionEditor.rule_editor = CKEDITOR.document.querySelector('#rule_editor');
    // competitionEditor.faq_editor = CKEDITOR.document.querySelector('#faq_editor');
    // competitionEditor.live_draw_info = CKEDITOR.document.querySelector('#live_draw_info');

    /*ClassicEditor.create(document.querySelector('#description_editor'), {
        // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
    }).then(editor => {
        competitionEditor.description_editor = editor;
    });

    ClassicEditor.create(document.querySelector('#rule_editor'), {
        // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
    }).then(editor => {
        competitionEditor.rule_editor = editor;
    });

    ClassicEditor.create(document.querySelector('#faq_editor'), {
        // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
    }).then(editor => {
        competitionEditor.faq_editor = editor;
    });

    ClassicEditor.create(document.querySelector('#live_draw_info'), {
        // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
    }).then(editor => {
        competitionEditor.live_draw_info = editor;
    });*/

    $("a[data-bs-toggle='tab']").on('show.bs.tab', function (event) {

        let currentTab = $(event.target).attr("id");

        let relatedTab = $(event.relatedTarget).attr("id");

        let mode = $("[name='mode']").val();

        if (mode == 'edit') {

            return saveTempData(relatedTab);

        }

        return true;

    });


    $("a[data-bs-toggle='tab']").on('shown.bs.tab', function (event) {

        let currentTab = $(event.target).attr("id");

        let relatedTab = $(event.relatedTarget).attr("id");

        let mode = $("[name='mode']").val();

        if (mode == 'edit') {

            $('.nav-tabs').find(".nav-link").removeClass("active-lighter").addClass("tab-brand");

            $('.nav-tabs').find(".active").removeClass("tab-brand").addClass("active-lighter");

            return false;
        }

        if (currentTab == 'reward-tab') {

            $("#publish_btn").removeClass("d-none");
            $("#next_btn").addClass("d-none");

        } else {

            $("#publish_btn").addClass("d-none");
            $("#next_btn").removeClass("d-none");

        }

        if (currentTab == 'details-tab') {
            $("#back_btn").addClass("d-none");
            $("#cancel_btn").removeClass("d-none");
        } else {
            $("#back_btn").removeClass("d-none");
            $("#cancel_btn").addClass("d-none");
        }
    });


    $("#back_btn").on("click", function () {

        let currentTab = $('.nav-tabs .nav-link.active');

        let currentNavItem = currentTab.closest('.nav-item');

        let prevNavItem = currentNavItem.prev('.nav-item');

        if (prevNavItem.length > 0) {

            let prevTabLink = prevNavItem.find('.nav-link');

            prevTabLink.tab('show');
        }
    });

    $('.datepicker').each(function (index, dateElem) {

        $(dateElem).datepicker({
            dateFormat: 'dd/mm/yy',
            minDate: 0,
            onSelect: function (dateText, inst) {
                $(dateElem).valid();
            }
        });

    });

    $('.show_time').each(function (index, timeElem) {

        $(timeElem).timepicker({
            timeFormat: "H:i"
        });

    });

    $("#createBasicCompetition").validate({
        submitHandler: function () {
            $("div.error").hide();
            return false;
        },
        debug: false
    });

    $("#saveLegalsContent").validate({
        //ignore: [],
        submitHandler: function () {
            $("div.error").hide();
            return false;
        },
        debug: false
    });

    $("#savProductsContent").validate({
        ignore: [],
        submitHandler: function () {
            $("div.error").hide();
            return false;
        },
        debug: false
    });

    $("#savQuestionContent").validate({
        ignore: [],
        rules: {
            'question': {
                required: function (element) {
                    return $("#globalcustom").prop("checked");
                }
            },
            'answer1': {
                required: function (element) {
                    return $("#globalcustom").prop("checked");
                }
            },
            'answer2': {
                required: function (element) {
                    return $("#globalcustom").prop("checked");
                }
            },
            'answer3': {
                required: function (element) {
                    return $("#globalcustom").prop("checked");
                }
            },
            // 'answer_1': {
            //     require_from_group: [1, ".correct-answer"]
            // },
            // 'answer_2': {
            //     require_from_group: [1, ".correct-answer"]
            // },
            // 'answer_3': {
            //     require_from_group: [1, ".correct-answer"]
            // }
        }
    });

    jQuery.validator.addClassRules({
        prize_title: {
            required: true
        },
        prize_type: {
            required: true
        },
        prize_qty: {
            required: true
        },
        prize_image: {
            required: true
        },
        prct_available: {
            required: true
        },
        instant_tickets: {
            required: true
        }
    });

    $("#saveInstantContent").validate(
        { ignore: ":hidden:not(.prize_image)" }
    );

    $("#saveRewardContent").validate(
        { ignore: ":hidden:not(.prize_image)" }
    );

    $(".create_competition").on('click', function (e) {

        let btn_id = $(e.currentTarget).attr("id");

        var isValid = true;

        if (btn_id == 'publish_btn') {

            let comp_record = $("#create-comp-content").find("[name='record']").val();

            if (typeof comp_record != 'undefined' && comp_record == '') {

                isValid = false;

                $('.nav-tabs a[href="#details_content"]').tab('show');

                $("#createBasicCompetition").valid();

            } else {

                if ($("#enable_reward_wins").prop('checked') == true) {

                    if (!$("#saveRewardContent").valid()) {

                        isValid = false;
                    }

                } else if (!$("#savProductsContent").valid()) {

                    $('.nav-tabs a[href="#products_content"]').tab('show');

                    $("#savProductsContent").valid();

                    isValid = false;

                } else if (!$("#saveLegalsContent").valid() || $("#saveLegalsContent").valid()) {

                    if ($("#saveLegalsContent").valid()) {

                        let comp_rule = competitionEditor.rule_editor.getData();

                        let comp_faq = competitionEditor.faq_editor.getData();

                        if (comp_rule == '' || comp_faq == '') {

                            if (comp_rule == '') {

                                let ruleElement = $("#saveLegalsContent").find("#rule_editor").closest("div");

                                if (!ruleElement.find("#rule_editor-error").length) {
                                    ruleElement.append('<label id="rule_editor-error" class="error" for="rule_editor">This field is required.</label>');
                                } else {
                                    ruleElement.find("#rule_editor-error").text('This field is required').show();
                                }
                            }

                            if (comp_faq == '') {

                                let faqElement = $("#saveLegalsContent").find("#faq_editor").closest("div");

                                if (!faqElement.find("#faq_editor-error").length) {
                                    faqElement.append('<label id="faq_editor-error" class="error" for="faq_editor">This field is required.</label>');
                                } else {
                                    faqElement.find("#faq_editor-error").text('This field is required').show();
                                }
                            }

                            $('.nav-tabs a[href="#legals_content"]').tab('show');

                            isValid = false;

                        }
                    } else {

                        $('.nav-tabs a[href="#legals_content"]').tab('show');

                        isValid = false;
                    }

                } else if ($("#enable_instant_wins").prop('checked') == true) {

                    if (!$("#saveInstantContent").valid()) {

                        $('.nav-tabs a[href="#insant_wins"]').tab('show');

                        isValid = false;
                    }

                }
            }
        }

        if (!isValid) return false;

        let activeTab = $('.tab-pane.active').find("[name='step']").val();

        if (activeTab == 'details') {

            if ($("#createBasicCompetition").valid()) {

                let comp_desc = competitionEditor.description_editor.getData();

                if (comp_desc == '') {

                    let editorElement = $("#createBasicCompetition").find("#description_editor").closest("div");

                    if (!editorElement.find("#description_editor-error").length) {
                        editorElement.append('<label id="description_editor-error" class="error" for="description_editor">This field is required.</label>');
                    }

                } else {

                    createCompetition($("#createBasicCompetition"));

                }
            }
        }

        if (activeTab == 'legals') {

            if ($("#saveLegalsContent").valid()) {

                let comp_rule = competitionEditor.rule_editor.getData();

                let comp_faq = competitionEditor.faq_editor.getData();

                if (comp_rule == '' || comp_faq == '') {

                    if (comp_rule == '') {

                        let ruleElement = $("#saveLegalsContent").find("#rule_editor").closest("div");

                        if (!ruleElement.find("#rule_editor-error").length) {
                            ruleElement.append('<label id="rule_editor-error" class="error" for="rule_editor">This field is required.</label>');
                        }
                    }

                    if (comp_faq == '') {

                        let faqElement = $("#saveLegalsContent").find("#faq_editor").closest("div");

                        if (!faqElement.find("#faq_editor-error").length) {
                            faqElement.append('<label id="faq_editor-error" class="error" for="faq_editor">This field is required.</label>');
                        }
                    }

                } else {

                    updateCompetition($("#saveLegalsContent"));
                }
            }
        }

        if (activeTab == 'products') {

            if ($("#savProductsContent").valid()) {

                let comp_draw_info = competitionEditor.live_draw_info.getData();

                if (comp_draw_info == '' && $("#mySwitch").prop('checked') == true) {

                    let editorElement = $("#savProductsContent").find("#live_draw_info").closest("div");

                    if (!editorElement.find("#live_draw_info-error").length) {
                        editorElement.append('<label id="live_draw_info-error" class="error" for="live_draw_info">This field is required.</label>');
                    } else {
                        $("#live_draw_info-error").html("This field is required.");
                        $("#live_draw_info-error").show();
                    }

                } else {

                    updateCompetition($("#savProductsContent"));

                    let record = $("#create-comp-content").find("[name='record']").val();

                    let total_sell_tickets = $("[name='total_sell_tickets']").val();

                    jQuery.ajax({
                        type: "POST",
                        url: ajax_object.ajax_url,
                        data: { action: 'check_generate_ticket_numbers', 'record': record, 'total_sell_tickets': total_sell_tickets },
                        success: function (response) {
                        }
                    });

                }
            }
        }

        if (activeTab == 'question') {

            if ($("#globalcustom").prop('checked') == true) {

                if ($("#savQuestionContent").valid()) {

                    if ($('.correct-answer:checked').length == 0) {

                        if (!$("#answer1").closest("div").find("#correct-answer-error").length) {
                            $("#answer1").closest("div").append('<label id="correct-answer-error" class="error" for="correct_answer">At least one answer is required to be selected as correct</label>');
                        } else {
                            $("#answer1").closest("div").find("#correct-answer-error").text('At least one answer is required to be selected as correct').show();
                        }

                    } else {
                        updateCompetition($("#savQuestionContent"));
                    }
                }
            } else {

                $('.nav-tabs').find(".active").removeClass("active-accent").addClass("tab-lighter");
                $('.nav-tabs a[href="#legals_content"]').tab('show');
                $('.nav-tabs').find(".active").addClass("active-accent");

            }

        }

        if (activeTab == 'instant') {

            if ($("#enable_instant_wins").prop('checked') == true) {

                let total_sell_tickets = $("[name='total_sell_tickets']").val();

                let total_prize_qty = 0;

                $("#saveInstantContent").find(".lineItemRow").each(function (index, prizeRow) {

                    total_prize_qty += parseInt($(prizeRow).find(".prize_qty").val());

                });

                if (total_sell_tickets >= total_prize_qty) {

                    if ($("#saveInstantContent").valid()) {

                        updateLineItemElementByOrder();

                        $("#total_prizes").val($(".instant_content").find(".lineItemRow").length);

                        updateCompetition($("#saveInstantContent"));

                    }
                } else {

                    if (!$("#qty-error").length) {

                        $("#instant_wins_content").prepend('<div class="text-center mb-3"><label id="qty-error" class="error" for="qty-error">Prize quantity must be less than Total Sellable ticket.</label></div>');

                    }
                }
            } else {

                /*let comp_tickets = $("#prize_ticket_list").val();

                if(typeof comp_tickets != 'undefined' && comp_tickets == ''){

                    $("#instant_wins_content").prepend('<div class="text-center mb-3"><label id="ticket-error" class="error" for="qty-error">Please generate tickets for this competition.</label></div>');

                    return false;

                } else {
                
                    $('.nav-tabs').find(".active").removeClass("active-accent").addClass("tab-lighter");
                    $('.nav-tabs a[href="#reward_wins"]').tab('show');
                    $('.nav-tabs').find(".active").addClass("active-accent");
                }*/

                $('.nav-tabs').find(".active").removeClass("active-accent").addClass("tab-lighter");
                $('.nav-tabs a[href="#reward_wins"]').tab('show');
                $('.nav-tabs').find(".active").addClass("active-accent");
            }
        }

        if (activeTab == 'reward') {

            if ($("#enable_reward_wins").prop('checked') == true) {

                if ($("#saveRewardContent").valid()) {

                    updateRewardLineItemElementByOrder();

                    $("#total_reward").val($(".reward_content").find(".lineItemRow").length);

                    updateCompetition($("#saveRewardContent"));

                }

            } else {

                updateCompetition($("#saveRewardContent"));

            }
        }

    });

    $('#globalcustom').on('click', function () {
        if ($("#globalcustom").prop('checked') == true) {
            $('.customdisable').removeAttr('disabled');
            $('.custom_label').removeClass('text-secondary');
        }

        if ($("#globalcustom").prop('checked') == false) {
            $('.customdisable').attr('disabled', 'disabled');
            $('.custom_label').addClass('text-secondary')
        }
    });

    $('.correct-answer', $("#savQuestionContent")).on('change', function () {
        $('.correct-answer').not(this).prop('disabled', this.checked);
        $('.form-control.correct-answer').not($(this).closest('.mb-3').find('.form-control.correct-answer')).prop('disabled', this.checked);
    });



    $("#generate_ticket_numbers").on("click", function () {

        let total_purchasing = $("#total_ticket_purchased").val();

        if (total_purchasing > 0 && mode == 'edit') {
            return true;
        }

        usedTickets = [];

        let total_sell_tickets = $("[name='total_sell_tickets']").val();

        let total_prize_qty = 0;

        $("#saveInstantContent").find(".lineItemRow").each(function (index, prizeRow) {

            total_prize_qty += parseInt($(prizeRow).find(".prize_qty").val());

        });

        if (total_sell_tickets >= total_prize_qty) {

            $(".show_loader").removeClass("d-none");

            let record = $("#saveInstantContent").find("[name='record']").val();

            let mode = $("[name='mode']").val();

            let call_action = 'generate_ticket_numbers';

            if (mode == 'edit') {

                call_action = 'generate_temporary_ticket_numbers';
            }

            jQuery.ajax({
                type: "POST",
                url: ajax_object.ajax_url,
                data: { action: call_action, 'record': record, 'mode': mode, 'total_qty': total_prize_qty, 'total_sell_tickets': total_sell_tickets },
                success: function (response) {

                    $(".show_loader").addClass("d-none");

                    let res = JSON.parse(response);

                    let ticketIndex = 0;

                    $("#prize_ticket_list").val(total_sell_tickets);

                    $("#instant_wins_content").find(".lineItemRow").each(function (index, prizeTicketElement) {

                        $(prizeTicketElement).find(".prize_tickets .form-control").each((index, input) => {

                            if (jQuery.inArray(res.data[ticketIndex], usedTickets) >= 0) {

                                console.log("ticket number assigned");

                            } else {

                                $(input).val(res.data[ticketIndex]);

                                usedTickets.push(res.data[ticketIndex]);
                            }

                            ticketIndex++;

                        });

                    });

                    $("#qty-error").remove();
                }
            });

        } else {

            if (!$("#qty-error").length) {

                $("#instant_wins_content").prepend('<div class="text-center mb-3"><label id="qty-error" class="error" for="qty-error">Prize quantity must be less than Total Sellable ticket.</label></div>');

            }
        }

    });

    function createCompetition(form) {

        let formData = form.serializeArray();

        var data = {
            action: 'create_competition_record',
        };

        $(formData).each(function (index, field) {
            data[field.name] = field.value;
        });

        data.description = competitionEditor.description_editor.getData();

        jQuery.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: data,
            success: function (response) {
                $('.nav-tabs').find(".active").removeClass("active-accent").addClass("tab-lighter");
                let res = JSON.parse(response);
                if (data.step == 'details') {
                    $('.nav-tabs a[href="#products_content"]').tab('show');
                }
                $('.nav-tabs').find(".active").addClass("active-accent");
                window.history.pushState({}, 'page=create-competition', '?page=create-competition&id=' + res.record);
                $("[name='record']").val(res.record);
            },
        });
    }

    function updateCompetition(form) {

        let formData = form.serializeArray();

        var data = {
            action: 'update_competition_record',
        };

        $(formData).each(function (index, field) {
            data[field.name] = field.value;
        });

        if (data.step == 'legals') {

            data.competition_rules = competitionEditor.rule_editor.getData();

            data.faq = competitionEditor.faq_editor.getData();
        }

        if (data.step == 'products') {

            data.live_draw_info = competitionEditor.live_draw_info.getData();
        }

        if (data.step == 'question') {

            data.correct_answer = $('.correct-answer:checked').closest(".ans_content").find(".question-ans").val();
        }


        jQuery.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: data,
            success: function (response) {
                if (data.step != 'reward') {
                    $('.nav-tabs').find(".active").removeClass("active-accent").addClass("tab-lighter");
                }
                let res = JSON.parse(response);
                if (data.step == 'products') {
                    $('.nav-tabs a[href="#question_content"]').tab('show');
                } else if (data.step == 'question') {
                    $('.nav-tabs a[href="#legals_content"]').tab('show');
                } else if (data.step == 'legals') {
                    $('.nav-tabs a[href="#insant_wins"]').tab('show');
                } else if (data.step == 'instant') {
                    $('.nav-tabs a[href="#reward_wins"]').tab('show');
                } else if (data.step == 'reward') {
                    window.location.href = "admin.php?page=competitions_menu";
                }
                if (data.step != 'reward') {
                    $('.nav-tabs').find(".active").addClass("active-accent");
                }

            },
        });
    }

    checkLineItemRow();

    var instantRow = $(".instant_content").find(".lineItemRow").length;

    $("#add_price", $(".instant_content")).on("click", function (e) {

        let total_purchasing = $("#total_ticket_purchased").val();

        if (total_purchasing > 0 && mode == 'edit') {
            return true;
        }

        e.preventDefault();

        let newRow = $(".instant_content").find(".lineItemCloneCopy").clone(true);

        newRow.removeClass("lineItemCloneCopy").addClass("lineItemRow").removeClass("d-none");

        newRow.find(".image").addClass("prize_image");

        var newRowNum = getLineItemNextRowNumber();

        updateRowNumberForRow(newRow, newRowNum);

        newRow.appendTo($("#instant_wins_content"));

        checkLineItemRow();

        registerWPMediaFrame(newRow);

    });

    $(".instant_content").on('click', '.delete_price', function (e) {

        let mode = $("[name='mode']").val();

        let total_purchasing = $("#total_ticket_purchased").val();

        if (mode == 'edit' && total_purchasing > 0) {
            return true;
        }

        var element = jQuery(e.currentTarget);

        element.closest('.lineItemRow').remove();

        checkLineItemRow();
    });

    $(".instant_content").on('focusout', '.quantity', function (e) {

        let total_qty = $(this).val();

        prizeTicketElement = $(e.currentTarget).closest(".lineItemRow").find(".prize_tickets");

        let currentRow = $(e.currentTarget).closest(".lineItemRow").find(".rowNumber").val();

        prizeTicketElement.html("");

        for (var i = 1; i <= total_qty; i++) {

            let newElement = $('<div class="col-2">' +
                '<input type="text" name="ticket' + currentRow + '_' + i + '" id="ticket' + currentRow + '_' + i + '" value="" class="form-control instant_tickets ticket' + currentRow + '_' + i + '" />' +
                '</div>');
            prizeTicketElement.append(newElement);
        }

    });

    function getLineItemNextRowNumber() {

        return ++instantRow;
    }

    function checkLineItemRow() {

        let numRows = $(".instant_content").find(".lineItemRow").length;

        if (numRows > 1) {
            showLineItemsDeleteIcon();
        } else {
            hideLineItemsDeleteIcon();
        }

    }

    function showLineItemsDeleteIcon() {

        $("#instant_wins_content").find('.deleteRow').show();
    }

    function hideLineItemsDeleteIcon() {

        $("#instant_wins_content").find('.deleteRow').hide();
    }

    function updateRowNumberForRow(lineItemRow, expectedSequenceNumber, currentSequenceNumber) {

        if (typeof currentSequenceNumber == 'undefined') {
            currentSequenceNumber = 0;
        }

        let idFields = new Array('title', 'cash_value', 'price_type', 'quantity', 'image');

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

        updateRowNumberForTicket(lineItemRow, expectedSequenceNumber);

        return lineItemRow;
    }

    function updateRowNumberForTicket(lineItemRow, expectedSequenceNumber, currentSequenceNumber) {

        if (typeof currentSequenceNumber == 'undefined') {
            currentSequenceNumber = 0;
        }

        let total_qty = lineItemRow.find(".prize_qty").val();

        for (var i = 1; i <= total_qty; i++) {

            let actualElementId = "ticket" + currentSequenceNumber + '_' + i;

            let expectedElementId = "ticket" + expectedSequenceNumber + '_' + i;

            lineItemRow.find('#' + actualElementId).attr('id', expectedElementId)
                .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);

        }

    }

    function updateLineItemElementByOrder() {

        let lineItems = $(".instant_content").find(".lineItemRow");

        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var expectedRowIndex = (index + 1);
            var expectedRowId = 'row' + expectedRowIndex;
            var actualRowId = lineItemRow.attr('id');
            if (expectedRowId != actualRowId) {
                var actualIdComponents = actualRowId.split('row');
                updateRowNumberForRow(lineItemRow, expectedRowIndex, actualIdComponents[1]);
                updateRowNumberForTicket(lineItemRow, expectedRowIndex, actualIdComponents[1]);
            }
        });
    }



    checkRewardLineItemRow();

    var rewardRow = $(".reward_content").find(".lineItemRow").length;

    $("#add_price", $(".reward_content")).on("click", function (e) {

        e.preventDefault();

        let newRow = $(".reward_content").find(".lineItemCloneCopy").clone(true);

        newRow.removeClass("lineItemCloneCopy").addClass("lineItemRow").removeClass("d-none");

        newRow.find(".image").addClass("prize_image");

        var newRowNum = getRewardLineItemNextRowNumber();

        updateRewardRowNumberForRow(newRow, newRowNum);

        newRow.appendTo($("#reward_wins_content"));

        checkRewardLineItemRow();

        updateAvailablePercentages();

        registerWPMediaFrame(newRow);
    });

    $(".reward_content").on('click', '.delete_reward_price', function (e) {

        var element = jQuery(e.currentTarget);

        element.closest('.lineItemRow').remove();

        checkRewardLineItemRow();

        updateAvailablePercentages();
    });

    function getRewardLineItemNextRowNumber() {

        return ++rewardRow;
    }

    function checkRewardLineItemRow() {

        let numRows = $(".reward_content").find(".lineItemRow").length;

        if (numRows > 1) {
            showRewardLineItemsDeleteIcon();
        } else {
            hideRewardLineItemsDeleteIcon();
        }

    }

    function showRewardLineItemsDeleteIcon() {

        $("#reward_wins_content").find('.deleteRow').show();
    }

    function hideRewardLineItemsDeleteIcon() {

        $("#reward_wins_content").find('.deleteRow').hide();
    }

    function updateRewardRowNumberForRow(lineItemRow, expectedSequenceNumber, currentSequenceNumber) {

        if (typeof currentSequenceNumber == 'undefined') {
            currentSequenceNumber = 0;
        }

        let idFields = new Array('title', 'cash_value', 'price_type', 'prct_available', 'image');

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

    function updateRewardLineItemElementByOrder() {

        let lineItems = $(".reward_content").find(".lineItemRow");

        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var expectedRowIndex = (index + 1);
            var expectedRowId = 'row' + expectedRowIndex;
            var actualRowId = lineItemRow.attr('id');
            if (expectedRowId != actualRowId) {
                var actualIdComponents = actualRowId.split('row');
                updateRewardRowNumberForRow(lineItemRow, expectedRowIndex, actualIdComponents[1]);
            }
        });
    }

    var usedTickets = [];

    document.getElementById('csvFile').addEventListener('change', handleCSVFile);

    function handleCSVFile(event) {

        const fileInput = event.target;

        const file = fileInput.files[0];

        if (file) {

            const reader = new FileReader();

            reader.onload = function (e) {

                $(".btn-close").trigger("click");

                const csvContent = e.target.result;

                let activeTab = $('.tab-pane.active').find("[name='step']").val();

                if (activeTab == 'instant') {
                    processInstantWinCSVContent(csvContent);
                } else if (activeTab == 'reward') {
                    processCSVContent(csvContent);
                }

            };

            reader.readAsText(file);
        }
    }

    function processInstantWinCSVContent(csvContent) {

        Papa.parse(csvContent, {
            header: true,
            complete: function (results) {

                let rows = results.data;

                index = 0;

                rows.forEach(rowData => {

                    if (rowData['Title'] == '') return false;

                    index++;

                    let newRow = false;

                    if (index == 1) {
                        newRow = $(".instant_content").find("#row1");
                    } else {

                        newRow = $(".instant_content").find(".lineItemCloneCopy").clone(true);

                        newRow.removeClass("lineItemCloneCopy").addClass("lineItemRow").removeClass("d-none");

                        newRow.find(".image").addClass("prize_image");

                        let newRowNum = getLineItemNextRowNumber();

                        updateRowNumberForRow(newRow, newRowNum);

                        newRow.appendTo($("#instant_wins_content"));
                    }

                    newRow.find('.title').val(rowData['Title']);
                    newRow.find('.price_type').val(rowData['Prize Type']);
                    newRow.find('.quantity').val(rowData['Quantity']).trigger("focusout");
                    newRow.find('.cash_value').val(rowData['Cash Value']);

                    registerWPMediaFrame(newRow);

                    newRow.find(".wp_media_frame").addClass("d-none");
                    newRow.find(".image_preview_container").removeClass("d-none");
                    newRow.find(".image_preview_container .img-content").html('<img src="' + rowData['Image URL'] + '" alt="" width="150px" height="150px">');
                    newRow.find(".prize_image").val(rowData['Image URL']);

                    let ticketNumbers = rowData['Ticket Numbers'].trim().split(",");

                    newRow.find(".prize_tickets .form-control").each((ticketIndex, input) => {
                        if (jQuery.inArray(ticketNumbers[ticketIndex], usedTickets) >= 0) {
                            console.log("ticket number assigned");
                        } else {
                            $(input).val(ticketNumbers[ticketIndex]);
                            usedTickets.push(ticketNumbers[ticketIndex]);
                        }
                    });

                    checkLineItemRow();
                });
            }
        });

    }

    function processCSVContent(csvContent) {

        Papa.parse(csvContent, {
            header: true,
            complete: function (results) {

                let rows = results.data;

                index = 0;

                rows.forEach(rowData => {

                    if (rowData['Title'] == '') return false;

                    index++;

                    let newRow = false;

                    if (index == 1) {

                        newRow = $(".reward_content").find("#row1");

                    } else {

                        newRow = $(".reward_content").find(".lineItemCloneCopy").clone(true);

                        newRow.removeClass("lineItemCloneCopy").addClass("lineItemRow").removeClass("d-none");

                        newRow.find(".image").addClass("prize_image");

                        let newRowNum = getRewardLineItemNextRowNumber();

                        updateRewardRowNumberForRow(newRow, newRowNum);

                        newRow.appendTo($("#reward_wins_content"));

                        registerWPMediaFrame(newRow);

                    }

                    newRow.find('.title').val(rowData['Title']);
                    newRow.find('.price_type').val(rowData['Prize Type']);
                    newRow.find('.prct_available').val(rowData['Percentage']);
                    newRow.find('.cash_value').val(rowData['Cash Value']);

                    newRow.find(".wp_media_frame").addClass("d-none");
                    newRow.find(".image_preview_container").removeClass("d-none");
                    newRow.find(".image_preview_container .img-content").html('<img src="' + rowData['Image URL'] + '" alt="" width="150px" height="150px">');
                    newRow.find(".prize_image").val(rowData['Image URL']);

                    //Image URL
                    checkRewardLineItemRow();
                });
            }
        });
    }

    function updateAvailablePercentages() {

        let totalPrizes = $(".lineItemRow", $("#reward_wins_content")).length;

        let defaultPercentage = 100 / (totalPrizes + 1);

        $("#reward_wins_content").find(".lineItemRow").each(function (index, row) {

            let percentageField = $(row).find(".prct_available");

            let calculatedPercentage = (index + 1) * defaultPercentage;

            calculatedPercentage = calculatedPercentage.toFixed(2)

            calculatedPercentage = calculatedPercentage.replace(/\.00$/, '');

            percentageField.val(calculatedPercentage);
        });
    }

    $('#feature_image').on('click', function (e) {

        e.preventDefault();

        var frame = wp.media({
            title: 'Select or Upload Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            if (attachment.height < 400) {
                // Display an error message or perform some action
                alert('Image height must be at least 400 pixels.');
                return;
            }
            $("#feature_img_upload_container").addClass("d-none");
            $('#feature-image-container').removeClass("d-none");
            $('#feature-image-container #img-content').html('<img src="' + attachment.url + '" alt="" width="150px" height="150px">');
            $("#feature-image-container [name='featured_image']").val(attachment.url);
        });

        frame.open();

    });

    $("#remove_featured_image").on("click", function () {
        $("#feature_img_upload_container").removeClass("d-none");
        $('#feature-image-container').addClass("d-none");
        $("#feature-image-container [name='featured_image']").val("");
    });

    $("#gallery_image").on('click', function (e) {

        e.preventDefault();

        let frame = wp.media({
            title: 'Select or Upload Image/Video',
            button: {
                text: 'Insert'
            },
            multiple: true,
            library: {
                type: ['video', 'image']
            },
        });

        frame.on('select', function () {

            var attachments = frame.state().get('selection').toJSON();

            let isValid = true;

            attachments.forEach(attachment => {

                if (attachment.type == "image" && attachment.height < 400) {
                    isValid = false;
                    return;
                }

                let imageUrl = attachment.url;

                if (!selectedGalleryImageUrls.includes(imageUrl)) {
                    selectedGalleryImageUrls.push(imageUrl);
                    updateImageUrlsInput();
                    displayImagePreview(imageUrl, attachment.type);
                }
            });

            if (!isValid) {
                alert("Some images have a height less than 400 pixels and cannot be previewed.");
            }


        });

        frame.open();

    });

    $(".remove_gallery_image").on("click", function (e) {

        let imageUrl = $(e.currentTarget).closest(".gallery_preview").find(".gallery-img-content").children().attr("src");

        $(e.currentTarget).closest(".gallery_preview").remove();

        selectedGalleryImageUrls = selectedGalleryImageUrls.filter(url => url !== imageUrl);

        updateImageUrlsInput();

    });

    $("#instant_wins_content").find(".lineItemRow").each(function (index, elem) {

        registerWPMediaFrame($(elem));

    });

    $("#reward_wins_content").find(".lineItemRow").each(function (index, elem) {

        registerWPMediaFrame($(elem));

    });

    function updateImageUrlsInput() {
        let imageUrlInput = $("[name='gallery_image']");
        imageUrlInput.val(selectedGalleryImageUrls.join(','));
    }

    // Function to display the selected image preview
    function displayImagePreview(imageUrl, type) {

        $("#gallery-image-container").removeClass("d-none");

        if (type == 'video') {
            let galleryImagePreview = $(".gallery_content_clone").clone(true);
            galleryImagePreview.removeClass("gallery_content_clone").removeClass("d-none").addClass("video_container");
            galleryImagePreview.find(".remove_gallery_image").removeClass("d-none");
            galleryImagePreview.find(".gallery-img-content").html('<video src="' + imageUrl + '" controls="controls" preload="metadata"></video>');
            galleryImagePreview.appendTo($("#gallery-image-container"));
        } else {
            let galleryImagePreview = $(".gallery_content_clone").clone(true);
            galleryImagePreview.removeClass("gallery_content_clone").removeClass("d-none");
            galleryImagePreview.find(".remove_gallery_image").removeClass("d-none");
            galleryImagePreview.find(".gallery-img-content").html('<img src="' + imageUrl + '" alt="">');
            galleryImagePreview.prependTo($("#gallery-image-container"));
        }
    }

    function registerWPMediaFrame(lineItemRow) {

        let step = lineItemRow.closest("form").find("[name='step']").val();

        lineItemRow.find(".wp_media_frame").on("click", function (e) {

            e.preventDefault();

            var frame = wp.media({
                title: 'Select or Upload Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });

            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                if (step == 'reward' && attachment.height < 200) {
                    alert('Image height must be at least 200 pixels.');
                    return;
                } else if (step == 'instant' && attachment.height < 80) {
                    alert('Image height must be at least 80 pixels.');
                    return;
                }
                lineItemRow.find(".wp_media_frame").addClass("d-none");
                lineItemRow.find(".image_preview_container").removeClass("d-none");
                lineItemRow.find(".image_preview_container .img-content").html('<img src="' + attachment.url + '" alt="" width="150px" height="150px">');
                lineItemRow.find(".prize_image").val(attachment.url);
                lineItemRow.find(".prize_image").valid();
            });


            frame.open();
        });

    }

    $(".remove_instant_media").on("click", function (e) {

        e.preventDefault();

        let mode = $("[name='mode']").val();

        let total_purchasing = $("#total_ticket_purchased").val();

        if (mode == 'edit' && total_purchasing > 0) {
            return true;
        }

        $(e.currentTarget).closest(".image_preview_container").find(".img-content").html();

        $(e.currentTarget).closest(".image_preview_container").addClass("d-none");

        $(e.currentTarget).closest(".image_editor").find(".wp_media_frame").removeClass("d-none");

        $(e.currentTarget).closest(".image_editor").find(".prize_image").val("");


    });

    $(".remove_reward_media").on("click", function (e) {

        e.preventDefault();

        $(e.currentTarget).closest(".image_preview_container").find(".img-content").html();

        $(e.currentTarget).closest(".image_preview_container").addClass("d-none");

        $(e.currentTarget).closest(".image_editor").find(".wp_media_frame").removeClass("d-none");

        $(e.currentTarget).closest(".image_editor").find(".prize_image").val("");


    });


    let mode = $("[name='mode']").val();

    let total_purchasing = $("#total_ticket_purchased").val();

    if (mode == 'edit' && total_purchasing > 0) {
        $("#savQuestionContent").find("input[type='text']").attr("readonly", true);
        $("#savQuestionContent").find("input[type='checkbox']").attr("disabled", true);
        $("#saveInstantContent").find("input[type!='hidden']").attr("disabled", true);
        $("#saveInstantContent").find(".price_type").attr("disabled", true);
    } else if (mode == 'edit') {
        if ($("#globalcustom").prop('checked') == true) {
            $('.customdisable').removeAttr('disabled');
            $('.custom_label').removeClass('text-secondary');
        }

        if ($("#globalcustom").prop('checked') == false) {
            $('.customdisable').attr('disabled', 'disabled');
            $('.custom_label').addClass('text-secondary')
        }
    }

    if (mode == 'edit') {

        let galleryImages = $("[name='gallery_image']").val();

        if (typeof galleryImages != 'undefined' && galleryImages != '') {

            selectedGalleryImageUrls = galleryImages.split(",");
        }
    }

    function saveTempData(tabName) {

        if (tabName == 'details-tab') {

            if ($("#createBasicCompetition").valid()) {

                let comp_desc = competitionEditor.description_editor.getData();

                if (comp_desc == '') {

                    let editorElement = $("#createBasicCompetition").find("#description_editor").closest("div");

                    if (!editorElement.find("#description_editor-error").length) {
                        editorElement.append('<label id="description_editor-error" class="error" for="description_editor">This field is required.</label>');
                    } else {
                        editorElement.find("#description_editor-error").text('This field is required.').show();
                    }

                    $('.nav-tabs a[href="#details_content"]').tab('show');

                    return false;

                } else {

                    let formData = $("#createBasicCompetition").serializeArray();

                    let data = {
                        action: 'save_temp_competition_record',
                    };

                    $(formData).each(function (index, field) {
                        data[field.name] = field.value;
                    });

                    data.description = competitionEditor.description_editor.getData();

                    saveTempCompetition(data);

                    return true;
                }

            } else {

                $('.nav-tabs a[href="#details_content"]').tab('show');

                return $("#createBasicCompetition").valid();
            }

        }

        if (tabName == 'products-tab') {

            if ($("#savProductsContent").valid()) {

                let comp_draw_info = competitionEditor.live_draw_info.getData();

                if (comp_draw_info == '' && $("#mySwitch").prop('checked') == true) {

                    console.log("here");

                    let editorElement = $("#savProductsContent").find("#live_draw_info").closest("div");

                    if (!editorElement.find("#live_draw_info-error").length) {
                        editorElement.append('<label id="live_draw_info-error" class="error" for="live_draw_info">This field is required.</label>');
                    } else {
                        $("#live_draw_info-error").html("This field is required.");
                        $("#live_draw_info-error").show();
                    }

                    $('.nav-tabs a[href="#products_content"]').tab('show');

                    return false;

                } else {

                    let formData = $("#savProductsContent").serializeArray();

                    let data = {
                        action: 'save_temp_competition_record',
                    };

                    $(formData).each(function (index, field) {
                        data[field.name] = field.value;
                    });

                    data.live_draw_info = competitionEditor.live_draw_info.getData();

                    saveTempCompetition(data);

                    let total_sell_tickets = $("[name='total_sell_tickets']").val();

                    let total_purchasing = $("#total_ticket_purchased").val();

                    if (total_purchasing > 0 && mode == 'edit') {

                        return true;

                    } else {

                        jQuery.ajax({
                            type: "POST",
                            url: ajax_object.ajax_url,
                            data: { action: 'generate_temporary_ticket_numbers', 'record': data.record, 'total_sell_tickets': total_sell_tickets },
                            success: function (response) {
                            }
                        });

                        return true;
                    }
                }
            } else {

                $('.nav-tabs a[href="#products_content"]').tab('show');

                return $("#savProductsContent").valid();
            }
        }

        if (tabName == 'question-tab') {

            let total_purchasing = $("#total_ticket_purchased").val();

            if (total_purchasing > 0) {

                let formData = $("#savQuestionContent").serializeArray();

                let data = {
                    action: 'save_temp_competition_record',
                };

                $(formData).each(function (index, field) {
                    data[field.name] = field.value;
                });

                data.save_original = true;

                saveTempCompetition(data);

                return true;

            } else {

                if ($("#globalcustom").prop('checked') == true) {

                    if ($("#savQuestionContent").valid()) {

                        if ($('.correct-answer:checked').length == 0) {

                            $("#savQuestionContent").valid();

                            if (!$("#answer1").closest("div").find("#correct-answer-error").length) {
                                $("#answer1").closest("div").append('<label id="correct-answer-error" class="error" for="correct_answer">At least one answer is required to be selected as correct</label>');
                            } else {
                                $("#answer1").closest("div").find("#correct-answer-error").text('At least one answer is required to be selected as correct').show();
                            }

                            $('.nav-tabs a[href="#question_content"]').tab('show');

                            return false;

                        } else {

                            let formData = $("#savQuestionContent").serializeArray();

                            let data = {
                                action: 'save_temp_competition_record',
                            };

                            $(formData).each(function (index, field) {
                                data[field.name] = field.value;
                            });

                            data.save_original = false;

                            data.correct_answer = $('.correct-answer:checked').closest(".ans_content").find(".question-ans").val();

                            saveTempCompetition(data);

                            return true;
                        }
                    } else {

                        $('.nav-tabs a[href="#question_content"]').tab('show');

                        return $("#savQuestionContent").valid();
                    }
                } else {

                    let formData = $("#savQuestionContent").serializeArray();

                    let data = {
                        action: 'save_temp_competition_record',
                    };

                    $(formData).each(function (index, field) {
                        data[field.name] = field.value;
                    });

                    data.save_original = false;

                    data.correct_answer = $('.correct-answer:checked').closest(".ans_content").find(".question-ans").val();

                    saveTempCompetition(data);

                    return true;

                }

            }
        }

        if (tabName == 'legals-tab') {

            if ($("#saveLegalsContent").valid()) {

                let comp_rule = competitionEditor.rule_editor.getData();

                let comp_faq = competitionEditor.faq_editor.getData();

                if (comp_rule == '' || comp_faq == '') {

                    if (comp_rule == '') {

                        let ruleElement = $("#saveLegalsContent").find("#rule_editor").closest("div");

                        if (!ruleElement.find("#rule_editor-error").length) {
                            ruleElement.append('<label id="rule_editor-error" class="error" for="rule_editor">This field is required.</label>');
                        } else {
                            ruleElement.find("#rule_editor-error").text('This field is required').show();
                        }
                    }

                    if (comp_faq == '') {

                        let faqElement = $("#saveLegalsContent").find("#faq_editor").closest("div");

                        if (!faqElement.find("#faq_editor-error").length) {
                            faqElement.append('<label id="faq_editor-error" class="error" for="faq_editor">This field is required.</label>');
                        } else {
                            faqElement.find("#faq_editor-error").text('This field is required').show();
                        }
                    }

                    $('.nav-tabs a[href="#legals_content"]').tab('show');

                    return false;

                } else {

                    let formData = $("#saveLegalsContent").serializeArray();

                    let data = {
                        action: 'save_temp_competition_record',
                    };

                    $(formData).each(function (index, field) {
                        data[field.name] = field.value;
                    });

                    data.competition_rules = competitionEditor.rule_editor.getData();

                    data.faq = competitionEditor.faq_editor.getData();

                    saveTempCompetition(data);

                    return true;
                }
            }
        }

        if (tabName == 'instant-tab') {

            let total_purchasing = $("#total_ticket_purchased").val();

            let mode = $("[name='mode']").val();

            if (total_purchasing > 0 && mode == 'edit') {
                return true;
            }

            if ($("#enable_instant_wins").prop('checked') == true) {

                let total_sell_tickets = $("[name='total_sell_tickets']").val();

                let total_prize_qty = 0;

                $("#saveInstantContent").find(".lineItemRow").each(function (index, prizeRow) {

                    total_prize_qty += parseInt($(prizeRow).find(".prize_qty").val());

                });

                if (total_sell_tickets >= total_prize_qty) {

                    if ($("#saveInstantContent").valid()) {

                        updateLineItemElementByOrder();

                        $("#total_prizes").val($(".instant_content").find(".lineItemRow").length);

                        let formData = $("#saveInstantContent").serializeArray();

                        let data = {
                            action: 'save_temp_competition_record',
                        };

                        $(formData).each(function (index, field) {
                            data[field.name] = field.value;
                        });

                        saveTempCompetition(data);

                        return true;

                    } else {

                        $('.nav-tabs a[href="#insant_wins"]').tab('show');

                        return false;
                    }
                } else {

                    if (!$("#qty-error").length) {

                        $("#instant_wins_content").prepend('<div class="text-center mb-3"><label id="qty-error" class="error" for="qty-error">Prize quantity must be less than Total Sellable ticket.</label></div>');

                    }
                }
            } else {

                updateLineItemElementByOrder();

                $("#total_prizes").val($(".instant_content").find(".lineItemRow").length);

                let formData = $("#saveInstantContent").serializeArray();

                let data = {
                    action: 'save_temp_competition_record',
                };

                $(formData).each(function (index, field) {
                    data[field.name] = field.value;
                });

                saveTempCompetition(data);

                return true;
            }

        }

        if (tabName == 'reward-tab') {

            let total_purchasing = $("#total_ticket_purchased").val();

            let mode = $("[name='mode']").val();

            // if (total_purchasing > 0 && mode == 'edit') {
            //     return true;
            // }

            if ($("#enable_reward_wins").prop('checked') == true) {

                if ($("#saveRewardContent").valid()) {

                    updateRewardLineItemElementByOrder();

                    $("#total_reward").val($(".reward_content").find(".lineItemRow").length);

                    let formData = $("#saveRewardContent").serializeArray();

                    let data = {
                        action: 'save_temp_competition_record',
                    };

                    $(formData).each(function (index, field) {
                        data[field.name] = field.value;
                    });

                    saveTempCompetition(data);

                    return true;

                } else {

                    $('.nav-tabs a[href="#reward_wins"]').tab('show');

                    return false;
                }

            } else {

                let formData = $("#saveRewardContent").serializeArray();

                let data = {
                    action: 'save_temp_competition_record',
                };

                $(formData).each(function (index, field) {
                    data[field.name] = field.value;
                });

                saveTempCompetition(data);

                return true;
            }
        }
    }

    function saveTempCompetition(data) {

        jQuery.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: data,
            success: function (response) {
                return JSON.parse(response);
            },
        });

    }



    $("#save_comp").on("click", function (e) {

        e.preventDefault();

        let updateComp = true;

        if (!$("#createBasicCompetition").valid()) {

            $('.nav-tabs a[href="#details_content"]').tab('show');

            $("#createBasicCompetition").valid();

            updateComp = false;
        }

        if (!$("#savProductsContent").valid()) {

            $('.nav-tabs a[href="#products_content"]').tab('show');

            $("#savProductsContent").valid();

            updateComp = false;
        }

        if ($("#globalcustom").prop('checked') == true) {

            if ($("#savQuestionContent").valid()) {

                if ($('.correct-answer:checked').length == 0) {

                    if (!$("#answer1").closest("div").find("#correct-answer-error").length) {
                        $("#answer1").closest("div").append('<label id="correct-answer-error" class="error" for="correct_answer">At least one answer is required to be selected as correct</label>');
                    } else {
                        $("#answer1").closest("div").find("#correct-answer-error").text('At least one answer is required to be selected as correct').show();
                    }

                    $('.nav-tabs a[href="#question_content"]').tab('show');

                    updateComp = false;
                }
            } else {

                updateComp = false;
            }
        }


        if (!$("#saveLegalsContent").valid() || $("#saveLegalsContent").valid()) {

            if ($("#saveLegalsContent").valid()) {

                let comp_rule = competitionEditor.rule_editor.getData();

                let comp_faq = competitionEditor.faq_editor.getData();

                if (comp_rule == '' || comp_faq == '') {

                    if (comp_rule == '') {

                        let ruleElement = $("#saveLegalsContent").find("#rule_editor").closest("div");

                        if (!ruleElement.find("#rule_editor-error").length) {
                            ruleElement.append('<label id="rule_editor-error" class="error" for="rule_editor">This field is required.</label>');
                        } else {
                            ruleElement.find("#rule_editor-error").text('This field is required').show();
                        }
                    }

                    if (comp_faq == '') {

                        let faqElement = $("#saveLegalsContent").find("#faq_editor").closest("div");

                        if (!faqElement.find("#faq_editor-error").length) {
                            faqElement.append('<label id="faq_editor-error" class="error" for="faq_editor">This field is required.</label>');
                        } else {
                            faqElement.find("#faq_editor-error").text('This field is required').show();
                        }
                    }

                    $('.nav-tabs a[href="#legals_content"]').tab('show');

                    updateComp = false;
                }
            } else {

                updateComp = false;
            }
        }


        if ($("#enable_instant_wins").prop('checked') == true) {

            if (!$("#saveInstantContent").valid()) {

                $('.nav-tabs a[href="#insant_wins"]').tab('show');

                updateComp = false;
            }
        }


        if ($("#enable_reward_wins").prop('checked') == true) {

            if (!$("#saveRewardContent").valid()) {

                $('.nav-tabs a[href="#reward_wins"]').tab('show');

                updateComp = false;
            }
        }

        if (!updateComp) return false;

        let activeTab = $('.nav-tabs .nav-link.active').attr("id");

        saveTempData(activeTab);

        setTimeout(function () {

            let comp_record = $("[name='record']").val();

            $('<form action="' + ajax_object.ajax_url + '"><input type="hidden" name="action" value="update_competition_record" /><input type="hidden" name="update_from_temp" value="1"/><input type="hidden" name="record" value="' + comp_record + '"/></form>').appendTo('body').submit();
        }, 1000);

    });

    var total_prize_qty_for_validation = 0;

    jQuery.validator.addMethod("greaterThanQTY", function (value, element) {

        if ($("#enable_instant_wins").prop('checked') == true) {

            total_prize_qty_for_validation = 0;

            $("#saveInstantContent").find(".lineItemRow").each(function (index, prizeRow) {

                total_prize_qty_for_validation += parseInt($(prizeRow).find(".prize_qty").val());

            });

            if (value < total_prize_qty_for_validation) {

                return false;
            }

        }

        return true;

    }, "Total Sellable ticket must be greater than Instant Wins total quantity" + total_prize_qty_for_validation);

    jQuery.validator.addMethod("checkEligibility", function (value, element) {

        let mode = $("[name='mode']").val();

        if (mode == 'edit') {
            let ori_val = $(element).data("value");

            if (value < ori_val) return false;
        }
        return true;
    }, "You cannot decrease Total sellable Tickets to original value");

    jQuery.validator.addMethod("greaterThanSaleStart", function (value, element) {

        if (value == '') return true;

        let saleEndDate = new Date(parseDate(value));

        let saleStartDate = $("[name='sale_start_date']").val();

        let drawDate = $("[name='draw_date']").val();

        let closingDate = $("[name='closing_date']").val();

        if (saleStartDate == '' && closingDate == '' && drawDate == '') return true;

        let isValid = true;

        if (saleStartDate != '' && closingDate != '' && drawDate != '') {

            saleStartDate = new Date(parseDate(saleStartDate));

            drawDate = new Date(parseDate(drawDate));

            closingDate = new Date(parseDate(closingDate));

            isValid = (saleEndDate < closingDate && saleEndDate < drawDate && saleStartDate < saleEndDate);

            if (!isValid) return isValid;
        }

        if (saleStartDate != '') {

            saleStartDate = new Date(parseDate(saleStartDate));

            isValid = saleEndDate > saleStartDate;

            if (!isValid) return isValid;
        }

        if (drawDate != '') {

            drawDate = new Date(parseDate(drawDate));

            isValid = saleEndDate < drawDate;

            if (!isValid) return isValid;
        }

        if (closingDate != '') {

            closingDate = new Date(parseDate(closingDate));

            isValid = saleEndDate < closingDate;

            if (!isValid) return isValid;
        }

        return isValid;

    }, "Sale Price End must be greater than Sale Price Start");

    jQuery.validator.addMethod("lessThanSaleEnd", function (value, element) {

        if (value == '') return true;

        let saleStartDate = new Date(parseDate(value));

        let saleEndDate = $("[name='sale_end_date']").val();

        let drawDate = $("[name='draw_date']").val();

        let closingDate = $("[name='closing_date']").val();

        let isValid = true;

        if (saleEndDate == '' && closingDate == '' && drawDate == '') return true;

        if (saleEndDate != '' && closingDate != '' && drawDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            drawDate = new Date(parseDate(drawDate));

            closingDate = new Date(parseDate(closingDate));

            isValid = (saleStartDate < closingDate && saleStartDate < drawDate && saleStartDate < saleEndDate);

            if (!isValid) return isValid
        }

        if (saleEndDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            isValid = saleStartDate < saleEndDate;

            if (!isValid) return isValid;
        }

        if (drawDate != '') {

            drawDate = new Date(parseDate(drawDate));

            isValid = saleStartDate < drawDate;

            if (!isValid) return isValid;
        }

        if (closingDate != '') {

            closingDate = new Date(parseDate(closingDate));

            isValid = saleStartDate < closingDate;

            if (!isValid) return isValid;
        }

        return isValid;
    }, "Sale Price Start must be less than Sale Price End, Draw Date and Closing Date");

    jQuery.validator.addMethod("lessThanClosingDate", function (value, element) {

        let saleStartDate = $("[name='sale_start_date']").val();

        let saleEndDate = $("[name='sale_end_date']").val();

        let closingDate = $("[name='closing_date']").val();

        let drawDate = new Date(parseDate(value));

        let isValid = true;

        if (saleEndDate == '' && closingDate == '' && saleStartDate == '') return true;

        if (saleEndDate != '' && closingDate != '' && saleStartDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            saleStartDate = new Date(parseDate(saleStartDate));

            closingDate = new Date(parseDate(closingDate));

            if (!isValid) return isValid
            isValid = (saleStartDate < drawDate && saleEndDate < drawDate && closingDate >= drawDate);
        }

        if (saleEndDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            isValid = saleEndDate < drawDate;

            if (!isValid) return isValid;
        }

        if (saleStartDate != '') {

            saleStartDate = new Date(parseDate(saleStartDate));

            isValid = saleStartDate < drawDate;

            if (!isValid) return isValid;
        }

        if (closingDate != '') {

            closingDate = new Date(parseDate(closingDate));

            isValid = drawDate <= closingDate;

            if (!isValid) return isValid;
        }

        return isValid;

    }, "Draw Date must be less than or equal to Closing Date or greater than Sale Price Start and Sale Price End");

    jQuery.validator.addMethod("greaterThanDrawDate", function (value, element) {

        let saleStartDate = $("[name='sale_start_date']").val();

        let saleEndDate = $("[name='sale_end_date']").val();

        let drawDate = $("[name='draw_date']").val();

        let closingDate = new Date(parseDate(value));

        let isValid = true;

        if (saleEndDate == '' && drawDate == '' && saleStartDate == '') return true;

        if (saleEndDate != '' && drawDate != '' && saleStartDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            saleStartDate = new Date(parseDate(saleStartDate));

            drawDate = new Date(parseDate(drawDate));

            isValid = (saleStartDate < closingDate && saleEndDate < closingDate && closingDate >= drawDate);
            if (!isValid) return isValid
        }

        if (saleEndDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            isValid = saleEndDate < closingDate;

            if (!isValid) return isValid;
        }

        if (saleStartDate != '') {

            saleStartDate = new Date(parseDate(saleStartDate));

            isValid = saleStartDate < closingDate;

            if (!isValid) return isValid;
        }

        if (drawDate != '') {

            drawDate = new Date(parseDate(drawDate));

            isValid = drawDate <= closingDate;

            if (!isValid) return isValid;
        }

        return isValid;
    }, "Closing Date must be greater than Sale Price Start, Sale Price End and Draw Date");

    // Assuming the format is "dd/mm/yy"

    function parseDate(dateString) {
        return moment(dateString, 'DD/MM/YY').toDate();
    }


});
