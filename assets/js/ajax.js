function ajaxRequest(method, url, data, callback, isFormData = false) {
    const $submitBtn = $('#submitBtn');
    const $spinner = $submitBtn.find('.spinner-border');

    // Disable the submit button and show the spinner
    $submitBtn.prop('disabled', true);
    $spinner.removeClass('d-none');

    $.ajax({
        type: method,
        url: url,
        data: isFormData ? data : JSON.stringify(data),
        contentType: isFormData ? false : 'application/json',
        processData: !isFormData,
        success: function(response) {
            callback(response);
        },
        error: function(xhr, status, error) {
            let errorMessage = 'An error occurred';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    errorMessage = xhr.responseText;
                }
            }
            callback({ success: false, message: errorMessage });
        },
        complete: function() {
            // Enable the submit button and hide the spinner
            $submitBtn.prop('disabled', false);
            $spinner.addClass('d-none');
        }
    });
}