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

class setting_private_key extends admin_setting_configtextarea {

    /**
     * Constructor
     */
    public function __construct() { 
        parent::__construct(
            'auth_mo_saml/private_key',
            get_string('mo_saml_private_key', 'auth_mo_saml'),
            get_string('mo_saml_private_key_desc', 'auth_mo_saml'),
            '', 
            PARAM_RAW_TRIMMED,
        );
    }

    public function write_setting( $value ) {
        $value = trim($value);
        return parent::write_setting($value);
    }

    public function validate($value) {
        global $CFG;

        $db_public_cert = get_config('auth_mo_saml', 'public_certificate');
        if( @openssl_x509_check_private_key( $db_public_cert, $value ) === false ) {
            return get_string( 'mo_saml_private_key_invalid_msg', 'auth_mo_saml');
        }

        $public_cert_file_location = $CFG->dirroot."/auth/mo_saml/resources/sp-certificate.crt";
        $public_cert_from_file = file_get_contents( $public_cert_file_location );
        $private_key_file_location = $CFG->dirroot."/auth/mo_saml/resources/sp-key.key";
        
        if( ! empty( $db_public_cert ) && $db_public_cert !== $public_cert_from_file ) {
            self::mo_saml_write_into_files( $public_cert_file_location, $db_public_cert );
            self::mo_saml_write_into_files( $private_key_file_location, $value );
        }

        return true;
    }

    public static function mo_saml_write_into_files( $file, $data ) {
        $file = fopen( $file, 'w' );
        fwrite( $file, $data );
        fclose( $file );
    }

}