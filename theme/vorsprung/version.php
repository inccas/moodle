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
 * Vorsprung - A Theme based on Boost Vorsprung.
 *
 * @package    theme_vorsprung
 * @copyright  2022 Jörg Hagemann, joerg.hagemann@fernuni-hagen.de
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// This is the version of the plugin.
$plugin->version   = 2022072800;

// This is the version of Moodle this plugin requires = version of moodle, boost_vorsprung requires
$plugin->requires  = 2021051100;

// This is the component name of the plugin - it always starts with 'theme_'
// for themes and should be the same as the name of the folder.
$plugin->component = 'theme_vorsprung';

// This is a list of plugins, this plugin depends on (and their versions).
$plugin->dependencies = [
    'theme_boost_vorsprung' => '2021051700'
//    'atto_snippet' => '2018041000'
];