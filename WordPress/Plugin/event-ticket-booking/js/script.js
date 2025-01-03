jQuery(document).ready(function($) {
    $('#show-qr-btn').on('click', function() {
        $('#qr-code').show(); // Show the QR code
    });

    $('#confirm-payment-btn').on('click', function() {
        // Show the transaction ID input and hide both buttons
        $('#transaction-id-container').show();
        $('#show-qr-btn').hide(); // Hide the Proceed to Pay button
        $('#qr-code').hide(); // Hide the QR Code Image
        $('#confirm-payment-btn').hide(); // Hide the Payment Done button
        $('#transaction-id-container input').focus(); // Focus on the input

        // Display the message
        $('#booking-result').append('<div class="etb-form-info">Note: Please Wait or press Enter after entering the UPI transaction ID to complete the process.</div>');
    });

    $('#transaction-id-container input').on('change', function() {
        var transactionId = $(this).val();

        if (transactionId) {
            var form = $('#ticket-booking-form')[0]; // Get the form element
            var formData = new FormData(form);

            fetch('/wp-admin/admin-ajax.php?action=evtb_process_booking', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                $('#booking-result').html(data); // Display the response
                
                // Check for success message in the response
                if (data.includes('Booking successful')) {
                    alert('Take a screenshot before pressing OK and closing the browser.'); // Alert user to wait
                    
                    // Extract name, email, and ticket number from formData
                    var name = formData.get('name');
                    var email = formData.get('email');
                    var ticketNumber = formData.get('ticket_number');
                    
                    // Display booking details
                    $('#booking-result').append(`
                        <div class="success">
                            Name: ${name}<br>
                            Email: ${email}<br>
                            Ticket Number: ${ticketNumber}
                        </div>
                    `);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $('#booking-result').append('<div class="error">An error occurred. Please try again.</div>'); // Display error message
            });
        } else {
            // Display an error message and scroll to the transaction ID field
            $('#booking-result').append('<div class="error">Please enter a valid transaction ID to proceed.</div>');
            $('html, body').animate({
                scrollTop: $('#transaction-id-container').offset().top
            }, 500);
        }
    });
});
