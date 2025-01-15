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


namespace local_boostnavigation\local;
/**
 * Theme Boost Union Dhoch3 - Hook: Allows plugins to add any elements to the page <head> html tag.
 *
 * @package    local_boostnavigation
 * @copyright  2025 Danou Nauck <danou@web-wizards.it>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class before_http_headers
{
    /**
     * Callback to add head elements.
     *
     * @param \core\hook\output\before_http_headers $hook
     */
    public static function callback(\core\hook\output\before_http_headers $hook): void
    {
        /**
         * Hack into the Moodle page build process as early as possible to modify $CFG by leveraging Moodle's *_before_http_headers() hook.
         */
        global $CFG;

        // Fetch config.
        $config = get_config('local_boostnavigation');

        // If the plugin setting modifymycoursesrootnodeshowfiltered (which is explained is enabled above in
        // local_boostnavigation_extend_navigation()), then it is necessary to avoid that the nav drawer course list length is limited.
        // We realize that by setting $CFG->navcourselimit to a very high value and we do this here and automatically so that the
        // admin can't forget it.
        if (isset($config->modifymycoursesrootnodeshowfiltered) && $config->modifymycoursesrootnodeshowfiltered == true
            && $CFG->navshowmycoursecategories == false) {
            $CFG->navcourselimit = 100000;
        }
    }
}
