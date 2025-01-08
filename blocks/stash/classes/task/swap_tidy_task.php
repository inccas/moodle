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

namespace block_stash\task;

class swap_tidy_task extends \core\task\scheduled_task {

    /**
     * Get the name of this task.
     */
    public function get_name() {
        return get_string('tidyswap', 'block_stash');
    }

    /**
     * Execute the tidy up task.
     */
    public function execute() {
        global $DB;

        $swapids = $DB->get_records('block_stash_swap', ['status' => \block_stash\swap::BLOCK_STASH_SWAP_COMPLETED]);

        $DB->delete_records_list('block_stash_swap_detail', 'swapid', array_keys($swapids));
        $DB->delete_records_list('block_stash_swap', 'id', array_keys($swapids));
    }
}
