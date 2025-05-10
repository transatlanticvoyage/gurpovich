<?php
namespace Gurpovich;

class Gurpovich {
    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->plugin_name = 'gurpovich';
        $this->version = GURPOVICH_VERSION;
        
        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    private function load_dependencies() {
        // Load required files
        require_once GURPOVICH_PLUGIN_DIR . 'includes/class-gurpovich-loader.php';
        require_once GURPOVICH_PLUGIN_DIR . 'admin/class-gurpovich-admin.php';
        
        // Load screen files
        $screen_files = glob(GURPOVICH_PLUGIN_DIR . 'admin/screens/*.php');
        foreach ($screen_files as $file) {
            require_once $file;
        }
    }

    private function define_admin_hooks() {
        $plugin_admin = new Admin\Gurpovich_Admin($this->get_plugin_name(), $this->get_version());
        
        // Add admin menu
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        
        // Add admin styles and scripts
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