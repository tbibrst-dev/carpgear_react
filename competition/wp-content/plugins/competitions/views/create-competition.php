<?php

$recordData = [];

$mode = "create";

$comp_tickets_purchased = 0;

global $wpdb;

if (isset($_REQUEST['record']) && $_REQUEST['record'] > 0) {

    $wpdb->competition = $wpdb->prefix . 'competitions';

    $query = $wpdb->prepare("SELECT * FROM {$wpdb->competition} WHERE id = %s", $_REQUEST['record']);

    $recordData = $wpdb->get_row($query, ARRAY_A);

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes WHERE competition_id = %s", $_REQUEST['record']);

    $instant_wins = $wpdb->get_results($query, ARRAY_A);

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_instant_prizes_tickets WHERE competition_id = %s", $_REQUEST['record']);

    $prize_res = $wpdb->get_results($query, ARRAY_A);

    $prize_tickets = [];

    if (!empty($prize_res)) {

        foreach ($prize_res as $res) {

            $prize_tickets[$res['instant_id']][] = $res['ticket_number'];
        }
    }

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "comp_reward WHERE competition_id = %s", $_REQUEST['record']);

    $reward_wins = $wpdb->get_results($query, ARRAY_A);

    $original_qty = $recordData['total_sell_tickets'];

    if (!empty($recordData['draw_date']))
        $recordData['draw_date'] = date("d/m/Y", strtotime($recordData['draw_date']));
    if (!empty($recordData['closing_date']))
        $recordData['closing_date'] = date("d/m/Y", strtotime($recordData['closing_date']));
    if (!empty($recordData['sale_start_date']))
        $recordData['sale_start_date'] = date("d/m/Y", strtotime($recordData['sale_start_date']));
    if (!empty($recordData['sale_end_date']))
        $recordData['sale_end_date'] = date("d/m/Y", strtotime($recordData['sale_end_date']));
    $recordData['question_options'] = json_decode($recordData['question_options'], true);

    if (!empty($recordData['description']))
        $recordData['description'] = html_entity_decode(stripslashes($recordData['description']), ENT_QUOTES, 'UTF-8');
    if (!empty($recordData['live_draw_info']))
        $recordData['live_draw_info'] = html_entity_decode(stripslashes($recordData['live_draw_info']), ENT_QUOTES, 'UTF-8');

    $recordData['gallery_videos'] = json_decode($recordData['gallery_videos'], true);

    $slider_sorting = stripslashes($recordData['slider_sorting']);
    $recordData['slider_sorting'] = json_decode(stripslashes($recordData['slider_sorting']), true);
}

$today = date("Y-m-d");

$query = $wpdb->prepare("SELECT id, title FROM " . $wpdb->prefix . "competitions WHERE status = 'Open' and total_sell_tickets > total_ticket_sold and (draw_date > %s )", $today);
// $query = $wpdb->prepare("SELECT id, title FROM " . $wpdb->prefix . "competitions WHERE status = 'Open' AND 'draw_date' >".);

$open_competitions = $wpdb->get_results($query, ARRAY_A);

?>

<div id="competitions-plugin-container">
    <div class="header_container">
        <div class="container-fluid">

          


            <div class="row">
                <h3 class="col-md-6 header-text">Add a new Competition</h3>
                <div class="col-md-6 text-end">
                    <a href="<?php echo admin_url('admin.php?page=competitions_menu'); ?>" id="cancel_btn"><button
                            type="button" class="btn btn-sm btn-default">Cancel</button></a>
                    <button type="button" class="btn btn-sm btn-accent create_competition d-none " id="save_as_draft">Save As Draft</button>

                    <button type="button" class="btn btn-sm btn-default move_back d-none" id="back_btn">Back</button>
                    <button type="button" class="btn btn-sm btn-accent create_competition" id="next_btn">Next</button>
                    <button type="button" class="btn btn-sm btn-accent create_competition d-none"
                        id="publish_btn">Publish</button>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-content pt-3">
        <ul class="nav nav-tabs mb-3" id="create-comp" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active active-accent" id="details-tab" href="#details_content" data-bs-toggle="tab"
                    role="tab" aria-controls="details_content" aria-selected="true">Details</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="products-tab" href="#products_content" data-bs-toggle="tab" role="tab"
                    aria-controls="products_content" aria-selected="false">Products</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="question-tab" href="#question_content" data-bs-toggle="tab" role="tab"
                    aria-controls="question_content" aria-selected="false">Question</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="instant-tab" href="#insant_wins" data-bs-toggle="tab" role="tab"
                    aria-controls="insant_wins" aria-selected="false">Instant Wins</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="reward-tab" href="#reward_wins" data-bs-toggle="tab" role="tab"
                    aria-controls="reward_wins" aria-selected="false">Reward Wins</a>
            </li>
        </ul>
        <div class="tab-content" id="create-comp-content">
            <div class="tab-pane fade show active" id="details_content" role="tabpanel" aria-labelledby="details-tab">
                <?php include "tabs/details.php"; ?>
            </div>
            <div class="tab-pane fade" id="products_content" role="tabpanel" aria-labelledby="products-tab">
                <?php include "tabs/products.php"; ?>
            </div>
            <div class="tab-pane fade" id="question_content" role="tabpanel" aria-labelledby="question-tab">
                <?php include "tabs/question.php"; ?>
            </div>
            <div class="tab-pane fade" id="insant_wins" role="tabpanel" aria-labelledby="instant-tab">
                <?php include "tabs/instant.php"; ?>
            </div>
            <div class="tab-pane fade" id="reward_wins" role="tabpanel" aria-labelledby="reward-tab">
                <?php include "tabs/reward.php"; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="csvModal" tabindex="-1" aria-labelledby="csvModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close close_btn" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                    <div class="csv_modal_content" id="dropArea">
                        <div class="csv_modal_content">
                            <div class="form-group">
                                <label class="modal-label">Drag & drop a file or</label>
                                <br />
                                <label for="csvFile" class="btn btn-sm btn-accent btn-acc-sm">Choose a file</label>
                                <input type="file" id="csvFile" accept=".csv" class="d-none">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
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
    });

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

            reader.onload = function(e) {

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

        // const requiredColumns = [
        //     "Title",
        //     "Prize Type",
        //     "Cash Value",
        //     "Quantity",
        //     "Image URL",
        //     "Ticket Numbers",
        //     "Web Order",
        //     "Comp Id",
        //     "Prize Value",
        //     "Number of Tickets"

        // ];

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
            complete: async function(results) {

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
                // const validRows = rows.filter(row => {
                //     // Check if all required fields have non-empty values
                //     return requiredColumns.some(col => row[col]?.trim());
                // });

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
                            success: function(response) {
                                console.log('response++++++++++', response.data.exists);
                                if (response.data && response.data.exists == true) {
                                    resolve(true);
                                } else {
                                    resolve(false);
                                }
                            },
                            error: function() {
                                reject('Error validating Comp Id');
                            }
                        });
                    });
                }

                const errors = [];
                // Validate and process each row
                for (const [index, row] of validRows.entries()) {


                    // Validation: Check required fields
                    // requiredColumns.forEach(col => {
                    //     if (!row[col]?.trim()) {
                    //         errors.push(`${col} column is missing in row ${index + 1}`);
                    //     }
                    // });

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

        // const requiredColumns = [
        //     "Title",
        //     "Prize Type",
        //     "Cash Value",
        //     "Percentage",
        //     "Image URL",
        //     "Web Order",
        //     "Comp Id",
        //     "Prize Value",
        //     "Number of Tickets"
        // ];

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
            complete: async function(results) {

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
                        console.log('compId+++++++++++++++++++********************', compId);

                        jQuery.ajax({
                            url: ajax_object.ajax_url, // WordPress provides ajaxurl variable
                            type: 'POST',
                            data: {
                                action: 'validate_comp_id', // Custom action
                                comp_id: compId
                            },
                            success: function(response) {
                                console.log('response++++++++++', response.data.exists);
                                if (response.data && response.data.exists == true) {
                                    resolve(true);
                                } else {
                                    resolve(false);
                                }
                            },
                            error: function() {
                                reject('Error validating Comp Id');
                            }
                        });
                    });
                }

                const errors = [];


                // Validate and process each row
                for (const [index, row] of validRows.entries()) {


                    // Validation: Check required fields
                    // requiredColumns.forEach(col => {
                    //     if (!row[col]?.trim()) {
                    //         errors.push(`${col} is missing in row ${index + 1}`);
                    //     }
                    // });



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
</script> -->