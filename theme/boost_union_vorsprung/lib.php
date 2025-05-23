<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme Boost Union VORsprung - Library
 *
 * @package    theme_boost_union_vorsprung
 * @copyright  2025 Danou Nauck <danou@nauck.eu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Constants which are use throughout this theme.
defined('THEME_BOOST_UNION_VORSPRUNG_SETTING_INHERITANCE_INHERIT') || define('THEME_BOOST_UNION_VORSPRUNG_SETTING_INHERITANCE_INHERIT', 0);
defined('THEME_BOOST_UNION_VORSPRUNG_SETTING_INHERITANCE_DUPLICATE') || define('THEME_BOOST_UNION_VORSPRUNG_SETTING_INHERITANCE_DUPLICATE', 1);

/**
 * Returns the main SCSS content.
 *
 * @param \core\output\theme_config $theme The theme config object.
 * @return string
 */
function theme_boost_union_vorsprung_get_main_scss_content($theme) {
    global $CFG;

    // Require the necessary libraries.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // As a start, get the compiled main SCSS from Boost Union.
    // This way, Boost Union VORsprung will ship the same SCSS code as Boost Union itself.
    $scss = theme_boost_union_get_main_scss_content(\core\output\theme_config::load('boost_union'));

    // And add Boost Union VORsprung's main SCSS file to the stack.
    $scss .= file_get_contents($CFG->dirroot . '/theme/boost_union_vorsprung/scss/post.scss');

    return $scss;
}

/**
 * Get SCSS to prepend.
 *
 * @param \core\output\theme_config $theme The theme config object.
 * @return string
 */
function theme_boost_union_vorsprung_get_pre_scss($theme) {
    global $CFG;

    // Require the necessary libraries.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // As a start, initialize the Pre SCSS code with an empty string.
    $scss = '';

    // Then, if configured, get the compiled pre SCSS code from Boost Union.
    // This should not be necessary as Moodle core calls the *_get_pre_scss() functions from all parent themes as well.
    // However, as soon as Boost Union would use $theme->settings in this function, $theme would be this theme here and
    // not Boost Union. The Boost Union developers are aware of this topic, but faults can always happen.
    // If such a fault happens, the Boost Union VORsprung administrator can switch the inheritance to 'Duplicate'.
    // This way, we will add the pre SCSS code with the explicit use of the Boost Union configuration to the stack.
    $inheritanceconfig = get_config('theme_boost_union_vorsprung', 'prescssinheritance');
    if ($inheritanceconfig == THEME_BOOST_UNION_VORSPRUNG_SETTING_INHERITANCE_DUPLICATE) {
        $scss .= theme_boost_union_get_pre_scss(\core\output\theme_config::load('boost_union'));
    }

    // And add Boost Union VORsprung's pre SCSS file to the stack.
    $scss .= file_get_contents($CFG->dirroot . '/theme/boost_union_vorsprung/scss/pre.scss');

    /**********************************************************
     * EXTENSION POINT:
     * Compose and add additional pre-SCSS code here.
     * It will be added on top of Boost Union's pre-SCSS code.
     *********************************************************/

    return $scss;
}

/**
 * Inject additional SCSS.
 *
 * @param \core\output\theme_config $theme The theme config object.
 * @return string
 */
function theme_boost_union_vorsprung_get_extra_scss($theme) {
    global $CFG;

    // Require the necessary libraries.
    require_once($CFG->dirroot . '/theme/boost_union/lib.php');

    // As a start, initialize the Extra SCSS code with an empty string.
    $scss = '';

    // Then, if configured, get the compiled extra SCSS code from Boost Union.
    // This should not be necessary as Moodle core calls the *_get_extra_scss() functions from all parent themes as well.
    // However, as soon as Boost Union would use $theme->settings in this function, $theme would be this theme here and
    // not Boost Union. The Boost Union developers are aware of this topic, but faults can always happen.
    // If such a fault happens, the Boost Union VORsprung administrator can switch the inheritance to 'Duplicate'.
    // This way, we will add the extra SCSS code with the explicit use of the Boost Union configuration to the stack.
    $inheritanceconfig = get_config('theme_boost_union_vorsprung', 'extrascssinheritance');
    if ($inheritanceconfig == THEME_BOOST_UNION_VORSPRUNG_SETTING_INHERITANCE_DUPLICATE) {
        $scss .= theme_boost_union_get_extra_scss(\core\output\theme_config::load('boost_union'));
    }

    /**********************************************************
     * EXTENSION POINT:
     * Compose and add additional SCSS code here.
     * It will be added on top of Boost Union's SCSS code.
     *********************************************************/

    return $scss;
}

/**
 * Callback function for theme_boost_union to allow Boost Union VORsprung to add cards to the Boost Union settings overview page.
 * This function is expected to return an array of arrays containing values with the keys 'label', 'desc', 'btn' and 'url'.
 *
 * @return array
 */
function theme_boost_union_vorsprung_extend_busettingsoverview() {

    $cards[] = [
        'label' => get_string('pluginname', 'theme_boost_union_vorsprung'),
        'desc' => get_string('settingsoverview_buc_desc', 'theme_boost_union_vorsprung'),
        'btn' => 'primary',
        'url' => new \core\url('/admin/settings.php', ['section' => 'theme_boost_union_vorsprung']),
    ];

    return $cards;
}

/**
 * Callback function which allows themes to alter the CSS URLs.
 * We use this function to change the CSS URL to the flavour CSS URL if a flavour applies to the current page.
 *
 * @copyright 2024 Alexander Bias <bias@alexanderbias.de>
 *
 * @param mixed $urls The CSS URLs (passed as reference).
 */
function theme_boost_union_vorsprung_alter_css_urls(&$urls) {
    global $CFG;

    // Require Boost Union library.
    require_once($CFG->dirroot.'/theme/boost_union/lib.php');

    // Call Boost Union's theme_boost_union_alter_css_urls() function which implements the logic to change the CSS URL for flavours.
    theme_boost_union_alter_css_urls($urls);
}

/**
 * Render the message drawer to be included in the top of the body of each page.
 *
 * @return string HTML
 */
function core_course_vorsprung_drawer(): string {
    global $PAGE, $OUTPUT;

    // echo("<br><br><hr><br>core_course_vorsprung_drawer:<br>");
    // var_dump($PAGE);

    // If the course index is explicitly set and if it should be hidden.
    if ($PAGE->get_show_course_index() === false) {
        return '';
    }

    // Only add course index on non-site course pages.
    if (!$PAGE->course || $PAGE->course->id == SITEID) {
        return '';
    }

    // Show course index to users can access the course only.
    if (!can_access_course($PAGE->course, null, '', true)) {
        return '';
    }

    $format = course_get_format($PAGE->course);
    $renderer = $format->get_renderer($PAGE);
   # echo("<hr><br>renderer:<br>");
   # var_dump($renderer);


    $themeconfig = get_config('theme_boost_union_vorsprung');
    // Enable the toggle button in the settings.php to show bigger or smaller menu elements
    if (isset($themeconfig->sidemenudetails) && ($themeconfig->sidemenudetails == 1)) {
        // $templatecontext['courseindex_slim'] = $courseindex;
    } else {
        // $templatecontext['courseindex'] = $courseindex;
    }

    if (method_exists($renderer, 'course_index_drawer')) {
        $result =  $renderer->course_index_drawer($format);
        # $result =  $OUTPUT->render_from_template('core_courseformat/local/courseindex/drawer', []);
        # var_dump($result);
        // echo("<pre>");
        // print_r($result);
        // echo("</pre>");
        return $result;
    } else {
        echo("<h2> NOT method_exists(renderer, 'course_index_drawer2'))</h2>");
    }

    return '';
}