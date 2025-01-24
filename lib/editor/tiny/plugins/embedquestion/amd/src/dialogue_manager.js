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

import Templates from 'core/templates';
import {get_string as getString} from 'core/str';
import Modal from 'core/modal';
import ModalFactory from 'core/modal_factory';
import Pending from 'core/pending';
import {addIconToContainerRemoveOnCompletion} from 'core/loadingicon';
import {getRelevantContextId} from './options';
import {call as fetchMany} from 'core/ajax';
import Notification from 'core/notification';
import Fragment from 'core/fragment';

/**
 * Manages the embed question dialog.
 *
 * @module    tiny_embedquestion/dialogue_manager
 * @copyright 2024 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export const DialogManager = class {

    /** @property {Object} current Tiny MCE editor instance */
    editor = null;

    /** @property {Object} current display dialog */
    currentModal = null;

    /**
     * Dialog constructor.
     *
     * @constructor
     * @param {Object} editor current editor instance.
     */
    constructor(editor) {
        this.editor = editor;
    }

    /**
     * Displays a modal dialogue for managing embed question.
     *
     * @async
     */
    displayDialogue = async() => {
        if (typeof Modal.create !== "undefined") {
            this.currentModal = await Modal.create({
                large: true,
                title: getString('pluginname', 'tiny_embedquestion'),
                body: '<div class="tiny_embedquestion-wrap"></div>',
                show: true,
                removeOnClose: true
            });
        } else {
            // TODO Need to be remove after we no longer support 4.2 and below.
            this.currentModal = await ModalFactory.create({
                title: getString('pluginname', 'tiny_embedquestion'),
                body: '<div class="tiny_embedquestion-wrap"></div>',
                large: true,
                removeOnClose: true
            });
            this.currentModal.show();
        }

        const pendingModalReady = new Pending('tiny_embedquestion/displayDialogue');
        const body = this.currentModal.getBody()[0];
        addIconToContainerRemoveOnCompletion(
            body, pendingModalReady
        );

        let existingCode = this.getEmbedCodeFromTextSelection(this.editor);
        if (existingCode) {
            existingCode = existingCode.embedCode;
        }
        const dialogManager = this;
        Fragment.loadFragment('tiny_embedquestion', 'questionselector', getRelevantContextId(this.editor),
            {contextId: getRelevantContextId(this.editor), embedCode: existingCode}).then(function(html, js) {

            Templates.replaceNodeContents(body, html, js);
            body.querySelector('#embedqform #id_submitbutton').addEventListener('click', dialogManager.getEmbedCode);
            pendingModalReady.resolve();
            return dialogManager.currentModal;
        }).catch(Notification.exception);
    };

    /**
     * Handler for when the form button is clicked.
     * Make an AJAX request to the server to get the embed code.
     *
     * @param {Event} e - the click event.
     */
    getEmbedCode = (e) => {
        e.preventDefault();
        const iframeDescription = document.getElementById('id_iframedescription').value;
        const questionIdnumber = document.getElementById('id_questionidnumber').value;
        const dialogManager = this;
        // Required value of questionidnumber.
        // Note that the form also validates this, and deals with displaying a message to the user.
        if (!questionIdnumber) {
            return;
        }

        // Validate iframedescription.
        // If it is present, then it must have at least 3 characters and a maximum of 100 characters.
        // (It can be left blank to get the default description.)
        // Note that the form also validates this, and deals with displaying a message to the user.
        if (iframeDescription.length && (iframeDescription.length < 3 || iframeDescription.length > 100)) {
            return;
        }

        dialogManager.getEmbedCodeCall(iframeDescription, questionIdnumber).then(function(embedCode) {
            dialogManager.insertEmbedCode(embedCode);
            return dialogManager;
        }).catch(Notification.exception);
    };

    /**
     * Ajax call to get the embed code from back end.
     *
     * @param {String} iframeDescription - Description for the the embed code
     * @param {Number} questionIdnumber - question id number.
     * @returns {Promise}
     */
    getEmbedCodeCall = (iframeDescription, questionIdnumber) => {
        return fetchMany([{
            methodname: 'filter_embedquestion_get_embed_code',
            args: {
                courseid: document.querySelector('input[name=courseid]').value,
                categoryidnumber: document.getElementById('id_categoryidnumber').value,
                questionidnumber: questionIdnumber,
                iframedescription: iframeDescription,
                behaviour: document.getElementById('id_behaviour')?.value || '',
                maxmark: document.getElementById('id_maxmark')?.value || '',
                variant: document.getElementById('id_variant')?.value || '',
                correctness: document.getElementById('id_correctness')?.value || '',
                marks: document.getElementById('id_marks')?.value || '',
                markdp: document.getElementById('id_markdp')?.value || '',
                feedback: document.getElementById('id_feedback')?.value || '',
                generalfeedback: document.getElementById('id_generalfeedback')?.value || '',
                rightanswer: document.getElementById('id_rightanswer')?.value || '',
                history: document.getElementById('id_history')?.value || '',
                forcedlanguage: document.getElementById('id_forcedlanguage')?.value || ''
            }
        }])[0];
    };

    /**
     * Handles when we get the embed code from the AJAX request.
     *
     * @param {String} embedCode - the embed code to insert.
     */
    insertEmbedCode = (embedCode) => {
        const existingCode = this.getEmbedCodeFromTextSelection(this.editor);
        if (existingCode) {
            // Replace the existing code.
            const parent = this.editor.selection.getNode();
            const text = parent.textContent;
            parent.textContent = text.slice(0, existingCode.start) +
                embedCode + text.slice(existingCode.end);
        } else {
            this.editor.insertContent(embedCode);
        }
        this.currentModal.destroy();
    };

    /**
     * Get the embed  code of the current selected text,
     *
     * @param {TinyMCE} editor
     * @returns {boolean|Object}
     * return false if we can't find the match pattern.
     * return Object {start: start position of the text, end: end position of the text, embedCode: embed code of the string}
     */
    getEmbedCodeFromTextSelection = (editor) => {

        // Find the embed code in the surrounding text.
        const selection = editor.selection.getSel(),
            selectedNode = editor.selection.getNode(),
            pattern = /\{Q\{(?:(?!\}Q\}).)*\}Q\}/g;
        let text,
            returnValue = false,
            patternMatches;

        if (!selection) {
            return false;
        }
        const range = selection.rangeCount ? selection.getRangeAt(0) : null;
        if (!range) {
            return false;
        }
        text = selectedNode.textContent;
        patternMatches = text.match(pattern);

        if (!patternMatches || !patternMatches.length) {
            return false;
        }
        // This pattern matches at least once. See if this pattern matches our current position.
        // Note: We return here to break the Y.Array.find loop - any truthy return will stop any subsequent
        // searches which is the required behaviour of this function.
        for (let i = 0; i < patternMatches.length; ++i) {
            let startIndex = 0;
            while (text.indexOf(patternMatches[i], startIndex) !== -1) {
                // Determine whether the cursor is in the current occurrence of this string.
                // Note: We do not support a selection exceeding the bounds of an equation.
                const start = text.indexOf(patternMatches[i], startIndex),
                    end = start + patternMatches[i].length,
                    startMatches = (selection.anchorOffset >= start && selection.anchorOffset < end),
                    endMatches = (selection.focusOffset <= end && selection.focusOffset > start),
                    reserveStartMatches = (selection.anchorOffset <= end && selection.anchorOffset > start),
                    reserveEndMatches = (selection.focusOffset >= start && selection.focusOffset < end);
                if ((startMatches && endMatches) || (reserveStartMatches && reserveEndMatches)) {
                    // Save all data for later.
                    returnValue = {
                        // Outer match data.
                        start: start,
                        end: end,
                        embedCode: patternMatches[i]
                    };

                    // This breaks out the loop
                    break;
                }

                // Update the startIndex to match the end of the current match so that we can continue hunting
                // for further matches.
                startIndex = end;
            }
        }
        return returnValue;
    };
};
