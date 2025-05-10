<?php
namespace Gurpovich;

class Gurpovich_Activator {
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'gurpo_pageideas';
        $charset_collate = $wpdb->get_charset_collate();

        // Create the gurpo_pageideas table
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

        // Create the gurpo_driggs table
        $driggs_table = $wpdb->prefix . 'gurpo_driggs';
        $driggs_sql = "CREATE TABLE IF NOT EXISTS $driggs_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            driggs_domain varchar(255) NOT NULL,
            driggs_industry varchar(255) NOT NULL,
            driggs_city varchar(255) NOT NULL,
            driggs_brand_name_1 varchar(255) NOT NULL,
            driggs_site_type_or_purpose text NOT NULL,
            driggs_email_1 varchar(255) NOT NULL,
            driggs_address_1 varchar(255) NOT NULL,
            driggs_phone1 varchar(50) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($driggs_sql);
    }

    public static function deactivate() {
        // Optional: Add cleanup code here if needed
        // For example, you might want to remove the tables:
        // global $wpdb;
        // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}gurpo_pageideas");
        // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}gurpo_driggs");
    }
} 