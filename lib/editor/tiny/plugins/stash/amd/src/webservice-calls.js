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
 * All Ajax calls to web services
 *
 * @copyright  2023 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Get all drop data.
 *
 * @param {int} contextid - The context id.
 * @returns {Promise<void>}
 */
export const getAllDropData = (contextid) => Ajax.call([{
    methodname: 'block_stash_get_all_drops',
    args: {contextid: contextid}
}])[0];

/**
 * Get all item data.
 *
 * @param {int} courseid - The course id.
 * @returns {Promise<void>}
 */
export const getAllItemData = (courseid) => Ajax.call([{
    methodname: 'block_stash_get_items',
    args: {courseid: courseid}
}])[0];

export const createItem = (courseid, itemdata) => Ajax.call([{
    methodname: 'block_stash_add_item',
    args: {
        courseid: courseid,
        itemname: itemdata.itemname,
        scarceitem: itemdata.scarceitem,
        amountlimit: itemdata.amountlimit,
        itemimage: itemdata.itemimage,
        description: itemdata.description,
    }
}])[0];

/**
 * Create the drop.
 *
 * @param {number} courseid - The course id.
 * @param {object} dropdata - The data for the drop.
 */
export const createDrop = (courseid, dropdata) => Ajax.call([{
    methodname: 'block_stash_add_drop',
    args: {
        courseid: courseid,
        itemid: dropdata.itemid,
        name: dropdata.location,
        maxpickup: dropdata.supplies,
        pickupinterval: dropdata.pickupinterval
    }
}])[0];

export const createTrade = (tradedata) => Ajax.call([{
    methodname: 'block_stash_create_trade',
    args: tradedata
}])[0];
