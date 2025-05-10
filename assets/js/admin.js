(function($) {
    'use strict';

    // Initialize the admin interface
    function initAdmin() {
        // Handle form submissions
        $('.gurpovich-form').on('submit', function(e) {
            if (!confirm(gurpovichAdmin.i18n.confirmSubmit)) {
                e.preventDefault();
                return false;
            }
        });

        // Handle delete confirmations
        $('.gurpovich-delete').on('click', function(e) {
            if (!confirm(gurpovichAdmin.i18n.confirmDelete)) {
                e.preventDefault();
                return false;
            }
        });

        // Handle AJAX operations
        $('.gurpovich-ajax-action').on('click', function(e) {
            e.preventDefault();
            const $button = $(this);
            const action = $button.data('action');
            const data = $button.data('params') || {};

            $button.prop('disabled', true);

            $.ajax({
                url: gurpovichAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: action,
                    nonce: gurpovichAdmin.nonce,
                    ...data
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('success', response.data.message || gurpovichAdmin.i18n.success);
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        }
                    } else {
                        showNotice('error', response.data.message || gurpovichAdmin.i18n.error);
                    }
                },
                error: function() {
                    showNotice('error', gurpovichAdmin.i18n.error);
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        });

        // Initialize tooltips
        $('.gurpovich-tooltip').tooltipster({
            theme: 'tooltipster-light',
            maxWidth: 300
        });

        // Initialize sortable tables
        $('.gurpovich-sortable').tablesorter({
            sortList: [[0,0]],
            widgets: ['zebra']
        });
    }

    // Show notice message
    function showNotice(type, message) {
        const $notice = $('<div>')
            .addClass('gurpovich-notice gurpovich-notice-' + type)
            .text(message);

        $('.gurpovich-wrap').prepend($notice);

        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Document ready
    $(document).ready(function() {
        initAdmin();
    });

})(jQuery); 