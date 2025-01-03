<?php
// Callback function for the introduction page
function evtb_intro_page() {
    ?>
    <div class="wrap">
        <h1>Welcome to Event Ticket Bookings!</h1>
        <p>Thank you for selecting the Event Ticket Bookings plugin. Below are some important details to ensure a seamless experience:</p>
        <h2>Features:</h2>
        
        * Effortlessly manage your event ticket bookings.<br>
        * Utilize shortcodes to display events and ticket options on your site.<br>
        * Upon activation, four submenus will be available: Event Ticket Booking, Plugin Introduction, Manage Events, and QR Code.<br>
        * There is event admin page to set the events and use of shortcode to showcase the events as a card view on front end.<br>
        * Once the event is set, you can see the event or events you configured appearing in the dropdown box for the booking form.<br>
        * Users will need to fill in the fields and choose the event for which they want to participate or gain entry.<br>
        * Once the scan and payment are done (which is, of course, a simple setup with no third-party integration), <br>
        clicking "Payment Done" will reveal a new field to enter the UPI transaction ID, and then press enter to complete the booking.<br>
        * Once the booking is completed, you will see a ticket number along with your name and email address will be generated and displayed on the page below the form.

        <h2>Using Shortcodes:</h2>
        <p>To implement the shortcode, simply add the following code to your post or page. This will display the booking form on page or post.</p>
        <pre>Shortcode for bookings: [evtb_booking] and Shortcode for Event(s): [evtb_events]</pre>
    </div>

    <?php
}