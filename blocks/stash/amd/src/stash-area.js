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
 * Stash module.
 *
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/templates',
    'block_stash/item-modal',
    'block_stash/drop',
    'block_stash/trade',
    'core/pubsub'
], function($, Templates, ItemModal, Drop, Trade, PubSub) {

    /**
     * Stash class.
     *
     * @class
     * @param {Node} node The node.
     */
    function StashArea(node) {
        this._node = $(node);
        this._setUp();
    }
    StashArea.prototype._node = null;
    StashArea.prototype._userItemTemplate = 'block_stash/user_item';

    StashArea.prototype._setUp = function() {
        PubSub.subscribe('block_stash/drop/pickedup', this._dropPickedUpListener.bind(this));
        PubSub.subscribe('trade:pickedup', this._dropPickedUpListener.bind(this));

        this._setUpUserItemAreClickable();
    };

    /**
     * Add a user item to the stash.
     *
     * @param {UserItem} userItem The user item.
     * @return {Promise}
     */
    StashArea.prototype.addUserItem = function(userItem) {
        return this._renderUserItem(userItem).then(function(html, js) {
            var node = $(html),
                container = this._node.find('.item-list');
            node.data('useritem', userItem);
            this._makeUserItemNodeClickable(node);
            container.append(' ');  // A hacky separator to replicate natural rendering.
            container.append(node);
            Templates.runTemplateJS(js);
        }.bind(this));
    };

    /**
     * Whether the item is in the stash.
     *
     * @param {Number} id The item ID.
     * @return {Boolean}
     */
    StashArea.prototype.containsItem = function(id) {
        return this.getUserItemNode(id).length > 0;
    };

    /**
     * Listens to drop picked up events.
     *
     * @param {Event} e The event.
     */
    StashArea.prototype._dropPickedUpListener = function(e) {
        var userItem = e.useritem;
        if (this.containsItem(userItem.getItem().get('id'))) {
            this.updateUserItemQuantity(userItem);
        } else {
            this.addUserItem(userItem).then(function() {
                this._node.find('.empty-content').remove();
            }.bind(this));
        }
    };

    /**
     * Get the user item node.
     *
     * @param {Number} id The item ID.
     * @return {Node}
     */
    StashArea.prototype.getUserItemNode = function(id) {
        return this._node.find('.block-stash-item[data-id=' + id + ']');
    };

    /**
     * Make a user item node clickable.
     *
     * @param {Node} node The node.
     */
    StashArea.prototype._makeUserItemNodeClickable = function(node) {
        node.attr('tabindex', 0);
        node.attr('role', 'button');
        node.attr('aria-haspopup', 'true');
    };

    /**
     * Render a user item.
     *
     * @param {UserItem} userItem The user item.
     * @return {Promise}
     */
    StashArea.prototype._renderUserItem = function(userItem) {
        return Templates.render(this._userItemTemplate, {
            item: userItem.getItem().getData(),
            useritem: userItem.getData(),
        });
    };

    /**
     * Set-up process to handle items being clickable.
     */
    StashArea.prototype._setUpUserItemAreClickable = function() {
        // Make all items as clickable.
        this._node.find('.item-list .block-stash-item').each(function(i, node) {
            this._makeUserItemNodeClickable($(node));
        }.bind(this));

        // Delegate event.
        var handler = function(e) {
            var node = $(e.currentTarget),
                itemId = node.data('id');

            if (!itemId) {
                return;
            }

            ItemModal.init(itemId);
            e.preventDefault();
        };
        var selector = '.block-stash-item[aria-haspopup="true"]';
        this._node.find('.item-list').delegate(selector, 'click', handler);
        this._node.find('.item-list').delegate(selector, 'keydown', function(e) {
            if (e.keyCode != 13 && e.keyCode != 32) {
                return;
            }
            handler(e);
        });
    };

    /**
     * Update the quantity of a user item.
     *
     * @param {UserItem} userItem The user item.
     */
    StashArea.prototype.updateUserItemQuantity = function(userItem) {
        var node = this.getUserItemNode(userItem.getItem().get('id')),
            quantityNode = node.find('.item-quantity'),
            newQuantity = userItem.get('quantity'),
            quantity = parseInt(quantityNode.text(), 10);

        quantityNode.text(newQuantity);
        node.removeClass('item-quantity-' + quantity);
        node.addClass('item-quantity-' + newQuantity);
    };

    return /** @alias module:block_stash/stash */ StashArea;

});
