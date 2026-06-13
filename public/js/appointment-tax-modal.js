/**
 * Global Tax Modal Handler for Appointment Forms
 * This script provides consistent tax modal behavior across all appointment forms
 */

window.AppointmentTaxModal = {
    /**
     * Initialize tax modal toggle functionality
     * @param {string} toggleBtnId - ID of the toggle button
     * @param {string} panelId - ID of the tax details panel
     */
    init: function (toggleBtnId, panelId) {
        const toggleBtn = document.getElementById(toggleBtnId);
        const panel = document.getElementById(panelId);

        if (!toggleBtn || !panel) {
            console.warn('Tax modal elements not found:', { toggleBtnId, panelId });
            return;
        }

        // Remove any existing event listeners
        $(toggleBtn).off('click.taxModal');

        // Add click event listener
        $(toggleBtn).on('click.taxModal', function (e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent click from bubbling to document
            const $icon = $(this).find('i');
            const $panel = $(panel);

            if ($panel.is(':visible')) {
                $panel.slideUp(300);
                $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            } else {
                $panel.slideDown(300);
                $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            }
        });
    },

    /**
     * Close tax modal
     * @param {string} panelId - ID of the tax details panel
     * @param {string} toggleBtnId - ID of the toggle button
     */
    close: function (panelId, toggleBtnId) {
        const panel = document.getElementById(panelId);
        const toggleBtn = document.getElementById(toggleBtnId);

        if (panel) {
            $(panel).hide();
        }

        if (toggleBtn) {
            const $icon = $(toggleBtn).find('i');
            $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    },

    /**
     * Initialize click-outside-to-close functionality
     * @param {string} panelId - ID of the tax details panel
     * @param {string} toggleBtnId - ID of the toggle button
     */
    initClickOutside: function (panelId, toggleBtnId) {
        // Use a slight delay to ensure the toggle click completes first
        $(document).off('click.taxModalOutside').on('click.taxModalOutside', function (e) {
            const $panel = $(`#${panelId}`);
            const $target = $(e.target);

            // Only close if panel is visible AND click is outside both panel and toggle button
            if ($panel.is(':visible') &&
                !$target.closest(`#${panelId}`).length &&
                !$target.closest(`#${toggleBtnId}`).length) {
                setTimeout(function () {
                    window.AppointmentTaxModal.close(panelId, toggleBtnId);
                }, 10);
            }
        });
    },

    /**
     * Initialize all tax modal functionality for a form
     * @param {Object} config - Configuration object
     */
    initAll: function (config) {
        const defaultConfig = {
            toggleBtnId: 'tax-toggle-btn',
            panelId: 'tax-details-modal',
            enableClickOutside: true
        };

        const finalConfig = Object.assign(defaultConfig, config);

        // Initialize toggle functionality
        this.init(finalConfig.toggleBtnId, finalConfig.panelId);

        // Initialize click outside functionality if enabled
        if (finalConfig.enableClickOutside) {
            this.initClickOutside(finalConfig.panelId, finalConfig.toggleBtnId);
        }
    }
};

// Auto-initialize common tax modal configurations
$(document).ready(function () {
    // Initialize clinic appointment tax modal
    if (document.getElementById('tax-toggle-btn') && document.getElementById('tax-details-modal')) {
        window.AppointmentTaxModal.initAll({
            toggleBtnId: 'tax-toggle-btn',
            panelId: 'tax-details-modal'
        });
    }

    // Initialize global appointment tax modal
    if (document.getElementById('global-tax-toggle-btn') && document.getElementById('global-tax-details-expanded')) {
        window.AppointmentTaxModal.initAll({
            toggleBtnId: 'global-tax-toggle-btn',
            panelId: 'global-tax-details-expanded'
        });
    }
});
