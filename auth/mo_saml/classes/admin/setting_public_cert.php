<?php
// This file is part of miniOrange moodle plugin - http://moodle.org/
//
// This Plugin is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This Program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   auth_mo_saml
 * @copyright   2020  miniOrange
 * @category    document
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later, see license.txt
 */

namespace auth_mo_saml\admin;

use admin_setting_configtextarea;

defined('MOODLE_INTERNAL') || die();

global $CFG;

class setting_public_cert extends admin_setting_configtextarea {

    /**
     * Constructor
     */
    public function __construct() { 
        parent::__construct(
            'auth_mo_saml/public_certificate',
            get_string('mo_saml_public_certificate', 'auth_mo_saml'),
            get_string('mo_saml_public_certificate_desc', 'auth_mo_saml'),
            '',
            PARAM_RAW_TRIMMED,
        );
    }

    public function write_setting( $value ) {
        $value = trim($value);
        return parent::write_setting($value);
    }

    public function validate($value) {
        $error_message = get_string( 'mo_saml_public_cert_invalid_msg', 'auth_mo_saml');
        return empty( @openssl_x509_read( $value ) ) ? $error_message : true;
    }    

}