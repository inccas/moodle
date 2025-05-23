/**
 * Theme mode toggle JavaScript
 *
 * @package    block_thememode
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    return {
        init: function() {

            console.log("successfully toggled the JS init");
            var toggle = $('#thememode-toggle');
            var modeText = $('.mode-text');

            // Listen for changes on the toggle
            toggle.on('change', function() {
                var darkMode = toggle.is(':checked');

                // Update the text based on the current state
                modeText.text(darkMode ? M.util.get_string('lightmode', 'block_thememode') :
                                       M.util.get_string('darkmode', 'block_thememode'));

                // Save user preference via AJAX
                Ajax.call([{
                    methodname: 'block_thememode_set_preference',
                    args: {
                        mode: darkMode ? 1 : 0
                    },
                    done: function() {
                        // Apply dark mode immediately
                        applyThemeMode(darkMode);
                    },
                    fail: Notification.exception
                }]);
            });

            // Apply the theme mode on page load
            applyThemeMode(toggle.is(':checked'));
        }
    };

    /**
     * Apply the theme mode by adding/removing CSS classes and updating the DOM
     *
     * @param {boolean} darkMode - Whether to enable dark mode
     */
    function applyThemeMode(darkMode) {
        var body = $('body');

        if (darkMode) {
            body.addClass('dark-mode');
            body.removeClass('light-mode');
        } else {
            body.addClass('light-mode');
            body.removeClass('dark-mode');
        }

        // Dispatch an event for other components that might need to respond
        $(document).trigger('thememode:changed', [darkMode]);
    }
});
