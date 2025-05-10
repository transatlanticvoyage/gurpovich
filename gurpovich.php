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

// Autoloader for plugin classes
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'Gurpovich\\';
    
    // Base directory for the namespace prefix
    $base_dir = GURPOVICH_PLUGIN_DIR . 'includes/';
    
    // Check if the class uses the namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace namespace separators with directory separators
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function gurpovich_init() {
    // Initialize main plugin class
    $plugin = new Gurpovich\Gurpovich();
    $plugin->run();
}
add_action('plugins_loaded', 'gurpovich_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich-activator.php';
    Gurpovich\Gurpovich_Activator::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich-activator.php';
    Gurpovich\Gurpovich_Activator::deactivate();
}); 