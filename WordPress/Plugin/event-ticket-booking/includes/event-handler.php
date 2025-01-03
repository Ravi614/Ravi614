<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Shortcode to display events as cards on front end
function evtb_display_events($atts) {
    global $wpdb;

    // Fetch existing events
    $events = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}event_list");

    // Start output buffering
    ob_start();
    
    // Check if there are any events
    if ($events) {
        echo '<div class="evtb-events">';
        echo '<h2>' . esc_html__('Upcoming Events', 'event-ticket-booking') . '</h2>';
        echo '<div class="event-cards">';
        
        foreach ($events as $event) {
            echo '<div class="event-card">';
            echo '<h3>' . esc_html($event->name) . '</h3>';
            echo '<p><strong>' . esc_html__('Start Date:', 'event-ticket-booking') . '</strong> ' . esc_html(gmdate('Y-m-d H:i', strtotime($event->start_date))) . '</p>';
            echo '<p><strong>' . esc_html__('End Date:', 'event-ticket-booking') . '</strong> ' . esc_html(gmdate('Y-m-d H:i', strtotime($event->end_date))) . '</p>';
            echo '</div>'; // Close card
        }
        
        echo '</div>'; // Close event-cards
        echo '</div>'; // Close evtb-events
    } else {
        echo '<p>' . esc_html__('No events found.', 'event-ticket-booking') . '</p>';
    }

    // Return the output
    return ob_get_clean();
}
?>
