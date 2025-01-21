<?php
namespace theme_boost_union_ida\local;

use core\hook\output\before_standard_head_html_generation;

class hook_callbacks {
    public static function before_standard_head_html(before_standard_head_html_generation $hook) {
       # $hook->add_html('<link rel="stylesheet" href="path/to/your/style.css">');
        global $CFG, $PAGE;

        // Initialize HTML (even though we do not add any HTML at this stage of the implementation).
        $html = '';

        // If a theme other than Boost Union or a child theme of it is active, return directly.
        // This is necessary as the before_standard_html_head() callback is called regardless of the active theme.
        if ($PAGE->theme->name != 'boost_union_ida' && !in_array('boost_union_ida', $PAGE->theme->parents)) {
            return $html;
        }

        // Require local library.
        require_once($CFG->dirroot . '/theme/boost_union_ida/locallib.php');

        // Add the FontAwesome icons to the page.
        theme_boost_union_ida_add_fontawesome_to_page();

        // Add the flavour CSS to the page.
        theme_boost_union_ida_add_flavourcss_to_page();

        // Return an empty string to keep the caller happy.
        return $html;
    }
}
