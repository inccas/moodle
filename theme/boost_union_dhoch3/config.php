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
 * Theme Boost Union DHoch3 - Theme config
 *
 * @package    theme_boost_union_dhoch3
 * @copyright  2024 Alexander Bias <bias@alexanderbias.de>
 *             based on code by Lars Bonczek
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Let codechecker ignore some sniffs for this file as we do not need a login check here..
// phpcs:disable moodle.Files.RequireLogin.Missing

// As a start, inherit the whole theme config from Boost Union.
// This move will save us from duplicating all lines from Boost Union's config.php into Boost Union DHoch3's config.php.
// This statement uses require (and not require_once) by purpose to make sure that all Boost Union settings are added
// to the $THEME object even if the Boost Union config was already included in some other place.
require($CFG->dirroot . '/theme/boost_union/config.php');

// Then, we require Boost Union DHoch3's locallib.php to make sure that it's always loaded.
require_once($CFG->dirroot . '/theme/boost_union_dhoch3/locallib.php');

// Next, we overwrite only the settings which differ between Boost Union and Boost Union DHoch3.
$THEME->name = 'boost_union_dhoch3';
$THEME->scss = function($theme) {
    return theme_boost_union_dhoch3_get_main_scss_content($theme);
};
$THEME->parents = ['boost_union', 'boost'];
$THEME->extrascsscallback = 'theme_boost_union_dhoch3_get_extra_scss';
$THEME->prescsscallback = 'theme_boost_union_dhoch3_get_pre_scss';

// We need to duplicate the rendererfactory even if it is set to the same value as in Boost Union.
// The theme_config::get_renderer() method needs it to be directly in the theme_config object.
$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// Lastly, we replicate some settings from Boost Union at runtime into Boost Union DHoch3's settings.
// This becomes necessary if Moodle core code accesses a theme setting at $this->page->theme->settings->*.
// In this case, the setting must exist in the currently active theme, otherwise it won't be found.
// While Boost Union duplicates all settings from Boost Core and does not suffer from this issue,
// it would be quite ugly to duplicate all of these settings again to Boost Union DHoch3.
// Currently, this affects these Boost Core settings:
// unaddableblocks - called from blocklib.php.
$unaddableblocks = get_config('theme_boost_union', 'unaddableblocks');
if (!empty($unaddableblocks)) {
    $THEME->settings->unaddableblocks = $unaddableblocks;
}
unset($unaddableblocks);
// SCSS - called in theme_boost_get_extra_scss.
$scss = get_config('theme_boost_union', 'scss');
if (!empty($scss)) {
    $THEME->settings->scss = $scss;
}
unset($scss);
// SCSSpre - called in theme_boost_get_pre_scss.
$scsspre = get_config('theme_boost_union', 'scsspre');
if (!empty($scsspre)) {
    $THEME->settings->scsspre = $scsspre;
}
unset($scsspre);
