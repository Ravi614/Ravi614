<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die; // Exit if accessed directly
}

function evtb_uninstall_plugin() {
    global $wpdb;

    // Define the table names
    $tables_to_drop = [
        $wpdb->prefix . 'event_tickets',
        $wpdb->prefix . 'event_list',
    ];

    // Drop custom tables - safely formatting the query
    foreach ($tables_to_drop as $table) {
        // Safely concatenate the table name into the query string
        $sql_delete = "DROP TABLE IF EXISTS {$wpdb->prefix}$table";  // Ensure table name includes prefix

        // Execute the query
        $wpdb->query($sql_delete);
    }

    // Delete the option storing the QR code URL
    delete_option('evtb_qr_code_url');

    // Optional: Delete transients if you have any related to your plugin
    delete_transient('evtb_event_data');  // Deleting a specific transient

    // Or, delete all transients manually if needed
    $transients = $wpdb->get_results("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_ev_tb_%'");
    foreach ($transients as $transient) {
        delete_option($transient->option_name); // Remove transient value
    }

    // Also delete the expiration time for transients
    $transient_time = $wpdb->get_results("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_ev_tb_%'");
    foreach ($transient_time as $transient) {
        delete_option($transient->option_name); // Remove transient timeout
    }

    // Additional cleanup for other plugin-specific options or data if needed
    // Example: delete custom options, post meta, or user meta if your plugin stores them
    // delete_option('evtb_some_other_option');
    // delete_user_meta($user_id, 'evtb_user_meta_key');
}

register_uninstall_hook(__FILE__, 'evtb_uninstall_plugin');

?>