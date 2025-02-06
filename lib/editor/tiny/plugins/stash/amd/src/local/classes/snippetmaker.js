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
 * Tiny stash snippet maker.
 *
 * @module      tiny_stash/local/classes/snippetmaker
 * @copyright   2023 Adrian Greeve <adriangreeve.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class SnippetMaker {

    /**
     * Constructor.
     *
     * @param {string} hash
     * @param {string} name
     */
    constructor(hash, name) {
        this.hash = hash;
        this.name = name;
    }

    /**
     * Set the text of the button or link.
     *
     * @param {string} text
     */
    setText(text) {
        this.text = text;
    }

    getImageAndText() {
        let string = '[stashdrop';
        let secret = 'secret="' + this.hash + '"';
        string += ' ' + secret;
        let buttontext = 'text="' + this.text + '"';
        string += ' ' + buttontext;
        let fullname = 'name="' + this.name + '"';
        string += ' ' + fullname;
        string += ' image]';
        return string;
    }

    getImage() {
        let string = '[stashdrop';
        let secret = 'secret="' + this.hash + '"';
        string += ' ' + secret;
        let fullname = 'name="' + this.name + '"';
        string += ' ' + fullname;
        string += ' image]';
        return string;
    }

    getText() {
        let string = '[stashdrop';
        let secret = 'secret="' + this.hash + '"';
        string += ' ' + secret;
        let buttontext = 'text="' + this.text + '"';
        string += ' ' + buttontext;
        let fullname = 'name="' + this.name + '"';
        string += ' ' + fullname;
        string += ']';
        return string;
    }
}
