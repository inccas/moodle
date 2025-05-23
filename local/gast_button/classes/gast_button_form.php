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

namespace local_gast_button;
defined('MOODLE_INTERNAL') || die();


class gast_button_form extends \moodleform {

    private $submitbuttontext = 'Zu den DUO-Modulen';
    private $align = '';

    public function __construct($action, $buttontext = null, $align = '') {
        if (isset($buttontext) && !empty($buttontext)) {
            $this->submitbuttontext = $buttontext;
        }
        $this->align =strtolower($align);
        parent::__construct($action);
    }

    public function definition() {

        $mform = $this->_form;
        $mform->addElement('hidden', 'productId');
        $mform->setType('productId', PARAM_INT);
        $mform->addElement('hidden', 'clientId');
        $mform->setType('clientId', PARAM_INT);
        $mform->addElement('hidden', 'userId');
        $mform->setType('userId', PARAM_ALPHANUM);
        $mform->addElement('hidden', 'returnUrl');
        $mform->setType('returnUrl', PARAM_URL);
        $mform->addElement('hidden', 'targetUrl');
        $mform->setType('targetUrl', PARAM_URL);
//        $mform->addElement('submit', 'gastSubmit', $this->submitbuttontext);

        $btn_input = '<input type="submit" class="btn btn-primary" formtarget="_blank" name="gastSubmit" id="id_gastSubmit" value="' . $this->submitbuttontext . '" >';
        $alignclasses = [
            'center' => 'd-flex justify-content-center',
            'right' => 'd-flex justify-content-end'
        ];
        if (array_key_exists($this->align, $alignclasses)) {
            $mform->addElement('html', '<div class="'.$alignclasses[$this->align].'">'.$btn_input.'</div>');
        } else {
            $mform->addElement('html', $btn_input);
        }
    }
}
