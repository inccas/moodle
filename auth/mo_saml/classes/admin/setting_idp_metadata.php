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
use auth_mo_saml\IDPMetadataReader;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/adminlib.php");


class setting_idp_metadata extends admin_setting_configtextarea {
    /**
     * Constructor
     */
    public function __construct() { 
        // All parameters are hardcoded because there can be only one instance:
        // When it validates, it saves extra configs, preventing this component from being reused as is.
        parent::__construct(
            'auth_mo_saml/idpmetadata',
            get_string('mo_saml_idp_metadata', 'auth_mo_saml'),
            get_string('mo_saml_idp_metadata_desc', 'auth_mo_saml'),
            '',
            PARAM_RAW,
            80,
            5
        );
    }

    public function validate($value) {
        
        $value = trim($value);
        $actuallink = $_SERVER['HTTP_REFERER']; 

        global $CFG;
        $config = get_config("auth_mo_saml");

        $idp_config_option = get_config('auth_mo_saml','idpconfigoption');


        if($idp_config_option == "Manual Configuration"){
            return true;
        }
        
        if (empty($value)) {
            redirect($actuallink, 'Please input a valid metadata file.', null, \core\output\notification::NOTIFY_ERROR);
            return;
        }

        $this->_handle_upload_metadata($value);

        return true;
    }

    function _handle_upload_metadata($value){
        if ($this->check_xml($value)) {
            $file = $value;
        } else {
            $url = filter_var( htmlspecialchars($value), FILTER_SANITIZE_URL );
            // $url=$_POST['metadata_url'];
            $response = $xml = file_get_contents($url);
            if(!is_null($response))
                $file = $response;
            else
                $file = null;
        }
        
        $this->upload_metadata($file);
    }

    public function check_xml($xml) {
        $declaration = '<?xml';

        $xml = trim($xml);
        if (substr($xml, 0, strlen($declaration)) === $declaration) {
            return true;
        }

        libxml_use_internal_errors(true);
        if (simplexml_load_string($xml)) {
            return true;
        }

        return false;
    }

    function upload_metadata($file)
    {
        global $CFG;
        $actuallink = $_SERVER['HTTP_REFERER'];
        $old_error_handler = set_error_handler(array($this,'handleXmlError'));
        $document = new DOMDocument();
        $document->loadXML($file);
        restore_error_handler();
        $first_child = $document->firstChild;

        if(!empty($first_child)) {
            $metadata = new IDPMetadataReader($document);
            $identity_providers = $metadata->getIdentityProviders();
            if(empty($identity_providers)) {

                redirect($actuallink, 'Please input a valid metadata file.', null, \core\output\notification::NOTIFY_ERROR);
                return;
            }
            foreach($identity_providers as $key => $idp){
               
                $saml_login_binding_type = 'HttpRedirect';
                $saml_login_url = '';
                if(array_key_exists('HTTP-Redirect', $idp->getLoginDetails()))
                    $saml_login_url = $idp->getLoginURL('HTTP-Redirect');
                else if(array_key_exists('HTTP-POST', $idp->getLoginDetails())) {
                    $saml_login_binding_type = 'HttpPost';
                    $saml_login_url = $idp->getLoginURL('HTTP-POST');
                }
                $saml_logout_binding_type = 'HttpRedirect';
                $saml_logout_url = '';

                if(array_key_exists('HTTP-Redirect', $idp->getLogoutDetails()))
                    $saml_logout_url = $idp->getLogoutURL('HTTP-Redirect');
                else if(array_key_exists('HTTP-POST', $idp->getLogoutDetails())){
                    $saml_logout_binding_type = 'HttpPost';
                    $saml_logout_url = $idp->getLogoutURL('HTTP-POST');
                }
                $saml_issuer = $idp->getEntityID();
                $saml_x509_certificate = $idp->getSigningCertificate();
                
                set_config('loginurl', $saml_login_url, 'auth_mo_saml');
                set_config('samlissuer', $saml_issuer, 'auth_mo_saml'); 
                set_config('samlxcertificate', trim($saml_x509_certificate[0]), 'auth_mo_saml');
                set_config('logouturl', $saml_logout_url, 'auth_mo_saml');

                set_config('login_url_temp', $saml_login_url, 'auth_mo_saml');
                set_config('entityid_temp', $saml_issuer, 'auth_mo_saml');
                set_config('certificate_temp', trim($saml_x509_certificate[0]), 'auth_mo_saml');
                set_config('logout_url_temp', $saml_logout_url, 'auth_mo_saml');
                
                return true;
                break;
            }
        } else {
            if(!empty($_POST['metadata_file'])){
                redirect($actuallink, 'Please input a valid metadata file.', null, \core\output\notification::NOTIFY_ERROR);
            } else if(!empty($_POST['metadata_url'])){
               
                redirect($actuallink, 'Please input a valid metadata file.', null, \core\output\notification::NOTIFY_ERROR);
            } else {
                redirect($actuallink, 'Please input a valid metadata file.', null, \core\output\notification::NOTIFY_ERROR);
                return;
            }
        }
    }

    function handleXmlError($errno, $errstr, $errfile, $errline) {
        if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0)) {
            return;
        } else {
            return false;
        }
    }


}