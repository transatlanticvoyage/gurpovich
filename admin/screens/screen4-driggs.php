<?php
namespace Gurpovich\Admin\Screens;

if (!defined('ABSPATH')) exit;

class Screen4_Driggs {
    public function render() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gurpo_driggs';
        
        echo '<div class="wrap">';
        echo '<div style="font-weight:bold; font-size:1.2em; margin-bottom:10px;">Screen 4 - Driggs</div>';
        echo '<h1>Driggs Information Manager</h1>';

        // Handle form submission
        if (isset($_POST['submit_driggs'])) {
            if (!isset($_POST['gurpovich_driggs_nonce']) || !wp_verify_nonce($_POST['gurpovich_driggs_nonce'], 'gurpovich_driggs_action')) {
                wp_die('Security check failed');
            }

            $data = array(
                'driggs_domain' => sanitize_text_field($_POST['driggs_domain']),
                'driggs_industry' => sanitize_text_field($_POST['driggs_industry']),
                'driggs_city' => sanitize_text_field($_POST['driggs_city']),
                'driggs_brand_name_1' => sanitize_text_field($_POST['driggs_brand_name_1']),
                'driggs_site_type_or_purpose' => sanitize_textarea_field($_POST['driggs_site_type_or_purpose']),
                'driggs_email_1' => sanitize_email($_POST['driggs_email_1']),
                'driggs_address_1' => sanitize_text_field($_POST['driggs_address_1']),
                'driggs_phone1' => sanitize_text_field($_POST['driggs_phone1'])
            );

            $wpdb->insert($table_name, $data);
            echo '<div class="updated"><p>Driggs information added successfully.</p></div>';
        }

        // Display form
        echo '<form method="post" class="gurpovich-form">';
        wp_nonce_field('gurpovich_driggs_action', 'gurpovich_driggs_nonce');
        echo '<p><label for="driggs_domain">Domain:</label><br>';
        echo '<input type="text" name="driggs_domain" id="driggs_domain" required style="width:100%;max-width:300px;"></p>';
        echo '<p><label for="driggs_industry">Industry:</label><br>';
        echo '<input type="text" name="driggs_industry" id="driggs_industry" required style="width:100%;max-width:300px;"></p>';
        echo '<p><label for="driggs_city">City:</label><br>';
        echo '<input type="text" name="driggs_city" id="driggs_city" required style="width:100%;max-width:300px;"></p>';
        echo '<p><label for="driggs_brand_name_1">Brand Name:</label><br>';
        echo '<input type="text" name="driggs_brand_name_1" id="driggs_brand_name_1" required style="width:100%;max-width:300px;"></p>';
        echo '<p><label for="driggs_site_type_or_purpose">Site Type/Purpose:</label><br>';
        echo '<textarea name="driggs_site_type_or_purpose" id="driggs_site_type_or_purpose" rows="4" style="width:100%;max-width:300px;"></textarea></p>';
        echo '<p><label for="driggs_email_1">Email:</label><br>';
        echo '<input type="email" name="driggs_email_1" id="driggs_email_1" required style="width:100%;max-width:300px;"></p>';
        echo '<p><label for="driggs_address_1">Address:</label><br>';
        echo '<input type="text" name="driggs_address_1" id="driggs_address_1" required style="width:100%;max-width:300px;"></p>';
        echo '<p><label for="driggs_phone1">Phone:</label><br>';
        echo '<input type="text" name="driggs_phone1" id="driggs_phone1" required style="width:100%;max-width:300px;"></p>';
        echo '<p><input type="submit" name="submit_driggs" class="button button-primary" value="Add Driggs Information"></p>';
        echo '</form>';

        // Display existing Driggs information
        $driggs_info = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC");
        if ($driggs_info) {
            echo '<h2>Existing Driggs Information</h2>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>Domain</th><th>Industry</th><th>City</th><th>Brand Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead>';
            echo '<tbody>';
            foreach ($driggs_info as $info) {
                echo '<tr>';
                echo '<td>' . esc_html($info->driggs_domain) . '</td>';
                echo '<td>' . esc_html($info->driggs_industry) . '</td>';
                echo '<td>' . esc_html($info->driggs_city) . '</td>';
                echo '<td>' . esc_html($info->driggs_brand_name_1) . '</td>';
                echo '<td>' . esc_html($info->driggs_email_1) . '</td>';
                echo '<td>' . esc_html($info->driggs_phone1) . '</td>';
                echo '<td><a href="?page=gurposcreen4&action=edit&id=' . esc_attr($info->id) . '" class="button button-small">Edit</a> ';
                echo '<a href="?page=gurposcreen4&action=delete&id=' . esc_attr($info->id) . '" class="button button-small" onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }

        echo '</div>';
    }
}

// Initialize and render the screen
$screen = new Screen4_Driggs();
$screen->render(); 