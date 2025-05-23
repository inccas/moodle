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
 * Theme functions.
 *
 * @package    theme_vorsprung
 * @copyright  2022 Jörg Hagemann, joerg.hagemann@fernuni-hagen.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function theme_vorsprung_page_init(moodle_page $page) {
    $page->requires->js_call_amd('theme_vorsprung/initjquery', 'init');
    $page->requires->js_call_amd('theme_vorsprung/anchor2collapsed', 'init');
    $page->requires->js_call_amd('theme_vorsprung/copy2clipboard', 'init');
    $page->requires->js_call_amd('theme_vorsprung/normalizeSlideHeights', 'init');
    return true;
}

function theme_vorsprung_update_settings_images($settingname) {
    global $CFG;

    // The setting name that was updated comes as a string like 's_theme_vorsprung_loginbackgroundimage'.
    // We split it on '_' characters.
    $parts = explode('_', $settingname);
    // And get the last one to get the setting name..
    $settingname = end($parts);

    // Admin settings are stored in system context.
    $syscontext = context_system::instance();
    // This is the component name the setting is stored in.
    $component = 'theme_vorsprung';

    // This is the value of the admin setting which is the filename of the uploaded file.
    $filename = get_config($component, $settingname);
    // We extract the file extension because we want to preserve it.
    $extension = substr($filename, strrpos($filename, '.') + 1);

    // This is the path in the moodle internal file system.
    $fullpath = "/{$syscontext->id}/{$component}/{$settingname}/0{$filename}";
    // Get an instance of the moodle file storage.
    $fs = get_file_storage();
    // This is an efficient way to get a file if we know the exact path.
    if ($file = $fs->get_file_by_hash(sha1($fullpath))) {
        // We got the stored file - copy it to dataroot.
        // This location matches the searched for location in theme_config::resolve_image_location.
        $pathname = $CFG->dataroot . '/pix_plugins/theme/vorsprung/' . $settingname . '.' . $extension;

        // This pattern matches any previous files with maybe different file extensions.
        $pathpattern = $CFG->dataroot . '/pix_plugins/theme/vorsprung/' . $settingname . '.*';

        // Make sure this dir exists.
        @mkdir($CFG->dataroot . '/pix_plugins/theme/vorsprung/', $CFG->directorypermissions, true);

        // Delete any existing files for this setting.
        foreach (glob($pathpattern) as $filename) {
            @unlink($filename);
        }

        // Copy the current file to this location.
        $file->copy_content_to($pathname);
    }

    // Reset theme caches.
    theme_reset_all_caches();
}

/**
 * Returns the main SCSS content located at /theme/vorsprung/scss/
 *
 * @return string
 */
function theme_vorsprung_get_main_scss_content($theme) {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/vorsprung/scss/vorsprung.scss');
}


function theme_vorsprung_import_settings() {
    \theme_vorsprung\local\util::import_theme_settings('vorsprung');
    theme_reset_all_caches();
}


