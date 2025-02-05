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
 * Theme Boost Union - Core renderer
 *
 * @package    theme_boost_union
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost_union_vorsprung\output;

use context_course;
use context_system;
use moodle_url;
use stdClass;
use core\di;
use core\hook\manager as hook_manager;
use core\hook\output\before_standard_footer_html_generation;
use core\output\html_writer;
use core_block\output\block_contents;

/**
 * Extending the core_renderer interface.
 *
 * @package    theme_boost_union_vorsprung
 * @copyright
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost_union\output\core_renderer {

    public function header() {
        global $COURSE,$CFG;

        // Let's check every course that is running this theme, weather it has any of our given titles in it.
        if (stripos($COURSE->fullname,"Mathematik") !== false) {
            // We found the Mathematics course, so lets add a CSS class to the page
            $this->page->add_body_class('vorsprung-mathe');
            # $CFG->fullnamedisplay = " Mathematik-Mathematik";
        } else if (stripos($COURSE->fullname,"Physik") !== false) {
            // We found the Physics course, so lets add a CSS class to the page
            $this->page->add_body_class('vorsprung-physik');
        } else if (stripos($COURSE->fullname,"Informatik") !== false) {
            // We found the Informatics course, so lets add a CSS class to the page
            $this->page->add_body_class('vorsprung-informatik');
        } else if (stripos($COURSE->fullname,"Chemie") !== false) {
            // We found the Chemisty course, so lets add a CSS class to the page
            $this->page->add_body_class('vorsprung-chemie');
        } else if (stripos($COURSE->fullname,"Campus") !== false) {
            // We found the Campus VORsprung course, so lets add a CSS class to the page
            $this->page->add_body_class('vorsprung-campus');
        }

        return parent::header();
    }

}
