<?php

// Admin page to display bookings
function evtb_bookings_page() {
    global $wpdb;
    $bookings = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}event_tickets");

    echo '<h1>' . esc_html__('Event Ticket Bookings', 'event-ticket-booking') . '</h1>';
    echo '<table class="widefat striped">';
    echo '<thead>';
    echo '<tr><th scope="col">' . esc_html__('ID', 'event-ticket-booking') . '</th>';
    echo '<th scope="col">' . esc_html__('Name', 'event-ticket-booking') . '</th>';
    echo '<th scope="col">' . esc_html__('Email', 'event-ticket-booking') . '</th>';
    echo '<th scope="col">' . esc_html__('Event ID', 'event-ticket-booking') . '</th>';
    echo '<th scope="col">' . esc_html__('Transaction ID', 'event-ticket-booking') . '</th>';
    echo '<th scope="col">' . esc_html__('Purchase Date', 'event-ticket-booking') . '</th>';
    echo '<th scope="col">' . esc_html__('Ticket Number', 'event-ticket-booking') . '</th></tr>';
    echo '</thead>';
    echo '<tbody>';
    
    if (empty($bookings)) {
        echo '<tr><td colspan="7">' . esc_html__('No bookings found.', 'event-ticket-booking') . '</td></tr>';
    } else {
        foreach ($bookings as $booking) {
            echo '<tr>';
            echo '<td>' . esc_html($booking->id) . '</td>';
            echo '<td>' . esc_html($booking->name) . '</td>';
            echo '<td>' . esc_html($booking->email) . '</td>';
            echo '<td>' . esc_html($booking->event_id) . '</td>';
            echo '<td>' . esc_html($booking->transaction_id) . '</td>';
            echo '<td>' . esc_html($booking->purchase_date) . '</td>';
            echo '<td>' . esc_html($booking->ticket_number) . '</td>';
            echo '</tr>';
        }
    }
    
    echo '</tbody>';
    echo '</table>';
}