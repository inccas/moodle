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
use admin_setting_heading;

/**
 * Settings for label type admin setting.
 *
 * @package    auth_mo_saml
 * @copyright  2020  miniOrange
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later, see license.txt
 */
class setting_textonly extends admin_setting_heading {
    /**
     * Returns an HTML string
     * @param mixed $data
     * @param string $query
     * @return string Returns an HTML string 
     */
    public function output_html($data, $query = '') {
        return format_admin_setting($this, $this->visiblename, '', $this->description);
    }

    public function fetch_values() {
        global $CFG;
        $config = get_config("auth_mo_saml");
        $actuallink = $_SERVER['HTTP_REFERER'];
        $idp_config_option = get_config('auth_mo_saml','idpconfigoption');
        $value = get_config('auth_mo_saml', 'idpmetadata');

        $idp_name = get_config('auth_mo_saml', 'identityname'); 
        $loginurl = get_config('auth_mo_saml', 'login_url_temp');
        $entityid = get_config('auth_mo_saml', 'entityid_temp');
        $certificate = get_config('auth_mo_saml', 'certificate_temp');
        $logouturl = get_config('auth_mo_saml', 'logout_url_temp');
        // if($idp_name == ""){
        //     redirect($actuallink, 'Please input a valid IDP Name.', null, \core\output\notification::NOTIFY_ERROR);
        //     return;
        // }

        if($idp_config_option == "Manual Configuration"){
            $saml_loginurl = get_config('auth_mo_saml', 'loginurl');
            $Idp_entityid = get_config('auth_mo_saml', 'samlissuer');
            $x509_certificate = get_config('auth_mo_saml', 'samlxcertificate');
            $saml_logouturl = get_config('auth_mo_saml', 'logouturl');

            $identity_providers = array();

            $identity_providers[0] = array();
            $identity_providers[0]['idp_name'] = $idp_name;
            $identity_providers[0]['saml_loginurl'] = $saml_loginurl;
            $identity_providers[0]['Idp_entityid'] = $Idp_entityid;
            $identity_providers[0]['x509_certificate'] = $x509_certificate;
            $identity_providers[0]['saml_logouturl'] = $saml_logouturl;

            set_config('identity_providers',json_encode($identity_providers), 'auth_mo_saml');

            // if($Idp_entityid == "") {
            //     redirect($actuallink, 'Please input a valid IDP Entity-ID.', null, \core\output\notification::NOTIFY_ERROR);
            //     return;
            // }
            // else if($saml_loginurl == ""){
            //     redirect($actuallink, 'Please input a valid SAML Login URL.', null, \core\output\notification::NOTIFY_ERROR);
            //     return;
            // }
            // else if($x509_certificate == ""){
            //     redirect($actuallink, 'Please input a valid X.509 Certificate.', null, \core\output\notification::NOTIFY_ERROR);
            //     return;
            // }

            return true;
        }

        // if($entityid == "") {
        //     redirect($actuallink, 'Please input a valid IDP Entity-ID in IDP Metadata URL or XML.', null, \core\output\notification::NOTIFY_ERROR);
        //     return;
        // }
        // else if($loginurl == ""){
        //     redirect($actuallink, 'Please input a valid SAML Login URL in IDP Metadata URL or XML.', null, \core\output\notification::NOTIFY_ERROR);
        //     return;
        // }
        // else if($certificate == ""){
        //     redirect($actuallink, 'Please input a valid X.509 Certificate in IDP Metadata URL or XML.', null, \core\output\notification::NOTIFY_ERROR);
        //     return;
        // }

        if($value != ""){
            $saml_loginurl = get_config('auth_mo_saml', 'login_url_temp');
            $Idp_entityid = get_config('auth_mo_saml', 'entityid_temp');
            $x509_certificate = get_config('auth_mo_saml', 'certificate_temp');
            $saml_logouturl = get_config('auth_mo_saml', 'logout_url_temp');

            set_config('loginurl', $saml_loginurl, 'auth_mo_saml');
            set_config('samlissuer', $Idp_entityid, 'auth_mo_saml');
            set_config('samlxcertificate', $x509_certificate, 'auth_mo_saml');
            set_config('logouturl', $saml_logouturl, 'auth_mo_saml');
            $identity_providers = array();

            $identity_providers[0] = array();
            $identity_providers[0]['idp_name'] = $idp_name;
            $identity_providers[0]['saml_loginurl'] = $saml_loginurl;
            $identity_providers[0]['Idp_entityid'] = $Idp_entityid;
            $identity_providers[0]['x509_certificate'] = $x509_certificate;
            $identity_providers[0]['saml_logouturl'] = $saml_logouturl;

            set_config('identity_providers',json_encode($identity_providers), 'auth_mo_saml');
        }

        return true;
    }
}