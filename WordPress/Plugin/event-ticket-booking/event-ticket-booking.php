<?php
/*
Plugin Name: Event Ticket Booking
Description: A simple plugin to manage event ticket bookings with validation and security.
Version: 1.0.0
Author: Ravi Shankar
Text Domain: event-ticket-booking
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define( 'EVTB_VERSION', '1.0.0' );
define( 'EVTB_DIR', plugin_dir_path( __FILE__ ) );
define( 'EVTB_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once EVTB_DIR . 'includes/event-handler.php';
require_once EVTB_DIR . 'includes/booking-handler.php';

// Admin necessary files
require_once EVTB_DIR . 'admin/intro-page.php';
require_once EVTB_DIR . 'admin/manage-event-page.php';
require_once EVTB_DIR . 'admin/event-booking-page.php';
require_once EVTB_DIR . 'admin/qrcode-page.php';

// Enqueue styles and scripts for the plugin
function evtb_enqueue_scripts() {
    wp_enqueue_style('evtb-style', plugins_url('/css/style.css', __FILE__));
    wp_enqueue_script('evtb-script', plugins_url('/js/script.js', __FILE__), array('jquery'), null, true); // Load script in footer
}
add_action('wp_enqueue_scripts', 'evtb_enqueue_scripts');

// Create custom database tables for event tickets and event list
function evtb_activate_plugin() {
    global $wpdb;

    // Define the table names
    $event_tickets_table = $wpdb->prefix . 'event_tickets';
    $event_list_table = $wpdb->prefix . 'event_list';

    // Define the character set and collation
    $charset_collate = $wpdb->get_charset_collate();

    // SQL statement to create event_tickets table
    $tickets_sql = "CREATE TABLE IF NOT EXISTS $event_tickets_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email varchar(100) NOT NULL,
        event_id mediumint(9) NOT NULL,
        purchase_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        ticket_number varchar(50) NOT NULL,
        transaction_id varchar(100) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // SQL statement to create event_list table
    $events_sql = "CREATE TABLE IF NOT EXISTS $event_list_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        start_date datetime NOT NULL,
        end_date datetime NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    // Include the upgrade script
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    // Create tables using dbDelta
    dbDelta($tickets_sql);
    dbDelta($events_sql);
}

// Hook the function to the plugin activation
register_activation_hook(__FILE__, 'evtb_activate_plugin');

// Deactivation hook to delete data from the custom tables
function evtb_deactivate_plugin() {
    global $wpdb;

    // Define the table names with the correct prefix
    // $event_tickets_table = $wpdb->prefix . 'event_tickets';
    // $event_list_table = $wpdb->prefix . 'event_list';

    // Ensure that the table names only contain safe characters (alphanumeric + underscores)
    // if ( !preg_match('/^[a-z0-9_]+$/i', $event_tickets_table) || !preg_match('/^[a-z0-9_]+$/i', $event_list_table) ) {
    //     // Invalid table name, return or log error
    //     return;
    // }

    // Safely delete all data from event_tickets and event_list tables without using $wpdb->prepare() for table names
    $wpdb->query("DELETE FROM {$wpdb->prefix}event_tickets");  // Correct way to use the table name with prefix
    $wpdb->query("DELETE FROM {$wpdb->prefix}event_list"); 
}

// Hook the function to the plugin deactivation
register_deactivation_hook(__FILE__, 'evtb_deactivate_plugin');

// Register shortcodes
function evtb_register_shortcodes() {
    add_shortcode('evtb_booking', 'evtb_booking_form');
    add_shortcode('evtb_events', 'evtb_display_events');
}
add_action( 'init', 'evtb_register_shortcodes' );

// Add an admin menu for event ticket bookings
function evtb_admin_menu() {
    // Create the main menu
    $parent_slug = 'evtb-bookings';
    add_menu_page('Event Ticket Bookings', 'Event Ticket Bookings', 'manage_options', $parent_slug, 'evtb_bookings_page');

    // Create submenus
    add_submenu_page($parent_slug, 'Plugin Introduction', 'Introduction', 'manage_options', 'evtb-intro', 'evtb_intro_page'); // New intro page
    add_submenu_page($parent_slug, 'Manage Events', 'Manage Events', 'manage_options', 'evtb-manage-events', 'evtb_manage_events_page');
    
    // Add submenu for QR Code settings
    add_submenu_page($parent_slug, 'QR Code Settings', 'QR Code', 'manage_options', 'evtb-qr-code', 'evtb_qr_code_page');
}

add_action('admin_menu', 'evtb_admin_menu');


?>