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
 * External services for theme mode toggling.
 *
 * @package    block_thememode
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

class block_thememode_external extends external_api {
    
    /**
     * Returns description of set_preference parameters
     * @return external_function_parameters
     */
    public static function set_preference_parameters() {
        return new external_function_parameters(
            array(
                'mode' => new external_value(PARAM_INT, 'The mode preference (0=light, 1=dark)')
            )
        );
    }

    /**
     * Set the user's theme mode preference
     * @param int $mode The mode preference (0=light, 1=dark)
     * @return array status
     */
    public static function set_preference($mode) {
        global $USER;
        
        // Parameter validation
        $params = self::validate_parameters(self::set_preference_parameters(), array('mode' => $mode));
        
        // Set user preference
        set_user_preference('thememode_darkmode', $params['mode'], $USER->id);
        
        return array(
            'status' => true
        );
    }

    /**
     * Returns description of set_preference returns
     * @return external_single_structure
     */
    public static function set_preference_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'Status: true if success')
            )
        );
    }
}
