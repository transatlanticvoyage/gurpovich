<?php
namespace Gurpovich\Admin\Screens;

if (!defined('ABSPATH')) exit;

class Screen10_Images {
    public function render() {
        // Suppress all admin notices except our own on this page
        add_action('admin_print_scripts', function() {
            echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }</style>';
        }, 1);

        echo '<div class="wrap">';
        echo '<h1>Image Management</h1>';

        // Handle image upload
        if (isset($_POST['upload_image']) && check_admin_referer('gurpovich_image_upload', 'gurpovich_image_nonce')) {
            $this->handle_image_upload();
        }

        // Handle image deletion
        if (isset($_POST['delete_image']) && check_admin_referer('gurpovich_image_delete', 'gurpovich_image_nonce')) {
            $this->handle_image_deletion();
        }

        // Display upload form
        $this->render_upload_form();

        // Display existing images
        $this->render_image_list();
        
        echo '</div>';
    }

    private function handle_image_upload() {
        if (!current_user_can('upload_files')) {
            wp_die('You do not have sufficient permissions to upload files.');
        }

        if (!isset($_FILES['gurpovich_image']) || $_FILES['gurpovich_image']['error'] !== UPLOAD_ERR_OK) {
            echo '<div class="error"><p>Error uploading file. Please try again.</p></div>';
            return;
        }

        $file = $_FILES['gurpovich_image'];
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        
        if (!in_array($file['type'], $allowed_types)) {
            echo '<div class="error"><p>Invalid file type. Only JPG, PNG, and GIF files are allowed.</p></div>';
            return;
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload('gurpovich_image', 0);
        
        if (is_wp_error($attachment_id)) {
            echo '<div class="error"><p>' . esc_html($attachment_id->get_error_message()) . '</p></div>';
        } else {
            // Store additional metadata if needed
            update_post_meta($attachment_id, '_gurpovich_image_type', sanitize_text_field($_POST['image_type'] ?? ''));
            echo '<div class="updated"><p>Image uploaded successfully.</p></div>';
        }
    }

    private function handle_image_deletion() {
        if (!current_user_can('delete_posts')) {
            wp_die('You do not have sufficient permissions to delete files.');
        }

        $image_id = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;
        
        if ($image_id > 0) {
            $result = wp_delete_attachment($image_id, true);
            if ($result) {
                echo '<div class="updated"><p>Image deleted successfully.</p></div>';
            } else {
                echo '<div class="error"><p>Error deleting image.</p></div>';
            }
        }
    }

    private function render_upload_form() {
        echo '<div class="card">';
        echo '<h2>Upload New Image</h2>';
        echo '<form method="post" enctype="multipart/form-data">';
        wp_nonce_field('gurpovich_image_upload', 'gurpovich_image_nonce');
        
        echo '<p><label for="gurpovich_image">Select Image:</label><br>';
        echo '<input type="file" name="gurpovich_image" id="gurpovich_image" accept="image/*" required></p>';
        
        echo '<p><label for="image_type">Image Type:</label><br>';
        echo '<select name="image_type" id="image_type">';
        echo '<option value="logo">Logo</option>';
        echo '<option value="banner">Banner</option>';
        echo '<option value="thumbnail">Thumbnail</option>';
        echo '<option value="other">Other</option>';
        echo '</select></p>';
        
        echo '<p><input type="submit" name="upload_image" class="button button-primary" value="Upload Image"></p>';
        echo '</form>';
        echo '</div>';
    }

    private function render_image_list() {
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $images = new \WP_Query($args);

        if ($images->have_posts()) {
            echo '<div class="card" style="margin-top: 20px;">';
            echo '<h2>Uploaded Images</h2>';
            echo '<div class="gurpovich-image-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">';
            
            while ($images->have_posts()) {
                $images->the_post();
                $image_id = get_the_ID();
                $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                $image_type = get_post_meta($image_id, '_gurpovich_image_type', true);
                
                echo '<div class="gurpovich-image-item" style="border: 1px solid #ddd; padding: 10px; text-align: center;">';
                echo '<img src="' . esc_url($image_url) . '" style="max-width: 100%; height: auto;">';
                echo '<p><strong>Type:</strong> ' . esc_html($image_type ?: 'Not specified') . '</p>';
                echo '<p><strong>ID:</strong> ' . esc_html($image_id) . '</p>';
                
                // Delete form
                echo '<form method="post" style="margin-top: 10px;">';
                wp_nonce_field('gurpovich_image_delete', 'gurpovich_image_nonce');
                echo '<input type="hidden" name="image_id" value="' . esc_attr($image_id) . '">';
                echo '<input type="submit" name="delete_image" class="button button-small" value="Delete" onclick="return confirm(\'Are you sure you want to delete this image?\');">';
                echo '</form>';
                
                echo '</div>';
            }
            
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="card" style="margin-top: 20px;">';
            echo '<p>No images uploaded yet.</p>';
            echo '</div>';
        }
        
        wp_reset_postdata();
    }
}

// Initialize and render the screen
$screen = new Screen10_Images();
$screen->render(); 