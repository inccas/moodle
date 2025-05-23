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
 * Option helper for the Moodle tiny_embedquestion plugin.
 *
 * @module      tiny_embedquestion/options
 * @copyright   2024 The Open University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {getPluginOptionName} from 'editor_tiny/options';
import {pluginName} from './common';
const relevantContextId = getPluginOptionName(pluginName, 'relevantContextId');

/**
 * Register the options for the Tiny Equation plugin.
 *
 * @param {TinyMCE} editor
 */
export const register = (editor) => {
    const registerOption = editor.options.register;

    registerOption(relevantContextId, {
        processor: 'number',
    });

};

/**
 * Get the context id for the Tiny embed question plugin.
 *
 * @param {TinyMCE} editor
 * @returns {Number} - context id
 */
export const getRelevantContextId = (editor) => editor.options.get(relevantContextId);
