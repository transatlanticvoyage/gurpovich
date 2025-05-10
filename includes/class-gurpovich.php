<?php
namespace Gurpovich;

class Gurpovich {
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = GURPOVICH_VERSION;
        $this->plugin_name = 'gurpovich';
        
        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    private function load_dependencies() {
        // Load the loader class
        require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich-loader.php';
        
        // Load the admin class
        require_once GURPOVICH_PLUGIN_DIR . 'admin/class-gurpovich-admin.php';
        
        // Create new loader
        $this->loader = new Gurpovich_Loader();
    }

    private function define_admin_hooks() {
        $plugin_admin = new Admin\Gurpovich_Admin($this->get_plugin_name(), $this->get_version());
        
        // Add menu items
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        
        // Add admin scripts and styles
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    public function run() {
        $this->loader->run();
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }
} 