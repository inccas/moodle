<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 *  Tiny text editor library file.
 *
 * @package     tiny_embedquestion
 * @copyright   2024 The Open University
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use filter_embedquestion\form\embed_options_form;

/**
 * Server side controller used by core Fragment javascript to return a moodle form html.
 * This is used for the question selection form displayed in the embedquestion atto dialogue.
 * Reference https://docs.moodle.org/dev/Fragment.
 * Based on similar function in mod/assign/lib.php.
 *
 * @param array $args Must contain contextid
 * @return string
 */
function tiny_embedquestion_output_fragment_questionselector(array $args): string {
    global $CFG;
    require_once($CFG->dirroot . '/filter/embedquestion/filter.php');
    $context = context::instance_by_id($args['contextId']);
    $mform = new embed_options_form(null, ['context' => $context]);

    $currentvalue = $args['embedCode'];
    if ($currentvalue && preg_match(filter_embedquestion::get_filter_regexp(), $currentvalue, $matches)) {

        [$embedid, $toform] = filter_embedquestion::parse_embed_code($matches[1]);
        if ($embedid !== null) {
            $toform['questionidnumber'] = $embedid->questionidnumber;
            $toform['categoryidnumber'] = $embedid->categoryidnumber;
            // Decode iframedescription data to form.
            if (isset($toform['iframedescription'])) {
                $toform['iframedescription'] = base64_decode($toform['iframedescription']);
            }
            $mform->set_data($toform);
        }
    }

    return $mform->render();
}
