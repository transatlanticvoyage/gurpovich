<?php
/**
 * Plugin Name: Gurpovich Cleanup
 * Description: Cleanup script for Gurpovich plugin
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

// Add cleanup menu
function gurpovich_cleanup_menu() {
    add_menu_page(
        'Gurpovich Cleanup',
        'Gurpovich Cleanup',
        'administrator',
        'gurpovich-cleanup',
        'gurpovich_cleanup_page',
        'dashicons-admin-tools',
        999
    );
}
add_action('admin_menu', 'gurpovich_cleanup_menu');

// Cleanup page
function gurpovich_cleanup_page() {
    if (isset($_POST['cleanup']) && check_admin_referer('gurpovich_cleanup')) {
        global $wpdb;
        
        // Delete plugin options
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'gurpovich%'");
        
        // Delete plugin tables
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}gurpo_pageideas");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}gurpo_driggs");
        
        echo '<div class="notice notice-success"><p>Cleanup completed.</p></div>';
    }
    
    ?>
    <div class="wrap">
        <h1>Gurpovich Cleanup</h1>
        <form method="post">
            <?php wp_nonce_field('gurpovich_cleanup'); ?>
            <p>This will remove all Gurpovich plugin data from the database.</p>
            <input type="submit" name="cleanup" class="button button-primary" value="Cleanup Database" onclick="return confirm('Are you sure? This cannot be undone.');">
        </form>
    </div>
    <?php
} 