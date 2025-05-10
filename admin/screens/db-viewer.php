<?php
namespace Gurpovich\Admin\Screens;

if (!defined('ABSPATH')) exit;

class DB_Viewer {
    public function render() {
        global $wpdb;
        
        echo '<div class="wrap">';
        echo '<div style="font-weight:bold; font-size:1.2em; margin-bottom:10px;">DB Table Viewer</div>';
        echo '<h1>Database Table Viewer</h1>';

        // Get all tables
        $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}gurpo_%'", ARRAY_N);
        
        if (!empty($tables)) {
            echo '<h2>Available Tables</h2>';
            echo '<ul>';
            foreach ($tables as $table) {
                $table_name = $table[0];
                echo '<li><a href="?page=gurpo-db-viewer&table=' . esc_attr($table_name) . '">' . esc_html($table_name) . '</a></li>';
            }
            echo '</ul>';
        }

        // Display table contents if selected
        if (isset($_GET['table'])) {
            $table_name = sanitize_text_field($_GET['table']);
            if (strpos($table_name, $wpdb->prefix . 'gurpo_') === 0) {
                $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name");
                $rows = $wpdb->get_results("SELECT * FROM $table_name");
                
                if ($columns && $rows) {
                    echo '<h2>Contents of ' . esc_html($table_name) . '</h2>';
                    echo '<table class="wp-list-table widefat fixed striped">';
                    echo '<thead><tr>';
                    foreach ($columns as $column) {
                        echo '<th>' . esc_html($column->Field) . '</th>';
                    }
                    echo '<th>Actions</th>';
                    echo '</tr></thead>';
                    echo '<tbody>';
                    foreach ($rows as $row) {
                        echo '<tr>';
                        foreach ($columns as $column) {
                            $field = $column->Field;
                            echo '<td>' . esc_html($row->$field) . '</td>';
                        }
                        echo '<td><a href="?page=gurpo-db-viewer&table=' . esc_attr($table_name) . '&action=edit&id=' . esc_attr($row->id) . '" class="button button-small">Edit</a> ';
                        echo '<a href="?page=gurpo-db-viewer&table=' . esc_attr($table_name) . '&action=delete&id=' . esc_attr($row->id) . '" class="button button-small" onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                } else {
                    echo '<p>No data found in this table.</p>';
                }
            } else {
                echo '<div class="error"><p>Invalid table name.</p></div>';
            }
        }
        
        echo '</div>';
    }
}

// Initialize and render the screen
$screen = new DB_Viewer();
$screen->render(); 