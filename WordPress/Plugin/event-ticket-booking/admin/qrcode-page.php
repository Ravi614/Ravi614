<?php
// Display the QR Code settings page
function evtb_qr_code_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('QR Code Settings', 'event-ticket-booking'); ?></h1>
        
        <!-- Form to upload the QR code image -->
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('evtb_save_qr_code', 'evtb_nonce'); ?> <!-- Nonce field to protect the form -->
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Upload QR Code', 'event-ticket-booking'); ?></th>
                    <td>
                        <input type="file" name="qr_code" accept="image/jpeg, image/jpg, image/png" required />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"></th>
                    <td>
                        <input type="submit" value="<?php esc_attr_e('Upload QR Code', 'event-ticket-booking'); ?>" class="button-primary" />
                    </td>
                </tr>
            </table>
        </form>

        <?php
        // Display the current QR Code URL if it exists
        $qr_code_url = get_option('evtb_qr_code_url');
        if ($qr_code_url) {
            echo '<h2>' . esc_html__('Current QR Code', 'event-ticket-booking') . '</h2>';
            echo '<img src="' . esc_url($qr_code_url) . '" width="400" height="400" alt="' . esc_attr__('QR Code', 'event-ticket-booking') . '" />';
            echo '<p>' . esc_html__('QR Code URL: ', 'event-ticket-booking') . esc_html($qr_code_url) . '</p>';
        }

        // Check if there are any messages to display (success or error)
        if (isset($_POST['evtb_nonce'])) {
            // Check nonce for security
            if (!isset($_POST['evtb_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['evtb_nonce'])), 'evtb_save_qr_code')) {
                echo '<div class="error"><p>' . esc_html__('Nonce verification failed.', 'event-ticket-booking') . '</p></div>';
            } elseif (!empty($_FILES['qr_code']['name'])) {
                // Use wp_handle_upload to move the file to the uploads directory
                $movefile = wp_handle_upload($_FILES['qr_code'], array('test_form' => false));

                // Check for errors
                if ($movefile && !isset($movefile['error'])) {
                    // Get the file URL after upload
                    $qr_code_url = esc_url_raw($movefile['url']);  // Sanitized URL

                    // Save the QR code URL to the options table
                    update_option('evtb_qr_code_url', $qr_code_url);

                    // Display success message
                    echo '<div class="updated"><p>' . esc_html__('QR Code uploaded successfully!', 'event-ticket-booking') . '</p></div>';

                    // Force reload of the page after upload to show the updated image using PHP header redirect
                    // sanitizes the URL by removing any potentially unsafe characters.
                    header("Location: " . filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
                    exit(); // Make sure to call exit after the redirect to stop further execution
                } else {
                    // If there was an error with the upload, show the error message
                    echo '<div class="error"><p>' . esc_html__('Error uploading QR Code: ', 'event-ticket-booking') . esc_html($movefile['error']) . '</p></div>';
                }
            } else {
                // Display error if no file was selected
                echo '<div class="error"><p>' . esc_html__('No file selected for upload.', 'event-ticket-booking') . '</p></div>';
            }
        }
        ?>
    </div>
    <?php
}

// Handle QR Code upload (No redirection, stays on the same page)
function evtb_handle_qr_code_upload() {
    // This function is now just for form submission handling (handled above)
}

add_action('admin_init', 'evtb_handle_qr_code_upload'); // Use admin_init to handle form submission
?>