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

 require(__DIR__ . '/../../config.php');
 require_once 'xmlseclibs.php';
 require_once('response.php');
 require_once('utilities.php');
 require_once('assertion.php');
 require_once('functions.php');
 require_once('LogoutRequest.php');
 
 global $CFG, $USER, $SESSION;
 global $_POST, $_GET, $_SERVER;
 if (isset($_GET['wantsurl'])) {
     $wantsurl = $SESSION->wantsurl = clean_param($_GET['wantsurl'], PARAM_URL);
 }
 if (empty($wantsurl) && isset($SESSION->wantsurl)) {
     $wantsurl = $SESSION->wantsurl;
 }
 $pluginconfig = get_config('auth_mo_saml');

 if ( array_key_exists('SAMLResponse', $_GET) && !empty($_GET['SAMLResponse'])) {
     // Reading saml response and extracting useful data.
     $response = $_GET['SAMLResponse'];
 
     if (array_key_exists('RelayState', $_GET) && !empty( $_GET['RelayState'] ) && $_GET['RelayState'] != '/') {
         $relaystate = $_GET['RelayState'];
     } else {
         $relaystate = '';
     }
     $response = base64_decode($response);
     $samlResponse = gzinflate($response);
     $document = new DOMDocument();
     $document->loadXML($samlResponse);
     $samlresponsexml = $document->firstChild;
 
     if($samlresponsexml->localName == 'LogoutResponse') {
         require_logout();  
         set_moodle_cookie('nobody');
 
         if(empty($relaystate))
         {
             $relaystate=$CFG->wwwroot.'/';
         }
 
         header('Location: ' . $relaystate);
         exit;
     }
}
 
 
 if ( array_key_exists('SAMLResponse', $_POST) && !empty($_POST['SAMLResponse'])) {
     // Reading saml response and extracting useful data.
     $response = $_POST['SAMLResponse'];
     
     if (array_key_exists('RelayState', $_POST) && !empty( $_POST['RelayState'] ) && $_POST['RelayState'] != '/') {
         $relaystate = $_POST['RelayState'];
     } else {
         $relaystate = '';
     }
     $response = base64_decode($response);
     $document = new DOMDocument();
     $document->loadXML($response);
     $samlresponsexml = $document->firstChild;
 
     if($samlresponsexml->localName == 'LogoutResponse') {        
         require_logout();  
         set_moodle_cookie('nobody');
 
         if(empty($relaystate))
         {
             $relaystate=$CFG->wwwroot.'/';
         }
 
         header('Location: ' . $relaystate);
         exit;
     }
 
 }
 
 if(array_key_exists('SAMLRequest', $_REQUEST) && !empty($_REQUEST['SAMLRequest'])) 
 {
     $config = get_config('auth_mo_saml');
     if (!isset($config->logouturl)) {
         $config->logouturl = '';
     }
     $logout_url = $config->logouturl;
     $logout_binding_type = '';
 
     $samlRequest = htmlspecialchars($_REQUEST['SAMLRequest']);
     $relayState = '/';
     if(array_key_exists('RelayState', $_REQUEST)) {
         $relayState = htmlspecialchars($_REQUEST['RelayState']);
     }
 
     $samlRequest = base64_decode($samlRequest);
     if(array_key_exists('SAMLRequest', $_GET) && !empty($_GET['SAMLRequest'])) {
         $samlRequest = gzinflate($samlRequest);
     }
 
     $document = new DOMDocument();
     $document->loadXML($samlRequest);
     $samlRequestXML = $document->firstChild;
     if( $samlRequestXML->localName == 'LogoutRequest' ) {
         $logoutRequest = new SAML2SPLogoutRequest( $samlRequestXML );
         if( ! session_id() || session_id() == '' || !isset($_SESSION) ) {
             session_start();
         }
         $_SESSION['mo_saml_logout_request'] = $samlRequest;
         $_SESSION['mo_saml_logout_relay_state'] = $relayState;
     
         createLogoutResponseAndRedirect($logout_url, $logout_binding_type);
     }
 }