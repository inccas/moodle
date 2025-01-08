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


 use RobRichards\XMLSecLibs\XMLSecurityKey;
 use \RobRichards\XMLSecLibs\XMLSecurityDSig;

 require(__DIR__ . '/../../config.php');

require_once('functions.php');
require_once('xmlseclibs.php');
require_once('response.php');
require_once('utilities.php');


global $CFG, $USER, $SESSION;
global $_POST, $_GET, $_SERVER;

$pluginconfig = get_config('auth_mo_saml');

if (!isset($_POST['SAMLResponse']) || isset($_REQUEST['option'])) {

    if (isset($_REQUEST['option']) && $_REQUEST['option'] == 'testConfig' ) {
        $sendrelaystate = 'testValidate';
        // Checking the purpose of saml request.
    }
    else {
        $sendrelaystate = $CFG->wwwroot.'/auth/mo_saml/index.php';
    }

    if(!empty($sendrelaystate))
		$relayStatePath = parse_url($sendrelaystate, PHP_URL_PATH);
    $relayStatePath = empty($relayStatePath) ? "/" : $relayStatePath;
    if(!empty($sendrelaystate))
		$relayStateQuery = parse_url($sendrelaystate, PHP_URL_QUERY);
    
	if(!empty($relayStateQuery) )
	    $sendrelaystate = $relayStatePath . '?' . $relayStateQuery;
    else
        $sendrelaystate = $relayStatePath;

    $identity_providers = json_decode($pluginconfig->identity_providers,true);
    $ssourl = $identity_providers[0]['saml_loginurl'];
    $acsurl = $CFG->wwwroot.'/auth/mo_saml/index.php';
    // Acs for the plugin.
    $issuer = (isset($pluginconfig->spentityid)&& !empty($pluginconfig->spentityid) ) ? $pluginconfig->spentityid : $CFG->wwwroot;
    // Plugin base url.
    $forceauthn = 'false';
    // Disabled forceauthn.

    if (!(isset($pluginconfig->nameidformat))) {
        $nameidformat="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress";
    } else {
        $nameidformat =$pluginconfig->nameidformat;
    }

    $samlrequest = create_authn_request($acsurl, $issuer, $forceauthn);

    $redirect = $ssourl;
    if (strpos($ssourl, '?') !== false) {
        $redirect .= '&';
    } else {
        $redirect .= '?';
    }

    if($pluginconfig->saml_request_signed == 'on'){
        $samlRequest = "SAMLRequest=" . $samlrequest . "&RelayState=" . urlencode($sendrelaystate) . '&SigAlg='. urlencode(XMLSecurityKey::RSA_SHA256);
        $param =array( 'type' => 'private');
        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $param);
        $url = $CFG->dirroot.'/auth/mo_saml/resources/sp-key.key';
        $key->loadKey($url, true);
        
        $objXmlSecDSig = new XMLSecurityDSig();
        $signature = $key->signData($samlRequest);
        $signature = base64_encode($signature);
        $redirect .= $samlRequest . '&Signature=' . urlencode($signature);
        header('Location: '.$redirect);
        exit();
    }

    $redirect .= 'SAMLRequest=' . $samlrequest . '&RelayState=' . urlencode($sendrelaystate);
    // Requested attributes are included.
    header('Location: '.$redirect);
    // Redirecting the login page to IdP login page.
    exit();
}

if ( array_key_exists('SAMLResponse', $_POST) && !empty($_POST['SAMLResponse'])) {
    $response = $_POST['SAMLResponse'];

    if (array_key_exists('RelayState', $_POST) && !empty( $_POST['RelayState'] ) && $_POST['RelayState'] != '/') {
        $relaystate = $_POST['RelayState'];
    } else {
        $relaystate = '';
    }
    $identity_providers = json_decode($pluginconfig->identity_providers,true);

    $response = base64_decode($response);
    $document = new DOMDocument();
    $document->loadXML($response);
    $samlresponsexml = $document->firstChild;
    $certfromplugin = $identity_providers[0]['x509_certificate'];
    $certfromplugin = utilities::sanitize_certificate($certfromplugin);
    $certfromplugin = trim($certfromplugin);
    $certfpfromplugin = XMLSecurityKey::getRawThumbprint($certfromplugin);
    $acsurl = $CFG->wwwroot.'/auth/mo_saml/index.php';
    $samlresponse = new saml_response_class($samlresponsexml);
    $responsesignaturedata = $samlresponse->get_signature_data();
    $assertionsignaturedata = current($samlresponse->get_assertions())->get_signature_data();
    $certfpfromplugin = iconv('UTF-8', "CP1252//IGNORE", $certfpfromplugin);
    $certfpfromplugin = preg_replace('/\s+/', '', $certfpfromplugin);
    if (!empty($responsesignaturedata)) {
        $validsignature = utilities::process_response($acsurl, $certfpfromplugin, $responsesignaturedata, $samlresponse);
        if ($validsignature === false) {
            echo 'Invalid signature in the SAML Response.';
            exit;
        }
    }
    if (!empty($assertionsignaturedata)) {
        $validsignature = utilities::process_response($acsurl, $certfpfromplugin, $assertionsignaturedata, $samlresponse);
        if ($validsignature === false) {
            echo 'Invalid signature in the SAML Assertion.';
            exit;
        }
    }
    $issuer = $identity_providers[0]['Idp_entityid'];
    $spentityid = (isset($pluginconfig->spentityid)&& !empty($pluginconfig->spentityid) ) ? $pluginconfig->spentityid : $CFG->wwwroot;
    utilities::validate_issuer_and_audience($samlresponse, $spentityid, $issuer);
    $ssoemail = current(current($samlresponse->get_assertions())->get_name_id());
    $attrs = current($samlresponse->get_assertions())->get_attributes();

    if(property_exists($pluginconfig, "roleattribute") && !empty($pluginconfig->roleattribute))
    {
        $roleattribute = $pluginconfig->roleattribute;
        $role = $attrs[$roleattribute];
    }

    $rolesfromidp = '';
    
    if(isset($roleattribute) && !empty($roleattribute))
    {
        foreach($role as $key => $value)
        {	
            $rolesfromidp = $rolesfromidp.$value.';';	
        }
    }
    
    $attrs['NameID'] = array("0" => $ssoemail);
    // Setting nameid value.
    $sessionindex = current($samlresponse->get_assertions())->get_session_index();
    $SESSION->mo_saml_attributes = $attrs;
    // Setting coming attributes in session variable.
    $SESSION->mo_saml_nameID = $ssoemail;
    $SESSION->mo_saml_sessionIndex=$sessionindex;
    if ($relaystate == 'testValidate') {
        // Checking relaystate for purpose of saml response.
        mo_saml_checkmapping($attrs, $relaystate, $sessionindex);
        // In this way we are showing saml attributes but do no login.
    } else {
        $samlplugin = get_auth_plugin('mo_saml');
		$pluginconfig = get_config('auth_mo_saml');
        $accountmatcher = "username";
        $samluser = $samlplugin->get_userinfo(null);
        $USER = auth_mo_saml_authenticate_user_login($accountmatcher, $samluser, $rolesfromidp, 'true', 'true');

        if ($USER != false) {
            $USER->loggedin = true;
            $USER->site = $CFG->wwwroot;
            $USER = get_complete_user_data('id', $USER->id);
            
            // Everywhere we can access user by its id.
            complete_user_login($USER);
            // Here user get login with its all field assigned.
            $SESSION->isSAMLSessionControlled = true;
            // Work of saml response is done here.
            if (isset($wantsurl)) {
                // Need to set wantsurl, where we redirect.
                $urltogo = clean_param($wantsurl, PARAM_URL);
            } else {
                $urltogo = $CFG->wwwroot.'/';
            }
            if (!$urltogo || $urltogo == '') {
                $urltogo = $CFG->wwwroot.'/';
            }
            unset($SESSION->wantsurl);
            redirect($urltogo, 0);
        } else {
            // This block executed only when user is not created.
            print_error('USER is not created.');
        }
    }
}