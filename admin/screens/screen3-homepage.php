<?php
namespace Gurpovich\Admin\Screens;

if (!defined('ABSPATH')) exit;

class Screen3_Homepage {
    public function render() {
        // Suppress all admin notices except our own on this page
        add_action('admin_print_scripts', function() {
            echo '<style>
                .notice, .update-nag, .updated, .error, .is-dismissible, .notice-success, .notice-warning, .notice-error, .notice-info, .notice-alt, .notice-large, .notice-inline, .notice-dismiss, .aios-notice, .aioseo-notice, .rank-math-notice, .yoast-notice, .elementor-message, .elementor-notice, .elementor-admin-message, .elementor-admin-notice, .elementor-message-success, .elementor-message-warning, .elementor-message-error, .elementor-message-info { display: none !important; }
                .update-nag, .update-message, .wp-footer, #wpfooter { display: none !important; }
            </style>';
        }, 1);

        echo '<div class="wrap">';
        echo '<div style="font-weight:bold; font-size:1.2em; margin-bottom:10px;">Screen 3 - Inject 1 -Homepage</div>';
        echo '<h1>balarfi</h1>';
        
        echo '<form method="post" id="balarfi-form">';
        echo '<input type="hidden" name="page" value="gurposcreen3" />';
        echo '<hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;">';
        
        echo '<div style="width:100%;background:#d6ecff;color:#1a2333;font-weight:bold;font-size:1.1em;padding:8px 0 8px 12px;margin-bottom:10px;">Select A Page To Inject Your Zeeprex_Submit Text Into</div>';
        
        echo '<table class="form-table"><tbody>';
        echo '<tr><th><label for="balarfi_page_id">Select a page</label></th>';
        echo '<td style="display:flex;align-items:center;">';
        echo '<select name="balarfi_page_id" id="balarfi_page_id" onchange="this.form.submit();" style="margin-right:12px;">';
        // Add your page options here
        echo '</select>';
        echo '<input type="radio" name="kardwaj_radio" value="select" style="margin-left:8px;" checked onclick="this.form.submit();">';
        echo '</td></tr>';
        
        echo '<tr><th><label for="kardwaj_default">Use default kardwaj page</label></th>';
        echo '<td style="display:flex;align-items:center;">';
        echo '<input type="text" id="kardwaj_default" value="(default)" style="width:180px; margin-right:12px; background:#eee; color:#888; border:1px solid #ccc;" readonly />';
        echo '<input type="radio" name="kardwaj_radio" value="default" style="margin-left:8px;" onclick="this.form.submit();">';
        echo '</td></tr>';
        
        echo '<tr><th><label for="manual_post_id">Type in a wp post id</label></th>';
        echo '<td style="display:flex;align-items:center;">';
        echo '<input type="text" name="manual_post_id" id="manual_post_id" value="" style="width:120px; margin-right:12px;" />';
        echo '<input type="radio" name="kardwaj_radio" value="manual" style="margin-left:8px;" onclick="this.form.submit();">';
        echo '</td></tr>';
        echo '</tbody></table>';
        
        echo '<hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;">';
        
        echo '<table class="form-table"><tbody>';
        echo '<tr><th><label for="temprex_1_scraped">temprex_1_scraped</label><br />';
        echo '<button type="submit" name="scrape_temprex_fresh" style="background:#111;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;margin-top:8px;">scrape temprex fresh</button></th>';
        echo '<td colspan="2"><div style="display:flex;gap:18px;">';
        echo '<textarea id="temprex_1_scraped" name="temprex_1_scraped" style="width: 400px; height: 250px;" readonly></textarea>';
        echo '<textarea id="temprex_1_scraped_bracketed" style="width: 400px; height: 250px;" readonly></textarea>';
        echo '</div></td></tr>';
        
        echo '<tr><td colspan="3"><hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;"></td></tr>';
        
        echo '<tr><th><label for="temprex_2_cached_by_hand">temprex_2_cached_by_hand</label><br />';
        echo '<button type="submit" name="cache_temprex_2" style="background:#4a2c2a;color:#fff;font-weight:bold;text-transform:lowercase;padding:8px 18px;border:none;border-radius:4px;cursor:pointer;margin-top:8px;">cache now</button></th>';
        echo '<td colspan="2"><div style="display:flex;gap:18px;">';
        echo '<textarea id="temprex_2_cached_by_hand" name="temprex_2_cached_by_hand" style="width: 400px; height: 250px;"></textarea>';
        echo '<textarea id="temprex_2_cached_by_hand_bracketed" style="width: 400px; height: 250px;" readonly></textarea>';
        echo '</div></td></tr>';
        
        echo '<tr><td colspan="3"><hr style="border:0; border-top:2px solid #333; margin:18px 0 18px 0;"></td></tr>';
        
        echo '<tr><th><label for="zeeprex_submit">zeeprex_submit</label></th>';
        echo '<td colspan="2">';
        // Add your zeeprex_submit content here
        echo '</td></tr>';
        
        echo '</tbody></table>';
        echo '</form>';
        
        echo '</div>'; // End wrap
    }
} 