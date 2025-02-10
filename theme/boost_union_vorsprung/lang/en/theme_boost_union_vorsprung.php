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
 * Theme Boost Union VORsprung - Language pack
 *
 * @package    theme_boost_union_vorsprung
 * @copyright  2025 Danou Nauck <danou@nauck.eu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Let codechecker ignore some sniffs for this file as it is perfectly well ordered, just not alphabetically.
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment
// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder

// General.
$string['pluginname'] = 'Boost Union VORsprung';
$string['choosereadme'] = 'This plugin is just a boilerplate template one can use to develop Boost Union child themes.';
$string['configtitle'] = 'Boost Union VORsprung';
$string['settingsoverview_buc_desc'] = 'With Boost Union VORsprung, you can customize Boost Union to your own local needs.';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Inheritance';
$string['inheritanceinherit'] = 'Inherit';
$string['inheritanceduplicate'] = 'Duplicate';
$string['inheritanceoptionsexplanation'] = 'Most of the time, inheriting will be perfectly fine. However, it may happen that imperfect code is integrated into Boost Union which prevents simple SCSS inheritance for particular Boost Union features. If you encounter any issues with Boost Union features which seem not to work in Boost Union VORsprung as well, try to switch this setting to \'Dupliate\' and, if this solves the problem, report an issue on Github (see the README.md file for details how to report an issue).';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre SCSS inheritance';
$string['prescssinheritancesetting_desc'] = 'With this setting, you control if the pre SCSS code from Boost Union should be inherited or duplicated.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra SCSS inheritance';
$string['extrascssinheritancesetting_desc'] = 'With this setting, you control if the extra SCSS code from Boost Union should be inherited or duplicated.';

/**************************************************************
 * EXTENSION POINT:
 * Add your language strings for your settings here.
 *************************************************************/

// Privacy API.
$string['privacy:metadata'] = 'The Boost Union VORsprung theme does not store any personal data about any user.';
$string['poweredbyinccas'] = 'Powered by <a href="https://www.inccas.de" target="_blank">INCCAS</a>';

// Settings for the left side menu / Drawer left
$string['generalmenusettings'] = 'Menu Settings';
$string['generalmenusettings_heading'] = 'In this section you can configure the appearance of the left side drawer to your needs and define if more subsections should be displayed. And if they should be displayed, which ones should show up.';
$string['generalmenusettings_showfull'] = 'Sidedrawer accordeon';
$string['generalmenusettings_showfull_desc'] = 'Do you want to show the left sidedrawer with many more options, that can be opened up in an accordion style?';
$string['generalmenusettings_sidedrawer_menu_options'] = 'Which elements should appear in the sidebar';
$string['generalmenusettings_sidedrawer_menu_options_help'] = 'Activate all activities that are then on a whitelist. All of these activities will then be displayed with the corresponding title text in the left menu tree in the sidedrawer left.';

