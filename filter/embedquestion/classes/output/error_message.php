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

namespace filter_embedquestion\output;

use renderer_base;

/**
 * Represents an error that is shown if the question cannot be.
 *
 * @package   filter_embedquestion
 * @copyright 2018 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class error_message implements \renderable, \templatable {
    /** @var string the error message text. */
    private $message;

    /**
     * The error_message constructor.
     *
     * @param string $message the error message to display.
     */
    public function __construct(string $message) {
        $this->message = $message;
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        return ['message' => $this->message];
    }
}
