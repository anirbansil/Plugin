<?php
/**
 * Plugin Name: Frontend Form Handler
 * Description: A custom plugin to handle frontend form submissions and store data in the database.
 * Version: 1.0
 * Author: Your Name
 */

// Ensure the file is not accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Hook to create database table upon plugin activation
register_activation_hook(__FILE__, 'ffh_create_table');

function ffh_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submissions';

    // SQL to create the table
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(20) NOT NULL,
        message text NOT NULL,
        example_select varchar(255) NOT NULL,
        terms tinyint(1) NOT NULL,
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Hook to register shortcode
add_action('init', 'ffh_register_shortcode');

function ffh_register_shortcode() {
    add_shortcode('frontend_form', 'ffh_render_form');
}

// Function to render the form
function ffh_render_form() {
    ob_start();
    ?>
    <form id="ffh-form" method="post">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Enter your message" required></textarea>
        </div>
        <div class="form-group">
            <label for="exampleSelect">Select Option</label>
            <select class="form-control" id="exampleSelect" name="exampleSelect" required>
                <option value="">Choose...</option>
                <option value="Option 1">Option 1</option>
                <option value="Option 2">Option 2</option>
                <option value="Option 3">Option 3</option>
            </select>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
            <label class="form-check-label" for="terms">I agree to the terms and conditions</label>
        </div>
        <button type="submit" class="btn btn-primary mt-3" id="submit-form">Submit</button>
        <div id="form-response"></div>
    </form>
    <?php
    ffh_enqueue_scripts(); // Ensure scripts are enqueued for the form
    return ob_get_clean();
}

// Handle AJAX form submission
add_action('wp_ajax_ffh_form_submit', 'ffh_handle_form_submission');
add_action('wp_ajax_nopriv_ffh_form_submit', 'ffh_handle_form_submission');

function ffh_handle_form_submission() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'form_submissions';

    // Check nonce for security
    check_ajax_referer('ffh_form_nonce', 'security');

    // Sanitize and validate inputs
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $message = sanitize_textarea_field($_POST['message']);
    $example_select = sanitize_text_field($_POST['exampleSelect']);
    $terms = isset($_POST['terms']) ? 1 : 0;

    // Insert data into the database
    $result = $wpdb->insert(
        $table_name,
        array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'example_select' => $example_select,
            'terms' => $terms
        )
    );

    // Return response
    if ($result !== false) {
        wp_send_json_success('Thank you for your submission!');
    } else {
        wp_send_json_error('An error occurred, please try again.');
    }
}

function ffh_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('ffh-ajax-script', plugin_dir_url(__FILE__) . 'js/ffh-ajax.js', array('jquery'), null, true);

    wp_localize_script('ffh-ajax-script', 'ffh_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ffh_form_nonce')
    ));
}

?>
