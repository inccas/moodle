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
 * User stash exporter.
 *
 * This contains the information required to display a user's stash.
 *
 * @package    block_stash
 * @copyright  2019 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\external;
defined('MOODLE_INTERNAL') || die();

use lang_string;
use moodle_url;
use renderer_base;
use stdClass;

/**
 * User stash exporter class.
 *
 * @package    block_stash
 * @copyright  2019 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_stash_exporter extends exporter {

    protected static function define_related() {
        return [
            'context' => 'context',
            'useritems' => 'stdClass[]',
        ];
    }

    protected static function define_properties() {
        return [
            'useritems' => [
                'type' => user_item_summary_exporter::read_properties_definition(),
                'multiple' => true
            ],
        ];
    }

    protected function get_other_values(renderer_base $output) {
        $data = new stdClass();
		foreach ($this->related['useritems'] as $useritem) {
            $exporter = new user_item_summary_exporter([], ['context' => $this->related['context'],
                                                            'item' => $useritem->item,
                                                            'useritem' => $useritem->useritem]);
            $data->useritems[] = $exporter->export($output);
        }
        return (array) $data;
    }

}
