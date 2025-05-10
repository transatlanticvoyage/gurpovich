<?php
/**
 * Plugin Name: Test Admin Access
 * Description: A minimal test plugin to verify admin access
 * Version: 1.0
 * Author: Test
 */

if (!defined('ABSPATH')) exit;

// Add a test menu item
function test_admin_menu() {
    add_menu_page(
        'Test Admin',
        'Test Admin',
        'read',
        'test-admin',
        'test_admin_page',
        'dashicons-admin-generic',
        1
    );
}
add_action('admin_menu', 'test_admin_menu');

// Test page content
function test_admin_page() {
    ?>
    <div class="wrap">
        <h1>Test Admin Page</h1>
        <p>Current user ID: <?php echo get_current_user_id(); ?></p>
        <p>User roles: <?php echo implode(', ', wp_get_current_user()->roles); ?></p>
        <p>Can read: <?php echo current_user_can('read') ? 'yes' : 'no'; ?></p>
        <p>Can manage_options: <?php echo current_user_can('manage_options') ? 'yes' : 'no'; ?></p>
        <p>Is admin: <?php echo current_user_can('administrator') ? 'yes' : 'no'; ?></p>
    </div>
    <?php
}

// Add activation hook
register_activation_hook(__FILE__, 'test_admin_activate');
function test_admin_activate() {
    // Create a test option
    add_option('test_admin_activated', 'yes');
}

// Add deactivation hook
register_deactivation_hook(__FILE__, 'test_admin_deactivate');
function test_admin_deactivate() {
    // Remove the test option
    delete_option('test_admin_activated');
} 