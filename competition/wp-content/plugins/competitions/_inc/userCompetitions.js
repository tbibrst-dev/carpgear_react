document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll('#wallet_unlock, #wallet_lock').forEach(function (element) {
        element.addEventListener('click', function (e) {
            e.preventDefault();
        });
    });

    // Event listeners for lock and unlock account buttons
    document.querySelectorAll('#lock_account, #unlock_account').forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            var userId = button.getAttribute('data-user-id');
            var lockAction = button.getAttribute('data-lock-action');

            // Create a new AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ajax_object.ajax_url, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            if (parseInt(lockAction) == 1) {
                                document.getElementById('lock-account-description').style.display = 'none';
                                document.getElementById('lock-account-div').style.display = 'block';
                                document.getElementById('lock_account').disabled = true;
                            } else {
                                document.getElementById('unlock-account-description').style.display = 'none';
                                document.getElementById('unlock-account-div').style.display = 'block';
                                document.getElementById('unlock_account').disabled = true;
                            }
                        }
                    } else {
                        console.error(xhr.responseText);
                    }
                }
            };

            // Prepare data to send
            var data = 'action=update_lock_account';
            data += '&user_id=' + encodeURIComponent(userId);
            data += '&lock_action=' + encodeURIComponent(lockAction);

            // Send the request
            xhr.send(data);
        });
    });
});
