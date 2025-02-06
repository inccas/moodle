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
 * Add a new item internal
 *
 * @copyright  2023 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Fragment from 'core/fragment';
import Templates from 'core/templates';
import $ from 'jquery';
import * as Toast from 'core/toast';
import * as WebService from 'tiny_stash/webservice-calls';
import {getContextId} from 'editor_tiny/options';
import {getCourseId} from 'tiny_stash/options';
import * as DropAdd from 'tiny_stash/drop-add';
import {get_string as getString} from 'core/str';
import * as itemGetter from 'block_stash/local/datasources/items-getter';

let CourseId = 0;
let Editor = {};
let Status = 'Clear';
let Item = {};

const SAVEONLY = 'save';
const SAVEGAIN = 'gain';
const SAVELOSS = 'loss';
const SAVELOCATION = 'savelocation';

export const init = (editor, location) => {
    Editor = editor;
    let contextid = getContextId(editor);
    CourseId = getCourseId(editor);
    let courseid = CourseId;

    let areanode = document.querySelector('.tiny-stash-next-slide');
    Fragment.loadFragment('tiny_stash', 'add_item_form', contextid ,{courseid}).then((html, js) => {
        Templates.appendNodeContents(areanode, html, js);
    });

    let footerdata = {};
    if (location == 'location') {
        footerdata.location = true;
    }
    if (location == 'trade') {
        footerdata.trade = true;
    }

    Templates.render('tiny_stash/local/footers/add-item-footer', footerdata).then((html, js) => {
        let modalfooter = document.querySelector('.modal-footer');
        // Remove existing buttons.
        removeChildren(modalfooter);
        Templates.appendNodeContents(modalfooter, html, js);
        addFooterListeners(location);
    });
};

export const setStatus = (newstatus) => {
    Status = newstatus;
};

export const getStatus = () => {
    return Status;
};

export const getItem = () => {
    return Item;
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
 * Add listeners to the footer buttons.
 *
 * @param {string} location - The location of where we came from.
 */
const addFooterListeners = (location) => {
    let backbutton = document.querySelector('button[data-action="back"]');
    backbutton.addEventListener('click', (e) => {
        shiftBack(e, location);
    });
    if (location == 'location') {
        let addbutton = document.querySelector('button[data-action="add"]');
        addbutton.addEventListener('click', (e) => {
            saveItem(e, SAVEONLY);
        });
        let addlocationbutton = document.querySelector('button[data-action="add-and-location"]');
        addlocationbutton.addEventListener('click', (e) => {
            saveItem(e, SAVELOCATION);
        });
    } else {
        let addbutton = document.querySelector('button[data-action="add-gain"]');
        addbutton.addEventListener('click', (e) => {
            saveItem(e, SAVEGAIN);
        });
        let addbuttonloss = document.querySelector('button[data-action="add-loss"]');
        addbuttonloss.addEventListener('click', (e) => {
            saveItem(e, SAVELOSS);
        });
    }
};

/**
 * Shift the carousel back.
 *
 * @param {event} e - The related event.
 * @param {string} location - The location of where we came from.
 */
const shiftBack = (e, location) => {
    e.preventDefault();
    if (location == 'trade') {
        $('.carousel').carousel(3);
    } else {
        $('.carousel').carousel('prev');
    }
    $('.carousel').carousel('pause');
    // Clear this page.
    let areanode = document.querySelector('.tiny-stash-next-slide');
    removeChildren(areanode);
    // Replace footer.
    if (location == 'trade') {
        Templates.render('tiny_stash/local/footers/add-trade-footer', {}).then((html, js) => {
            let modalfooter = document.querySelector('.modal-footer');
            // Remove existing buttons.
            removeChildren(modalfooter);
            Templates.appendNodeContents(modalfooter, html, js);
        });
    } else {
        Templates.render('tiny_stash/local/footers/main-footer', {}).then((html, js) => {
            let modalfooter = document.querySelector('.modal-footer');
            // Remove existing buttons.
            removeChildren(modalfooter);
            Templates.appendNodeContents(modalfooter, html, js);
        });
    }
};

const saveItem = (event, aftersave) => {
    let formdata = document.querySelector('.tiny-stash-next-slide form');
    let submitdata = {
        itemname: formdata.querySelector('#id_name').value,
        scarceitem: formdata.querySelector('#id_scarceitem').checked,
        amountlimit: (formdata.querySelector('#id_amountlimit').value) ? formdata.querySelector('#id_amountlimit').value : 0,
        itemimage: formdata.querySelector('#id_image').value,
        description: formdata.querySelector('#id_detail_text').value,
    };
    validateForm(submitdata).then((result) => {
        if (result) {
            WebService.createItem(CourseId, submitdata).then((itemdata) => {
                itemGetter.resetCache();
                Status = 'Saved';
                if (aftersave == SAVELOCATION) {
                    addLocation(event, itemdata);
                } else if (aftersave == SAVEONLY) {
                    shiftBack(event);
                } else if (aftersave == SAVEGAIN) {
                    // Save to gain column.
                    Status = SAVEGAIN;
                    Item = itemdata;
                    shiftBack(event, 'trade');
                } else {
                    // Save to loss column.
                    Status = SAVELOSS;
                    Item = itemdata;
                    shiftBack(event, 'trade');
                }
            });
        }
    });
};

const addLocation = (e, itemdata) => {
    e.preventDefault();
    $('.carousel').carousel('next');
    $('.carousel').carousel('pause');
    let areanode = document.querySelector('.tiny-stash-next-slide');
    removeChildren(areanode);
    DropAdd.init({itemdata}, Editor);
};

const validateForm = async (formdata) => {
    window.console.log(formdata);
    await Toast.addToastRegion(document.querySelector('.tiny-stash-next-slide'));
    if (!formdata.itemname) {
        Toast.add(getString('namewarning', 'tiny_stash'), {
            type: 'warning',
            autohide: true,
            closeButton: true,
        });
        // Focus on the element.
        document.querySelector('#id_name').focus();
        return false;
    }
    let hoboy = document.querySelector('.tiny-stash-next-slide form');
    let yeppo = hoboy.querySelector('.fp-content');
    // window.console.log(yeppo.children.length);
    if (yeppo.children.length < 1) {
        Toast.add(getString('imagewarning', 'tiny_stash'), {
            type: 'warning',
            autohide: true,
            closeButton: true,
        });
        // Focus on the element.
        document.querySelector('#id_image').focus();
        return false;
    }
    return true;
};
