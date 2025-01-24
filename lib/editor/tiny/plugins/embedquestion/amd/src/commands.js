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
 * Commands helper for the Moodle tiny_embedquestion plugin.
 *
 * @module      tiny_embedquestion/commands
 * @copyright   2024 The Open University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage} from 'editor_tiny/utils';
import {get_string as getString} from 'core/str';
import {
    component,
    buttonName,
    icon
} from './common';
import {DialogManager} from "./dialogue_manager";

/**
 * Get the setup function for the buttons.
 *
 * This is performed in an async function which ultimately returns the registration function as the
 * Tiny.AddOnManager.Add() function does not support async functions.
 *
 * @returns {function} The registration function to call within the Plugin.add function.
 */
export const getSetup = async() => {
    const [
        buttonText,
        buttonImage,
    ] = await Promise.all([
        getString('pluginname', component),
        getButtonImage('icon', component),
    ]);

    return async(editor) => {
        registerManagerCommand(editor, buttonText, buttonImage);
    };
};

/**
 * Registers a custom command for embed question in the editor.
 *
 * @async
 * @param {Object} editor - The editor instance.
 * @param {string} buttonText - The text to display as a tooltip for the button.
 * @param {Object} buttonImage - The image to be displayed on the button.
 */
const registerManagerCommand = async(editor, buttonText, buttonImage) => {
    const handleDialogManager = async() => {
        const dialog = new DialogManager(editor);
        await dialog.displayDialogue();
    };

    editor.ui.registry.addIcon(icon, buttonImage.html);

    editor.ui.registry.addMenuItem(buttonName, {
        icon: icon,
        text: buttonText,
        onAction: async() => {
            await handleDialogManager();
        }
    });
};
