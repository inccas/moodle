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
 * Tiny stash UI.
 *
 * @module      tiny_stash/ui
 * @copyright   2023 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Templates from 'core/templates';
import {getContextId} from 'editor_tiny/options';
import {getCourseId, getShortCodes, getItemDataFromEditor, getTradeDataFromEditor} from 'tiny_stash/options';
import $ from 'jquery';
import * as DropAdd from 'tiny_stash/drop-add';
import * as AddItem from 'tiny_stash/additem';
import * as AddTrade from 'tiny_stash/addtrade';
import * as Help from 'tiny_stash/helptabs';
import SnippetMaker from 'tiny_stash/local/classes/snippetmaker';
import * as WebService from 'tiny_stash/webservice-calls';
import {get_string as getString} from 'core/str';
import { getDropDataFromEditor } from './options';

let itemsData = {};
let tradeData = {};
let Snippet = {};

const TS_ITEM_PAGE = 1;
const TS_DROP_PAGE = 2;
const TS_TRADE_PAGE = 3;

/**
 * Handle action
 * @param {TinyMCE} editor
 */
export const handleAction = (editor) => {
    displayDialogue(editor);
};

export const handleInit = editor => async () => {
    // NB: This function updates tradeData in the global scope.
    // tradeData is used below in hashcodetotrade.
    formatTradeInformation(getTradeDataFromEditor(editor), getItemDataFromEditor(editor));

    // These mappings associate shortcodes to objects which contain the necessary information
    // to render a template.
    const hashcodetoitem = Object.values(getDropDataFromEditor(editor)).reduce((c, v) => ({ ...c, [v.hashcode]: v }), {});
    const hashcodetotrade = Object.values(tradeData).reduce((c, v) => ({ ...c, [v.hashcode]: v }), {});
    const allhashcodes = [...Object.keys(hashcodetoitem), ...Object.keys(hashcodetotrade)];

    const templategetters = {
        stashdrop: data => ({
            template: "tiny_stash/item-preview",
            context: { ...data, ...(data.image ? { image: hashcodetoitem[data.secret].imageurl } : {}) }
        }),
        stashtrade: data => ({ template: "tiny_stash/trade-preview", context: { ...hashcodetotrade[data.secret] } })
    };

    const shortcodes = getShortCodes(editor).filter(shortcode => templategetters.hasOwnProperty(shortcode));
    const shortcodetopromise = shortcode => {
        const data = shortcode.matchAll(/(\w+)(?:=?"([^"]*)")?/g).reduce((c, v) => ({ ...c, ...{[v[1]]: v[2] ?? v[1]}}), {});

        // This can happen when an item has been inserted but then later deleted
        // via the stash settings. We can't render it in this case (as it's deleted)
        // so instead just return the shortcode.
        if (!allhashcodes.includes(data.secret)) {
            return Promise.resolve(shortcode);
        }

        const {template, context} = templategetters[Object.keys(data)[0]](data);
        return Templates.renderForPromise(template, {shortcode: shortcode, ...context}).then(preview => preview.html);
    };

    // Mildly obscure code warning. Normally when you use a regex to split
    // a string, the match itself is not included in the resulting array.
    //
    // From MDN docs:
    //     When found, separator is removed from the string, and the
    //     substrings are returned in an array.
    //
    // By adding the ( ) around the pattern, the match itself is not included
    // but the capture group (which is the entire matched string) is spliced
    // in to the array.
    //
    // From MDN docs:
    //     If separator is a regular expression with capturing groups, then
    //     each time separator matches, the captured groups (including any
    //     undefined results) are spliced into the output array. This
    //     behavior is specified by the regexp's Symbol.split method.
    const regex = new RegExp(`((?:<p>){0,1}\\[(?:${shortcodes.join('|')})[^\\]]*\](?:<\\/p>){0,1})`);
    const promises = editor.getContent().split(regex).map(segment => {
        const trimmed = segment.replace(/^<p>|<\/p>$/g, '');
        const isshortcode = shortcodes.reduce((c, v) => c || (trimmed.startsWith(`[${v}`) && trimmed.endsWith(']')), false);

        return isshortcode ? shortcodetopromise(trimmed) : Promise.resolve(segment);
    });

    Promise.all(promises).then(rendered => editor.setContent(rendered.join('')));
};

export const handleSubmit = editor => () => {
    const tempcontainer = document.createElement('div');
    const content = editor.getContent();
    tempcontainer.innerHTML = content;

    tempcontainer.querySelectorAll('.tiny-stash-preview').forEach(stashitem => {
        const shortcode = stashitem.querySelectorAll('.tiny-stash-shortcode')[0].textContent;
        stashitem.replaceWith(shortcode);
    });

    tempcontainer.querySelectorAll('.tiny-stash-trade-preview').forEach(trade => {
        const shortcode = trade.querySelectorAll('.tiny-stash-shortcode')[0].textContent;
        trade.replaceWith(shortcode);
    });

    // TinyMCE does this weird thing where it leaves HTML like:
    //
    // <p style="top: 8px;" data-mce-caret="after" data-mce-bogus="all">
    //
    // when the caret is present after an item with contenteditable="false"
    //
    // getContent() is __supposed__ to remove these "internal" nodes before
    // returning the string - but it doesn't seem to work all the time and
    // there's at least one bug report that seems possibly related:
    //
    // https://github.com/tinymce/tinymce/issues/8032
    //
    // All of the problematic nodes that I've observed have the data-mce-bogus
    // attribute, so we just strip those out here.
    tempcontainer.querySelectorAll('[data-mce-bogus]').forEach(node => node.parentNode.removeChild(node));

    // Only update the editor content to shortcodes when there are no validation
    // errors. If we don't do this then the content will be updated, but the form
    // won't submit and the user will be left looking at just shortcodes.
    if (editor.getElement().closest('form').querySelector('.form-control.is-invalid') === null) {
        editor.setContent(tempcontainer.innerHTML);
        editor.save();
    }
};

/**
 * Display the drop dialogue.
 *
 * @param {TinyMCE} editor
 * @returns {Promise<void>}
 */
const displayDialogue = async(editor) => {
    let contextid = getContextId(editor);
    let data = await getDropData(contextid);
    let courseid = getCourseId(editor);
    await updateItems(courseid);
    formatTradeInformation(data.trades, itemsData);

    if (Object.keys(itemsData).length == 0) {
        data.itemempty = true;
    } else if (data.items.length == 0) {
        data.dropempty = true;
    } else {
        data.normal = true;
    }

    // Need the same information for trades
    data.tradeavailable = (data.trades.length > 0);
    window.console.log(data);

    const modalPromises = await ModalFactory.create({
        title: getString('modalheading', 'tiny_stash'),
        type: ModalFactory.types.SAVE_CANCEL,
        body: Templates.render('tiny_stash/drop-code-selector', data),
        large: true,
    });

    modalPromises.show();
    const $root = await modalPromises.getRoot();
    const root = $root[0];

    $root.on(ModalEvents.hidden, () => {
        modalPromises.destroy();
    });

    $root.on(ModalEvents.bodyRendered, () => {
        Help.init();
        addTabListeners();
        addDropListener(editor);

        // Add a listener for the appearance select box.
        if (data.normal) {
            addAppearanceListener();
            addTextAndImageListener();
        }

        let additembuttons = document.querySelectorAll('.tiny-stash-add-item');
        for (let additembutton of additembuttons) {
            additembutton.addEventListener('click', (e) =>
                shiftAndMove(e, TS_ITEM_PAGE, AddItem, editor, e.currentTarget.dataset.location));
        }

        document.querySelector('.tiny-stash-add-trade').addEventListener('click', (e) =>
            shiftAndMove(e, TS_TRADE_PAGE, AddTrade, editor));

        $('.carousel').on('slide.bs.carousel', async () => {
            if (DropAdd.getStatus() == 'Saved') {
                // Reload the drop list.
                data = await getDropData(contextid);
                const zerostatenode = document.querySelector('.tiny-stash-zero-state');
                if (zerostatenode) {
                    // We now have a location we can add the drop form.
                    Templates.render('tiny_stash/local/tabs/drop-form', data).then((html, js) => {
                        const parentnode = zerostatenode.parentNode;
                        parentnode.removeChild(zerostatenode);
                        Templates.appendNodeContents(parentnode, html, js);
                        // Add back the listeners for the drop form.
                        addAppearanceListener();
                        addTextAndImageListener();
                    });
                }

                Templates.render('tiny_stash/drop-select', data).then((html, js) => {
                    let selectnode = document.querySelector('.tiny-stash-drop-select');
                    Templates.replaceNodeContents(selectnode, html, js);
                    let selectitemnode = document.querySelector('.tiny-stash-item-select');
                    for (let i=0; i< selectitemnode.options.length; i++) {
                        let option = selectitemnode.options[i];
                        if (option.dataset.hash == DropAdd.SavedIndex) {
                            option.selected = true;
                            setPreview(option.dataset.id, option.dataset.hash);
                        }
                    }
                });
                DropAdd.setStatus('Clear');
            }
            if (AddItem.getStatus() == 'Saved') {
                if (data.itemempty) {
                    if (data.items.length == 0) {
                        data.dropempty = true;
                        // Add the message about creating a location.
                        const contentnode = document.querySelector('.tiny-stash-zero-state');
                        AddItem.removeChildren(contentnode);
                        const messageheader = document.createElement('h3');
                        messageheader.innerText = await getString('locationneeded', 'tiny_stash');
                        const messagenode = document.createElement('p');
                        messagenode.innerText = await getString('nolocations', 'tiny_stash');
                        contentnode.appendChild(messageheader);
                        contentnode.appendChild(messagenode);
                        // enable the location button.
                        document.querySelector('.tiny-stash-add-drop').attributes.removeNamedItem('disabled');
                    }
                    data.itemempty = false;
                }
                // Reload the drop list.
                updateItems(courseid);
                // To be set in the hid section
                AddItem.setStatus('Clear');
            }

            if (AddTrade.getStatus() == 'Saved') {
                // Reload the trade select element.
                await updateItems(courseid);
                data = await getDropData(contextid);
                formatTradeInformation(data.trades, itemsData);

                const zerostatenode = document.querySelector('.tiny-stash-zero-state-trade');
                if (zerostatenode) {
                    // We now have a location we can add the trade form.
                    Templates.render('tiny_stash/local/tabs/trade-form', data).then((html, js) => {
                        const parentnode = zerostatenode.parentNode;
                        parentnode.removeChild(zerostatenode);
                        Templates.appendNodeContents(parentnode, html, js);
                    });
                }

                Templates.render('tiny_stash/local/selectors/trade-drop-selector', data).then((html, js) => {
                    let selectnode = document.querySelector('.tiny-stash-trade-select');
                    Templates.replaceNodeContents(selectnode, html, js);
                    let selectitemnode = document.querySelector('.tiny-stash-trade-selector');
                    for (let i=0; i< selectitemnode.options.length; i++) {
                        let option = selectitemnode.options[i];
                        if (option.dataset.hash == AddTrade.TradeHash) {
                            option.selected = true;
                            setTradePreview(option.dataset.hash);
                        }
                    }
                });
                AddTrade.setStatus('ready');
            }
        });
    });

    $root.on(ModalEvents.save, () => {
        let activetab = document.querySelector('[aria-selected="true"][data-tiny-stash]');
        const previewnodeclasssuffix = activetab.getAttribute('aria-controls') == 'items' ? 'preview' : 'trade-preview';
        const previewnode = document.querySelector('.tiny-stash-' + previewnodeclasssuffix).cloneNode(true);

        editor.execCommand('mceInsertContent', false, previewnode.outerHTML);
    });

    root.addEventListener('click', (event) => {
        let element = event.target;
        let elementtype = element.dataset.type;
        if (element.nodeName === "OPTION" && elementtype == 'item') {
            setPreview(element.dataset.id, element.dataset.hash);
        }
        if (element.nodeName === "OPTION" && elementtype == 'trade') {
            let codearea = document.getElementsByClassName('tiny-stash-trade-code');
            let dropcode = "[stashtrade secret=\"" + element.dataset.hash + "\"]";
            codearea[0].innerText = dropcode;
            setTradePreview(element.dataset.hash);
        }
    });
};

const updateItems = async (courseid) => {
    itemsData = {};
    let itemdata = await getItemData(courseid);
    if (itemdata.items) {
        itemdata.items.forEach((item) => {
            itemsData[item.id] = item;
        });
    }
};

const addTabListeners = () => {
    const tabnodes = document.querySelectorAll('.tiny-stash-tabs');
    tabnodes.forEach((tabnode) => {
        tabnode.addEventListener('click', (e) => {
            const tabname = e.currentTarget.children[0].attributes['id'].value;
            if (tabname == 'help-tab') {
                return;
            }
            const savenode = document.querySelector('button[data-action="save"]');
            if (!savenode) {
                Templates.render('tiny_stash/local/footers/main-footer', {}).then((html, js) => {
                    let modalfooter = document.querySelector('.modal-footer');
                    // Remove existing buttons.
                    AddItem.removeChildren(modalfooter);
                    Templates.appendNodeContents(modalfooter, html, js);
                });
            }
        });
    });
};

const addDropListener = (editor) => {
    document.getElementsByClassName('tiny-stash-add-drop')[0].addEventListener('click', (e) =>
        shiftAndMove(e, TS_DROP_PAGE, DropAdd, itemsData, editor));
};

const addAppearanceListener = () => {
    let selectnode = document.querySelector('.tiny-stash-appearance');
    selectnode.addEventListener('change', (e) => {
        let selectedelement = e.target.selectedOptions[0];
        if (selectedelement.value == 'text') {
            document.querySelector('.snippet-label').classList.remove('d-none');
            document.querySelector('.snippet-actiontext').classList.add('d-none');
        }
        if (selectedelement.value == 'image') {
            document.querySelector('.snippet-label').classList.add('d-none');
            document.querySelector('.snippet-actiontext').classList.add('d-none');
        }
        if (selectedelement.value == 'imageandbutton') {
            document.querySelector('.snippet-label').classList.add('d-none');
            document.querySelector('.snippet-actiontext').classList.remove('d-none');
        }

        let itemselect = document.querySelector('.tiny-stash-item-select').selectedOptions[0];
        setPreview(itemselect.dataset.id, itemselect.dataset.hash);
    });
};

const addTextAndImageListener = () => {
    let textnode = document.querySelector('input[name="actiontext"]');

    textnode.addEventListener('keyup', (e) => {
        // if no preview exit early.
        if (!document.querySelector('.block-stash-item')) {
            return;
        }

        let previewnode = document.querySelector('#item .preview');
        let buttontext = e.currentTarget.value;
        let previewbutton = previewnode.querySelector('.tiny-stash-button-preview');
        previewbutton.innerText = buttontext;
        // Update the snippet text.
        let codearea = document.querySelector('.tiny-stash-item-code');
        let shortcodearea = previewnode.querySelector('.tiny-stash-shortcode');
        Snippet.setText(buttontext);
        codearea.innerText = Snippet.getImageAndText();
        shortcodearea.innerText = Snippet.getImageAndText();
    });

    let labelnode = document.querySelector('input[name="label"]');

    labelnode.addEventListener('keyup', () => {
        // if no preview exit early.
        if (!document.querySelector('.block-stash-item')) {
            return;
        }
        let itemnode = document.querySelector('.tiny-stash-item-select');
        setPreview(itemnode.selectedOptions[0].dataset.id, itemnode.selectedOptions[0].dataset.hash);
    });

};

const setPreview = (itemid, hashcode) => {
    // Check the appearance to determine what to display and update.
    let appearanceselector = document.querySelector('.tiny-stash-appearance');
    let codearea = document.getElementsByClassName('tiny-stash-item-code');
    let buttontext = '';
    if (appearanceselector.value === 'text') {
        buttontext = document.querySelector('input[name="label"]').value;
    } else {
        buttontext = document.querySelector('input[name="actiontext"]').value;
    }
    Snippet = new SnippetMaker(hashcode, itemsData[itemid].name);
    Snippet.setText(buttontext);

    if (appearanceselector.value === 'imageandbutton') {
        codearea[0].innerText = Snippet.getImageAndText();
    } else if (appearanceselector.value === 'image') {
        codearea[0].innerText = Snippet.getImage();
    } else {
        codearea[0].innerText = Snippet.getText();
    }

    let previewnode = document.querySelector('#item .preview');
    previewnode.children.forEach(child => previewnode.removeChild(child));
    const templatedata = {
        'image': ['imageandbutton', 'image'].includes(appearanceselector.value) ? itemsData[itemid].imageurl : null,
        'text': ['imageandbutton', 'text'].includes(appearanceselector.value) ? buttontext : null,
        'shortcode': codearea[0].innerText
    };

    Templates.renderForPromise('tiny_stash/item-preview', templatedata).then(preview => {
        Templates.replaceNodeContents(previewnode, preview.html, preview.js);
    });
};

const formatTradeInformation = (tradedata, itemdata) => {
    let data = {};
    for (let tradedatum of tradedata) {
        data[tradedatum.tradeid] = {
            'tradeid': tradedatum.tradeid,
            'name': tradedatum.name,
            'gaintitle': tradedatum.gaintitle,
            'losstitle': tradedatum.losstitle,
            'hashcode': tradedatum.hashcode,
            'additems': [],
            'lossitems': []
        };

        for (let gainitem of tradedatum.additems) {
            if (gainitem) {
                data[tradedatum.tradeid].additems.push({
                    'itemid': gainitem.itemid,
                    'quantity': gainitem.quantity,
                    'name': itemdata[gainitem.itemid].name,
                    'imageurl': itemdata[gainitem.itemid].imageurl
                });
            }
        }

        for (let lossitem of tradedatum.lossitems) {
            if (lossitem) {
                data[tradedatum.tradeid].lossitems.push({
                    'itemid': lossitem.itemid,
                    'quantity': lossitem.quantity,
                    'name': itemdata[lossitem.itemid].name,
                    'imageurl': itemdata[lossitem.itemid].imageurl
                });
            }
        }
    }
    tradeData = data;
};

const setTradePreview = (hashcode) => {
    let codearea = document.getElementsByClassName('tiny-stash-trade-code');
    let dropcode = "[stashtrade secret=\"" + hashcode + "\"]";
    let selecteditem = {};

    codearea[0].innerText = dropcode;
    for (let tradeinfo of Object.entries(tradeData)) {
        if (tradeinfo[1].hashcode == hashcode) {
            selecteditem = tradeinfo[1];
            break;
        }
    }
    let tradepreviewnode = document.querySelector('#trade .preview');
    AddItem.removeChildren(tradepreviewnode);
    Templates.render('tiny_stash/trade-preview', {...selecteditem, shortcode: dropcode}).then((html, js) => {
        Templates.replaceNodeContents(tradepreviewnode, html, js);
    });
};

const shiftAndMove = (e, carousellocation, func, ...args) => {
    e.preventDefault();
    $('.carousel').carousel(carousellocation);
    $('.carousel').carousel('pause');
    func.init(...args);
};

const getDropData = async (contextid) => {
    try {
        let temp = await WebService.getAllDropData(contextid);
        return temp;
    } catch (e) {
        return {};
    }
};

const getItemData = async (courseid) => {
    try {
        let temp = await WebService.getAllItemData(courseid);
        return temp;
    } catch (e) {
        return {};
    }
};
