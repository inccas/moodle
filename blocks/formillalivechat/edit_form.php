<?php
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
 * FormillaLiveChat Form Definition.
 *
 * @package    block_formillalivechat
 * @copyright  formilla.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

/**
 * FormillaLiveChat Form Class
 *
 * @copyright formilla.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_formillalivechat_edit_form extends block_edit_form {

    /**
     * Sets the edit form fields
     *
     * @param simplehtml_form $mform
     * @return none
     */
    protected function specific_definition($mform) {

        // Start block specific section in config form.
        $mform->addElement('header', 'configheader', get_string('settingheader', 'block_formillalivechat'));

        $mform->addElement('text', 'config_formillalivechatid', get_string('formillachatidtext', 'block_formillalivechat'),
          array('size' => 36, 'style' => 'width:290px;'));
        $mform->setDefault('config_formillalivechatid', '');
        $mform->setType('config_formillalivechatid', PARAM_TEXT);

        $mform->addElement('html', get_string('formillasignuphtml', 'block_formillalivechat'));
    }
}
