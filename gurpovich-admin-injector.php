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

    add_submenu_page(
        'gurposcreen1',
        'Gurpovich 6',
        'Gurpovich 6',
        'manage_options',
        'gurposcreen6',
        'gurpo_screen6_page'
    );

    add_submenu_page(
        'gurposcreen1',
        'Fillernar 1',
        'Fillernar 1',
        'manage_options',
        'gurpofillernar1',
        'gurpo_fillernar1_page'
    );

    add_submenu_page(
        'gurposcreen1',
        'DB Table Viewer gurpo_pageideas',
        'DB Table Viewer gurpo_pageideas',
        'manage_options',
        'gurpo-db-viewer',
        'gurpo_db_viewer_page'
    );
}

// Render admin page and handle mapping save + JSON update
function gurpovich_injector_page() {
    // Suppress all admin notices except our own on this page
    add_action('admin_print_scripts', function() {
        echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }</style>';
    }, 1);
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

// Callback for the new admin page
function gurpovich_injector2_page() {
    // Suppress all admin notices except our own on this page
    add_action('admin_print_scripts', function() {
        echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }</style>';
    }, 1);
    echo '<h1>Gurpovich Injector - Main Screen 1</h1>';
    
    // Add JavaScript for confirmation
    echo '<script type="text/javascript">
        function confirmClear() {
            return confirm("Are you sure you want to clear all Post IDs? This action cannot be undone.");
        }
    </script>';
    
    // Kardwaj Key Connector Section
    echo '<h2>Kardwaj Key Connector of Post ID</h2>';
    echo '<form method="post">';
    wp_nonce_field('kardwaj_action', 'kardwaj_nonce');
    echo '<p><input type="text" name="kardwaj_keys" id="kardwaj_keys" placeholder="Paste your key here: 484, 28, 05, 49, 402, 204" style="width:100%;max-width:500px;"></p>';
    echo '<p>';
    echo '<input type="submit" name="kardwaj_submit" class="button button-primary" value="Insert Post IDs Into Pageideas">';
    echo '<input type="submit" name="kardwaj_clear" class="button" value="Clear Post IDs From Pageideas" style="background-color: #800000; color: white; margin-left: 10px;" onclick="return confirmClear();">';
    echo '</p>';
    echo '</form>';

    // Process Kardwaj submission
    if (isset($_POST['kardwaj_submit']) && check_admin_referer('kardwaj_action', 'kardwaj_nonce')) {
        $keys = trim($_POST['kardwaj_keys']);
        if (!empty($keys)) {
            // Split the input into an array of numbers
            $key_array = array_map('trim', explode(',', $keys));
            
            global $wpdb;
            $table_name = $wpdb->prefix . 'gurpo_pageideas';
            
            // Get all pageideas ordered by order_for_display_on_interface_1
            $pageideas = $wpdb->get_results("SELECT * FROM $table_name ORDER BY order_for_display_on_interface_1 ASC");
            
            // Update each pageidea with its corresponding key
            foreach ($pageideas as $index => $pageidea) {
                if (isset($key_array[$index])) {
                    $wpdb->update(
                        $table_name,
                        array('rel_wp_post_id_1' => $key_array[$index]),
                        array('id' => $pageidea->id)
                    );
                }
            }
            
            echo '<div class="updated"><p>Post IDs have been updated successfully.</p></div>';
        }
    }

    // Handle clear button
    if (isset($_POST['kardwaj_clear']) && check_admin_referer('kardwaj_action', 'kardwaj_nonce')) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gurpo_pageideas';
        
        // Clear all rel_wp_post_id_1 values using a direct SQL query
        $result = $wpdb->query("UPDATE $table_name SET rel_wp_post_id_1 = NULL");
        
        if ($result !== false) {
            echo '<div class="updated"><p>All Post IDs have been cleared.</p></div>';
            // Force a page refresh to show the cleared values
            echo '<script type="text/javascript">window.location.reload();</script>';
        } else {
            echo '<div class="error"><p>Error clearing Post IDs. Please try again.</p></div>';
        }
    }

    // Pageideas Section
    echo '<h2>Pageideas</h2>';
    echo '<style>
        .gurpo-table th, .gurpo-table td {
            vertical-align: middle !important;
            padding: 8px 6px;
        }
        .gurpo-table th {
            background: #f5f5f5;
        }
        .gurpo-table textarea {
            min-width: 220px;
            max-width: 350px;
            width: 100%;
        }
        .gurpo-table select {
            min-width: 140px;
            max-width: 140px;
            width: 140px;
        }
        .gurpo-table .vertical-sep {
            border-right: 2px solid #000000;
        }
        .gurpo-table .or-col {
            background: #000; color: #fff; font-weight: bold; text-align: center;
            min-width: 30px;
            max-width: 30px;
            width: 30px;
        }
        .gurpo-table .radio-col {
            width: 28px; min-width: 28px; max-width: 28px; text-align: center; padding-left: 2px; padding-right: 2px;
        }
        .gurpo-table .button-col {
            min-width: 170px;
        }
        .gurpo-table .temprex-txt {
            width: 70px;
            min-width: 70px;
            max-width: 70px;
            display: inline-block;
        }
        .gurpo-table .temprex-refresh-btn {
            background: #000;
            color: #fff;
            border: none;
            border-radius: 3px;
            font-size: 18px;
            width: 28px;
            height: 28px;
            margin-left: 4px;
            cursor: pointer;
            vertical-align: middle;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>';
    echo '<table class="widefat fixed gurpo-table" style="width:100%; min-width:1100px;">';
    echo '<thead>
        <tr>
            <th class="vertical-sep" style="background: #f5f5f5;"></th>
            <th colspan="8" style="background: #b6f5b6; color: #222; font-weight: bold; text-transform: lowercase; text-align: center; font-size: 1.1em;">choose a target page for injection</th>
            <th style="background: #f5f5f5;"></th>
        </tr>
        <tr>
            <th class="vertical-sep" style="width:120px; font-weight:bold;">pageidea</th>
            <th class="radio-col" style="font-weight:bold;"> </th>
            <th style="min-width:200px; font-weight:bold;">select a page</th>
            <th class="vertical-sep or-col" style="font-weight:bold;">OR</th>
            <th class="radio-col" style="font-weight:bold;"> </th>
            <th style="min-width:120px; font-weight:bold;">use assigned default</th>
            <th class="vertical-sep" style="min-width:90px; font-weight:bold;">rel_wp_post_id_1</th>
            <th class="vertical-sep" style="min-width:120px; font-weight:bold;">temprex_of_shortcodes</th>
            <th style="min-width:120px; font-weight:bold;">zeeprex_submit</th>
            <th style="min-width:220px; font-weight:bold;">prexnar1</th>
            <th class="button-col" style="font-weight:bold;"></th>
        </tr>
    </thead>
    <tbody>';

    global $wpdb;
    $table_name = $wpdb->prefix . 'gurpo_pageideas';
    
    // Get all pageideas ordered by order_for_display_on_interface_1
    $pageideas = $wpdb->get_results("
        SELECT * 
        FROM $table_name 
        ORDER BY order_for_display_on_interface_1 ASC
    ");

    // Get all published pages
    $args = array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    $pages = get_posts($args);

    if ($pageideas) {
        foreach ($pageideas as $pageidea) {
            // Get the temprex_of_shortcodes content if a post ID exists
            $temprex_content = '';
            if (!empty($pageidea->rel_wp_post_id_1)) {
                $temprex_content = get_post_meta($pageidea->rel_wp_post_id_1, 'gurpo_temprex_of_shortcodes', true);
            }

            echo '<tr>
                <td class="vertical-sep"><strong>' . esc_html($pageidea->name) . '</strong></td>
                <td class="radio-col">
                    <input type="radio" name="selection_type_' . esc_attr($pageidea->id) . '" value="custom" style="width: 20px; height: 20px;">
                </td>
                <td>
                    <select name="select_' . esc_attr(strtolower(str_replace(' ', '_', $pageidea->name))) . '">
                        <option value="">select a page</option>';
                        foreach ($pages as $page) {
                            $selected = ($page->ID == $pageidea->rel_wp_post_id_1) ? 'selected' : '';
                            echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>[' . esc_html($page->ID) . '] ' . esc_html($page->post_title) . '</option>';
                        }
                    echo '</select>
                </td>
                <td class="vertical-sep or-col">OR</td>
                <td class="radio-col">
                    <input type="radio" name="selection_type_' . esc_attr($pageidea->id) . '" value="default" style="width: 20px; height: 20px;">
                </td>
                <td>Use assigned default</td>
                <td class="vertical-sep">' .
                    '<input type="text" name="rel_wp_post_id_1_' . esc_attr($pageidea->id) . '" value="' . esc_attr($pageidea->rel_wp_post_id_1) . '" style="width:35px; text-align:center;" />'
                . '</td>
                <td class="vertical-sep">
                    <input type="text" class="temprex-txt" name="temprex_of_shortcodes_' . esc_attr($pageidea->id) . '" value="' . esc_attr($temprex_content) . '" />
                    <button type="button" class="temprex-refresh-btn" title="Refresh temprex_of_shortcodes">&#x21bb;</button>
                </td>
                <td><input type="text" name="zeeprex_submit_' . esc_attr($pageidea->id) . '" style="width:100%;" /></td>
                <td><textarea name="prexnar1_' . esc_attr($pageidea->id) . '" rows="4">' . esc_textarea(get_post_meta($pageidea->rel_wp_post_id_1, 'gurpo_prexnar1', true)) . '</textarea></td>
                <td class="button-col"><button class="button button-primary" style="background:#21759b; border-color:#21759b;">Save & Update Elementor</button></td>
            </tr>';
        }
    } else {
        echo '<tr><td colspan="9">No pageideas found in the database.</td></tr>';
    }
    echo '</tbody></table>';

    // Add JavaScript to handle radio button selection and prexnar1 content
    echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            const rows = document.querySelectorAll("tr");
            rows.forEach(row => {
                const radios = row.querySelectorAll("input[type=radio]");
                const select = row.querySelector("select");
                const textarea = row.querySelector("textarea");
                const refreshBtn = row.querySelector(".temprex-refresh-btn");
                const temprexInput = row.querySelector(".temprex-txt");
                
                if (radios && select) {
                    // Set initial state
                    if (select.value) {
                        radios[0].checked = true;
                    } else {
                        radios[1].checked = true;
                        if (textarea) textarea.disabled = true;
                    }
                    
                    // Handle radio changes
                    radios.forEach(radio => {
                        radio.addEventListener("change", function() {
                            if (this.value === "custom") {
                                select.disabled = false;
                                if (textarea) textarea.disabled = false;
                            } else {
                                select.disabled = true;
                                select.value = "";
                                if (textarea) {
                                    textarea.disabled = true;
                                    textarea.value = "";
                                }
                            }
                        });
                    });
                    
                    // Handle select changes
                    select.addEventListener("change", function() {
                        if (this.value) {
                            radios[0].checked = true;
                            if (textarea) textarea.disabled = false;
                        }
                    });
                }

                // Handle temprex refresh button
                if (refreshBtn && select && temprexInput) {
                    refreshBtn.addEventListener("click", function() {
                        var postId = select.value;
                        if (!postId) {
                            alert("Please select a page to scrape.");
                            return;
                        }
                        refreshBtn.disabled = true;
                        refreshBtn.innerHTML = "...";
                        var data = new FormData();
                        data.append("action", "gurpo_scrape_temprex");
                        data.append("post_id", postId);
                        fetch(ajaxurl, {
                            method: "POST",
                            credentials: "same-origin",
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                temprexInput.value = result.data.temprex;
                            } else {
                                alert("Error: " + result.data);
                            }
                        })
                        .catch(() => {
                            alert("AJAX error");
                        })
                        .finally(() => {
                            refreshBtn.disabled = false;
                            refreshBtn.innerHTML = "\u21bb";
                        });
                    });
                }
            });
        });
    </script>';

    // Service Pages Section
    echo '<h2>Service Pages</h2>';
    echo '<table class="widefat fixed" style="width:auto; min-width:900px;">';
    echo '<thead>
        <tr>
            <th style="color: #000000; font-weight: bold; text-transform: lowercase; border-right: 2px solid #000000;">pageidea</th>
            <th colspan="2" style="text-align:center; color: #000000; font-weight: bold; text-transform: lowercase;">select a page</th>
            <th style="color: #000000; font-weight: bold; text-transform: lowercase;">use assigned default</th>
            <th style="color: #000000; font-weight: bold; text-transform: lowercase;">rel_wp_post_id_1</th>
            <th style="color: #000000; font-weight: bold; text-transform: lowercase;"></th>
        </tr>
    </thead>
    <tbody>';

    $service_pages = [
        ['Service 1', ''],
        ['Service 2', ''],
        ['Service 3', ''],
        ['Service 4', ''],
        ['Service 5', '']
    ];

    foreach ($service_pages as $page) {
        list($label, $post_id) = $page;
        // Get the current post ID from the database
        $current_post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT rel_wp_post_id_1 FROM $table_name WHERE name = %s",
            $label
        ));
        
        echo '<tr>
            <td style="border-right: 2px solid #000000;"><strong>' . esc_html($label) . '</strong></td>
            <td>
                <select name="select_' . esc_attr(strtolower(str_replace(' ', '_', $label))) . '">
                    <option value="">select a page</option>
                    <!-- Populate with WP pages if needed -->
                </select>
            </td>
            <td style="text-align:center; background-color: #000000; color: #ffffff; font-weight:bold;">OR</td>
            <td>Use assigned default</td>
            <td>' . esc_html($current_post_id) . '</td>
            <td>
                <button class="button button-primary" style="background:#21759b; border-color:#21759b;">Save & Update Elementor</button>
            </td>
        </tr>';
    }
    echo '</tbody></table>';

    // Blog Posts Section
    echo '<h2>Blog Posts</h2>';
    echo '<table class="widefat fixed" style="width:auto; min-width:900px;">';
    echo '<thead>
        <tr>
            <th style="color: #000000; font-weight: bold; text-transform: lowercase; border-right: 2px solid #000000;">pageidea</th>
            <th colspan="2" style="text-align:center; color: #000000; font-weight: bold; text-transform: lowercase;">select a page</th>
            <th style="color: #000000; font-weight: bold; text-transform: lowercase;">use assigned default</th>
            <th style="color: #000000; font-weight: bold; text-transform: lowercase;">rel_wp_post_id_1</th>
            <th style="color: #000000; font-weight: bold; text-transform: lowercase;"></th>
        </tr>
    </thead>
    <tbody>';

    $blog_posts = [
        ['Blog Post 1', ''],
        ['Blog Post 2', ''],
        ['Blog Post 3', ''],
        ['Blog Post 4', ''],
        ['Blog Post 5', '']
    ];

    foreach ($blog_posts as $page) {
        list($label, $post_id) = $page;
        // Get the current post ID from the database
        $current_post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT rel_wp_post_id_1 FROM $table_name WHERE name = %s",
            $label
        ));
        
        echo '<tr>
            <td style="border-right: 2px solid #000000;"><strong>' . esc_html($label) . '</strong></td>
            <td>
                <select name="select_' . esc_attr(strtolower(str_replace(' ', '_', $label))) . '">
                    <option value="">select a page</option>
                    <!-- Populate with WP pages if needed -->
                </select>
            </td>
            <td style="text-align:center; background-color: #000000; color: #ffffff; font-weight:bold;">OR</td>
            <td>Use assigned default</td>
            <td>' . esc_html($current_post_id) . '</td>
            <td>
                <button class="button button-primary" style="background:#21759b; border-color:#21759b;">Save & Update Elementor</button>
            </td>
        </tr>';
    }
    echo '</tbody></table>';
}

// Callback functions for the new pages
function gurpo_screen3_page() {
    // Suppress all admin notices except our own on this page
    add_action('admin_print_scripts', function() {
        echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }</style>';
    }, 1);
    echo '<div class="wrap">';
    echo '<h1>balarfi</h1>';
    
    // Get all published pages
    $pages = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
    ]);
    
    // Get selected page ID from GET or default to first page
    $selected_page_id = isset($_GET['balarfi_page_id']) ? intval($_GET['balarfi_page_id']) : (isset($pages[0]) ? $pages[0]->ID : 0);
    
    // Get meta values for selected page
    $temprex = $selected_page_id ? get_post_meta($selected_page_id, 'gurpo_temprex_of_shortcodes', true) : '';
    $prexnar1 = $selected_page_id ? get_post_meta($selected_page_id, 'gurpo_prexnar1', true) : '';
    
    echo '<form method="get" id="balarfi-form">';
    echo '<input type="hidden" name="page" value="gurposcreen3" />';
    echo '<table class="form-table"><tbody>';
    echo '<tr><th><label for="balarfi_page_id">Select a page</label></th><td>';
    echo '<select name="balarfi_page_id" id="balarfi_page_id" onchange="document.getElementById(\'balarfi-form\').submit();">';
    foreach ($pages as $page) {
        $selected = $selected_page_id == $page->ID ? 'selected' : '';
        echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>[' . esc_html($page->ID) . '] ' . esc_html($page->post_title) . '</option>';
    }
    echo '</select>';
    echo '</td></tr>';
    
    echo '<tr><th><label for="temprex_of_shortcodes">temprex_of_shortcodes</label></th><td>';
    echo '<input type="text" id="temprex_of_shortcodes" name="temprex_of_shortcodes" value="' . esc_attr($temprex) . '" style="width: 400px;" readonly />';
    echo '</td></tr>';
    
    echo '<tr><th><label for="zeeprex_submit">zeeprex_submit</label></th><td>';
    echo '<input type="text" id="zeeprex_submit" name="zeeprex_submit" value="" style="width: 400px;" readonly />';
    echo '</td></tr>';
    
    echo '<tr><th><label for="prexnar1">prexnar1</label></th><td>';
    echo '<input type="text" id="prexnar1" name="prexnar1" value="' . esc_attr($prexnar1) . '" style="width: 400px;" readonly />';
    echo '</td></tr>';
    
    echo '</tbody></table>';
    echo '</form>';
    echo '</div>';
}

function gurpo_screen4_page() {
    // Suppress all admin notices except our own on this page
    add_action('admin_print_scripts', function() {
        echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }</style>';
    }, 1);
    echo '<div class="wrap"><h1>Gurpo Screen 4</h1></div>';
}

function gurpo_screen5_page() {
    // Suppress all admin notices except our own on this page
    add_action('admin_print_scripts', function() {
        echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }</style>';
    }, 1);
    echo '<div class="wrap"><h1>Gurpo Screen 5</h1></div>';
}

function gurpo_screen6_page() {
    // Suppress all admin notices except our own on this page
    add_action('admin_print_scripts', function() {
        echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }</style>';
    }, 1);
    echo '<div class="wrap"><h1>Gurpo Screen 6</h1></div>';
}

function gurpo_fillernar1_page() {
    // Suppress all admin notices except our own on this page
    add_action('admin_print_scripts', function() {
        echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }</style>';
    }, 1);
    echo '<div class="wrap">';
    echo '<h1>Fillernar - Special Custom Filler Content Update System</h1>';
    echo '<h2>Fillernar 1</h2>';
    echo '<div id="fillernar-content">';

    // Get and display the current fillernar content
    $fillernar_content = get_option('gurpo_fillernar1_content', array());
    foreach ($fillernar_content as $item) {
        echo '<div class="fillernar-number">' . esc_html($item) . '</div>';
    }

    echo '</div>';
    echo '</div>';
}

// Function to add a new number to Fillernar content
function add_new_fillernar_number() {
    $fillernar_content = get_option('gurpo_fillernar1_content', array());
    $fillernar_content[] = count($fillernar_content) + 1;
    update_option('gurpo_fillernar1_content', $fillernar_content);
    return true;
}

// Create database table and insert default data on plugin activation
register_activation_hook(__FILE__, 'gurpovich_plugin_activation');
function gurpovich_plugin_activation() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gurpo_pageideas';
    $charset_collate = $wpdb->get_charset_collate();

    // Create the table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        order_for_display_on_interface_1 int(11) NOT NULL,
        name varchar(255) NOT NULL,
        rel_wp_post_id_1 bigint(20) DEFAULT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Insert default data if table is empty
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    if ($count == 0) {
        $default_data = array(
            array('order_for_display_on_interface_1' => 1, 'name' => 'Home', 'rel_wp_post_id_1' => NULL),
            array('order_for_display_on_interface_1' => 2, 'name' => 'Services', 'rel_wp_post_id_1' => NULL),
            array('order_for_display_on_interface_1' => 3, 'name' => 'About', 'rel_wp_post_id_1' => NULL),
            array('order_for_display_on_interface_1' => 4, 'name' => 'Contact', 'rel_wp_post_id_1' => NULL),
            array('order_for_display_on_interface_1' => 5, 'name' => 'Privacy Policy', 'rel_wp_post_id_1' => NULL),
            array('order_for_display_on_interface_1' => 6, 'name' => 'Modern Slavery', 'rel_wp_post_id_1' => NULL)
        );

        foreach ($default_data as $row) {
            $wpdb->insert($table_name, $row);
        }
    }
}

// Clean up on plugin deactivation (optional - remove if you want to keep the data)
register_deactivation_hook(__FILE__, 'gurpovich_plugin_deactivation');
function gurpovich_plugin_deactivation() {
    // Uncomment the following line if you want to remove the table on deactivation
    // global $wpdb;
    // $table_name = $wpdb->prefix . 'gurpo_pageideas';
    // $wpdb->query("DROP TABLE IF EXISTS $table_name");
}

// Database table viewer page
function gurpo_db_viewer_page() {
    // Suppress all admin notices except our own on this page
    add_action('admin_print_scripts', function() {
        echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }</style>';
    }, 1);
    global $wpdb;
    $table_name = $wpdb->prefix . 'gurpo_pageideas';
    
    echo '<div class="wrap">';
    echo '<h1>DB Table Viewer gurpo_pageideas</h1>';
    
    // Get all records from the table
    $records = $wpdb->get_results("SELECT * FROM $table_name ORDER BY order_for_display_on_interface_1 ASC");
    
    if ($records) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="color: #000000; font-weight: bold;">id</th>';
        echo '<th style="color: #000000; font-weight: bold;">order_for_display_on_interface_1</th>';
        echo '<th style="color: #000000; font-weight: bold;">name</th>';
        echo '<th style="color: #000000; font-weight: bold;">rel_wp_post_id_1</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($records as $record) {
            echo '<tr>';
            echo '<td>' . esc_html($record->id) . '</td>';
            echo '<td>' . esc_html($record->order_for_display_on_interface_1) . '</td>';
            echo '<td>' . esc_html($record->name) . '</td>';
            echo '<td>' . esc_html($record->rel_wp_post_id_1) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No records found in the database table.</p>';
    }
    
    echo '</div>';
}

/**
 * Fetches the Elementor JSON data of a given post/page, extracts all [g_...] shortcodes and g_... keys/values in order,
 * and stores them (one per line) in the custom field gurpo_temprex_of_shortcodes for that post/page.
 *
 * @param int $post_id The ID of the post/page to scrape.
 * @return bool|string True on success, error message on failure.
 */
function scrape_temprex_from_existing_page($post_id) {
    if (empty($post_id) || !get_post($post_id)) {
        return 'Invalid post ID.';
    }
    $elementor_data = get_post_meta($post_id, '_elementor_data', true);
    if (empty($elementor_data)) {
        return 'No Elementor data found.';
    }
    $elements = is_string($elementor_data) ? json_decode($elementor_data, true) : $elementor_data;
    if (!is_array($elements)) {
        return 'Could not decode Elementor data.';
    }
    $matches = array();
    // Recursive function to search for g_... patterns
    $find_g_patterns = function($data) use (&$find_g_patterns, &$matches) {
        if (is_array($data)) {
            foreach ($data as $value) {
                $find_g_patterns($value);
            }
        } elseif (is_string($data)) {
            // Match [g_...] shortcodes
            if (preg_match_all('/\[g_[a-zA-Z0-9_]+\]/', $data, $m1)) {
                foreach ($m1[0] as $shortcode) {
                    $matches[] = $shortcode;
                }
            }
            // Match g_... words (not in brackets)
            if (preg_match_all('/\bg_[a-zA-Z0-9_]+\b/', $data, $m2)) {
                foreach ($m2[0] as $gword) {
                    $matches[] = $gword;
                }
            }
        }
    };
    $find_g_patterns($elements);
    // Remove duplicates, keep order
    $matches = array_values(array_unique($matches));
    $shortcode_list = implode("\n", $matches);
    update_post_meta($post_id, 'gurpo_temprex_of_shortcodes', $shortcode_list);
    return true;
}

// AJAX handler for scraping temprex from existing page
add_action('wp_ajax_gurpo_scrape_temprex', function() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied');
    }
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) {
        wp_send_json_error('No post ID provided');
    }
    $result = scrape_temprex_from_existing_page($post_id);
    if ($result === true) {
        $temprex = get_post_meta($post_id, 'gurpo_temprex_of_shortcodes', true);
        wp_send_json_success(['temprex' => $temprex]);
    } else {
        wp_send_json_error($result);
    }
});

// Function to handle Fillernar prompt
function handle_fillernar_prompt() {
    if (isset($_POST['fillernar_prompt']) && $_POST['fillernar_prompt'] === 'Fillernar - Special Custom Filler Content Update System. Hello cursor, please insert some new filler content on our fillernar page 1.') {
        if (add_new_fillernar_number()) {
            wp_redirect(admin_url('admin.php?page=gurpofillernar1'));
            exit;
        }
    }
}
add_action('admin_init', 'handle_fillernar_prompt');

// Add meta box to post/page editor
add_action('add_meta_boxes', 'gurpovich_add_indivinar_meta_box');
function gurpovich_add_indivinar_meta_box() {
    add_meta_box(
        'gurpovich_indivinar_rectangle_1',
        'Gurpovich Indivinar Rectangle 1',
        'gurpovich_indivinar_meta_box_callback',
        ['post', 'page'],
        'side',
        'high'
    );
}

// Meta box callback function
function gurpovich_indivinar_meta_box_callback($post) {
    wp_nonce_field('gurpovich_indivinar_action', 'gurpovich_indivinar_nonce');
    ?>
    <div style="padding: 10px 0;">
        <button type="button" id="scrape-temprex-button" class="button button-primary" style="width: 100%; margin-bottom: 10px;">
            scrape_temprex_from_existing_page
        </button>
    </div>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#scrape-temprex-button').on('click', function() {
            var button = $(this);
            button.prop('disabled', true).text('Scraping...');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'gurpovich_scrape_temprex',
                    post_id: <?php echo $post->ID; ?>,
                    nonce: $('#gurpovich_indivinar_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        alert('Temprex scraped successfully!');
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    alert('AJAX error occurred');
                },
                complete: function() {
                    button.prop('disabled', false).text('scrape_temprex_from_existing_page');
                }
            });
        });
    });
    </script>
    <?php
}

// AJAX handler for scraping temprex
add_action('wp_ajax_gurpovich_scrape_temprex', function() {
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Permission denied');
    }
    
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'gurpovich_indivinar_action')) {
        wp_send_json_error('Invalid nonce');
    }
    
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    if (!$post_id) {
        wp_send_json_error('Invalid post ID');
    }
    
    $result = scrape_temprex_from_existing_page($post_id);
    if ($result === true) {
        wp_send_json_success('Temprex scraped successfully');
    } else {
        wp_send_json_error($result);
    }
});

/**
 * Replaces shortcodes in Elementor data with user-provided content from zeeprex_submit and saves the updated data.
 *
 * @param int $post_id The ID of the Elementor page to update.
 * @param string $zeeprex_submit_text The user input containing [shortcode] and content pairs.
 * @return bool|string True on success, error message on failure.
 */
function function_inject_content_replace_shortcodes_1($post_id, $zeeprex_submit_text) {
    if (empty($post_id) || !get_post($post_id)) {
        return 'Invalid post ID.';
    }
    $elementor_data = get_post_meta($post_id, '_elementor_data', true);
    if (empty($elementor_data)) {
        return 'No Elementor data found.';
    }
    $elements = is_string($elementor_data) ? json_decode($elementor_data, true) : $elementor_data;
    if (!is_array($elements)) {
        return 'Could not decode Elementor data.';
    }
    // Parse zeeprex_submit_text into shortcode => content map
    $lines = preg_split('/\r\n|\r|\n/', $zeeprex_submit_text);
    $map = array();
    $current_shortcode = '';
    foreach ($lines as $line) {
        if (preg_match('/^\[(g_[a-zA-Z0-9_]+)\]$/', trim($line), $m)) {
            $current_shortcode = $m[0]; // include brackets
            $map[$current_shortcode] = '';
        } elseif ($current_shortcode !== '') {
            $map[$current_shortcode] .= ($map[$current_shortcode] === '' ? '' : "\n") . $line;
        }
    }
    // Remove shortcodes with empty content
    foreach ($map as $k => $v) {
        if (trim($v) === '') {
            unset($map[$k]);
        }
    }
    if (empty($map)) {
        return 'No valid shortcode-content pairs found.';
    }
    // Recursively replace shortcodes in Elementor data
    $replace_shortcodes = function($data) use (&$replace_shortcodes, $map) {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = $replace_shortcodes($v);
            }
            return $data;
        } elseif (is_string($data)) {
            foreach ($map as $shortcode => $content) {
                $data = str_replace($shortcode, $content, $data);
            }
            return $data;
        } else {
            return $data;
        }
    };
    $updated_elements = $replace_shortcodes($elements);
    // Save updated data
    update_post_meta($post_id, '_elementor_data', $updated_elements);
    // Save the submitted text in prexnar1 for reference
    update_post_meta($post_id, 'gurpo_prexnar1', $zeeprex_submit_text);
    return true;
}
?>