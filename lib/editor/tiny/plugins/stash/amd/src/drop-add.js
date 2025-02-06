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
 * The internal workings of the add location dialogue.
 *
 * @copyright  2023 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import Templates from 'core/templates';
import {getCourseId} from 'tiny_stash/options';
import {getSuggestedLocation} from 'tiny_stash/options';
import * as WebService from 'tiny_stash/webservice-calls';

let ItemsData = {};
let Editor = {};
export let SavedIndex = '';
export let Status = 'Clear';

/**
 * Initialisation function for the drop add modal.
 *
 * @param {object} itemsData - The data for the items.
 * @param {TinyMCE} editor - The editor object.
 */
export const init = (itemsData, editor) => {
    ItemsData = itemsData;
    Editor = editor;
    let areanode = document.querySelector('.tiny-stash-location');
    // format items for export to the template.
    let data = {items: []};
    for (let item of Object.entries(ItemsData)) {
        data.items.push({id: item[1].id, name: item[1].name});
    }
    if (data.items.length === 1) {
        data.oneonly = true;
    }
    data.suggestedlocation = getSuggestedLocation(editor);

    if (areanode) {
        Templates.render('tiny_stash/stash-drop-form', data).then((html, js) => {
            Templates.appendNodeContents(areanode, html, js);
        });
    }

    Templates.render('tiny_stash/local/footers/add-drop-footer', {}).then((html, js) => {
        let modalfooter = document.querySelector('.modal-footer');
        addFormListeners();
        // Remove existing buttons.
        removeChildren(modalfooter);
        Templates.appendNodeContents(modalfooter, html, js);
        addFooterListeners();
    });
};

/**
 * Add event listeners for the form.
 */
const addFormListeners = () => {
    const suppliesnode = document.querySelector('.location-supplies');
    const unlimitedflag = document.querySelector('#supplyunlimited');
    const pickupinterval = document.querySelector('.pickupinterval');
    const intervalnumber = document.querySelector('.intervalnumber');

    const updateFields = () => {
        if (suppliesnode.value > 1 || unlimitedflag.checked) {
            pickupinterval.disabled = false;
            intervalnumber.disabled = false;
        } else {
            pickupinterval.disabled = true;
            intervalnumber.disabled = true;
        }
    };

    const addListener = (node) => {
        node.addEventListener('change', updateFields);
        node.addEventListener('keyup', updateFields);
    };

    addListener(suppliesnode);
    addListener(unlimitedflag);
};

export const setStatus = (newstatus) => {
    Status = newstatus;
};

export const getStatus = () => {
    return Status;
};

/**
 * Remove all the children from a node.
 *
 * @param {node} node - The node to remove the children from.
 */
export const removeChildren = (node) => {
    while (node.firstChild) {
        node.removeChild(node.lastChild);
    }
};

/**
 * Save the drop.
 *
 * @param {event} e - The related event.
 */
const saveLocation = (e) => {
    let itemnode = document.querySelector('.stash-item');
    let itemvalue = 0;
    if (("id" in itemnode.dataset)) {
        itemvalue = itemnode.dataset.id;
    } else {
        itemvalue = itemnode.options[itemnode.selectedIndex].value;
    }

    let locationnode = document.querySelector('.location-name').value;
    let suppliesnode = document.querySelector('.location-supplies').value;
    let suppliesunlimitednode = document.querySelector('#supplyunlimited').checked;
    let intervalnumber = document.querySelector('.intervalnumber').value;
    let pickupintervalnode = document.querySelector('.pickupinterval');
    let pickupinterval = pickupintervalnode.options[pickupintervalnode.selectedIndex].value;
    if (suppliesunlimitednode) {
        suppliesnode = 0;
    }
    let realinterval = intervalnumber * pickupinterval;
    let data = {
        itemid: itemvalue,
        location: locationnode,
        supplies: suppliesnode,
        unlimited: suppliesunlimitednode,
        pickupinterval: realinterval
    };
    let courseid = getCourseId(Editor);
    WebService.createDrop(courseid, data).then((hashcode) => {
        SavedIndex = hashcode;
        Status = 'Saved';
        returnBack(e);
    });
};

/**
 * Add listeners to the footer buttons.
 */
const addFooterListeners = () => {
    let backbutton = document.querySelector('button[data-action="back"]');
    backbutton.addEventListener('click', (e) => {
        returnBack(e);
    });
    let addbutton = document.querySelector('button[data-action="add"]');
    addbutton.addEventListener('click', (e) => {
        saveLocation(e);
    });
};

/**
 * Shift the carousel back.
 *
 * @param {event} e - The related event.
 */
const returnBack = (e) => {
    e.preventDefault();
    let areanode = document.querySelector('.tiny-stash-location');
    window.console.log(areanode);
    removeChildren(areanode);

    $('.carousel').carousel(0);
    $('.carousel').carousel('pause');
    // Clear this page.
    // Replace footer.
    Templates.render('tiny_stash/local/footers/main-footer', {}).then((html, js) => {
        let modalfooter = document.querySelector('.modal-footer');
        // Remove existing buttons.
        removeChildren(modalfooter);
        Templates.appendNodeContents(modalfooter, html, js);
    });
};
