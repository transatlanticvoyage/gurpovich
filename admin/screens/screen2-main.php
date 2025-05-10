<?php
namespace Gurpovich\Admin\Screens;

if (!defined('ABSPATH')) exit;

class Screen2_Main {
    public function render() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gurpo_pageideas';
        
        echo '<div class="wrap">';
        echo '<div style="font-weight:bold; font-size:1.2em; margin-bottom:10px;">Screen 2</div>';
        echo '<h1>Page Ideas Manager</h1>';

        // Handle form submission
        if (isset($_POST['submit_page_idea'])) {
            if (!isset($_POST['gurpovich_page_idea_nonce']) || !wp_verify_nonce($_POST['gurpovich_page_idea_nonce'], 'gurpovich_page_idea_action')) {
                wp_die('Security check failed');
            }

            $order = intval($_POST['order']);
            $name = sanitize_text_field($_POST['name']);
            $post_id = !empty($_POST['post_id']) ? intval($_POST['post_id']) : null;

            $wpdb->insert(
                $table_name,
                array(
                    'order_for_display_on_interface_1' => $order,
                    'name' => $name,
                    'rel_wp_post_id_1' => $post_id
                ),
                array('%d', '%s', '%d')
            );

            echo '<div class="updated"><p>Page idea added successfully.</p></div>';
        }

        // Display form
        echo '<form method="post" class="gurpovich-form">';
        wp_nonce_field('gurpovich_page_idea_action', 'gurpovich_page_idea_nonce');
        echo '<p><label for="order">Order:</label><br>';
        echo '<input type="number" name="order" id="order" required style="width:100%;max-width:300px;"></p>';
        echo '<p><label for="name">Name:</label><br>';
        echo '<input type="text" name="name" id="name" required style="width:100%;max-width:300px;"></p>';
        echo '<p><label for="post_id">Related Post ID (optional):</label><br>';
        echo '<input type="number" name="post_id" id="post_id" style="width:100%;max-width:300px;"></p>';
        echo '<p><input type="submit" name="submit_page_idea" class="button button-primary" value="Add Page Idea"></p>';
        echo '</form>';

        // Display existing page ideas
        $page_ideas = $wpdb->get_results("SELECT * FROM $table_name ORDER BY order_for_display_on_interface_1 ASC");
        if ($page_ideas) {
            echo '<h2>Existing Page Ideas</h2>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Order</th><th>Name</th><th>Related Post ID</th><th>Actions</th></tr></thead>';
            echo '<tbody>';
            foreach ($page_ideas as $idea) {
                echo '<tr>';
                echo '<td>' . esc_html($idea->order_for_display_on_interface_1) . '</td>';
                echo '<td>' . esc_html($idea->name) . '</td>';
                echo '<td>' . ($idea->rel_wp_post_id_1 ? esc_html($idea->rel_wp_post_id_1) : 'None') . '</td>';
                echo '<td><a href="?page=gurposcreen2&action=edit&id=' . esc_attr($idea->id) . '" class="button button-small">Edit</a> ';
                echo '<a href="?page=gurposcreen2&action=delete&id=' . esc_attr($idea->id) . '" class="button button-small" onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }

        echo '</div>';
    }
}

// Initialize and render the screen
$screen = new Screen2_Main();
$screen->render(); 