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

    const dropArea = document.getElementById("dropArea");
    const fileInput = document.getElementById("csvFile");
    const fileNameDisplay = document.getElementById("fileName");

    // Prevent default behavior for drag events
    ["dragenter", "dragover", "dragleave", "drop"].forEach(event => {
        dropArea.addEventListener(event, (e) => e.preventDefault());
        dropArea.addEventListener(event, (e) => e.stopPropagation());
    });

    // Highlight drop area on drag over
    dropArea.addEventListener("dragover", () => {
        dropArea.style.border = "2px dashed #007bff";
    });

    // Remove highlight on drag leave
    dropArea.addEventListener("dragleave", () => {
        dropArea.style.border = "2px solid transparent";
    });

    // Handle dropped file
    dropArea.addEventListener("drop", (event) => {
        dropArea.style.border = "2px solid transparent";
        const files = event.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files; // Assign dropped file to input field
            handleCSVFile({
                target: fileInput
            }); // Trigger file handling
        }
    });

    // Handle file selection via input
    fileInput.addEventListener("change", handleCSVFile);


    function handleCSVFile(event) {
        const fileInput = event.target;

        const file = fileInput.files[0];

        if (!file) return;

        // Check file type (MIME type or extension)
        const validFileTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        const fileExtension = file.name.split('.').pop().toLowerCase();

        if (!validFileTypes.includes(file.type) && !['csv', 'xls', 'xlsx'].includes(fileExtension)) {
            alert('Please upload a valid CSV or Excel file.');
            fileInput.value = ''; // Reset file input
            return;
        }

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

        const requiredColumns = [
            "ticket_no",
            "type",
            "title",
            "value_points",
            "cash_alt",
            "image_url",
            "comp_id",
            "tickets",
            "web_order"
        ]

        const allowedTypes = ["prize", "ticket", "points", "Prize", "Ticket", "Points"]; // Allowed values for "Prize Type"

        Papa.parse(csvContent, {
            header: true,
            complete: async function (results) {

                let rows = results.data;

                // Get the headers from the parsed CSV
                const headers = results.meta.fields;
                // Check if all required columns are present
                const missingColumns = requiredColumns.filter(col => !headers.includes(col));
                if (missingColumns.length > 0) {
                    alert(`The following required columns are missing: ${missingColumns.join(", ")}`);
                    return; // Stop processing if columns are missing
                }              

                let validRows = rows.filter(row =>
                    requiredColumns.some(col => row[col]?.trim())
                );

                function isCompIdValid(compId) {
                    return new Promise((resolve, reject) => {
                        // Perform an AJAX request to validate Comp Id
                        console.log('compId+++++++++++++++++++********************', compId);

                        jQuery.ajax({
                            url: ajax_object.ajax_url, // WordPress provides ajaxurl variable
                            type: 'POST',
                            data: {
                                action: 'validate_comp_id', // Custom action
                                comp_id: compId
                            },
                            success: function (response) {
                                // console.log('response++++++++++', response.data.exists);
                                // if (response.data && response.data.exists == true) {
                                //     resolve(true);
                                // } else {
                                //     resolve(true);
                                // }
                                resolve(true);
                            },
                            error: function () {
                                reject('Error validating Comp Id');
                            }
                        });
                    });
                }

                const errors = [];
                // Validate and process each row
                for (const [index, row] of validRows.entries()) {                  

                    // Validation: Check ticket numbers are numeric
                    if (row["ticket_no"]?.trim()) {
                        const ticketNumbers = row["ticket_no"]
                            .split(",")
                            .map(num => num.trim());
                        if (!ticketNumbers.every(num => /^\d+$/.test(num))) {
                            errors.push(`ticket_no must be numeric in row ${index + 1}`);
                        }
                    }

                    // Validation: Check Prize Type
                    if (!allowedTypes.includes(row["type"]?.trim().toLowerCase())) {
                        errors.push(`Prize Type must be one of ${allowedTypes.join(", ")} in row ${index + 1}`);
                    }

                    // Validation: Check numeric fields
                    ["value_points", "cash_alt", "tickets"].forEach(col => {
                        if (row[col]?.trim() && isNaN(parseFloat(row[col]))) {
                            errors.push(`${col} must be numeric in row ${index + 1}`);
                        }
                    });

                    // Validation: Check web_order (must be 0 or 1)
                    const webOrder = row["web_order"]?.trim();
                    if (webOrder && !["0", "1"].includes(webOrder)) {
                        errors.push(`web_order must be 0 or 1 in row ${index + 1}`);
                    }


                    // Validation for Prize Type "Tickets"
                    // Validation for Prize Type "Tickets"
                    if (row["type"]?.trim().toLowerCase() === "ticket") {
                        const NumboT = row["tickets"]?.trim();

                        // Check if Number of Tickets is valid
                        if (isNaN(NumboT) || NumboT <= 0) {
                            errors.push(`Number of Tickets must be a valid positive number in row ${index + 1}`);
                        }

                        // Optional: Only validate comp_id if it exists
                        const compId = row["comp_id"]?.trim();
                        if (compId) {
                            // Check if Comp Id is a valid number
                            if (isNaN(compId) || compId <= 0) {
                                errors.push(`Comp Id must be a valid positive number in row ${index + 1}`);
                            } else {
                                // Check if Comp Id exists in the database
                                const isValidCompId = await isCompIdValid(compId);
                                if (!isValidCompId) {
                                    errors.push(`Comp Id ${compId} does not exist in the database in row ${index + 1}`);
                                }
                            }
                        }
                    }


                    if (errors.length > 0) {
                        console.error(`Validation errors in row ${index + 1}:`, errors);
                        alert(`Validation failed with the following errors:\n\n${errors.join("\n")}`);
                        return; // Stop further processing
                    }
                };



                const groupedRows = rows.reduce((acc, row) => {
                    const title = row["title"]?.trim(); // Ensure title is trimmed and not empty
                    if (!title) return acc; // Skip rows without a title

                    // Normalize ticket numbers
                    const ticketNumbers = row["ticket_no"] ?
                        row["ticket_no"]
                            .trim()
                            .split(",")
                            .map(num => num.trim())
                            .filter(num => /^\d+$/.test(num)) // Ensure ticket numbers are numeric
                        :
                        [];

                    // If the title doesn't exist in the accumulator, initialize it
                    if (!acc[title]) {
                        acc[title] = {
                            ...row, // Keep the full row data
                            ticketNumbers, // Store ticket numbers
                            totalTickets: ticketNumbers.length // Count of tickets for this title
                        };
                    } else {
                        // If the title exists, merge ticket numbers
                        acc[title].ticketNumbers = [
                            ...new Set([...acc[title].ticketNumbers, ...ticketNumbers]) // Remove duplicates
                        ];
                        // Update the total ticket count
                        acc[title].totalTickets = acc[title].ticketNumbers.length;
                    }

                    return acc;
                }, {});


                console.log('groupedRows', groupedRows);

                Object.values(groupedRows).forEach((rowData, index) => {
                    let newRow = false;

                    if (index === 0) {
                        newRow = $(".instant_content").find("#row1");
                    } else {
                        newRow = $(".instant_content").find(".lineItemCloneCopy").clone(true);
                        newRow.removeClass("lineItemCloneCopy").addClass("lineItemRow").removeClass("d-none");

                        newRow.find(".ticket_col").addClass("d-none");
                        newRow.find(".image").addClass("prize_image");

                        let newRowNum = getLineItemNextRowNumber();
                        updateRowNumberForRow(newRow, newRowNum);

                        newRow.appendTo($("#instant_wins_content"));
                    }

                    newRow.find(".title").val(rowData["title"]);
                    newRow.find(".price_type").val(rowData["type"]);
                    newRow.find(".quantity").val(rowData["totalTickets"]).trigger("focusout");

                    if (rowData["type"] === "points") {
                        newRow.find(".price_type").val('Points');

                    }

                    if (rowData["type"] === "prize") {
                        newRow.find(".price_type").val('Prize');
                    }

                    // Handle Prize Type-specific logic
                    if (rowData["type"] === "ticket") {
                        newRow.find(".price_type").val('Tickets');
                        // Hide cash_value and prize_value, unhide ticket_col
                        newRow.find(".value_col").addClass("d-none");
                        newRow.find(".ticket_col").removeClass("d-none");

                        // Set competition_prize and quantity if available
                        const selectedCompId = rowData["comp_id"];
                        newRow.find(".competition_prize").val(selectedCompId);
                        newRow.find(".prize_total_tickets").val(rowData["tickets"]);
                    } else {
                        // Show cash_value and prize_value, hide ticket_col
                        newRow.find(".value_col").removeClass("d-none");
                        newRow.find(".ticket_col").addClass("d-none");

                        // Set cash_value and prize_value
                        // Set cash_value and prize_value
                        if (rowData["type"] === "prize") {
                            newRow.find(".cash_value").val(rowData["cash_alt"]);

                        } else if (rowData["type"] === "points") {
                            newRow.find(".cash_value").val(rowData["value_points"]);

                        }
                    }

                    registerWPMediaFrame(newRow);

                    if (rowData["image_url"]?.trim()) {

                        newRow.find(".wp_media_frame").addClass("d-none");
                        newRow.find(".image_preview_container").removeClass("d-none");
                        newRow.find(".image_preview_container .img-content").html(
                            `<img src="${rowData["image_url"]}" alt="" width="150px" height="150px">`
                        );
                        newRow.find(".prize_image").val(rowData["image_url"]);
                    }

                    const ticketNumbers = rowData.ticketNumbers;

                    // Ensure that all ticket numbers are correctly mapped to the input fields
                    newRow.find(".prize_tickets .form-control").each((ticketIndex, input) => {
                        if (ticketNumbers[ticketIndex]) {
                            $(input).val(ticketNumbers[ticketIndex]);
                        } else {
                            // If there are more input fields than ticket numbers, clear remaining fields
                            $(input).val('');
                        }
                    });

                    // Handle checkbox for webOrderInstant based on value (0 or 1)
                    const webOrderInstantChecked = rowData["web_order"] === "1";
                    newRow.find(".webOrderInstant").prop("checked", webOrderInstantChecked);

                    checkLineItemRow();
                });



            }
        });

    }

    function processCSVContent(csvContent) {      

        const requiredColumns = [
            "type",
            "title",
            "value_points",
            "cash_alt",
            "image_url",
            "comp_id",
            "tickets",
            "web_order"
        ]


        const allowedTypes = ["prize", "ticket", "points", "Prize", "Ticket", "Points"]; // Allowed values for "Prize Type"

        Papa.parse(csvContent, {
            header: true,
            complete: async function (results) {

                let rows = results.data;

                // Get the headers from the parsed CSV
                const headers = results.meta.fields;
                // Check if all required columns are present
                const missingColumns = requiredColumns.filter(col => !headers.includes(col));
                if (missingColumns.length > 0) {
                    alert(`The following required columns are missing: ${missingColumns.join(", ")}`);
                    return; // Stop processing if columns are missing
                }


                // Filter out empty rows
                let validRows = rows.filter(row =>
                    requiredColumns.some(col => row[col]?.trim())
                );


                function isCompIdValid(compId) {
                    return new Promise((resolve, reject) => {
                        // Perform an AJAX request to validate Comp Id

                        jQuery.ajax({
                            url: ajax_object.ajax_url, // WordPress provides ajaxurl variable
                            type: 'POST',
                            data: {
                                action: 'validate_comp_id', // Custom action
                                comp_id: compId
                            },
                            success: function (response) {
                                if (response.data && response.data.exists == true) {
                                    resolve(true);
                                } else {
                                    resolve(false);
                                }
                            },
                            error: function () {
                                reject('Error validating Comp Id');
                            }
                        });
                    });
                }

                const errors = [];


                // Validate and process each row
                for (const [index, row] of validRows.entries()) {  

                    // Validation: Check Prize Type
                    if (!allowedTypes.includes(row["type"]?.trim().toLowerCase())) {
                        errors.push(`Prize Type must be one of ${allowedTypes.join(", ")} in row ${index + 1}`);
                    }

                    // Validation: Check numeric fields
                    ["value_points", "cash_alt", "tickets"].forEach(col => {
                        if (row[col]?.trim() && isNaN(parseFloat(row[col]))) {
                            errors.push(`${col} must be numeric in row ${index + 1}`);
                        }
                    });

                    // Validation: Check web_order (must be 0 or 1)
                    const webOrder = row["web_order"]?.trim();
                    if (webOrder && !["0", "1"].includes(webOrder)) {
                        errors.push(`web_order must be 0 or 1 in row ${index + 1}`);
                    }


                    // Validation for Prize Type "Tickets"
                    if (row["type"]?.trim().toLowerCase() === "ticket") {
                        const NumboT = row["tickets"]?.trim();

                        // Check if Number of Tickets is valid
                        if (isNaN(NumboT) || NumboT <= 0) {
                            errors.push(`Number of Tickets must be a valid positive number in row ${index + 1}`);
                        }

                        // Optional: Only validate comp_id if it exists
                        const compId = row["comp_id"]?.trim();
                        if (compId) {
                            // Check if Comp Id is a valid number
                            if (isNaN(compId) || compId <= 0) {
                                errors.push(`Comp Id must be a valid positive number in row ${index + 1}`);
                            } else {
                                // Check if Comp Id exists in the database
                                const isValidCompId = await isCompIdValid(compId);
                                if (!isValidCompId) {
                                    errors.push(`Comp Id ${compId} does not exist in the database in row ${index + 1}`);
                                }
                            }
                        }
                    }

                    if (errors.length > 0) {
                        console.error(`Validation errors in row ${index + 1}:`, errors);
                        alert(`Validation failed with the following errors:\n\n${errors.join("\n")}`);
                        return; // Stop further processing
                    }
                };

                const groupedRows = rows.reduce((acc, row) => {
                    const title = row["title"]?.trim(); // Ensure title is trimmed and not empty
                    if (!title) return acc; // Skip rows without a title

                    // If the title doesn't exist in the accumulator, add the row
                    if (!acc[title]) {
                        acc[title] = row; // Store the full row as-is
                    }

                    return acc;
                }, {});

                Object.values(groupedRows).forEach((rowData, index) => {
                    let newRow = false;

                    if (index === 0) {
                        newRow = $(".reward_content").find("#row1");
                    } else {
                        newRow = $(".reward_content").find(".lineItemCloneCopy").clone(true);
                        newRow.removeClass("lineItemCloneCopy").addClass("lineItemRow").removeClass("d-none");

                        newRow.find(".ticket_col").addClass("d-none");
                        newRow.find(".image").addClass("prize_image");

                        let newRowNum = getRewardLineItemNextRowNumber();
                        updateRewardRowNumberForRow(newRow, newRowNum);

                        newRow.appendTo($("#reward_wins_content"));
                    }

                    newRow.find(".title").val(rowData["title"]);
                    newRow.find(".price_type").val(rowData["type"]);
                    newRow.find(".quantity").val(rowData["totalTickets"]).trigger("focusout");

                    if (rowData["type"] === "points") {
                        newRow.find(".price_type").val('Points');

                    }

                    if (rowData["type"] === "prize") {
                        newRow.find(".price_type").val('Prize');
                    }

                    // Handle Prize Type-specific logic
                    if (rowData["type"] === "ticket") {
                        newRow.find(".price_type").val('Tickets');

                        // Hide cash_value and prize_value, unhide ticket_col
                        newRow.find(".value_col").addClass("d-none");
                        newRow.find(".ticket_col").removeClass("d-none");

                        // Set competition_prize and quantity if available
                        const selectedCompId = rowData["comp_id"];
                        newRow.find(".competition_prize").val(selectedCompId);
                        newRow.find(".prize_total_tickets").val(rowData["tickets"]);
                    } else {
                        // Show cash_value and prize_value, hide ticket_col
                        newRow.find(".value_col").removeClass("d-none");
                        newRow.find(".ticket_col").addClass("d-none");

                        // Set cash_value and prize_value
                        if (rowData["type"] == "prize") {
                            newRow.find(".cash_value").val(rowData["cash_alt"]);

                        } else {
                            newRow.find(".cash_value").val(rowData["value_points"]);

                        }
                    }

                    registerWPMediaFrame(newRow);

                    if (rowData["image_url"]?.trim()) {

                        newRow.find(".wp_media_frame").addClass("d-none");
                        newRow.find(".image_preview_container").removeClass("d-none");
                        newRow.find(".image_preview_container .img-content").html(
                            `<img src="${rowData["image_url"]}" alt="" width="150px" height="150px">`
                        );
                        newRow.find(".prize_image").val(rowData["image_url"]);
                    }

                    // Handle checkbox for webOrderReward based on value (0 or 1)
                    const webOrderInstantChecked = rowData["web_order"] == "1";
                    newRow.find(".webOrderReward").prop("checked", webOrderInstantChecked);

                    checkRewardLineItemRow();
                });




            }
        });
    }


    var ajax_object = {
        ajax_url: "/competition/wp-admin/admin-ajax.php"
    }

    var competitionEditor = {};

    var selectedGalleryImageUrls = [];

    var currentActiveTab = false;

    CKEDITOR.config.toolbar = [
        { name: 'document', items: ['Source'] },
        { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
        { name: 'basicstyles', items: ['Bold', 'Italic', '|', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
        { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language'] },
        { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
        { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'PageBreak'] },
        { name: 'table', items: ['Table', 'TableProperties', 'TableCellProperties', 'TableToolbar', 'TableColumn', 'TableRow'] },
    ];

    // Enable the Table Tools plugin
    CKEDITOR.config.extraPlugins = 'tabletools,table';

    // Preserve table structure when copying
    CKEDITOR.config.allowedContent = true;

    CKEDITOR.replace('description_editor');
    competitionEditor.description_editor = CKEDITOR.instances.description_editor;

    CKEDITOR.replace('live_draw_info');
    competitionEditor.live_draw_info = CKEDITOR.instances.live_draw_info;



    //CKEDITOR.replace('faq_editor');
    //competitionEditor.faq_editor = CKEDITOR.instances.faq_editor;

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


    function toggleInstantWinsContent() {

        if ($("#enable_instant_wins").prop('checked')) {
            $(".total_prize_label").show();
            $(".addInstantWinHeaderLabel").show();
            $("#instant_wins_content").show();
            $(".addInstantWinPlus").show();



        } else {
            $(".total_prize_label").hide();
            $("#instant_wins_content").hide();
            $(".addInstantWinPlus").hide();
            $(".addInstantWinHeaderLabel").hide();



        }
    }

    // Trigger the function when the checkbox is clicked
    $("#enable_instant_wins").on('change', function () {
        toggleInstantWinsContent();
    });

    // Initial check on page load
    toggleInstantWinsContent();


    function rewardWinTogglebutton() {

        if ($("#enable_reward_wins").prop('checked')) {
            $(".total_prize_label").show();
            $(".rewardWinHeaderLabel").show();
            $("#reward_wins_content").show();
            $(".rewardWinPlus").show();


        } else {
            $(".total_prize_label").hide();
            $("#reward_wins_content").hide();
            $(".rewardWinPlus").hide();
            $(".rewardWinHeaderLabel").hide();

        }
    }

    // Trigger the function when the checkbox is clicked
    $("#enable_reward_wins").on('change', function () {
        rewardWinTogglebutton();
    });

    // Initial check on page load
    rewardWinTogglebutton();







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
            $("#save_as_draft").addClass("d-none");

            $("#cancel_btn").removeClass("d-none");
        } else {
            $("#back_btn").removeClass("d-none");
            $("#cancel_btn").addClass("d-none");
            $("#save_as_draft").removeClass("d-none");
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
        },
        prize_value: {
            required: true
        }
    });

    $("#saveInstantContent").validate({
        ignore: ":hidden:not(.prize_image)",
    });

    $("#saveRewardContent").validate(
        { ignore: ":hidden:not(.prize_image)" }
    );



    $(".create_competition").on('click', function (e) {

        let btn_id = $(e.currentTarget).attr("id");

        var isValid = true;

        if (btn_id == 'save_as_draft') {
            console.log('update_competition_record_draft');
            let activeTab = $('.tab-pane.active').find("[name='step']").val();
            // if (activeTab == 'details') {

            //     if ($("#createBasicCompetition").valid()) {

            //         let comp_desc = competitionEditor.description_editor.getData();

            //         if (comp_desc == '') {

            //             let editorElement = $("#createBasicCompetition").find("#description_editor").closest("div");

            //             if (!editorElement.find("#description_editor-error").length) {
            //                 editorElement.append('<label id="description_editor-error" class="error" for="description_editor">This field is required.</label>');
            //             }

            //         } else {

            //             createCompetition($("#createBasicCompetition"));

            //         }
            //     }
            // }

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

                        updateCompetitiondraft($("#saveLegalsContent"));
                    }
                }
            }

            if (activeTab == 'products') {

                console.log('active tab');
                if ($("#savProductsContent").valid()) {

                    let comp_draw_info = competitionEditor.live_draw_info.getData();
                    let checkboxStatus = $('#mySwitch').is(':checked');



                    if (comp_draw_info == '' && ($("#mySwitch").prop('checked') == true)) {

                        let editorElement = $("#savProductsContent").find("#live_draw_info").closest("div");

                        if (!editorElement.find("#live_draw_info-error").length) {
                            editorElement.append('<label id="live_draw_info-error" class="error" for="live_draw_info">This field is required.</label>');
                        }

                    } else {

                        updateCompetitiondraft($("#savProductsContent"));

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
                            updateCompetitiondraft($("#savQuestionContent"));
                        }
                    }
                } else {

                    // $('.nav-tabs').find(".active").removeClass("active-accent").addClass("tab-lighter");
                    // $('.nav-tabs a[href="#insant_wins"]').tab('show');
                    // $('.nav-tabs').find(".active").addClass("active-accent");
                    updateCompetitiondraft($("#savQuestionContent"));

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

                        if ($("#saveInstantContent").valid() && validateFields()) {

                            updateLineItemElementByOrder();

                            $("#total_prizes").val($(".instant_content").find(".lineItemRow").length);

                            updateCompetitiondraft($("#saveInstantContent"));

                        }
                    } else {

                        if (!$("#qty-error").length) {

                            $("#instant_wins_content").prepend('<div class="text-center mb-3"><label id="qty-error" class="error" for="qty-error">Prize quantity must be less than Total Sellable ticket.</label></div>');

                        }
                    }
                } else {
                    updateCompetitiondraft($("#saveInstantContent"));
                    /*let comp_tickets = $("#prize_ticket_list").val();
    
                    if(typeof comp_tickets != 'undefined' && comp_tickets == ''){
    
                        $("#instant_wins_content").prepend('<div class="text-center mb-3"><label id="ticket-error" class="error" for="qty-error">Please generate tickets for this competition.</label></div>');
    
                        return false;
    
                    } else {
                    
                        $('.nav-tabs').find(".active").removeClass("active-accent").addClass("tab-lighter");
                        $('.nav-tabs a[href="#reward_wins"]').tab('show');
                        $('.nav-tabs').find(".active").addClass("active-accent");
                    }*/

                    // $('.nav-tabs').find(".active").removeClass("active-accent").addClass("tab-lighter");
                    // $('.nav-tabs a[href="#reward_wins"]').tab('show');
                    // $('.nav-tabs').find(".active").addClass("active-accent");
                }
            }

            if (activeTab == 'reward') {

                if ($("#enable_reward_wins").prop('checked') == true) {

                    if ($("#saveRewardContent").valid() && validateRewardFields()) {

                        updateRewardLineItemElementByOrder();

                        $("#total_reward").val($(".reward_content").find(".lineItemRow").length);

                        updateCompetitiondraft($("#saveRewardContent"));

                    }

                } else {

                    updateCompetitiondraft($("#saveRewardContent"));

                }
            }

        } else {

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

                console.log('active tab');
                if ($("#savProductsContent").valid()) {

                    let comp_draw_info = competitionEditor.live_draw_info.getData();
                    let checkboxStatus = $('#mySwitch').is(':checked');



                    if (comp_draw_info == '' && ($("#mySwitch").prop('checked') == true)) {

                        let editorElement = $("#savProductsContent").find("#live_draw_info").closest("div");

                        if (!editorElement.find("#live_draw_info-error").length) {
                            editorElement.append('<label id="live_draw_info-error" class="error" for="live_draw_info">This field is required.</label>');
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
                    $('.nav-tabs a[href="#insant_wins"]').tab('show');
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

                        if ($("#saveInstantContent").valid() && validateFields()) {

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

                    if ($("#saveRewardContent").valid() && validateRewardFields()) {

                        updateRewardLineItemElementByOrder();

                        $("#total_reward").val($(".reward_content").find(".lineItemRow").length);

                        updateCompetition($("#saveRewardContent"));

                    }

                } else {

                    updateCompetition($("#saveRewardContent"));

                }
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
            $('.custom_label').addClass('text-secondary');
            // Uncheck all checkboxes with the class 'customdisable'
            $('.customdisable').prop('checked', false);
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

        let selectedOptions = getSelectedColumns();

        data.sortedSelections = JSON.stringify(selectedOptions);

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
                // let res = JSON.parse(response);
                if (data.step == 'products') {
                    $('.nav-tabs a[href="#question_content"]').tab('show');
                } else if (data.step == 'question') {
                    $('.nav-tabs a[href="#insant_wins"]').tab('show');
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

    function updateCompetitiondraft(form) {

        let formData = form.serializeArray();
        console.log('formData', formData);
        var data = {
            action: 'update_competition_record_draft',
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
                // let res = JSON.parse(response);
                // if (data.step == 'products') {
                //     $('.nav-tabs a[href="#question_content"]').tab('show');
                // } else if (data.step == 'question') {
                //     $('.nav-tabs a[href="#insant_wins"]').tab('show');
                // } else if (data.step == 'legals') {
                //     $('.nav-tabs a[href="#insant_wins"]').tab('show');
                // } else if (data.step == 'instant') {
                //     $('.nav-tabs a[href="#reward_wins"]').tab('show');
                // } else if (data.step == 'reward') {
                //     window.location.href = "admin.php?page=competitions_menu";
                // }
                // if (data.step != 'reward') {
                //     $('.nav-tabs').find(".active").addClass("active-accent");
                // }
                window.location.href = base_wp_url + '/wp-admin/admin.php?page=competitions_menu';

            },
        });
    }

    checkLineItemRow();

    setTotalInstantPrizeCount();

    setInstantPrizeDescriptionEditor();

    var instantRow = $(".instant_content").find(".lineItemRow").length;

    $("#add_price", $(".instant_content")).on("click", function (e) {

        let total_purchasing = $("#total_ticket_purchased").val();

        if (total_purchasing > 0 && mode == 'edit') {
            return true;
        }

        e.preventDefault();

        let newRow = $(".instant_content").find(".lineItemCloneCopy").clone(true);

        newRow.removeClass("lineItemCloneCopy").addClass("lineItemRow").removeClass("d-none");

        newRow.find('.ticket_col').addClass('d-none')

        newRow.find(".image").addClass("prize_image");

        var newRowNum = getLineItemNextRowNumber();

        updateRowNumberForRow(newRow, newRowNum);

        newRow.appendTo($("#instant_wins_content"));

        checkLineItemRow();

        registerWPMediaFrame(newRow);

        newRow.find(".price_type").trigger("change");

        setTotalInstantPrizeCount();

        setInstantPrizeDescriptionEditor();

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

        setTotalInstantPrizeCount();
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

        setTotalInstantPrizeCount();
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

        let idFields = new Array('title', 'cash_value', 'prize_value', 'webOrderInstant', 'price_type', 'quantity', 'image', 'prize_total_tickets', 'competition_prize', 'prize_description', 'show_description');

        let expectedRowId = 'row' + expectedSequenceNumber;

        for (let idIndex in idFields) {
            let elementId = idFields[idIndex];
            let actualElementId = elementId + currentSequenceNumber;
            let expectedElementId = elementId + expectedSequenceNumber;
            lineItemRow.find('#' + actualElementId).attr('id', expectedElementId)
                .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);

            if (elementId == 'show_description') {
                lineItemRow.find(".show_description").filter('[name="' + actualElementId + '"]').attr('name', expectedElementId)
            }
        }

        lineItemRow.attr('id', expectedRowId).attr('data-row', expectedSequenceNumber);

        lineItemRow.find('input.rowNumber').val(expectedSequenceNumber);

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

        newRow.find('.ticket_col').addClass('d-none')

        newRow.find(".image").addClass("prize_image");

        var newRowNum = getRewardLineItemNextRowNumber();

        updateRewardRowNumberForRow(newRow, newRowNum);

        newRow.appendTo($("#reward_wins_content"));

        checkRewardLineItemRow();

        updateAvailablePercentages();

        registerWPMediaFrame(newRow);

        validateRewardFields();
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

        let idFields = new Array('title', 'cash_value', 'prize_value', 'webOrderReward', 'price_type', 'prct_available', 'image', 'competition_prize', 'prize_total_tickets');

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
    console.log("Drag and drop script loaded!");


    // document.getElementById('csvFile').addEventListener('change', handleCSVFile);
    // document.addEventListener("DOMContentLoaded", function () {

    //     const dropZone = document.getElementById("drop-zone");
    //     const fileInput = document.getElementById("csvFile");

    //     // Prevent default behavior for drag events
    //     ["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
    //         dropZone.addEventListener(eventName, (e) => {
    //             e.preventDefault();
    //             e.stopPropagation();
    //         });
    //     });

    //     // Highlight drop zone when file is dragged over
    //     ["dragenter", "dragover"].forEach(eventName => {
    //         dropZone.addEventListener(eventName, () => {
    //             dropZone.classList.add("highlight");
    //         });
    //     });

    //     // Remove highlight when dragging leaves or file is dropped
    //     ["dragleave", "drop"].forEach(eventName => {
    //         dropZone.addEventListener(eventName, () => {
    //             dropZone.classList.remove("highlight");
    //         });
    //     });

    //     // Handle file drop
    //     dropZone.addEventListener("drop", (e) => {
    //         const files = e.dataTransfer.files;
    //         if (files.length) {
    //             fileInput.files = files; // Assign dropped file to input
    //             handleCSVFile({ target: fileInput }); // Trigger the existing file handling function
    //         }
    //     });

    //     // Handle file selection via click (already in your code)
    //     fileInput.addEventListener("change", handleCSVFile);
    // });

    // function handleCSVFile(event) {

    //     const fileInput = event.target;

    //     const file = fileInput.files[0];

    //     if (!file) return;

    //     // Check file type (MIME type or extension)
    //     const validFileTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    //     const fileExtension = file.name.split('.').pop().toLowerCase();

    //     if (!validFileTypes.includes(file.type) && !['csv', 'xls', 'xlsx'].includes(fileExtension)) {
    //         alert('Please upload a valid CSV or Excel file.');
    //         fileInput.value = ''; // Reset file input
    //         return;
    //     }

    //     if (file) {

    //         const reader = new FileReader();

    //         reader.onload = function (e) {

    //             $(".btn-close").trigger("click");

    //             const csvContent = e.target.result;

    //             let activeTab = $('.tab-pane.active').find("[name='step']").val();

    //             if (activeTab == 'instant') {
    //                 processInstantWinCSVContent(csvContent);
    //             } else if (activeTab == 'reward') {
    //                 processCSVContent(csvContent);
    //             }

    //         };

    //         reader.readAsText(file);
    //     }
    // }


    // function processInstantWinCSVContent(csvContent) {

    //     // const requiredColumns = [
    //     //     "Title",
    //     //     "Prize Type",
    //     //     "Cash Value",
    //     //     "Quantity",
    //     //     "Image URL",
    //     //     "Ticket Numbers",
    //     //     "Web Order",
    //     //     "Comp Id",
    //     //     "Prize Value",
    //     //     "Number of Tickets"

    //     // ];

    //     const requiredColumns = [
    //         "ticket_no",
    //         "type",
    //         "title",
    //         "value_points",
    //         "cash_alt",
    //         "image_url",
    //         "comp_id",
    //         "tickets",
    //         "web_order"
    //     ]

    //     const allowedTypes = ["prize", "ticket", "points", "Prize", "Ticket", "Points"]; // Allowed values for "Prize Type"

    //     Papa.parse(csvContent, {
    //         header: true,
    //         complete: async function (results) {

    //             let rows = results.data;

    //             // Get the headers from the parsed CSV
    //             const headers = results.meta.fields;
    //             // Check if all required columns are present
    //             const missingColumns = requiredColumns.filter(col => !headers.includes(col));
    //             if (missingColumns.length > 0) {
    //                 alert(`The following required columns are missing: ${missingColumns.join(", ")}`);
    //                 return; // Stop processing if columns are missing
    //             }


    //             // Filter out empty rows
    //             // const validRows = rows.filter(row => {
    //             //     // Check if all required fields have non-empty values
    //             //     return requiredColumns.some(col => row[col]?.trim());
    //             // });

    //             let validRows = rows.filter(row =>
    //                 requiredColumns.some(col => row[col]?.trim())
    //             );

    //             function isCompIdValid(compId) {
    //                 return new Promise((resolve, reject) => {
    //                     // Perform an AJAX request to validate Comp Id
    //                     console.log('compId+++++++++++++++++++********************', compId);

    //                     jQuery.ajax({
    //                         url: ajax_object.ajax_url, // WordPress provides ajaxurl variable
    //                         type: 'POST',
    //                         data: {
    //                             action: 'validate_comp_id', // Custom action
    //                             comp_id: compId
    //                         },
    //                         success: function (response) {
    //                             console.log('response++++++++++', response.data.exists);
    //                             if (response.data && response.data.exists == true) {
    //                                 resolve(true);
    //                             } else {
    //                                 resolve(false);
    //                             }
    //                         },
    //                         error: function () {
    //                             reject('Error validating Comp Id');
    //                         }
    //                     });
    //                 });
    //             }

    //             const errors = [];
    //             // Validate and process each row
    //             for (const [index, row] of validRows.entries()) {


    //                 // Validation: Check required fields
    //                 // requiredColumns.forEach(col => {
    //                 //     if (!row[col]?.trim()) {
    //                 //         errors.push(`${col} column is missing in row ${index + 1}`);
    //                 //     }
    //                 // });

    //                 // Validation: Check ticket numbers are numeric
    //                 if (row["ticket_no"]?.trim()) {
    //                     const ticketNumbers = row["ticket_no"]
    //                         .split(",")
    //                         .map(num => num.trim());
    //                     if (!ticketNumbers.every(num => /^\d+$/.test(num))) {
    //                         errors.push(`ticket_no must be numeric in row ${index + 1}`);
    //                     }
    //                 }

    //                 // Validation: Check Prize Type
    //                 if (!allowedTypes.includes(row["type"]?.trim().toLowerCase())) {
    //                     errors.push(`Prize Type must be one of ${allowedTypes.join(", ")} in row ${index + 1}`);
    //                 }

    //                 // Validation: Check numeric fields
    //                 ["value_points", "cash_alt", "tickets"].forEach(col => {
    //                     if (row[col]?.trim() && isNaN(parseFloat(row[col]))) {
    //                         errors.push(`${col} must be numeric in row ${index + 1}`);
    //                     }
    //                 });

    //                 // Validation: Check web_order (must be 0 or 1)
    //                 const webOrder = row["web_order"]?.trim();
    //                 if (webOrder && !["0", "1"].includes(webOrder)) {
    //                     errors.push(`web_order must be 0 or 1 in row ${index + 1}`);
    //                 }


    //                 // Validation for Prize Type "Tickets"
    //                 // Validation for Prize Type "Tickets"
    //                 if (row["type"]?.trim().toLowerCase() === "ticket") {
    //                     const NumboT = row["tickets"]?.trim();

    //                     // Check if Number of Tickets is valid
    //                     if (isNaN(NumboT) || NumboT <= 0) {
    //                         errors.push(`Number of Tickets must be a valid positive number in row ${index + 1}`);
    //                     }

    //                     // Optional: Only validate comp_id if it exists
    //                     const compId = row["comp_id"]?.trim();
    //                     if (compId) {
    //                         // Check if Comp Id is a valid number
    //                         if (isNaN(compId) || compId <= 0) {
    //                             errors.push(`Comp Id must be a valid positive number in row ${index + 1}`);
    //                         } else {
    //                             // Check if Comp Id exists in the database
    //                             const isValidCompId = await isCompIdValid(compId);
    //                             if (!isValidCompId) {
    //                                 errors.push(`Comp Id ${compId} does not exist in the database in row ${index + 1}`);
    //                             }
    //                         }
    //                     }
    //                 }


    //                 if (errors.length > 0) {
    //                     console.error(`Validation errors in row ${index + 1}:`, errors);
    //                     alert(`Validation failed with the following errors:\n\n${errors.join("\n")}`);
    //                     return; // Stop further processing
    //                 }
    //             };



    //             const groupedRows = rows.reduce((acc, row) => {
    //                 const title = row["title"]?.trim(); // Ensure title is trimmed and not empty
    //                 if (!title) return acc; // Skip rows without a title

    //                 // Normalize ticket numbers
    //                 const ticketNumbers = row["ticket_no"]
    //                     ? row["ticket_no"]
    //                         .trim()
    //                         .split(",")
    //                         .map(num => num.trim())
    //                         .filter(num => /^\d+$/.test(num)) // Ensure ticket numbers are numeric
    //                     : [];

    //                 // If the title doesn't exist in the accumulator, initialize it
    //                 if (!acc[title]) {
    //                     acc[title] = {
    //                         ...row, // Keep the full row data
    //                         ticketNumbers, // Store ticket numbers
    //                         totalTickets: ticketNumbers.length // Count of tickets for this title
    //                     };
    //                 } else {
    //                     // If the title exists, merge ticket numbers
    //                     acc[title].ticketNumbers = [
    //                         ...new Set([...acc[title].ticketNumbers, ...ticketNumbers]) // Remove duplicates
    //                     ];
    //                     // Update the total ticket count
    //                     acc[title].totalTickets = acc[title].ticketNumbers.length;
    //                 }

    //                 return acc;
    //             }, {});


    //             console.log('groupedRows', groupedRows);

    //             Object.values(groupedRows).forEach((rowData, index) => {
    //                 let newRow = false;

    //                 if (index === 0) {
    //                     newRow = $(".instant_content").find("#row1");
    //                 } else {
    //                     newRow = $(".instant_content").find(".lineItemCloneCopy").clone(true);
    //                     newRow.removeClass("lineItemCloneCopy").addClass("lineItemRow").removeClass("d-none");

    //                     newRow.find(".ticket_col").addClass("d-none");
    //                     newRow.find(".image").addClass("prize_image");

    //                     let newRowNum = getLineItemNextRowNumber();
    //                     updateRowNumberForRow(newRow, newRowNum);

    //                     newRow.appendTo($("#instant_wins_content"));
    //                 }

    //                 newRow.find(".title").val(rowData["title"]);
    //                 newRow.find(".price_type").val(rowData["type"]);
    //                 newRow.find(".quantity").val(rowData["totalTickets"]).trigger("focusout");

    //                 if (rowData["type"] === "points") {
    //                     newRow.find(".price_type").val('Points');

    //                 }

    //                 if (rowData["type"] === "prize") {
    //                     newRow.find(".price_type").val('Prize');
    //                 }

    //                 // Handle Prize Type-specific logic
    //                 if (rowData["type"] === "ticket") {
    //                     newRow.find(".price_type").val('Tickets');
    //                     // Hide cash_value and prize_value, unhide ticket_col
    //                     newRow.find(".value_col").addClass("d-none");
    //                     newRow.find(".ticket_col").removeClass("d-none");

    //                     // Set competition_prize and quantity if available
    //                     const selectedCompId = rowData["comp_id"];
    //                     newRow.find(".competition_prize").val(selectedCompId);
    //                     newRow.find(".prize_total_tickets").val(rowData["tickets"]);
    //                 } else {
    //                     // Show cash_value and prize_value, hide ticket_col
    //                     newRow.find(".value_col").removeClass("d-none");
    //                     newRow.find(".ticket_col").addClass("d-none");

    //                     // Set cash_value and prize_value
    //                     // Set cash_value and prize_value
    //                     if (rowData["type"] === "prize") {
    //                         newRow.find(".cash_value").val(rowData["cash_alt"]);

    //                     } else if (rowData["type"] === "points") {
    //                         newRow.find(".cash_value").val(rowData["value_points"]);

    //                     }
    //                 }

    //                 registerWPMediaFrame(newRow);

    //                 if (rowData["image_url"]?.trim()) {

    //                     newRow.find(".wp_media_frame").addClass("d-none");
    //                     newRow.find(".image_preview_container").removeClass("d-none");
    //                     newRow.find(".image_preview_container .img-content").html(
    //                         `<img src="${rowData["image_url"]}" alt="" width="150px" height="150px">`
    //                     );
    //                     newRow.find(".prize_image").val(rowData["image_url"]);
    //                 }

    //                 const ticketNumbers = rowData.ticketNumbers;

    //                 // Ensure that all ticket numbers are correctly mapped to the input fields
    //                 newRow.find(".prize_tickets .form-control").each((ticketIndex, input) => {
    //                     if (ticketNumbers[ticketIndex]) {
    //                         $(input).val(ticketNumbers[ticketIndex]);
    //                     } else {
    //                         // If there are more input fields than ticket numbers, clear remaining fields
    //                         $(input).val('');
    //                     }
    //                 });

    //                 // Handle checkbox for webOrderInstant based on value (0 or 1)
    //                 const webOrderInstantChecked = rowData["web_order"] === "1";
    //                 newRow.find(".webOrderInstant").prop("checked", webOrderInstantChecked);

    //                 checkLineItemRow();
    //             });



    //         }
    //     });

    // }

    // function processCSVContent(csvContent) {

    //     // const requiredColumns = [
    //     //     "Title",
    //     //     "Prize Type",
    //     //     "Cash Value",
    //     //     "Percentage",
    //     //     "Image URL",
    //     //     "Web Order",
    //     //     "Comp Id",
    //     //     "Prize Value",
    //     //     "Number of Tickets"
    //     // ];

    //     const requiredColumns = [
    //         "type",
    //         "title",
    //         "value_points",
    //         "cash_alt",
    //         "image_url",
    //         "comp_id",
    //         "tickets",
    //         "web_order"
    //     ]


    //     const allowedTypes = ["prize", "ticket", "points", "Prize", "Ticket", "Points"]; // Allowed values for "Prize Type"

    //     Papa.parse(csvContent, {
    //         header: true,
    //         complete: async function (results) {

    //             let rows = results.data;

    //             // Get the headers from the parsed CSV
    //             const headers = results.meta.fields;
    //             // Check if all required columns are present
    //             const missingColumns = requiredColumns.filter(col => !headers.includes(col));
    //             if (missingColumns.length > 0) {
    //                 alert(`The following required columns are missing: ${missingColumns.join(", ")}`);
    //                 return; // Stop processing if columns are missing
    //             }


    //             // Filter out empty rows
    //             let validRows = rows.filter(row =>
    //                 requiredColumns.some(col => row[col]?.trim())
    //             );


    //             function isCompIdValid(compId) {
    //                 return new Promise((resolve, reject) => {
    //                     // Perform an AJAX request to validate Comp Id
    //                     console.log('compId+++++++++++++++++++********************', compId);

    //                     jQuery.ajax({
    //                         url: ajax_object.ajax_url, // WordPress provides ajaxurl variable
    //                         type: 'POST',
    //                         data: {
    //                             action: 'validate_comp_id', // Custom action
    //                             comp_id: compId
    //                         },
    //                         success: function (response) {
    //                             console.log('response++++++++++', response.data.exists);
    //                             if (response.data && response.data.exists == true) {
    //                                 resolve(true);
    //                             } else {
    //                                 resolve(false);
    //                             }
    //                         },
    //                         error: function () {
    //                             reject('Error validating Comp Id');
    //                         }
    //                     });
    //                 });
    //             }

    //             const errors = [];


    //             // Validate and process each row
    //             for (const [index, row] of validRows.entries()) {


    //                 // Validation: Check required fields
    //                 // requiredColumns.forEach(col => {
    //                 //     if (!row[col]?.trim()) {
    //                 //         errors.push(`${col} is missing in row ${index + 1}`);
    //                 //     }
    //                 // });



    //                 // Validation: Check Prize Type
    //                 if (!allowedTypes.includes(row["type"]?.trim().toLowerCase())) {
    //                     errors.push(`Prize Type must be one of ${allowedTypes.join(", ")} in row ${index + 1}`);
    //                 }

    //                 // Validation: Check numeric fields
    //                 ["value_points", "cash_alt", "tickets"].forEach(col => {
    //                     if (row[col]?.trim() && isNaN(parseFloat(row[col]))) {
    //                         errors.push(`${col} must be numeric in row ${index + 1}`);
    //                     }
    //                 });

    //                 // Validation: Check web_order (must be 0 or 1)
    //                 const webOrder = row["web_order"]?.trim();
    //                 if (webOrder && !["0", "1"].includes(webOrder)) {
    //                     errors.push(`web_order must be 0 or 1 in row ${index + 1}`);
    //                 }


    //                 // Validation for Prize Type "Tickets"
    //                 if (row["type"]?.trim().toLowerCase() === "ticket") {
    //                     const NumboT = row["tickets"]?.trim();

    //                     // Check if Number of Tickets is valid
    //                     if (isNaN(NumboT) || NumboT <= 0) {
    //                         errors.push(`Number of Tickets must be a valid positive number in row ${index + 1}`);
    //                     }

    //                     // Optional: Only validate comp_id if it exists
    //                     const compId = row["comp_id"]?.trim();
    //                     if (compId) {
    //                         // Check if Comp Id is a valid number
    //                         if (isNaN(compId) || compId <= 0) {
    //                             errors.push(`Comp Id must be a valid positive number in row ${index + 1}`);
    //                         } else {
    //                             // Check if Comp Id exists in the database
    //                             const isValidCompId = await isCompIdValid(compId);
    //                             if (!isValidCompId) {
    //                                 errors.push(`Comp Id ${compId} does not exist in the database in row ${index + 1}`);
    //                             }
    //                         }
    //                     }
    //                 }

    //                 if (errors.length > 0) {
    //                     console.error(`Validation errors in row ${index + 1}:`, errors);
    //                     alert(`Validation failed with the following errors:\n\n${errors.join("\n")}`);
    //                     return; // Stop further processing
    //                 }
    //             };

    //             const groupedRows = rows.reduce((acc, row) => {
    //                 const title = row["title"]?.trim(); // Ensure title is trimmed and not empty
    //                 if (!title) return acc; // Skip rows without a title

    //                 // If the title doesn't exist in the accumulator, add the row
    //                 if (!acc[title]) {
    //                     acc[title] = row; // Store the full row as-is
    //                 }

    //                 return acc;
    //             }, {});

    //             Object.values(groupedRows).forEach((rowData, index) => {
    //                 let newRow = false;

    //                 if (index === 0) {
    //                     newRow = $(".reward_content").find("#row1");
    //                 } else {
    //                     newRow = $(".reward_content").find(".lineItemCloneCopy").clone(true);
    //                     newRow.removeClass("lineItemCloneCopy").addClass("lineItemRow").removeClass("d-none");

    //                     newRow.find(".ticket_col").addClass("d-none");
    //                     newRow.find(".image").addClass("prize_image");

    //                     let newRowNum = getRewardLineItemNextRowNumber();
    //                     updateRewardRowNumberForRow(newRow, newRowNum);

    //                     newRow.appendTo($("#reward_wins_content"));
    //                 }

    //                 newRow.find(".title").val(rowData["title"]);
    //                 newRow.find(".price_type").val(rowData["type"]);
    //                 newRow.find(".quantity").val(rowData["totalTickets"]).trigger("focusout");

    //                 if (rowData["type"] === "points") {
    //                     newRow.find(".price_type").val('Points');

    //                 }

    //                 if (rowData["type"] === "prize") {
    //                     newRow.find(".price_type").val('Prize');
    //                 }

    //                 // Handle Prize Type-specific logic
    //                 if (rowData["type"] === "ticket") {
    //                     newRow.find(".price_type").val('Tickets');

    //                     // Hide cash_value and prize_value, unhide ticket_col
    //                     newRow.find(".value_col").addClass("d-none");
    //                     newRow.find(".ticket_col").removeClass("d-none");

    //                     // Set competition_prize and quantity if available
    //                     const selectedCompId = rowData["comp_id"];
    //                     newRow.find(".competition_prize").val(selectedCompId);
    //                     newRow.find(".prize_total_tickets").val(rowData["tickets"]);
    //                 } else {
    //                     // Show cash_value and prize_value, hide ticket_col
    //                     newRow.find(".value_col").removeClass("d-none");
    //                     newRow.find(".ticket_col").addClass("d-none");

    //                     // Set cash_value and prize_value
    //                     if (rowData["type"] == "prize") {
    //                         newRow.find(".cash_value").val(rowData["cash_alt"]);

    //                     } else {
    //                         newRow.find(".cash_value").val(rowData["value_points"]);

    //                     }
    //                 }

    //                 registerWPMediaFrame(newRow);

    //                 if (rowData["image_url"]?.trim()) {

    //                     newRow.find(".wp_media_frame").addClass("d-none");
    //                     newRow.find(".image_preview_container").removeClass("d-none");
    //                     newRow.find(".image_preview_container .img-content").html(
    //                         `<img src="${rowData["image_url"]}" alt="" width="150px" height="150px">`
    //                     );
    //                     newRow.find(".prize_image").val(rowData["image_url"]);
    //                 }

    //                 // Handle checkbox for webOrderReward based on value (0 or 1)
    //                 const webOrderInstantChecked = rowData["web_order"] == "1";
    //                 newRow.find(".webOrderReward").prop("checked", webOrderInstantChecked);

    //                 checkRewardLineItemRow();
    //             });




    //         }
    //     });
    // }


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

            /*var attachments = frame.state().get('selection').map(
                function (attachment) {
                    attachment.toJSON();
                    return attachment;
                });

            var attachments = frame.state().get('selection').toJSON();

            attachments.forEach(attachment => {
                if (attachment.height < 400) {
                    // Display an error message or perform some action
                    alert(attachment.filenae + 'Image has height less than 400 pixel should not be previewed');
                    return;
                }
                let imageUrl = attachment.url;
                if (!selectedGalleryImageUrls.includes(imageUrl)) {
                    selectedGalleryImageUrls.push(imageUrl);
                    updateImageUrlsInput();
                    displayImagePreview(imageUrl);
                }
            });*/

            var attachments = frame.state().get('selection').toJSON();

            console.log('attachments', attachments);
            console.log('selectedGalleryImageUrls before push', selectedGalleryImageUrls)
            let isValid = true;

            attachments.forEach(attachment => {
                if (attachment.type == "image" && attachment.height < 400) {
                    isValid = false;
                    return;
                }
                console.log(attachment);
                let imageUrl = attachment.url;
                console.log(imageUrl);
                if (!selectedGalleryImageUrls.includes(imageUrl)) {
                    selectedGalleryImageUrls.push(imageUrl);
                    updateImageUrlsInput();
                    displayImagePreview(imageUrl, attachment.type);
                }
            });
            console.log('selectedGalleryImageUrls after push', selectedGalleryImageUrls)

            if (!isValid) {
                alert("Some images have a height less than 400 pixels and cannot be previewed.");
            }


        });

        frame.open();

    });

    $(".remove_gallery_image").on("click", function (e) {

        let imageUrl = $(e.currentTarget).closest(".gallery_preview").find("img").attr("src");

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

    $(".gallery_video_container").find(".lineItemRow").each(function (index, elem) {

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
                multiple: false,
                library: {
                    type: 'image', // Restrict to images
                }
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

    $(".remove_detail_thumb_media").on("click", function (e) {

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

                    let selectedOptions = getSelectedColumns();

                    data.sortedSelections = JSON.stringify(selectedOptions);

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

                    let editorElement = $("#savProductsContent").find("#live_draw_info").closest("div");

                    if (!editorElement.find("#live_draw_info-error").length) {
                        editorElement.append('<label id="live_draw_info-error" class="error" for="live_draw_info">This field is required.</label>');
                    }

                    $('.nav-tabs a[href="#products_content"]').tab('show');

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

                    if ($("#saveInstantContent").valid() && validateFields()) {

                        updateLineItemElementByOrder();

                        $("#total_prizes").val($(".instant_content").find(".lineItemRow").length);

                        let formData = $("#saveInstantContent").serializeArray();

                        let data = {
                            action: 'save_temp_competition_record',
                        };

                        $(formData).each(function (index, field) {
                            data[field.name] = field.value;
                        });

                        $(".instant_content").find(".lineItemRow").each(function (index, domElement) {

                            let lineItemRow = jQuery(domElement);

                            let prizeDescElemId = lineItemRow.find(".prize_description").attr("id");

                            // console.log(prizeDescElemId);
                            // console.log(competitionEditor[prizeDescElemId].getData());
                            // data[prizeDescElemId] = competitionEditor[prizeDescElemId].getData();
                            if (prizeDescElemId && competitionEditor[prizeDescElemId]) {
                                data[prizeDescElemId] = competitionEditor[prizeDescElemId].getData();
                            } else {
                                data[prizeDescElemId] = "";

                            }

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


                // return false;
            }

        }

        if (tabName == 'reward-tab') {

            let total_purchasing = $("#total_ticket_purchased").val();

            let mode = $("[name='mode']").val();

            // if (total_purchasing > 0 && mode == 'edit') {
            //     return true;
            // }

            if ($("#enable_reward_wins").prop('checked') == true) {

                if ($("#saveRewardContent").valid() && validateRewardFields()) {

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

                // return false;
            }
        }
    }

    function saveTempDataDraft(tabName) {

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
                        action: 'save_temp_competition_record_draft',
                    };

                    $(formData).each(function (index, field) {
                        data[field.name] = field.value;
                    });

                    data.description = competitionEditor.description_editor.getData();

                    let selectedOptions = getSelectedColumns();

                    data.sortedSelections = JSON.stringify(selectedOptions);

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

                    let editorElement = $("#savProductsContent").find("#live_draw_info").closest("div");

                    if (!editorElement.find("#live_draw_info-error").length) {
                        editorElement.append('<label id="live_draw_info-error" class="error" for="live_draw_info">This field is required.</label>');
                    }

                    $('.nav-tabs a[href="#products_content"]').tab('show');

                } else {

                    let formData = $("#savProductsContent").serializeArray();

                    let data = {
                        action: 'save_temp_competition_record_draft',
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
                    action: 'save_temp_competition_record_draft',
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
                                action: 'save_temp_competition_record_draft',
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

                    // let formData = $("#savQuestionContent").serializeArray();

                    // let data = {
                    //     action: 'save_temp_competition_record_draft',
                    // };

                    // $(formData).each(function (index, field) {
                    //     data[field.name] = field.value;
                    // });

                    // data.save_original = false;

                    // data.correct_answer = $('.correct-answer:checked').closest(".ans_content").find(".question-ans").val();

                    // saveTempCompetition(data);

                    // return true;

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
                        action: 'save_temp_competition_record_draft',
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

                    if ($("#saveInstantContent").valid() && validateFields()) {

                        updateLineItemElementByOrder();

                        $("#total_prizes").val($(".instant_content").find(".lineItemRow").length);

                        let formData = $("#saveInstantContent").serializeArray();

                        let data = {
                            action: 'save_temp_competition_record_draft',
                        };

                        $(formData).each(function (index, field) {
                            data[field.name] = field.value;
                        });

                        $(".instant_content").find(".lineItemRow").each(function (index, domElement) {

                            let lineItemRow = jQuery(domElement);

                            let prizeDescElemId = lineItemRow.find(".prize_description").attr("id");

                            // console.log(prizeDescElemId);
                            // console.log(competitionEditor[prizeDescElemId].getData());
                            // data[prizeDescElemId] = competitionEditor[prizeDescElemId].getData();
                            if (prizeDescElemId && competitionEditor[prizeDescElemId]) {
                                data[prizeDescElemId] = competitionEditor[prizeDescElemId].getData();
                            } else {
                                data[prizeDescElemId] = "";

                            }

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


                // return false;
            }

        }

        if (tabName == 'reward-tab') {

            let total_purchasing = $("#total_ticket_purchased").val();

            let mode = $("[name='mode']").val();

            // if (total_purchasing > 0 && mode == 'edit') {
            //     return true;
            // }

            if ($("#enable_reward_wins").prop('checked') == true) {

                if ($("#saveRewardContent").valid() && validateRewardFields()) {

                    updateRewardLineItemElementByOrder();

                    $("#total_reward").val($(".reward_content").find(".lineItemRow").length);

                    let formData = $("#saveRewardContent").serializeArray();

                    let data = {
                        action: 'save_temp_competition_record_draft',
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

                // return false;
            }
        }
    }

    function saveTempCompetition(data) {
        jQuery.ajax({
            type: "POST",
            url: ajax_object.ajax_url,
            data: data,
            success: function (response) {
                return true;
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

        let comp_draw_info = competitionEditor.live_draw_info.getData();

        if (comp_draw_info == '' && ($("#mySwitch").prop('checked') == true)) {
            let editorElement = $("#savProductsContent").find("#live_draw_info").closest("div");

            if (!editorElement.find("#live_draw_info-error").length) {
                editorElement.append('<label id="live_draw_info-error" class="error" for="live_draw_info">This field is required.</label>');
            }
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

        if ($("#globalcustom").prop('checked') == false) {
            updateComp = true;
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
        }, 2000);

    });

    $("#save_as_draft_edit").on("click", function (e) {

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

        let comp_draw_info = competitionEditor.live_draw_info.getData();

        if (comp_draw_info == '' && ($("#mySwitch").prop('checked') == true)) {
            let editorElement = $("#savProductsContent").find("#live_draw_info").closest("div");

            if (!editorElement.find("#live_draw_info-error").length) {
                editorElement.append('<label id="live_draw_info-error" class="error" for="live_draw_info">This field is required.</label>');
            }
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

        saveTempDataDraft(activeTab);

        // setTimeout(function () {

        //     let comp_record = $("[name='record']").val();

        //     $('<form action="' + ajax_object.ajax_url + '"><input type="hidden" name="action" value="update_competition_record" /><input type="hidden" name="update_from_temp" value="1"/><input type="hidden" name="record" value="' + comp_record + '"/></form>').appendTo('body').submit();
        // }, 2000);
        window.location.href = base_wp_url + '/wp-admin/admin.php?page=competitions_menu';


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

    jQuery.validator.addMethod("checkQuantity", function (value, element) {
        let max_ticket = $("[name='max_ticket_per_user']").val();
        let quantity = $("[name='quantity']").val();


        max_ticket = parseInt(max_ticket, 10);
        quantity = parseInt(quantity, 10);
        value = parseInt(value, 10);

        if (value < max_ticket || value < quantity) {
            return false;
        }
        return true;
    }, "Quantity error: exceeds maximum tickets or quantity.");

    jQuery.validator.addMethod("matchQuantity", function (value, element) {
        let max_ticket = $("[name='total_sell_tickets']").val();
        let quantity = $("[name='quantity']").val();


        max_ticket = parseInt(max_ticket, 10);
        quantity = parseInt(quantity, 10);
        value = parseInt(value, 10);

        if (value > max_ticket) {
            return false;
        }
        return true;
    }, "Quantity error: exceeds sellable tickets or qunatity.");

    jQuery.validator.addMethod("checkTickets", function (value, element) {
        let max_ticket = $("[name='max_ticket_per_user']").val();
        let tickets = $("[name='total_sell_tickets']").val();


        max_ticket = parseInt(max_ticket, 10);
        tickets = parseInt(tickets, 10);
        value = parseInt(value, 10);

        if (value > tickets) {
            return false;
        }
        return true;
    }, "Quantity error: exceeds maximum tickets or sellable tickets.");

    jQuery.validator.addMethod("greaterThanSaleStart", function (value, element) {

        if (value == '') return true;

        let saleEndDate = new Date(parseDate(value));

        let saleStartDate = $("[name='sale_start_date']").val();

        let drawDate = $("[name='draw_date']").val();

        let closingDate = $("[name='closing_date']").val();

        let saleStartTimeHour = $("[name='sale_price_start_time_hour']").val();
        let saleStartTimeMinute = $("[name='sale_price_start_time_minute']").val();
        let saleEndTimeHour = $("[name='sale_price_end_time_hour']").val();
        let saleEndTimeMinute = $("[name='sale_price_end_time_minute']").val();

        let draw_time = $("[name='draw_time']").val();
        let closing_time = $("[name='closing_time']").val();

        if (saleStartDate == '' && closingDate == '' && drawDate == '') return true;
        let isValid = true;

        let salestartDateTime = moment(saleStartDate + ' ' + saleStartTimeHour + ':' + saleStartTimeMinute, 'DD/MM/YY HH:mm').toDate();
        let salesEndDateTime = moment(value + ' ' + saleEndTimeHour + ':' + saleEndTimeMinute, 'DD/MM/YY HH:mm').toDate();


        if (saleStartDate != '' && closingDate != '' && drawDate != '') {

            saleStartDate = new Date(parseDate(saleStartDate));

            drawDate = new Date(parseDate(drawDate));

            closingDate = new Date(parseDate(closingDate));

            isValid = (saleEndDate <= closingDate && saleEndDate <= drawDate && saleStartDate <= saleEndDate);



            console.log('first condition', isValid);

            if (!isValid) return isValid;
        }

        if (salestartDateTime != '' && salesEndDateTime != '' && saleStartDate != '' && saleEndDate != '') {
            isValid = (salestartDateTime < salesEndDateTime)
            if (!isValid) return isValid
        }


        if (saleStartDate != '') {

            saleStartDate = new Date(parseDate(saleStartDate));

            isValid = saleEndDate >= saleStartDate;
            console.log('second condition', isValid);

            if (!isValid) return isValid;
        }

        if (drawDate != '') {

            drawDate = new Date(parseDate(drawDate));

            isValid = saleEndDate <= drawDate;
            console.log('third condition', isValid);


            if (!isValid) return isValid;
        }

        if (closingDate != '') {

            closingDate = new Date(parseDate(closingDate));
            console.log('fourth condition', isValid);

            isValid = saleEndDate <= closingDate;

            if (!isValid) return isValid;
        }

        if (draw_time !== '' && drawDate !== '' && closing_time !== '' && closingDate !== '' && saleStartDate !== '' && saleEndDate !== '') {

            let drawDate = $("[name='draw_date']").val();

            let closingDate = $("[name='closing_date']").val();
            // Combine date and time for both draw and closing into single Date objects
            let drawDateTime = moment(drawDate + ' ' + draw_time, 'DD/MM/YY HH:mm').toDate();
            let closingDateTime = moment(closingDate + ' ' + closing_time, 'DD/MM/YY HH:mm').toDate();


            console.log('drawDateTime++++++++', (drawDateTime));
            console.log('closingDateTime+++++', (closingDateTime));
            console.log('drawDate+++++', (drawDate));
            console.log('closing_time+++++', (closing_time));

            // Validate that the closing time is before or equal to the draw time
            isValid = salesEndDateTime < closingDateTime;
            if (!isValid) return isValid;
        }

        return isValid;

    }, "Sale Price End must be Equal to or greater than Sale Price Start and less than or equal to Closing Date");

    jQuery.validator.addMethod("lessThanSaleEnd", function (value, element) {

        if (value == '') return true;

        let saleStartDate = new Date(parseDate(value));

        let saleEndDate = $("[name='sale_end_date']").val();

        let drawDate = $("[name='draw_date']").val();

        let closingDate = $("[name='closing_date']").val();

        let isValid = true;

        let saleStartTimeHour = $("[name='sale_price_start_time_hour']").val();
        let saleStartTimeMinute = $("[name='sale_price_start_time_minute']").val();
        let saleEndTimeHour = $("[name='sale_price_end_time_hour']").val();
        let saleEndTimeMinute = $("[name='sale_price_end_time_minute']").val();


        if (saleEndDate == '' && closingDate == '' && drawDate == '') return true;

        let salestartDateTime = moment(value + ' ' + saleStartTimeHour + ':' + saleStartTimeMinute, 'DD/MM/YY HH:mm').toDate();
        console.log("salestartDateTime", salestartDateTime);
        let salesEndDateTime = moment(saleEndDate + ' ' + saleEndTimeHour + ':' + saleEndTimeMinute, 'DD/MM/YY HH:mm').toDate();
        console.log("salesEndDateTime", salesEndDateTime);

        if (saleEndDate != '' && closingDate != '' && drawDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            drawDate = new Date(parseDate(drawDate));

            closingDate = new Date(parseDate(closingDate));

            isValid = (saleStartDate < closingDate && saleStartDate < drawDate && saleStartDate <= saleEndDate);

            if (!isValid) return isValid
        }

        if (salestartDateTime != '' && salesEndDateTime != '' && saleStartDate != '' && saleEndDate != '') {
            isValid = (salestartDateTime < salesEndDateTime)
            if (!isValid) return isValid
        }

        if (saleEndDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            isValid = saleStartDate <= saleEndDate;

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
            isValid = (saleStartDate < drawDate && saleEndDate < drawDate && closingDate <= drawDate);
        }

        if (saleEndDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            isValid = saleEndDate <= drawDate;

            if (!isValid) return isValid;
        }

        if (saleStartDate != '') {

            saleStartDate = new Date(parseDate(saleStartDate));

            isValid = saleStartDate < drawDate;

            if (!isValid) return isValid;
        }

        if (closingDate != '') {

            closingDate = new Date(parseDate(closingDate));

            isValid = drawDate >= closingDate;

            if (!isValid) return isValid;
        }

        return isValid;

    }, "Draw Date must be greater than or equal to Closing Date or greater than Sale Price Start and Sale Price End");

    jQuery.validator.addMethod("greaterThanDrawDate", function (value, element) {

        let saleStartDate = $("[name='sale_start_date']").val();

        let saleEndDate = $("[name='sale_end_date']").val();

        let drawDate = $("[name='draw_date']").val();

        let closingDate = new Date(parseDate(value));

        let isValid = true;

        let draw_time = $("[name='draw_time']").val();
        let closing_time = $("[name='closing_time']").val();
        let saleEndTimeHour = $("[name='sale_price_end_time_hour']").val();
        let saleEndTimeMinute = $("[name='sale_price_end_time_minute']").val();



        let salesEndDateTime = moment(saleEndDate + ' ' + saleEndTimeHour + ':' + saleEndTimeMinute, 'DD/MM/YY HH:mm').toDate();

        if (saleEndDate == '' && drawDate == '' && saleStartDate == '') return true;

        if (saleEndDate != '' && drawDate != '' && saleStartDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            saleStartDate = new Date(parseDate(saleStartDate));

            drawDate = new Date(parseDate(drawDate));

            isValid = (saleStartDate < closingDate && saleEndDate <= closingDate && closingDate <= drawDate);
            if (!isValid) return isValid
        }

        if (saleEndDate != '') {

            saleEndDate = new Date(parseDate(saleEndDate));

            isValid = saleEndDate <= closingDate;

            if (!isValid) return isValid;
        }

        if (saleStartDate != '') {

            saleStartDate = new Date(parseDate(saleStartDate));

            isValid = saleStartDate < closingDate;

            if (!isValid) return isValid;
        }

        if (drawDate != '') {

            drawDate = new Date(parseDate(drawDate));

            isValid = drawDate >= closingDate;

            if (!isValid) return isValid;
        }




        if (draw_time !== '' && drawDate !== '' && closing_time !== '' && closingDate !== '' && saleStartDate != '' && saleEndDate != '') {
            let closingDate = $("[name='closing_date']").val();
            // Combine date and time for both draw and closing into single Date objects
            let closingDateTime = moment(closingDate + ' ' + closing_time, 'DD/MM/YY HH:mm').toDate();
            // Validate that the closing time is before or equal to the draw time
            isValid = salesEndDateTime < closingDateTime;
            if (!isValid) return isValid;
        }

        return isValid;
    }, "Closing Date must be greater than Sale Price Start,less then or equal to Draw Date and  greater then or equal to Sale Price End ");


    jQuery.validator.addMethod("greaterThanDrawDateTime", function (value, element) {
        let draw_time = $("[name='draw_time']").val();
        let closing_time = $("[name='closing_time']").val();
        let drawDate = $("[name='draw_date']").val();
        let closingDate = $("[name='closing_date']").val();

        let saleEndDate = $("[name='sale_end_date']").val();



        let saleStartDate = $("[name='sale_start_date']").val();

        // console.log();

        let isValid = true;

        // Return true if none of the fields are filled in (no validation needed)
        if (draw_time === '' && drawDate === '' && closing_time === '' && closingDate === '') return true;

        let saleStartTimeHour = $("[name='sale_price_start_time_hour']").val();
        let saleStartTimeMinute = $("[name='sale_price_start_time_minute']").val();
        let saleEndTimeHour = $("[name='sale_price_end_time_hour']").val();
        let saleEndTimeMinute = $("[name='sale_price_end_time_minute']").val();

        let salestartDateTime = moment(saleStartDate + ' ' + saleStartTimeHour + ':' + saleStartTimeMinute, 'DD/MM/YY HH:mm').toDate();
        let salesEndDateTime = moment(saleEndDate + ' ' + saleEndTimeHour + ':' + saleEndTimeMinute, 'DD/MM/YY HH:mm').toDate();

        let drawDateTime = moment(drawDate + ' ' + draw_time, 'DD/MM/YY HH:mm').toDate();
        let closingDateTime = moment(closingDate + ' ' + closing_time, 'DD/MM/YY HH:mm').toDate();

        if (salestartDateTime != '' && salesEndDateTime != '' && saleStartDate != '' && saleEndDate != '') {
            isValid = (salestartDateTime < salesEndDateTime)
            if (!isValid) return isValid
        }

        if (salesEndDateTime != '' && closingDateTime != '' && saleStartDate != '' && saleEndDate != '') {
            isValid = (salesEndDateTime < closingDateTime)
            if (!isValid) return isValid
        }
        // Check if all necessary values are present for validation
        if (draw_time !== '' && drawDate !== '' && closing_time !== '' && closingDate !== '') {
            // Combine date and time for both draw and closing into single Date objects
            let drawDateTime = moment(drawDate + ' ' + draw_time, 'DD/MM/YY HH:mm').toDate();
            let closingDateTime = moment(closingDate + ' ' + closing_time, 'DD/MM/YY HH:mm').toDate();


            console.log('drawDateTime', (drawDateTime));
            console.log('closingDateTime', (closingDateTime));

            // Validate that the closing time is before or equal to the draw time
            isValid = closingDateTime <= drawDateTime;
            if (!isValid) return isValid;
        }

        return isValid;
    }, "The draw date and time should be equal to or greater than the closing date and time.");


    jQuery.validator.addMethod("greaterThanSalesDateTime", function (value, element) {

        let isValid = true;
        let draw_time = $("[name='draw_time']").val();
        let closing_time = $("[name='closing_time']").val();



        let saleEndDate = $("[name='sale_end_date']").val();

        let drawDate = $("[name='draw_date']").val();

        let closingDate = $("[name='closing_date']").val();

        let saleStartDate = $("[name='sale_start_date']").val();

        if (saleEndDate == '' && closingDate == '' && drawDate == '' && saleStartDate == '') return true;
        if (saleEndDate == '' && saleStartDate == '') return true;

        let saleStartTimeHour = $("[name='sale_price_start_time_hour']").val();
        let saleStartTimeMinute = $("[name='sale_price_start_time_minute']").val();
        let saleEndTimeHour = $("[name='sale_price_end_time_hour']").val();
        let saleEndTimeMinute = $("[name='sale_price_end_time_minute']").val();



        let salestartDateTime = moment(saleStartDate + ' ' + saleStartTimeHour + ':' + saleStartTimeMinute, 'DD/MM/YY HH:mm').toDate();
        let salesEndDateTime = moment(saleEndDate + ' ' + saleEndTimeHour + ':' + saleEndTimeMinute, 'DD/MM/YY HH:mm').toDate();



        if (salestartDateTime != '' && salesEndDateTime != '' && saleStartDate != '' && saleEndDate != '') {

            console.log('salestartDateTime---++6', salestartDateTime);
            console.log('salesEndDateTime---++6', salesEndDateTime);

            isValid = (salestartDateTime < salesEndDateTime)

            if (!isValid) return isValid
        }

        if (draw_time !== '' && closing_time !== '' && salestartDateTime != '' && salesEndDateTime != '' && saleStartDate != '' && saleEndDate != '') {
            let closingDateTime = moment(closingDate + ' ' + closing_time, 'DD/MM/YY HH:mm').toDate();
            console.log('closingDateTime---', closingDateTime);

            console.log('salestartDateTime---++', salestartDateTime);
            console.log('closingDateTime---++', closingDateTime);

            isValid = (salesEndDateTime < closingDateTime)
            if (!isValid) return isValid

        }


        return isValid;
    }, "The Sales End date and time should be equal to or greater than the closing date and time++.");


    // Assuming the format is "dd/mm/yy"

    function parseDate(dateString) {
        return moment(dateString, 'DD/MM/YY').toDate();
    }

    var videoCounter = $(".gallery_video_container .videoRow").length;

    $("#add_video").on("click", function (e) {

        videoCounter++;

        e.preventDefault();

        let newRow = $(".cloneVideoURLRow").clone(true);

        // newRow.removeClass("cloneVideoURLRow d-none").addClass("videoRow");
        newRow.removeClass("cloneVideoURLRow d-none").addClass("videoRow").attr("data-row", videoCounter).attr("id", "row[" + videoCounter + "]");


        newRow.find(".gallery_video_urls").attr("name", "gallery_video_urls[" + videoCounter + "]");
        newRow.find(".gallery_video_type").attr("name", "gallery_video_type[" + videoCounter + "]");

        let videoThumb = newRow.find("#gallery_video_thumb");
        videoThumb.attr("name", "gallery_video_thumb[" + videoCounter + "]");
        videoThumb.attr("id", "gallery_video_thumb[" + videoCounter + "]");

        newRow.appendTo($(".gallery_video_container"));
        registerWPMediaFrame(newRow);


    });

    $(".gallery_video_container").on("click", ".delete_video_url", function (e) {

        e.preventDefault();

        $(this).closest(".videoRow").remove();

        let keyIndex = 1;

        $(".gallery_video_container .videoRow .gallery_video_urls").each(function (index) {
            $(this).attr("name", "gallery_video_urls[" + keyIndex + "]");
            $(this).closest(".videoRow").find(".gallery_video_type").attr("name", "gallery_video_type[" + keyIndex + "]");
            keyIndex++;
        });

        videoCounter = $(".gallery_video_container .videoRow").length;
    });

    $(".main_prize_type").on("change", function (e) {

        $('input[name="cash"], input[name="points"], select[name="competitions_prize"], input[name="prize_tickets"]').removeAttr("required");
        $('input[name="cash"], input[name="points"], select[name="competitions_prize"], input[name="prize_tickets"]').rules('remove', 'required');

        let mainPrizeType = $(this).val();

        if (mainPrizeType == 'Points') {

            $(".prize_cash").addClass("d-none");

            $(".prize_tickets", $(".produtcs_content")).addClass("d-none");

            $(".prize_points", $(".produtcs_content")).removeClass("d-none");

            $('input[name="points"]').attr('required', true).rules('add', {
                required: true,
                number: true,
                min: 0
            });
        }

        if (mainPrizeType == 'Prize') {

            $(".prize_cash").removeClass("d-none");

            $(".prize_points", $(".produtcs_content")).addClass("d-none");

            $(".prize_tickets", $(".produtcs_content")).addClass("d-none");

            $('input[name="cash"]').attr('required', true).rules('add', {
                required: true,
                number: true,
                min: 0
            });
        }

        if (mainPrizeType == 'Tickets') {

            $(".prize_tickets", $(".produtcs_content")).removeClass("d-none");

            $(".prize_cash").addClass("d-none");

            $(".prize_points", $(".produtcs_content")).addClass("d-none");

            $('select[name="competitions_prize"]').attr('required', true).rules('add', {
                required: true
            });

            $('input[name="prize_tickets"]').attr('required', true).rules('add', {
                required: true,
                number: true,
                min: 0
            });
        }

    });

    $(".main_prize_type").trigger("change");

    $('#selections').select2({
        dropdownParent: $('.slider_sorting'),
        templateResult: formatState,
        templateSelection: formatState
    });


    function formatState(opt) {

        var $opt = $(
            '<span class="slider_choice_option">' + $(opt.element).text() + '</span>'
        );

        return $opt;
    };

    $("#selections").next('.select2-container').find('ul.select2-selection__rendered').sortable({
        containment: 'parent',
        update: function () {
            // Update the order of options in the actual select element
            $('#selections').children().remove();
            $('#selections').next('.select2-container').find('ul.select2-selection__rendered li[title]').each(function () {
                var value = $(this).attr('title');
                $('#selections').append('<option value="' + value + '" selected>' + value + '</option>');
            });
        }
    });

    function updateOrder() {
        var selectedValues = $('#selections').val();
        $('#selections').children('option').each(function () {
            var value = $(this).val();
            if (selectedValues.includes(value)) {
                $(this).remove();
                $('#selections').append('<option value="' + value + '" selected>' + value + '</option>');
            }
        });
        $('#selections').trigger('change.select2');
    }

    $('#selections').on('select2:select', function (e) {
        updateOrder();
    });

    $('#selections').on('select2:unselect', function (e) {
        updateOrder();
    });

    let sliderSorting = jQuery('input[name="slidersortinglist"]').val();
    if (sliderSorting.length > 0) arrangeSelectChoicesInOrder();

    function arrangeSelectChoicesInOrder() {
        var chosenElement = $("#selections").next('.select2-container');
        var choicesContainer = chosenElement.find('ul.select2-selection__rendered');
        var choicesList = choicesContainer.find('li.select2-selection__choice');
        var columnListSelectElement = $("#selections");
        var selectedOptions = columnListSelectElement.find('option:selected');
        var selectedOrder = JSON.parse(jQuery('input[name="slidersortinglist"]').val());

        for (var index = selectedOrder.length; index > 0; index--) {
            var selectedValue = selectedOrder[index - 1];
            var value = selectedValue.replace("'", "&#39;");
            var option = selectedOptions.filter('[value="' + value + '"]');
            choicesList.each(function (choiceListIndex, element) {
                var liElement = jQuery(element);
                if (liElement.find('.slider_choice_option').html() == option.html()) {
                    choicesContainer.prepend(liElement);
                    return false;
                }
            });
        }
    }

    function getSelectedColumns() {

        var columnListSelectElement = $("#selections");
        var select2Element = $("#selections").next('.select2-container');

        var selectedValuesByOrder = new Array();
        var selectedOptions = columnListSelectElement.find('option:selected');

        var orderedSelect2Options = select2Element.find('li.select2-selection__choice').find('.slider_choice_option');
        orderedSelect2Options.each(function (index, element) {
            var chosenOption = jQuery(element);
            selectedOptions.each(function (optionIndex, domOption) {
                var option = jQuery(domOption);
                if (option.html() == chosenOption.html()) {
                    selectedValuesByOrder.push(option.val());
                    return false;
                }
            });
        });
        return selectedValuesByOrder;
    }

    $("#instant_wins_content").on("change", ".price_type", function (e) {

        let currentElem = $(e.currentTarget);

        let value = currentElem.val();

        if (value == 'Tickets') {

            currentElem.closest(".lineItemRow").find(".ticket_col").removeClass("d-none");

            currentElem.closest(".lineItemRow").find(".value_col").addClass("d-none");

        } else {

            currentElem.closest(".lineItemRow").find(".ticket_col").addClass("d-none");

            currentElem.closest(".lineItemRow").find(".value_col").removeClass("d-none");
        }

        validateFields();
    });

    $("#instant_wins_content").find(".price_type").trigger("change");

    function validateFields() {

        let isValid = true;

        $("#saveInstantContent").find(".lineItemRow").each(function (index, row) {
            const type = $(row).find('.price_type').val();
            const valueField = $(row).find('.cash_value');
            const competitionField = $(row).find('.competition_prize');
            const ticketsField = $(row).find('.prize_total_tickets');

            // Reset validation states
            $(valueField).rules('remove');
            $(competitionField).rules('remove');
            $(ticketsField).rules('remove');

            if (type === 'Points') {
                $(valueField).rules('add', {
                    required: true,
                });
            }

            if (type === 'Tickets') {
                $(competitionField).rules('add', {
                    required: true,
                });
                $(ticketsField).rules('add', {
                    required: true,
                });
            }
        });

        return isValid;
    }

    $("#reward_wins_content").on("change", ".price_type", function (e) {

        let currentElem = $(e.currentTarget);

        let value = currentElem.val();

        if (value == 'Tickets') {

            currentElem.closest(".lineItemRow").find(".ticket_col").removeClass("d-none");

            currentElem.closest(".lineItemRow").find(".value_col").addClass("d-none");

        } else {

            currentElem.closest(".lineItemRow").find(".ticket_col").addClass("d-none");

            currentElem.closest(".lineItemRow").find(".value_col").removeClass("d-none");
        }

        validateRewardFields();
    });

    $("#reward_wins_content").find(".price_type").trigger("change");

    function validateRewardFields() {

        let isValid = true;

        $("#saveRewardContent").find(".lineItemRow").each(function (index, row) {
            const type = $(row).find('.price_type').val();
            const valueField = $(row).find('.cash_value');
            const competitionField = $(row).find('.competition_prize');
            const ticketsField = $(row).find('.prize_total_tickets');

            // Reset validation states
            $(valueField).rules('remove');
            $(competitionField).rules('remove');
            $(ticketsField).rules('remove');

            if (type === 'Points') {
                $(valueField).rules('add', {
                    required: true,
                });
            }

            if (type === 'Tickets') {
                $(competitionField).rules('add', {
                    required: true,
                });
                $(ticketsField).rules('add', {
                    required: true,
                });
            }
        });

        return isValid;
    }

    function setTotalInstantPrizeCount() {

        let lineItems = $(".instant_content").find(".lineItemRow");

        let totalPrize = 0;

        lineItems.each(function (index, domElement) {

            var lineItemRow = jQuery(domElement);

            let prizeQty = lineItemRow.find(".prize_qty").val();

            totalPrize += parseInt(prizeQty);

        });

        $("#instant_prize_total").html("(" + totalPrize + ")");
    }

    function setInstantPrizeDescriptionEditor() {

        let lineItems = $(".instant_content").find(".lineItemRow");

        lineItems.each(function (index, domElement) {

            var lineItemRow = jQuery(domElement);

            let prizeDescElemId = lineItemRow.find(".prize_description").attr("id");

            competitionEditor[prizeDescElemId] = CKEDITOR.replace(prizeDescElemId, {
                height: 170,
            });

            //competitionEditor[prizeDescElemId] = CKEDITOR.instances.prizeDescElemId;
        });
    }

    $(".instant_content").on("click", ".toggle_description", function (e) {

        if ($(e.currentTarget).closest(".switch").find('[type="checkbox"]').is(':checked')) {
            $(e.currentTarget).closest(".lineItemRow").find('.description-container').addClass("d-none");
        } else {
            $(e.currentTarget).closest(".lineItemRow").find('.description-container').removeClass("d-none");
        }

    });


    $('.reward-type-dropdown').on('change', function () {
        // Get the selected value
        const selectedValue = $(this).val();
        console.log('selectedValue', selectedValue);
        // Find the corresponding label in the same row
        const $rewardLabel = $(this).closest('.reward-row').find('.labelCashReward');

        console.log('rewardLabel', $rewardLabel);
        // Update the label text based on the selected value
        if (selectedValue == 'Points') {
            $rewardLabel.text('Points Value');
        } else if (selectedValue == 'Prize') {
            $rewardLabel.text('Cash Alt');
        }
    });

    $('.reward-type-dropdown').on('change', function () {
        // Get the selected value
        const selectedValue = $(this).val();
        console.log('selectedValue', selectedValue);
        // Find the corresponding label in the same row
        const $rewardLabel = $(this).closest('.instant-row').find('.labelCashInstant');

        console.log('rewardLabel', $rewardLabel);
        // Update the label text based on the selected value
        if (selectedValue == 'Points') {
            $rewardLabel.text('Points Value');
        } else if (selectedValue == 'Prize') {
            $rewardLabel.text('Cash Alt');
        }
    });


});
