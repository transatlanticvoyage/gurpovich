<?php
/*
Plugin Name: Gurpovich Admin Injector
Description: Injects user-defined content into Elementor data JSON directly upon mapping save.
Version: 5.8
Author: Sake Nova
*/

if (!defined('ABSPATH')) exit;

// Add admin menu
add_action('admin_menu', 'gurpovich_injector_menu');
function gurpovich_injector_menu() {
    add_menu_page(
        'Gurpovich Admin Injector',
        'Gurpovich Admin Injector',
        'manage_options',
        'gurpovich-admin-injector',
        'gurpovich_injector_page',
        'dashicons-admin-generic',
        2
    );
}

// Render admin page and handle mapping save + JSON update
function gurpovich_injector_page() {
    echo '<div class="wrap"><h1>Gurpovich Admin Injector</h1><form method="post">';
    wp_nonce_field('gurpovich_injector_action','gurpovich_injector_nonce');
    echo '<p><label for="gurp_post_id">WP Post/Page ID:</label><br><input type="number" name="gurp_post_id" id="gurp_post_id" required style="width:100%;max-width:300px;"></p>';
    echo '<p><label for="gurp_content">Content Markup (use [key] lines to identify keys):</label><br><textarea name="gurp_content" id="gurp_content" rows="10" style="width:100%;"></textarea></p>';
    echo '<p><input type="submit" name="gurp_inject" class="button button-primary" value="Save & Update Elementor"></p>';
    echo '</form>';

    if (isset($_POST['gurp_inject'])) {
        if (!isset($_POST['gurpovich_injector_nonce']) || !wp_verify_nonce($_POST['gurpovich_injector_nonce'],'gurpovich_injector_action')) {
            wp_die('Security check failed');
        }
        $post_id = intval($_POST['gurp_post_id']);
        $raw = wp_unslash($_POST['gurp_content']);
        // Parse mappings
        $map = array();
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $key = '';
        foreach ($lines as $line) {
            if (preg_match('/^\[(.+?)\]$/', trim($line), $m)) {
                $key = $m[1];
                $map[$key] = '';
            } elseif ($key !== '') {
                $map[$key] .= ($map[$key] === '' ? '' : "\n") . $line;
            }
        }
        // Save mapping meta
        update_post_meta($post_id,'gurp_map',$map);
        echo '<div class="updated"><p>Mapping saved.</p></div>';

        // Fetch and update Elementor JSON data
        $data = get_post_meta($post_id,'_elementor_data',true);
        if ($data) {
            $elements = is_string($data) ? json_decode($data,true) : $data;
            if (is_array($elements)) {
                $new = gurpovich_process_elements($elements,$map);
                update_post_meta($post_id,'_elementor_data',$new);
                echo '<div class="updated"><p>Elementor data updated.</p></div>';
            } else {
                echo '<div class="error"><p>Could not decode Elementor data.</p></div>';
            }
        } else {
            echo '<div class="error"><p>No Elementor data found for that ID.</p></div>';
        }
    }

    // Show mapping
    echo '<h2>Current Mapping</h2>';
    if (!empty($_POST['gurp_post_id'])) {
        $existing = get_post_meta(intval($_POST['gurp_post_id']),'gurp_map',true);
        if (is_array($existing)) {
            echo '<pre>' . esc_html(print_r($existing,true)) . '</pre>';
        } else {
            echo '<p>No mapping.</p>';
        }
    }
    echo '</div>';
}

// Recursive JSON processing
function gurpovich_process_elements($elements,$map) {
    foreach ($elements as &$el) {
        if (isset($el['settings']) && is_array($el['settings'])) {
            foreach ($el['settings'] as $skey => $sval) {
                if (is_string($sval)) {
                    foreach ($map as $key => $val) {
                        if (strpos($sval,$key)!==false) {
                            $el['settings'][$skey] = str_replace($key,$val,$sval);
                        }
                    }
                }
            }
        }
        if (isset($el['elements']) && is_array($el['elements'])) {
            $el['elements'] = gurpovich_process_elements($el['elements'],$map);
        }
    }
    return $elements;
}
?>