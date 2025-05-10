<?php
namespace Gurpovich\Admin;

class Gurpovich_Admin {
    private $plugin_name;
    private $version;
    private $current_screen;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->current_screen = '';
    }

    public function enqueue_styles() {
        // Only load on our plugin pages
        if (!$this->is_plugin_page()) {
            return;
        }

        // Main admin styles
        wp_enqueue_style(
            $this->plugin_name,
            GURPOVICH_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            $this->version,
            'all'
        );

        // Screen-specific styles
        $screen = get_current_screen();
        if ($screen) {
            $screen_id = $screen->id;
            $screen_specific_css = GURPOVICH_PLUGIN_URL . 'assets/css/screens/' . $screen_id . '.css';
            
            if (file_exists(GURPOVICH_PLUGIN_DIR . 'assets/css/screens/' . $screen_id . '.css')) {
                wp_enqueue_style(
                    $this->plugin_name . '-' . $screen_id,
                    $screen_specific_css,
                    array($this->plugin_name),
                    $this->version,
                    'all'
                );
            }
        }
    }

    public function enqueue_scripts() {
        // Only load on our plugin pages
        if (!$this->is_plugin_page()) {
            return;
        }

        // Main admin script
        wp_enqueue_script(
            $this->plugin_name,
            GURPOVICH_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            $this->version,
            false
        );

        // Add localized script data
        wp_localize_script($this->plugin_name, 'gurpovichAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gurpovich_admin_nonce'),
            'currentScreen' => $this->current_screen,
            'i18n' => array(
                'confirmDelete' => __('Are you sure you want to delete this item?', 'gurpovich'),
                'error' => __('An error occurred. Please try again.', 'gurpovich'),
                'success' => __('Operation completed successfully.', 'gurpovich')
            )
        ));

        // Screen-specific scripts
        $screen = get_current_screen();
        if ($screen) {
            $screen_id = $screen->id;
            $screen_specific_js = GURPOVICH_PLUGIN_URL . 'assets/js/screens/' . $screen_id . '.js';
            
            if (file_exists(GURPOVICH_PLUGIN_DIR . 'assets/js/screens/' . $screen_id . '.js')) {
                wp_enqueue_script(
                    $this->plugin_name . '-' . $screen_id,
                    $screen_specific_js,
                    array($this->plugin_name),
                    $this->version,
                    false
                );
            }
        }
    }

    public function add_plugin_admin_menu() {
        // Main menu item
        add_menu_page(
            __('Screen 1', 'gurpovich'),
            __('Screen 1', 'gurpovich'),
            'manage_options',
            'gurposcreen1',
            array($this, 'display_screen1_page'),
            'dashicons-admin-generic',
            2
        );

        // Submenu items
        $screens = array(
            array('Screen 2', 'Screen 2', 'gurposcreen2', 'display_screen2_page'),
            array('Screen 3 - Inject 1 -Homepage', 'Screen 3 - Inject 1 -Homepage', 'gurposcreen3', 'display_screen3_page'),
            array('Screen 4 - Driggs', 'Screen 4 - Driggs', 'gurposcreen4', 'display_screen4_page'),
            array('Screen 5 - Footer', 'Screen 5 - Footer', 'gurposcreen5', 'display_screen5_page'),
            array('Screen 6 - Logo', 'Screen 6 - Logo', 'gurposcreen6', 'display_screen6_page'),
            array('Fillernar 1', 'Fillernar 1', 'gurpofillernar1', 'display_fillernar1_page'),
            array('DB Table Viewer', 'DB Table Viewer', 'gurpo-db-viewer', 'display_db_viewer_page'),
            array('Screen 14 - dralo dummy driggs', 'Screen 14 - dralo dummy driggs', 'gurposcreen14', 'display_screen14_page'),
            array('Screen 15 - wafo dummy seo content', 'Screen 15 - wafo dummy seo content', 'gurposcreen15', 'display_screen15_page'),
            array('Screen 0 - API Keys', 'Screen 0 - API Keys', 'gurposcreen0', 'display_screen0_page'),
            array('Screen 20 - prompts', 'Screen 20 - prompts', 'gurposcreen20', 'display_screen20_page'),
            array('Screen 10 - images', 'Screen 10 - images', 'gurposcreen10', 'display_screen10_page')
        );

        foreach ($screens as $screen) {
            add_submenu_page(
                'gurposcreen1',
                __($screen[0], 'gurpovich'),
                __($screen[1], 'gurpovich'),
                'manage_options',
                $screen[2],
                array($this, $screen[3])
            );
        }
    }

    private function is_plugin_page() {
        $screen = get_current_screen();
        if (!$screen) {
            return false;
        }

        $this->current_screen = $screen->id;
        return strpos($this->current_screen, 'gurpo') === 0;
    }

    // Screen display methods
    public function display_screen1_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen1-injector.php';
    }

    public function display_screen2_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen2-main.php';
    }

    public function display_screen3_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen3-homepage.php';
        $screen = new \Gurpovich\Admin\Screens\Screen3_Homepage();
        $screen->render();
    }

    public function display_screen4_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen4-driggs.php';
    }

    public function display_screen5_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen5-footer.php';
    }

    public function display_screen6_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen6-logo.php';
    }

    public function display_fillernar1_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/fillernar1.php';
    }

    public function display_db_viewer_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/db-viewer.php';
    }

    public function display_screen14_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen14-dralo.php';
    }

    public function display_screen15_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen15-wafo.php';
    }

    public function display_screen0_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen0-api-keys.php';
    }

    public function display_screen20_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen20-prompts.php';
    }

    public function display_screen10_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'gurpovich'));
        }
        require_once GURPOVICH_PLUGIN_DIR . 'admin/screens/screen10-images.php';
    }
} 