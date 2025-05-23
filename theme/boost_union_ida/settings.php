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
 * Theme Boost Union IDA - Settings file
 *
 * @package    theme_boost_union_ida
 * @copyright  2025 Danou Nauck <danou@nauck.eu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Constants which are use throughout this theme.
defined('THEME_BOOST_UNION_IDA_SETTING_INHERITANCE_INHERIT') || define('THEME_BOOST_UNION_IDA_SETTING_INHERITANCE_INHERIT', 0);
defined('THEME_BOOST_UNION_IDA_SETTING_INHERITANCE_DUPLICATE') || define('THEME_BOOST_UNION_IDA_SETTING_INHERITANCE_DUPLICATE', 1);

if ($hassiteconfig || has_capability('theme/boost_union:configure', context_system::instance())) {

    // How this file works:
    // Boost Union's settings are divided into multiple settings pages which resides in its own settings category.
    // You will understand it as soon as you look at /theme/boost_union/settings.php.
    // This settings file here is built in a way that it adds another settings page to this existing settings
    // category. You can add all child-theme-specific settings to this settings page here.

    // However, there is still the $settings variable which is expected by Moodle core to be filled with the theme
    // settings and which is automatically linked from the theme selector page.
    // To avoid that there appears a broken "Boost Union IDA" settings page, we redirect the user to a settings
    // overview page if he opens this page.
    $mainsettingspageurl = new \core\url('/admin/settings.php', ['section' => 'themesettingboost_union_ida']);
    if ($ADMIN->fulltree && $PAGE->has_set_url() && $PAGE->url->compare($mainsettingspageurl)) {
        redirect(new \core\url('/admin/settings.php', ['section' => 'theme_boost_union_ida']));
    }

    // Create empty settings page structure to make the site administration work on non-admin pages.
    if (!$ADMIN->fulltree) {
        // Create Boost Union IDA settings page
        // (and allow users with the theme/boost_union:configure capability to access it).
        $tab = new admin_settingpage('theme_boost_union_ida',
                get_string('configtitle', 'theme_boost_union_ida', null, true),
                'theme/boost_union:configure');
        $ADMIN->add('theme_boost_union', $tab);
    }

    // Create full settings page structure.
    // phpcs:disable moodle.ControlStructures.ControlSignature.Found
    else if ($ADMIN->fulltree) {

        // Require the necessary libraries.
        require_once($CFG->dirroot . '/theme/boost_union/lib.php');
        require_once($CFG->dirroot . '/theme/boost_union/locallib.php');
        require_once($CFG->dirroot . '/theme/boost_union_ida/lib.php');
        require_once($CFG->dirroot . '/theme/boost_union_ida/locallib.php');

        // Prepare options array for select settings.
        // Due to MDL-58376, we will use binary select settings instead of checkbox settings throughout this theme.
        $yesnooption = [THEME_BOOST_UNION_SETTING_SELECT_YES => get_string('yes'),
                THEME_BOOST_UNION_SETTING_SELECT_NO => get_string('no'), ];


        // Create Boost Union IDA settings page with tabs
        // (and allow users with the theme/boost_union:configure capability to access it).
        $page = new theme_boost_admin_settingspage_tabs('theme_boost_union_ida',
                get_string('configtitle', 'theme_boost_union_ida', null, true),
                'theme/boost_union:configure');


        // Create general settings tab.
        $tab = new admin_settingpage('theme_boost_union_ida_general',
                get_string('generalsettings', 'theme_boost', null, true));

        // Create inheritance heading.
        $name = 'theme_boost_union_ida/inheritanceheading';
        $title = get_string('inheritanceheading', 'theme_boost_union_ida', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Prepare inheritance options.
        $inheritanceoptions = [
                THEME_BOOST_UNION_IDA_SETTING_INHERITANCE_INHERIT =>
                        get_string('inheritanceinherit', 'theme_boost_union_ida'),
                THEME_BOOST_UNION_IDA_SETTING_INHERITANCE_DUPLICATE =>
                        get_string('inheritanceduplicate', 'theme_boost_union_ida'),
        ];

        // Setting: Pre SCSS inheritance setting.
        $name = 'theme_boost_union_ida/prescssinheritance';
        $title = get_string('prescssinheritancesetting', 'theme_boost_union_ida', null, true);
        $description = get_string('prescssinheritancesetting_desc', 'theme_boost_union_ida', null, true).'<br />'.
                get_string('inheritanceoptionsexplanation', 'theme_boost_union_ida', null, true);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_BOOST_UNION_IDA_SETTING_INHERITANCE_INHERIT, $inheritanceoptions);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Setting: Extra SCSS inheritance setting.
        $name = 'theme_boost_union_ida/extrascssinheritance';
        $title = get_string('extrascssinheritancesetting', 'theme_boost_union_ida', null, true);
        $description = get_string('extrascssinheritancesetting_desc', 'theme_boost_union_ida', null, true).'<br />'.
                get_string('inheritanceoptionsexplanation', 'theme_boost_union_ida', null, true);
        $setting = new admin_setting_configselect($name, $title, $description,
                THEME_BOOST_UNION_IDA_SETTING_INHERITANCE_INHERIT, $inheritanceoptions);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);

        /**********************************************************
         * EXTENSION POINT:
         * Add your Boost Union IDA settings here.
         *********************************************************/

        // Create footer tab.
        $tab = new admin_settingpage('theme_boost_union_ida_content_footer', get_string('footertab', 'theme_boost_union', null, true));

        // Create footnote heading.
        $name = 'theme_boost_union_ida/footnoteheading';
        $title = get_string('footnoteheading', 'theme_boost_union', null, true);
        $setting = new admin_setting_heading($name, $title, null);
        $tab->add($setting);

        // Setting: Footnote.
        $name = 'theme_boost_union_ida/footnote';
        $title = get_string('footnotesetting', 'theme_boost_union', null, true);
        $description = get_string('footnotesetting_desc', 'theme_boost_union', null, true);
        $setting = new admin_setting_confightmleditor($name, $title, $description, '');
        $tab->add($setting);

        // Add tab to settings page.
        $page->add($tab);

        // Add settings page to the admin settings category.
        $ADMIN->add('theme_boost_union', $page);
    }
}
