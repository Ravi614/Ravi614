<?php
/**
 * Admin page for managing events.
 */
function evtb_manage_events_page() {
    global $wpdb;

    // Handle form submission for adding new events
    if (isset($_POST['evtb_add_event'])) {
        // Verify nonce for security
        if ( ! isset( $_POST['evtb_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['evtb_nonce'] ) ), 'evtb_add_event' ) ) {
            echo '<div class="error">Security check failed. Please try again.</div>';
            return;
        }

        // Sanitize input values
        $name = isset($_POST['event_name']) ? sanitize_text_field(wp_unslash($_POST['event_name'])) : ''; 
        $start_date = isset($_POST['event_start_date']) ? sanitize_text_field(wp_unslash($_POST['event_start_date'])) : '';
        $end_date = isset($_POST['event_end_date']) ? sanitize_text_field(wp_unslash($_POST['event_end_date'])) : '';

        if (empty($name) || empty($start_date) || empty($end_date)) {
            echo '<div class="error">Please fill in all fields.</div>';
        } elseif (strtotime($end_date) < strtotime($start_date)) {
            echo '<div class="error">End date must be after the start date.</div>';
        } else {
            // Prepare data for insertion
            $data = [
                'name' => $name,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ];
        
            // Insert into database
            $inserted = $wpdb->insert($wpdb->prefix . 'event_list', $data);
        
            // Check for errors
            if ($inserted === false) {
                echo '<div class="error">Error inserting event: ' . esc_html($wpdb->last_error) . '</div>';
            } else {
                echo '<div class="updated">Event added successfully!</div>';
            }
        }        
    }

    // Handle form submission for deleting events
    if (isset($_POST['evtb_delete_event'])) {
        // Verify nonce for security
        if (!isset($_POST['evtb_nonce']) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['evtb_nonce'] ) ), 'evtb_delete_event')) {
            echo '<div class="error">Security check failed. Please try again.</div>';
            return;
        }

        // Check if event_id is set
        if (isset($_POST['event_id'])) {
            $event_id = intval($_POST['event_id']);
            $deleted = $wpdb->delete($wpdb->prefix . 'event_list', ['id' => $event_id]);

            // Clear cached events after deletion
            wp_cache_delete('events', 'evtb_event_list');

            if ($deleted) {
                echo '<div class="updated">Event deleted successfully!</div>';
            } else {
                echo '<div class="error">Error deleting event with ID ' . esc_html($event_id) . '.</div>';
            }
        } else {
            echo '<div class="error">Event ID is missing.</div>';
        }
    }    

    // Fetch existing events with caching
    $events = wp_cache_get('events', 'evtb_event_list');

    if ($events === false) {
        // Prepare and fetch results from the database if not cached
        $events = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}event_list", ARRAY_A);
        
        // Cache the results for future use
        wp_cache_set('events', $events, 'evtb_event_list');
    }
    
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Manage Events', 'event-ticket-booking'); ?></h1>
        <form method="POST">
            <?php wp_nonce_field('evtb_add_event', 'evtb_nonce'); ?>
            <input type="text" name="event_name" placeholder="<?php esc_attr_e('Event Name', 'event-ticket-booking'); ?>" required>
            <input type="datetime-local" name="event_start_date" required>
            <input type="datetime-local" name="event_end_date" required>
            <button type="submit" name="evtb_add_event"><?php esc_html_e('Add Event', 'event-ticket-booking'); ?></button>
        </form>

        <h2><?php esc_html_e('Existing Events', 'event-ticket-booking'); ?></h2>
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th scope="col"><?php esc_html_e('ID', 'event-ticket-booking'); ?></th>
                    <th scope="col"><?php esc_html_e('Name', 'event-ticket-booking'); ?></th>
                    <th scope="col"><?php esc_html_e('Start Date', 'event-ticket-booking'); ?></th>
                    <th scope="col"><?php esc_html_e('End Date', 'event-ticket-booking'); ?></th>
                    <th scope="col"><?php esc_html_e('Action', 'event-ticket-booking'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php if ($events): ?>
                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo esc_html($event['id']); ?></td>
                        <td><?php echo esc_html($event['name']); ?></td>
                        <td><?php echo esc_html(gmdate('Y-m-d H:i', strtotime($event['start_date']))); ?></td>
                        <td><?php echo esc_html(gmdate('Y-m-d H:i', strtotime($event['end_date']))); ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <?php wp_nonce_field('evtb_delete_event', 'evtb_nonce'); ?>
                                <input type="hidden" name="event_id" value="<?php echo esc_attr($event['id']); ?>">
                                <button type="submit" name="evtb_delete_event"><?php esc_html_e('Delete', 'event-ticket-booking'); ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5"><?php esc_html_e('No events found.', 'event-ticket-booking'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>
