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
 * Mustache helper render static image urls.
 *
 * @package    core
 * @category   output
 * @copyright  2022 Jörg Hagemann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_vorsprung\helper;

use Mustache_LambdaHelper;
//use theme_vorsprung\output\core_renderer;

class mustache_staticimageurl_helper {

    /**
     * Read a static image relative path from a template and create relative url.
     *
     * @param string $args The comma separated list of args
     *                     relPath: required, the partial path relative to the theme's pix path
     * @return string the full path relative to the document root
     */
    public function staticimageurl($args, Mustache_LambdaHelper $helper): string {
        global $CFG;
        if(empty($args)) return '';
        $relPath = strtok($args, ",");
        $staticRoot = strtok(",");
        $staticRoot = $staticRoot ? '/' . trim($staticRoot) : VSP_PIXDIR;
        $relPath = $staticRoot . (common::startsWith($relPath, '/') ? '' : '/') . $relPath;
        $absPath = $CFG->dirroot . $relPath;
        $version = time();
        if (file_exists($absPath)) {
            $filemtime = @filemtime($absPath);
            if ($filemtime) {
                $version = $filemtime;
            }
        }
        return sprintf("%s?v=%s", $relPath, $version);
    }
}

