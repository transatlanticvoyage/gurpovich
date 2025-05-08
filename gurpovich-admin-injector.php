<?php
// No-op test commit for learning the deploy process
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
    // Main menu item
    add_menu_page(
        'Gurpovich 1',
        'Gurpovich 1',
        'manage_options',
        'gurposcreen1',
        'gurpovich_injector_page',
        'dashicons-admin-generic',
        2
    );

    // Submenu items
    add_submenu_page(
        'gurposcreen1',
        'Gurpovich 2',
        'Gurpovich 2',
        'manage_options',
        'gurposcreen2',
        'gurpovich_injector2_page'
    );

    add_submenu_page(
        'gurposcreen1',
        'Gurpovich 3',
        'Gurpovich 3',
        'manage_options',
        'gurposcreen3',
        'gurpo_screen3_page'
    );

    add_submenu_page(
        'gurposcreen1',
        'Gurpovich 4',
        'Gurpovich 4',
        'manage_options',
        'gurposcreen4',
        'gurpo_screen4_page'
    );

    add_submenu_page(
        'gurposcreen1',
        'Gurpovich 5',
        'Gurpovich 5',
        'manage_options',
        'gurposcreen5',
        'gurpo_screen5_page'
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
                echo '<div class="updated"><p>Elementor data updated. 1</p></div>';
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
    
    // Service Pages Section
    echo '<h2>Service Pages</h2>';
    echo '<div class="service-pages-section">';
    // Content will be added here later
    echo '</div>';
    
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

// Add a new admin menu page for the new interface
add_action('admin_menu', function() {
    add_menu_page(
        'Gurpovich Injector 2',
        'Gurpovich Injector 2',
        'manage_options',
        'gurposcreen2',
        'gurpovich_injector2_page',
        'dashicons-admin-generic',
        3 // Position after the original
    );
});

// Callback for the new admin page
function gurpovich_injector2_page() {
    echo '<h1>Gurpovich Injector - Main Screen 1</h1>';
    echo '<h2>Pages To Deal With:</h2>';
    echo '<table class="widefat fixed" style="width:auto; min-width:900px;">';
    echo '<thead>
        <tr>
            <th>Page</th>
            <th colspan="2" style="text-align:center;">Select a page</th>
            <th>Use assigned default</th>
            <th>wp-post-id</th>
            <th></th>
        </tr>
    </thead>
    <tbody>';

    $pages = [
        ['Home', 306],
        ['Services', 208],
        ['About', 188],
        ['Contact', 234],
        ['Privacy Policy', 126],
        ['Modern Slavery', 260],
    ];

    foreach ($pages as $page) {
        list($label, $post_id) = $page;
        echo '<tr>
            <td><strong>' . esc_html($label) . '</strong></td>
            <td>
                <select name="select_' . esc_attr(strtolower(str_replace(' ', '_', $label))) . '">
                    <option value="">select a page</option>
                    <!-- Populate with WP pages if needed -->
                </select>
            </td>
            <td style="text-align:center; color:#0073aa; font-weight:bold;">OR</td>
            <td>Use assigned default</td>
            <td>' . esc_html($post_id) . '</td>
            <td>
                <button class="button button-primary" style="background:#21759b; border-color:#21759b;">Save & Update Elementor</button>
            </td>
        </tr>';
    }

    echo '</tbody></table>';
}

// Add three new admin menu pages
add_action('admin_menu', function() {
    add_menu_page(
        'Gurpo Screen 3',
        'Gurpo Screen 3',
        'manage_options',
        'gurposcreen3',
        'gurpo_screen3_page',
        'dashicons-admin-generic',
        4
    );

    add_menu_page(
        'Gurpo Screen 4',
        'Gurpo Screen 4',
        'manage_options',
        'gurposcreen4',
        'gurpo_screen4_page',
        'dashicons-admin-generic',
        5
    );

    add_menu_page(
        'Gurpo Screen 5',
        'Gurpo Screen 5',
        'manage_options',
        'gurposcreen5',
        'gurpo_screen5_page',
        'dashicons-admin-generic',
        6
    );
});

// Callback functions for the new pages
function gurpo_screen3_page() {
    echo '<div class="wrap"><h1>Gurpo Screen 3</h1></div>';
}

function gurpo_screen4_page() {
    echo '<div class="wrap"><h1>Gurpo Screen 4</h1></div>';
}

function gurpo_screen5_page() {
    echo '<div class="wrap"><h1>Gurpo Screen 5</h1></div>';
}
?>
