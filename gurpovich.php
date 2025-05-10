<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://gurpovich.com
 * @since             1.0.0
 * @package           Gurpovich
 *
 * @wordpress-plugin
 * Plugin Name:       Gurpovich
 * Plugin URI:        https://gurpovich.com
 * Description:       Gurpovich plugin for WordPress
 * Version:           1.0.0
 * Author:            Gurpovich
 * Author URI:        https://gurpovich.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gurpovich
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('GURPOVICH_VERSION', '1.0.0');
define('GURPOVICH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GURPOVICH_PLUGIN_URL', plugin_dir_url(__FILE__));

// First, require the main class file directly
require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich.php';

// Then set up the autoloader
spl_autoload_register(function ($class) {
    // Project-specific namespace prefix
    $prefix = 'Gurpovich\\';
    
    // Base directory for the namespace prefix
    $base_dir = GURPOVICH_PLUGIN_DIR;
    
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
    // Log initialization attempt
    error_log('Gurpovich: Attempting to initialize plugin');
    
    try {
        // Create new instance of main plugin class
        $plugin = new \Gurpovich\Gurpovich();
        
        // Run the plugin
        $plugin->run();
        
        error_log('Gurpovich: Plugin initialized successfully');
    } catch (\Exception $e) {
        error_log('Gurpovich Error: ' . $e->getMessage());
        error_log('Gurpovich Error Trace: ' . $e->getTraceAsString());
    }
}

// Hook into WordPress
add_action('plugins_loaded', 'gurpovich_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich-activator.php';
    \Gurpovich\Gurpovich_Activator::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich-deactivator.php';
    \Gurpovich\Gurpovich_Deactivator::deactivate();
}); 