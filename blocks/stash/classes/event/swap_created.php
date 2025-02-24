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
 * The block_stash swap created event
 *
 * @package    block_stash
 * @copyright  2023 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The block_stash swap created event class.
 *
 * @package    block_stash
 * @since      Block stash 2.0
 * @copyright  2023 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class swap_created extends \core\event\base {

    protected function init() {
        $this->data['objecttable'] = 'block_stash_swap';
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    public static function get_name() {
        return get_string('eventswapcreated', 'block_stash');
    }

    public function get_description() {
        return "The user with id '$this->userid' requested a trade with the user '$this->relateduserid' with a swap id
               '$this->objectid'.";
    }

    public function get_url() {
        // Maybe direct to offers? but only each user can see their own offers. Teachers and above, nope.
        // return new \moodle_url('/blocks/stash/view.php', ['id' => $this->objectid]);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }
    }

}
