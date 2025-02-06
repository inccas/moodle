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
 * Add trades to a stash.
 *
 * @copyright 2023 Adrian Greeve <adriangreeve.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Fragment from 'core/fragment';
import {getContextId} from 'editor_tiny/options';
import {getCourseId} from 'tiny_stash/options';
import Templates from 'core/templates';
import $ from 'jquery';
import * as WebService from 'tiny_stash/webservice-calls';
import * as AddItem from 'tiny_stash/additem';

let CourseId = 0;
let Status = 'ready';
export let TradeHash = '';
let Footerlistenerenabled = false;

export const init = (editor) => {
    let contextid = getContextId(editor);
    CourseId = getCourseId(editor);
    let courseid = CourseId;
    Footerlistenerenabled = false;

    let areanode = document.querySelector('.tiny-stash-trade');
    Templates.render('tiny_stash/local/nav/trade-nav', {}).then((html, js) => {
        Templates.appendNodeContents(areanode, html, js);
        let addbuttonnode = document.querySelector('.tiny-stash-add-item[data-location="trade"]');
        addbuttonnode.addEventListener('click', (e) => {
            e.preventDefault();
            $('.carousel').carousel(1);
            $('.carousel').carousel('pause');
            let location = e.currentTarget.dataset.location;
            Footerlistenerenabled = false;
            AddItem.init(editor, location);
        });
    });

    Fragment.loadFragment('tiny_stash', 'add_trade_form', contextid ,{courseid}).then((html, js) => {
        Templates.appendNodeContents(areanode, html, js);
    });

    Templates.render('tiny_stash/local/footers/add-trade-footer', {}).then((html, js) => {
        let modalfooter = document.querySelector('.modal-footer');
        // Remove existing buttons.
        removeChildren(modalfooter);
        Templates.appendNodeContents(modalfooter, html, js);
        addFooterListeners();
    });

    $('.carousel').on('slid.bs.carousel', function (e) {
        let tthing = e.relatedTarget;
        if (tthing.classList.contains('tiny-stash-trade')) {
            // This questionable looking code removes unused dialoge divs that mess with the z-index.
            // Mainly from using the filepicker, it seems that the base background z-index count get incorrectly set.
            // If we remove those before working on this page then the z-index works fine.
            let unneededmodals = document.querySelectorAll('.moodle-dialogue-base[aria-hidden="true"]');
            for (let nodes of unneededmodals) {
                nodes.remove();
            }

            addFooterListeners();
            // check additem status
            let newitem = AddItem.getItem();
            let data = {
                id: newitem.id,
                itemid: newitem.id,
                name: newitem.name,
                quantity: 1,
                imageurl: newitem.imageurl,
            };
            if (AddItem.getStatus() == 'gain') {
                // if gain add item to gain column
                Templates.render('block_stash/trade_add_item_detail', data).then((html, js) => {
                    let tablenode = document.querySelector('.block_stash_item_box[data-type="gain"]>tbody');
                    Templates.appendNodeContents(tablenode, html, js);
                    registerActions();
                });
            }
            if (AddItem.getStatus() == 'loss') {
                // if loss add item to loss column
                Templates.render('block_stash/trade_loss_item_detail', data).then((html, js) => {
                    let tablenode = document.querySelector('.block_stash_item_box[data-type="loss"]>tbody');
                    Templates.appendNodeContents(tablenode, html, js);
                    registerActions();
                });
            }
            AddItem.setStatus('Clear');
        }
    });
};

/**
 * Remove all the children from a node.
 *
 * @param {node} node - The node to remove the children from.
 */
const removeChildren = (node) => {
    while (node.firstChild) {
        node.removeChild(node.lastChild);
    }
};

export const setStatus = (newstatus) => {
    Status = newstatus;
};

export const getStatus = () => {
    return Status;
};

const registerActions = () => {
    let deleteelements = document.getElementsByClassName('block-stash-delete-item');
    for (let delement of deleteelements) {
        delement.addEventListener('click', deleteItem);
    }
};

const deleteItem = (element) => {
    let child = element.currentTarget;
    let parent = child.closest('.block-stash-trade-item');
    parent.remove();
    element.preventDefault();
};

/**
 * Add listeners to the footer buttons.
 */
const addFooterListeners = () => {
    // For some reason slid.bs.carousel gets fired more than once depending on how often you've visited this page.
    // So to avoid adding listeners more than once, we check first.
    if (Footerlistenerenabled) {
        return;
    }
    let backbutton = document.querySelector('button[data-action="back"]');
    backbutton.addEventListener('click', (e) => {
        e.preventDefault();
        moveBack(e);
    });
    let addbutton = document.querySelector('button[data-action="add"]');
    addbutton.addEventListener('click', (e) => {
        saveTrade(e);
    });
    Footerlistenerenabled = true;
};

/**
 * Shift the carousel back.
 *
 * @param {event} e - The related event.
 */
const moveBack = (e) => {
    e.preventDefault();
    $('.carousel').carousel(0);
    let slider = $('.carousel').carousel();
    slider.carousel('pause');
    // Clear this page.
    let areanode = document.querySelector('.tiny-stash-trade');
    removeChildren(areanode);
    // Replace footer.
    Templates.render('tiny_stash/local/footers/main-footer', {}).then((html, js) => {
        let modalfooter = document.querySelector('.modal-footer');
        // Footerlistenerenabled = false;
        // Remove existing buttons.
        removeChildren(modalfooter);
        Templates.appendNodeContents(modalfooter, html, js);
    });
};

const saveTrade = (e) => {
    let formdata = document.querySelector('.tiny-stash-trade form');
    let allitems = formdata.querySelectorAll('.block-stash-quantity');
    let additems = [];
    let lossitems = [];
    for (let itemnode of allitems) {
        let itemid = itemnode.name.match(/\[(\d+)\]/)[1];
        if (itemnode.attributes['name'].value.includes('add_item')) {
            additems.push({itemid, quantity: itemnode.value});
        } else {
            lossitems.push({itemid, quantity: itemnode.value});
        }
    }
    let data = {
        'courseid' : formdata.querySelector('input[name="courseid"]').value,
        'stashid' : formdata.querySelector('input[name="stashid"]').value,
        'hashcode' : formdata.querySelector('input[name="hashcode"]').value,
        'title' : formdata.querySelector('input[name="title"]').value,
        'gain' : formdata.querySelector('input[name="gain"]').value,
        'loss' : formdata.querySelector('input[name="loss"]').value,
        'additems' : additems,
        'lossitems' : lossitems,
    };
    WebService.createTrade(data).then((result) => {
        TradeHash = result;
        setStatus('Saved');
        moveBack(e);
    });
};
