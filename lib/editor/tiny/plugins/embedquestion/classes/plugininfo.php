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

namespace tiny_embedquestion;

use context;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use context_course;
use editor_tiny\plugin_with_configuration;

/**
 * Tiny Embed question plugin for Moodle.
 *
 * @package     tiny_embedquestion
 * @copyright   2024 The Open University
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements plugin_with_buttons, plugin_with_configuration {

    public static function is_enabled(context $context, array $options, array $fpoptions,
            ?\editor_tiny\editor $editor = null): bool {
        // Users must have permission to embed content.
        // Get the course context, this is the only context we use.
        $context = context_course::instance(\filter_embedquestion\utils::get_relevant_courseid($context));
        return has_any_capability(['moodle/question:useall', 'moodle/question:usemine'], $context);
    }

    public static function get_available_buttons(): array {
        return [
            'tiny_embedquestion',
        ];
    }

    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?\editor_tiny\editor $editor = null
    ): array {
        // Get the course context, this is the only context we use.
        $context = \context_course::instance(
            \filter_embedquestion\utils::get_relevant_courseid($context));
        return [
            'relevantContextId' => $context->id,
        ];
    }
}
