document.addEventListener("DOMContentLoaded", function () {

    var dataTableColumns = {
        "limits": [
            { 'data': 'user_id', 'name': 'user_id' },
            { 'data': 'user_name', 'name': 'user_name' },
            { 'data': 'user_email', 'name': 'user_email' },
            { 'data': 'limit_duration', 'name': 'limit_duration' },
            { 'data': 'limit_value', 'name': 'limit_value' },
            { 'data': 'limit_balance', 'name': 'limit_balance' },
            { 'data': 'limit_created', 'name': 'limit_created' },
            { 'data': 'limit_renewal', 'name': 'limit_renewal' }
        ],
        "locks": [
            { 'data': 'user', 'name': 'user' },
            { 'data': 'user_name', 'name': 'user_name' },
            { 'data': 'user_email', 'name': 'user_email' },
            { 'data': 'locking_date', 'name': 'locking_date' },
            { 'data': 'lockout_date', 'name': 'lockout_date' }
        ],
        "paid": [
            { 'data': 'ticket_number', 'name': 'ticket_number' },
            { 'data': 'comp_title', 'name': 'comp_title' },
            { 'data': 'order_id', 'name': 'order_id' },
            { 'data': 'title', 'name': 'title' },
            { 'data': 'claimed', 'name': 'claimed' },
            { 'data': 'user_name', 'name': 'user_name' },
            { 'data': 'user_email', 'name': 'user_email' },
            { 'data': 'phone', 'name': 'phone' },
            { 'data': 'user_address', 'name': 'user_address' }
        ],
        "unpaid": [
            { 'data': 'ticket_number', 'name': 'ticket_number' },
            { 'data': 'comp_title', 'name': 'comp_title' },
            { 'data': 'order_id', 'name': 'order_id' },
            { 'data': 'title', 'name': 'title' },
            { 'data': 'user_name', 'name': 'user_name' },
            { 'data': 'user_email', 'name': 'user_email' },
            { 'data': 'phone', 'name': 'phone' },
            { 'data': 'user_address', 'name': 'user_address' },
        ],
        "claimed": [
            { 'data': 'id', 'name': 'id', orderable: false },
            { 'data': 'ticket_number', 'name': 'ticket_number' },
            { 'data': 'comp_title', 'name': 'comp_title' },
            { 'data': 'order_id', 'name': 'order_id' },
            { 'data': 'title', 'name': 'title' },
            { 'data': 'type', 'name': 'type' },
            { 'data': 'claimed', 'name': 'claimed' },
            { 'data': 'user_name', 'name': 'user_name' },
            { 'data': 'user_email', 'name': 'user_email' },
            { 'data': 'phone', 'name': 'phone' },
            { 'data': 'user_address', 'name': 'user_address' }

        ],
        "points-cred": [
            // { 'data': 'ticket_number', 'name': 'ticket_number' },
            // { 'data': 'comp_title', 'name': 'comp_title' },
            { 'data': 'order_id', 'name': 'order_id' },
            { 'data': 'title', 'name': 'title' },
            { 'data': 'user_name', 'name': 'user_name' },
            { 'data': 'user_email', 'name': 'user_email' },
            { 'data': 'phone', 'name': 'phone' },
            { 'data': 'user_address', 'name': 'user_address' }
        ],
        "ticket-cred": [
            { 'data': 'ticket_number', 'name': 'ticket_number' },
            { 'data': 'comp_title', 'name': 'comp_title' },
            { 'data': 'order_id', 'name': 'order_id' },
            { 'data': 'title', 'name': 'title' },
            { 'data': 'user_name', 'name': 'user_name' },
            { 'data': 'user_email', 'name': 'user_email' },
            { 'data': 'phone', 'name': 'phone' },
            { 'data': 'user_address', 'name': 'user_address' }
        ]


    };

    document.querySelectorAll('table.limit_lock_table').forEach((el) => {

        new DataTable('#' + el.getAttribute("id"), {
            "responsive": false,
            "aaSorting": [],
            pageLength: 25,
            deferRender: true,
            orderClasses: false,
            "bProcessing": false,
            "bStateSave": false,
            "bSortClasses": false,
            "bDeferRender": false,
            processing: true,
            serverSide: true,
            searching: false,
            lengthChange: false,
            ordering: false,
            ajax: {
                url: ajax_object.ajax_url,
                type: 'POST',
                data: function (d) {
                    var params = {
                        action: "get_all_list_ajax",
                        mode: el.getAttribute("data-view")
                    };
                    return $.extend({}, d, params);
                },
                beforeSend: function (xhr, settings) {
                    $(".show_loader").show();
                    return true;
                },
                complete: function (xhr, settings) {
                    $(".show_loader").hide();
                    return true;
                }
            },
            columns: dataTableColumns[el.getAttribute("data-view")],
            createdRow: function (row, data, dataIndex) {
                
                // if (data.ticket_number && !data.ticket_number.includes('bi-globe-americas')) {
                //     $(row).removeClass('highlight-row');
                //     $(row).addClass('highlight-row-magenta');
                // }
               

                // Check if the 'ticket_number' column contains the SVG icon
                if (data.ticket_number && !data.ticket_number.includes('bi-globe-americas')) {
                    $(row).addClass('highlight-row-blue');
                    $(row).removeClass('highlight-row-magenta');
                }


                if ($(row).find('td:contains("Cash Alt")').length > 0) {
                    $(row).removeClass('highlight-row-blue');
                    $(row).addClass('highlight-row-magenta');
                }
            }
        });
    });

    var dataTablesall = {};
    var filterValue = $('.select-claimed-value').val();
    var actionValue = $('.mark-claimed-value').val();
    var actionValuePrize = $('.mark-claimed-value-prize').val();

    if ($(".instant_win_table").length > 0) {

        document.querySelectorAll('table.instant_win_table').forEach((el) => {
            var tableId = '#' + el.getAttribute("id");
            dataTablesall[tableId] = new DataTable(tableId, {
                "responsive": false,
                "aaSorting": [],
                pageLength: 20,
                deferRender: true,
                orderClasses: false,
                "bProcessing": false,
                "bStateSave": false,
                "bSortClasses": false,
                "bDeferRender": false,
                processing: true,
                serverSide: true,
                searching: false,
                lengthChange: false,
                ordering: false,
                ajax: {
                    url: ajax_object.ajax_url,
                    type: 'POST',
                    data: function (d) {
                        var params = {
                            action: "get_all_list_ajax",
                            mode: el.getAttribute("data-view"),
                            search_value: $("#comp_search").val(),
                            filter: filterValue,
                            action_value: actionValue,
                            action_value_prize: actionValuePrize
                        };
                        return $.extend({}, d, params);
                    },
                    beforeSend: function (xhr, settings) {
                        $(".show_loader").show();
                        return true;
                    },
                    complete: function (xhr, settings) {
                        $(".show_loader").hide();
                        return true;
                    }
                },
                columns: dataTableColumns[el.getAttribute("data-view")],
                "createdRow": function (row, data, dataIndex) {
                    var firstCell = $('td:eq(0)', row);
                    firstCell.addClass('hover-cell');

                    // Fourth cell (index 3)
                    var fourthCell = $('td:eq(3)', row);
                    fourthCell.addClass('hover-cell');

                    var fifthCell = $('td:eq(4)', row);
                    fifthCell.addClass('hover-cell');

                   
                    // if (data.ticket_number && !data.ticket_number.includes('bi-globe-americas')) {
                    //     $(row).removeClass('highlight-row');
                    //     $(row).addClass('highlight-row-magenta');
                    // }
                    if (data.ticket_number && !data.ticket_number.includes('bi-globe-americas')) {
                        $(row).addClass('highlight-row-blue');
                        $(row).removeClass('highlight-row-magenta');
                    }
    
    
                    if ($(row).find('td:contains("Cash Alt")').length > 0) {
                        $(row).removeClass('highlight-row-blue');
                        $(row).addClass('highlight-row-magenta');
                    }
                }
            });
        });
        // Trigger filtering based on some input or change
        $('#comp_search').on('change', function () {
            showProcessingOverlay();
            let tablesToReload = Object.values(dataTablesall).length;
            let reloadCount = 0;

            function onTableReloadComplete() {
                reloadCount++;
                // Check if all tables have been reloaded
                if (reloadCount === tablesToReload) {
                    hideProcessingOverlay();
                }
            }

            Object.values(dataTablesall).forEach(function (table) {
                // table.ajax.reload();
                table.ajax.reload(function () {
                    onTableReloadComplete();
                }, false); // `false` to avoid redrawing immediately

            });
            // Uncheck the "select-all" checkbox
            $('#select-all').prop('checked', false);
            // Clear selected checkboxes
            $('.user-checkbox').prop('checked', false);
            // hideProcessingOverlay();
            if (tablesToReload === 0) {
                hideProcessingOverlay();
            }
        });

        $('#unpaidInstantWins').on('click', '.markaspaidwinner', function (e) {


            let id = $(e.currentTarget).data("id");
            let set = $(e.currentTarget).data("set");

            console.log('id', id);
            console.log('set', set);

            // Find the parent row of the clicked element
            let row = $(e.currentTarget).closest('tr');

            // Extract data from specific cells in the row
            let ticket = row.find('td').eq(0).text().trim(); // Eighth cell
            let competitionName = row.find('td').eq(1).text().trim(); // Second cell
            let competitionLink = row.find('td').eq(2).find('a').attr('href'); // Third cell, link href
            let userName = row.find('td').eq(4).text().trim(); // Fifth cell
            let userEmail = row.find('td').eq(5).text().trim(); // Sixth cell
            let userPhone = row.find('td').eq(6).text().trim(); // Seventh cell
            let userAddress = row.find('td').eq(7).text().trim(); // Eighth cell



            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'mark_as_paid_unclaim',
                    id: id,
                    type: set,
                    text: competitionName,
                    email: userEmail,
                    ticket: ticket
                },
                success: function (response) {



                    Object.values(dataTablesall).forEach(function (table) {
                        table.ajax.reload();
                    });

                    // hideProcessingOverlay();
                    // hideProcessingMessage();
                    reloadAllTables(); // Close the modal


                },
                error: function (xhr, status, error) {
                    // hideProcessingMessage();
                    console.error('Error marking as paid:', error);
                }
            });

        });

    }

    if ($("#instant_win_tabs").length > 0) {

        var previousActiveTab = $('#instant_win_tabs .btn-accent');

        $('#instant_win_tabs').on('click', 'a', function (e) {

            e.preventDefault();

            previousActiveTab.removeClass('btn-accent').addClass('btn-black');

            $(this).removeClass('btn-black').addClass('btn-accent');

            previousActiveTab = $(this);

            let target = $(this).data('bs-target');

            $('.tab-pane').removeClass('show active');

            $(target).addClass('show active');

            DataTables.tables({ visible: true, api: true }).columns.adjust();
        });
    }




    $(document).find('.select-claimed-value').on('change', function (e) {
        showProcessingOverlay();
        const value = $(this).val();
        resetMarkClaimedDropdown();
        filterValue = $('.select-claimed-value').val();
        actionValue = $('.mark-claimed-value').val();
        actionValuePrize = $('.mark-claimed-value-prize').val();

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'claimed_type',
                value: value
            },
            success: function (response) {

                let tablesToReload = Object.values(dataTablesall).length;
                let reloadCount = 0;

                function onTableReloadComplete() {
                    reloadCount++;
                    // Check if all tables have been reloaded
                    if (reloadCount === tablesToReload) {
                        hideProcessingOverlay();
                    }
                }

                Object.values(dataTablesall).forEach(function (table) {
                    // table.ajax.reload();
                    table.ajax.reload(function () {
                        onTableReloadComplete();
                    }, false); // `false` to avoid redrawing immediately

                });
                // Uncheck the "select-all" checkbox
                $('#select-all').prop('checked', false);
                // Clear selected checkboxes
                $('.user-checkbox').prop('checked', false);
                // hideProcessingOverlay();
                if (tablesToReload === 0) {
                    hideProcessingOverlay();
                }

            },
            error: function (xhr, status, error) {
                console.error('Error marking as paid:', error);
                hideProcessingOverlay();
            }
        });


    });

    function getTableIdFromDataTable(dataTable) {
        return $(dataTable.table().container()).find('table').data('id');
    }




    function checkSameType(arr) {
        if (!Array.isArray(arr) || arr.length === 0) {
            return false; // Return false for empty or invalid arrays
        }
        const firstType = arr[0]?.type; // Get the type of the first element
        const allSameType = arr.every(item => item.type === firstType);

        return allSameType ? firstType : false; // Return the type if all match, otherwise false
    }

    function markasPaid(selectedIds) {
        return new Promise((resolve, reject) => {
            if (!Array.isArray(selectedIds) || selectedIds.length === 0) {
                // hideProcessingMessage();
                reject(new Error('Please select at least one row'));
                return;
            }

            console.log('selectedIds++++++++++++++++',selectedIds);
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'mark_as_paid',
                    selected_data: JSON.stringify(selectedIds)
                },
                success: function (response) {
                    console.log('Successfully marked as paid');
                    alert('Successfully marked as paid');
                    reloadAllTables()
                        .then(() => {
                            resetUIState();
                            resolve(true);
                        })
                        .catch(error => {
                            console.error('Error reloading tables:', error);
                            resetUIState();
                            reject(error);
                        });
                },
                error: function (xhr, status, error) {
                    console.error('Error marking as paid:', error);
                    resetUIState();
                    reject(error);
                }
            });
        });
    }


    function markasPaidAndPrint(arr) {
        return new Promise((resolve, reject) => {
            if (!Array.isArray(arr) || arr.length === 0) {
                // hideProcessingMessage();
                reject(new Error('Please select at least one row'));
                return;
            }

            // updateProcessingStatus(`Processing ${arr.length} selected items...`);

            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'mark_as_paid_prize',
                    selected_data: JSON.stringify(arr),
                },
                success: function (response) {
                    if (response?.printers) {
                        openPrintWindow(response.printers);
                        reloadAllTables();
                        resetUIState();
                        // hideProcessingMessage();
                        resolve(true);
                    } else {
                        reject(new Error('Something went wrong, please try again later!'));
                    }
                },
                error: function (xhr, status, error) {
                    // hideProcessingMessage();
                    console.error('Error marking as paid:', error);
                    reject(error);
                }
            });
        });
    }


    function getSelectedRowsData() {
        const columns = {
            ticketNumber: 1,
            competitionName: 2,
            orderId: 3,
            prize: 4,
            type: 5,
            winner: 6,
            email: 7,
            tel: 8,
            address: 9,
            table: 10,
        };

        return $('.user-checkbox:checked').map(function () {
            const $row = $(this).closest('tr');
            const $cells = $row.find('td');

            const mode = $row.find('.mark-paid').data('set'); 

            return {
                id: $(this).data('id'),
                mainid:$(this).data('mainid'),
                compid:$(this).data('compid'),
                table: mode,
                ticketNumber: $cells.eq(columns.ticketNumber).text().trim(),
                competitionName: $cells.eq(columns.competitionName).text().trim(),
                orderId: $cells.eq(columns.orderId).text().trim(),
                prize: $cells.eq(columns.prize).text().trim(),
                type: $cells.eq(columns.type).text().trim(),
                winner: $cells.eq(columns.winner).text().trim(),
                email: $cells.eq(columns.email).text().trim(),
                tel: $cells.eq(columns.tel).text().trim(),
                address: $cells.eq(columns.address).text().trim()
            };
        }).get();
    }

    function reloadAllTables() {
        return new Promise((resolve) => {
            const tables = Object.values(dataTablesall);
            const tablesToReload = tables.length;

            if (tablesToReload === 0) {
                hideProcessingOverlay();
                resolve();
                return;
            }

            let reloadCount = 0;
            tables.forEach(table => {
                table.ajax.reload(() => {
                    reloadCount++;
                    if (reloadCount === tablesToReload) {
                        hideProcessingOverlay();
                        resolve();
                    }
                }, false);
            });
        });
    }

    function resetUIState() {
        $('#select-all').prop('checked', false);
        $('.user-checkbox').prop('checked', false);
    }

    function openPrintWindow(pdfUri) {
        const width = 1200;
        const height = 600;
        const top = (window.innerHeight - height) / 2;
        const left = (window.innerWidth - width) / 2;
        window.open(pdfUri, '_blank', `width=${width},height=${height},top=${top},left=${left}`);
    }



    $('.mark-claimed-value').on('change', async function () {
        var action = $(this).val();
        var selectedIds = $('.user-checkbox:checked').map(function () {
            return $(this).data('id');
        }).get();

        if (selectedIds.length == 0) {
            alert('Please select at least one row');
            return;
        }
        const selectedRowsData = getSelectedRowsData();

        console.log('selectedRowsData', selectedRowsData);



        const isSameType = checkSameType(selectedRowsData);



        console.log('isSameType', isSameType);

        if (!isSameType) {
            alert('Please select Cash only or Prize only!');
            return;
        }

        try {
            if (action === '1') {

                if (isSameType == 'Prize') {
                    alert('Please select Cash only ');
                    return;
                } else {

                    console.log('selectedRowsData+++++++++', selectedRowsData);
                    await markasPaid(selectedRowsData);

                }


            } else if (action === '2') {
                if (isSameType == 'Cash' || isSameType == 'Web Order-Prize') {
                    alert('Please select Prize only for Printing Shipping labels!');
                    return;

                } else {

                    console.log('selectedRowsData', selectedRowsData);
                    // Store the object in localStorage
                    localStorage.setItem('competitionData', JSON.stringify(selectedRowsData));
                    await markasPaidAndPrint(selectedRowsData);
                    reloadAllTables();
                    resetUIState();
                    // hideProcessingMessage();


                }
            }
        } catch (error) {
            alert(error.message);
        }

    });

    // Handle "select-all" checkbox change
    $('#select-all').on('change', function () {

        var isChecked = $(this).prop('checked');
        $('.user-checkbox').prop('checked', isChecked);
        updateSelectedIds();
        resetMarkClaimedDropdown();
    });

    // Handle individual checkbox change
    // When a user checkbox is clicked
    $('#claimedInstantWins').on('change', '.user-checkbox', function () {
        if ($(this).is(':checked')) {
            // Uncheck all other checkboxes
            $('.user-checkbox').not(this).prop('checked', false);
        }
        // Update the selected IDs and reset dropdown as necessary
        updateSelectedIds();
        resetMarkClaimedDropdown();
    });

    // Function to update selected IDs based on checked checkboxes
    // Function to update selected ID based on checked checkbox
    function updateSelectedIds() {
        var selectedIds = $('.user-checkbox:checked').map(function () {
            return $(this).data('id');
        }).get();
        console.log('Selected ID:', selectedIds); // This will contain only one ID at most
    }

    // Function to reset the mark-claimed-value dropdown
    function resetMarkClaimedDropdown() {
        $('.mark-claimed-value').val('0'); // Reset to default value (empty)
        $('.mark-claimed-value-prize').val('0'); // Reset to default value (empty)
    }

    // Show the overlay and processing indicator
    function showProcessingOverlay() {
        $('#instant-overlay').show();
        $('#claimedInstantWins_processing').show();
    }

    // Hide the overlay and processing indicator
    function hideProcessingOverlay() {
        $('#instant-overlay').hide();
        $('#claimedInstantWins_processing').hide();
    }

    $('#unpaidInstantWins tbody').on('click', '.edit-prize-title', function () {
        const columns = {
            ticketNumber: 0,
            competitionName: 1,
            orderId: 2,
            prize: 3,
            winner: 4,
            email: 5,
            tel: 6,
            address: 7
        };

        var row = $(this).closest('tr');  // Get the row
        const $cells = row.find('td');

        // Access individual cells using the columns object
        const ticketNumber = $cells.eq(columns.ticketNumber).text();
        const competitionName = $cells.eq(columns.competitionName).text();
        const orderId = $cells.eq(columns.orderId).text();
        // const prize = $cells.eq(columns.prize).text();
        let prize = decodeHtml($cells.eq(columns.prize).html());
        const type = $cells.eq(columns.type).text();
        const winner = $cells.eq(columns.winner).text();
        const email = $cells.eq(columns.email).text();
        const tel = $cells.eq(columns.tel).text();
        const address = $cells.eq(columns.address).text();


        // Access the ticketNumber cell
        const ticketCell = $cells.eq(columns.ticketNumber); // Access the first cell (index 0)


        // Access the <a> element and extract data attributes
        const linkElement = ticketCell.find('a.mark-paid'); // Find the <a> element
        const dataId = linkElement.data('id'); // Extract data-id: 620
        const dataSet = linkElement.data('set'); // Extract data-set: "instant"

        // Logging the extracted values for demonstration
        // console.log('Ticket Number:', ticketNumber);
        // console.log('Competition Name:', competitionName);
        // console.log('Order ID:', orderId);
        // console.log('Prize:', prize);
        // console.log('Type:', type);
        // console.log('Winner:', winner);
        // console.log('Email:', email);
        // console.log('Telephone:', tel);
        // console.log('Address:', address);
        // console.log('dataId:', dataId);
        // console.log('dataSet:', dataSet);
        console.log('Prize:', prize);
        prize = prize.replace(/<img[^>]*alt=["']([^"']*)["'][^>]*>/g, '$1');

        // Optionally strip remaining HTML tags
        prize = prize.replace(/<[^>]*>/g, '');
        $('#editPrizeInput').val(prize);

        $('#editModaltitle').show();


        // Handle the update button
        $('#updatePrizeBtn').off('click').on('click', function () {

            var newPrizeTitle = $('#editPrizeInput').val();

            // Check if the input is empty
            if (newPrizeTitle.trim() === "") {
                // Display error message
                $('#editPrizeInput').addClass('is-invalid');  // Add a class to style the input field with an error
                $('#error-message').remove();  // Remove previous error message if any
                $('#editPrizeInput').after('<div id="error-message" class="text-danger">This field is required.</div>');  // Add error message below the input field
                return;  // Stop further execution
            } else {
                // Remove error styling if input is valid
                $('#editPrizeInput').removeClass('is-invalid');
                $('#error-message').remove();  // Remove the error message
            }
            // Update the prize title in the DataTable
            console.log('dataId++++++++++:', dataId);
            console.log('dataSet+++++++:', dataSet);
            console.log('dataSnewPrizeTitleet+++++++:', newPrizeTitle);
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'change_prize_title',
                    id: dataId,
                    type: dataSet,
                    text: newPrizeTitle,
                    ticketNumber: ticketNumber
                },
                success: function (response) {


                    $('#editModaltitle').hide();
                    Object.values(dataTablesall).forEach(function (table) {
                        table.ajax.reload();
                    });

                    hideProcessingOverlay();
                    // hideProcessingMessage();
                    reloadAllTables(); // Close the modal


                },
                error: function (xhr, status, error) {
                    // hideProcessingMessage();
                    console.error('Error marking as paid:', error);
                }
            });

        });

        // Handle the cancel button
        $('#cancelEditBtn').off('click').on('click', function () {
            $('#editModaltitle').hide(); // Close the modal without saving changes
            // Remove error styling if input is valid
            $('#editPrizeInput').removeClass('is-invalid');
            $('#error-message').remove();  // Remove the error message
        });

        // // Open the modal and populate the input with the prize title
        // $('#editPrizeInput').val(rowData[3]);  // Assuming prize title is in 4th column (index 3)

        // // Show the modal
        // $('#editModal').show();

        // // Set up the update button to update the row data
        // $('#updatePrizeBtn').off('click').on('click', function () {
        //     // Get the new prize title from the input field
        //     var newPrizeTitle = $('#editPrizeInput').val();

        //     // Update the table cell (the 4th column in the row)
        //     table.cell(row, 3).data(newPrizeTitle).draw();

        //     // Close the modal
        //     $('#editModal').hide();
        // });

        // // Cancel button to close the modal without updating
        // $('#cancelEditBtn').off('click').on('click', function () {
        //     $('#editModal').hide();
        // });
    });

    $('#paidInstantWins tbody').on('click', '.edit-prize-title', function () {
        const columns = {
            ticketNumber: 0,
            competitionName: 1,
            orderId: 2,
            prize: 3,
            winner: 4,
            email: 5,
            tel: 6,
            address: 7
        };

        var row = $(this).closest('tr');  // Get the row
        const $cells = row.find('td');

        // Access individual cells using the columns object
        const ticketNumber = $cells.eq(columns.ticketNumber).text();
        const competitionName = $cells.eq(columns.competitionName).text();
        const orderId = $cells.eq(columns.orderId).text();
        // const prize = $cells.eq(columns.prize).text();
        let prize = decodeHtml($cells.eq(columns.prize).html());
        const type = $cells.eq(columns.type).text();
        const winner = $cells.eq(columns.winner).text();
        const email = $cells.eq(columns.email).text();
        const tel = $cells.eq(columns.tel).text();
        const address = $cells.eq(columns.address).text();


        // Access the ticketNumber cell
        const ticketCell = $cells.eq(columns.ticketNumber); // Access the first cell (index 0)


        // Access the <a> element and extract data attributes
        const linkElement = ticketCell.find('a.mark-paid'); // Find the <a> element
        const dataId = linkElement.data('id'); // Extract data-id: 620
        const dataSet = linkElement.data('set'); // Extract data-set: "instant"

        // Logging the extracted values for demonstration
        // console.log('Ticket Number:', ticketNumber);
        // console.log('Competition Name:', competitionName);
        // console.log('Order ID:', orderId);
        // console.log('Prize:', prize);
        // console.log('Type:', type);
        // console.log('Winner:', winner);
        // console.log('Email:', email);
        // console.log('Telephone:', tel);
        // console.log('Address:', address);
        // console.log('dataId:', dataId);
        // console.log('dataSet:', dataSet);

        console.log('Prize:', prize);
        prize = prize.replace(/<img[^>]*alt=["']([^"']*)["'][^>]*>/g, '$1');

        // Optionally strip remaining HTML tags
        prize = prize.replace(/<[^>]*>/g, '');
        $('#editPrizeInput').val(prize);

        $('#editModaltitle').show();


        // Handle the update button
        $('#updatePrizeBtn').off('click').on('click', function () {

            var newPrizeTitle = $('#editPrizeInput').val();

            // Check if the input is empty
            if (newPrizeTitle.trim() === "") {
                // Display error message
                $('#editPrizeInput').addClass('is-invalid');  // Add a class to style the input field with an error
                $('#error-message').remove();  // Remove previous error message if any
                $('#editPrizeInput').after('<div id="error-message" class="text-danger">This field is required.</div>');  // Add error message below the input field
                return;  // Stop further execution
            } else {
                // Remove error styling if input is valid
                $('#editPrizeInput').removeClass('is-invalid');
                $('#error-message').remove();  // Remove the error message
            }
            // Update the prize title in the DataTable
            console.log('dataId++++++++++:', dataId);
            console.log('dataSet+++++++:', dataSet);
            console.log('dataSnewPrizeTitleet+++++++:', newPrizeTitle);
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'change_prize_title',
                    id: dataId,
                    type: dataSet,
                    text: newPrizeTitle,
                    ticketNumber: ticketNumber
                },
                success: function (response) {


                    $('#editModaltitle').hide();
                    Object.values(dataTablesall).forEach(function (table) {
                        table.ajax.reload();
                    });

                    hideProcessingOverlay();
                    // hideProcessingMessage();
                    reloadAllTables(); // Close the modal


                },
                error: function (xhr, status, error) {
                    // hideProcessingMessage();
                    console.error('Error marking as paid:', error);
                }
            });

        });

        // Handle the cancel button
        $('#cancelEditBtn').off('click').on('click', function () {
            $('#editModaltitle').hide(); // Close the modal without saving changes
            // Remove error styling if input is valid
            $('#editPrizeInput').removeClass('is-invalid');
            $('#error-message').remove();  // Remove the error message
        });

        // // Open the modal and populate the input with the prize title
        // $('#editPrizeInput').val(rowData[3]);  // Assuming prize title is in 4th column (index 3)

        // // Show the modal
        // $('#editModal').show();

        // // Set up the update button to update the row data
        // $('#updatePrizeBtn').off('click').on('click', function () {
        //     // Get the new prize title from the input field
        //     var newPrizeTitle = $('#editPrizeInput').val();

        //     // Update the table cell (the 4th column in the row)
        //     table.cell(row, 3).data(newPrizeTitle).draw();

        //     // Close the modal
        //     $('#editModal').hide();
        // });

        // // Cancel button to close the modal without updating
        // $('#cancelEditBtn').off('click').on('click', function () {
        //     $('#editModal').hide();
        // });
    });


    $('#claimedInstantWins tbody').on('click', '.edit-prize-title', function () {
        const columns = {
            ticketNumber: 1,
            competitionName: 2,
            orderId: 3,
            prize: 4,
            winner: 5,
            email: 6,
            tel: 7,
            address: 8
        };

        var row = $(this).closest('tr');  // Get the row
        const $cells = row.find('td');

        // Access individual cells using the columns object
        const ticketNumber = $cells.eq(columns.ticketNumber).text();
        const competitionName = $cells.eq(columns.competitionName).text();
        const orderId = $cells.eq(columns.orderId).text();
        // const prize = $cells.eq(columns.prize).text();
        let prize = decodeHtml($cells.eq(columns.prize).html());
        const strippedText = prize.replace(/<[^>]*>/g, '');
        const type = $cells.eq(columns.type).text();
        const winner = $cells.eq(columns.winner).text();
        const email = $cells.eq(columns.email).text();
        const tel = $cells.eq(columns.tel).text();
        const address = $cells.eq(columns.address).text();


        // Access the ticketNumber cell
        const ticketCell = $cells.eq(columns.ticketNumber); // Access the first cell (index 0)


        // Access the <a> element and extract data attributes
        const linkElement = ticketCell.find('a.mark-paid'); // Find the <a> element
        const dataId = linkElement.data('id'); // Extract data-id: 620
        const dataSet = linkElement.data('set'); // Extract data-set: "instant"


        console.log('Prize:', prize);
        console.log('strippedText:', strippedText);

        prize = prize.replace(/<img[^>]*alt=["']([^"']*)["'][^>]*>/g, '$1');

        // Optionally strip remaining HTML tags
        prize = prize.replace(/<[^>]*>/g, '');

        console.log('Prize:++++++++++++', prize);

        $('#editPrizeInput').val(prize);

        $('#editModaltitle').show();


        // Handle the update button
        $('#updatePrizeBtn').off('click').on('click', function () {

            var newPrizeTitle = $('#editPrizeInput').val();

            // Check if the input is empty
            if (newPrizeTitle.trim() === "") {
                // Display error message
                $('#editPrizeInput').addClass('is-invalid');  // Add a class to style the input field with an error
                $('#error-message').remove();  // Remove previous error message if any
                $('#editPrizeInput').after('<div id="error-message" class="text-danger">This field is required.</div>');  // Add error message below the input field
                return;  // Stop further execution
            } else {
                // Remove error styling if input is valid
                $('#editPrizeInput').removeClass('is-invalid');
                $('#error-message').remove();  // Remove the error message
            }
            // Update the prize title in the DataTable
            console.log('dataId++++++++++:', dataId);
            console.log('dataSet+++++++:', dataSet);
            console.log('dataSnewPrizeTitleet+++++++:', newPrizeTitle);
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'change_prize_title',
                    id: dataId,
                    type: dataSet,
                    text: newPrizeTitle,
                    ticketNumber: ticketNumber
                },
                success: function (response) {


                    $('#editModaltitle').hide();
                    Object.values(dataTablesall).forEach(function (table) {
                        table.ajax.reload();
                    });

                    hideProcessingOverlay();
                    // hideProcessingMessage();
                    reloadAllTables(); // Close the modal


                },
                error: function (xhr, status, error) {
                    // hideProcessingMessage();
                    console.error('Error marking as paid:', error);
                }
            });

        });

       



        // Handle the cancel button
        $('#cancelEditBtn').off('click').on('click', function () {
            $('#editModaltitle').hide(); // Close the modal without saving changes
            // Remove error styling if input is valid
            $('#editPrizeInput').removeClass('is-invalid');
            $('#error-message').remove();  // Remove the error message
        });


    });

    function decodeHtml(html) {
        var txt = document.createElement('textarea');
        txt.innerHTML = html;
        return txt.value;
    }


});