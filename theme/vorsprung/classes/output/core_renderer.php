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

namespace theme_vorsprung\output;

use stdClass;
use theme_boost_vorsprung\output\core_renderer as core_renderer_boost_vorsprung;
use theme_vorsprung\helper\mustache_staticimageurl_helper;

defined('MOODLE_INTERNAL') || die;

global $CFG;

/**
 * Renderers of theme vorsprung
 *
 * @package    theme_vorsprung
 * @copyright  2022 Jörg Hagemann, https://vorsprung.fernuni-hagen.de
 */
class core_renderer extends core_renderer_boost_vorsprung {
    use core_renderer_toolbox;
 }
