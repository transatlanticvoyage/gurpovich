<?php
/**
 * Plugin Name: Gurpovich Admin Injector
 * Description: Injects user-defined content into Elementor data JSON directly upon mapping save.
 * Version: 5.8
 * Author: Sake Nova
 */

if (!defined('ABSPATH')) exit;

// Define plugin constants
define('GURPOVICH_VERSION', '5.8');
define('GURPOVICH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GURPOVICH_PLUGIN_URL', plugin_dir_url(__FILE__));

// Debug plugin initialization
error_log('Gurpovich plugin initializing');
error_log('Plugin directory: ' . GURPOVICH_PLUGIN_DIR);
error_log('Plugin URL: ' . GURPOVICH_PLUGIN_URL);

// Autoloader for plugin classes
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'Gurpovich\\';
    
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Define the base directories to search
    $base_dirs = array(
        GURPOVICH_PLUGIN_DIR . 'includes/',
        GURPOVICH_PLUGIN_DIR . 'admin/'
    );
    
    // Try to find the file in each base directory
    foreach ($base_dirs as $base_dir) {
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        error_log('Trying to load class file: ' . $file);
        if (file_exists($file)) {
            error_log('Found class file: ' . $file);
            require $file;
            return;
        }
    }
    error_log('Could not find class file for: ' . $class);
});

// Initialize the plugin
function gurpovich_init() {
    error_log('Gurpovich plugin init function called');
    
    // Load required files
    require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich-loader.php';
    require_once GURPOVICH_PLUGIN_DIR . 'admin/class-gurpovich-admin.php';
    
    // Initialize main plugin class
    $plugin = new Gurpovich\Gurpovich();
    $plugin->run();
    
    // Directly register admin menu as a fallback
    if (is_admin()) {
        $admin = new Gurpovich\Admin\Gurpovich_Admin('gurpovich', GURPOVICH_VERSION);
        add_action('admin_menu', array($admin, 'add_plugin_admin_menu'));
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
    }
}
add_action('plugins_loaded', 'gurpovich_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    error_log('Gurpovich plugin activating');
    require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich-activator.php';
    Gurpovich\Gurpovich_Activator::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    error_log('Gurpovich plugin deactivating');
    require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich-activator.php';
    Gurpovich\Gurpovich_Activator::deactivate();
}); 