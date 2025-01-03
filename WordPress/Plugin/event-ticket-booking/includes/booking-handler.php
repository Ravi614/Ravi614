<?php

// Shortcode function for the booking form
function evtb_booking_form() {
    global $wpdb;
    // Define a cache key
    $cache_key = 'event_list_cache';
    
    // Attempt to get events from cache first
    $events = wp_cache_get($cache_key);

    if ($events === false) {
        // Fetch existing events with a prepared statement
        $events = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}event_list");

        // Store the results in cache for future use
        wp_cache_set($cache_key, $events);
    }

    // Get the QR code URL from the options
    $qr_code_url = get_option('evtb_qr_code_url');

    ob_start();

    ?>
    <form id="ticket-booking-form">
        <?php wp_nonce_field('evtb_ticket_booking', 'evtb_nonce'); ?>
        <input type="text" name="name" placeholder="<?php esc_attr_e('Your Name', 'event-ticket-booking'); ?>" required>
        <input type="email" name="email" placeholder="<?php esc_attr_e('Your Email', 'event-ticket-booking'); ?>" required>
        <select name="event_id" required>
            <option value=""><?php esc_html_e('Select Event', 'event-ticket-booking'); ?></option>
            <?php foreach ($events as $event): ?>
                <option value="<?php echo esc_attr($event->id); ?>">
                    <?php echo esc_html($event->name); ?> - Start: <?php echo esc_html(gmdate('Y-m-d H:i', strtotime($event->start_date))); ?>, End: <?php echo esc_html(gmdate('Y-m-d H:i', strtotime($event->end_date))); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="ticket_number" value="<?php echo esc_attr(uniqid()); ?>">

        <div id="transaction-id-container" style="display:none;">
            <input type="text" name="transaction_id" placeholder="<?php esc_attr_e('Enter Correct UPI Transaction ID', 'event-ticket-booking'); ?>" required>
        </div>

        <button type="button" id="show-qr-btn"><?php esc_html_e('Proceed to Pay', 'event-ticket-booking'); ?></button>
    </form>

    <div id="qr-code" style="display:none;">
        <h2><?php esc_html_e('Scan to Pay', 'event-ticket-booking'); ?></h2>
        <?php if ($qr_code_url): ?>
            <img id="evtb-upi-qr-code" src="<?php echo esc_url($qr_code_url); ?>" alt="<?php esc_attr_e('UPI QR Code', 'event-ticket-booking'); ?>" width="400" height="400">
            <button id="confirm-payment-btn"><?php esc_html_e('Payment Done', 'event-ticket-booking'); ?></button>
        <?php else: ?>
            <p><?php esc_html_e('No QR Code available. Please upload one in the admin settings.', 'event-ticket-booking'); ?></p>
        <?php endif; ?>
    </div>

    <div id="booking-result"></div> <!-- Feedback messages will be displayed here -->

    <?php
    return ob_get_clean();
}

// Process the booking
function evtb_process_booking() {
    global $wpdb;

    // Check nonce
    if (!isset($_POST['evtb_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['evtb_nonce'])), 'evtb_ticket_booking')) {
        echo '<div class="error">' . esc_html__('Security check failed. Please try again.', 'event-ticket-booking') . '</div>';
        wp_die();
    }

    // Sanitize input with checks
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $event_id = isset($_POST['event_id']) ? intval(wp_unslash($_POST['event_id'])) : 0;
    $ticket_number = isset($_POST['ticket_number']) ? sanitize_text_field(wp_unslash($_POST['ticket_number'])) : '';
    $transaction_id = isset($_POST['transaction_id']) ? sanitize_text_field(wp_unslash($_POST['transaction_id'])) : '';

    // Basic validation
    if (empty($name) || empty($email) || !is_email($email) || empty($event_id) || empty($transaction_id)) {
        echo '<div class="error">' . esc_html__('Please fill in all fields correctly.', 'event-ticket-booking') . '</div>';
        wp_die();
    }

    // Check for duplicate email
    $cache_key = 'email_check_' . md5($email); // Create a unique cache key based on the email
    $existing_email = wp_cache_get($cache_key);

    if ($existing_email === false) {
        // If not in cache, query the database
        $existing_email = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}event_tickets WHERE email = %s", $email));
        
        // Store the result in cache for future use
        wp_cache_set($cache_key, $existing_email);
    }

    if ($existing_email > 0) {
        echo '<div class="error">' . esc_html__('This email address is already associated with a booking.', 'event-ticket-booking') . '</div>';
        wp_die();
    }

    // Check for duplicate transaction ID
    $cache_key = 'transaction_check_' . md5($transaction_id); // Create a unique cache key based on the transaction ID
    $existing_transaction = wp_cache_get($cache_key);

    if ($existing_transaction === false) {
        // If not in cache, query the database
        $existing_transaction = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}event_tickets WHERE transaction_id = %s", $transaction_id));

        // Store the result in cache for future use
        wp_cache_set($cache_key, $existing_transaction);
    }

    if ($existing_transaction > 0) {
        echo '<div class="error">' . esc_html__('This transaction ID has already been used for a booking.', 'event-ticket-booking') . '</div>';
        wp_die();
    }

    // Insert into database
    $result = $wpdb->insert(
        $wpdb->prefix . 'event_tickets',
        [
            'name' => $name,
            'email' => $email,
            'event_id' => $event_id,
            'ticket_number' => $ticket_number,
            'transaction_id' => $transaction_id
        ],
        ['%s', '%s', '%d', '%s', '%s'] // Data types for prepared statement
    );

    // Check if insert was successful
    if ($result) {
        echo '<div class="success">' . esc_html__('Booking successful! Your details are:', 'event-ticket-booking') . '</div>';
    } else {
        echo '<div class="error">' . esc_html__('There was an error processing your booking. Please try again.', 'event-ticket-booking') . '</div>';
    }
    wp_die(); // Ensure to end the AJAX call properly
}

add_action('wp_ajax_evtb_process_booking', 'evtb_process_booking');
add_action('wp_ajax_nopriv_evtb_process_booking', 'evtb_process_booking'); // For non-logged in users
