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
 * Tiny stash Content configuration.
 *
 * @module      tiny_stash/commands
 * @copyright   2023 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage} from 'editor_tiny/utils';
import {handleAction, handleInit, handleSubmit} from './ui';
import {get_string as getString} from 'core/str';
import {
    component,
    buttonName,
    icon,
} from './common';
import {register, canManage} from './options';

export const getSetup = async() => {
    const [
        buttonText,
        buttonImage,
    ] = await Promise.all([
        getString('buttontitle', component),
        getButtonImage('icon', component),
    ]);

    return (editor) => {
        register(editor);
        // Check permissions.
        if (!canManage(editor)) {
            return;
        }

        // Register the Stash Icon.
        editor.ui.registry.addIcon(icon, buttonImage.html);

        // Register the Menu Button as a toggle.
        // This means that when highlighted over an existing Stash element it will show as toggled on.
        editor.ui.registry.addToggleButton(buttonName, {
            icon,
            tooltip: buttonText,
            onAction: () => handleAction(editor),
            onSetup: (api) => {
                api.setActive(editor.formatter.match('stash'));
                const changed = editor.formatter.formatChanged('stash', (state) => api.setActive(state));
                return () => changed.unbind();
            },
        });

        // Add the Stash Menu Item.
        // This allows it to be added to a standard menu, or a context menu.
        editor.ui.registry.addMenuItem(buttonName, {
            icon,
            text: buttonText,
            onAction: () => handleAction(editor),
        });

        editor.on("init", handleInit(editor));
        editor.on("Submit", handleSubmit(editor));
    };
};
