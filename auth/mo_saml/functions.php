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

 require_once('utilities.php');
 use RobRichards\XMLSecLibs\XMLSecurityKey;
 use \RobRichards\XMLSecLibs\XMLSecurityDSig;

 function create_authn_request($acsurl, $issuer, $forceauthn = 'false') { 

    $requestxmlstr = '<?xml version="1.0" encoding="UTF-8"?>' .
                    '<samlp:AuthnRequest xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol" ID="' . generate_id() .
                    '" Version="2.0" IssueInstant="' . generate_timestamp() . '"';
    if ( $forceauthn == 'true') {
        $requestxmlstr .= ' ForceAuthn="true"';
    }
    $requestxmlstr .= ' ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST" AssertionConsumerServiceURL="' . $acsurl .
                    '" ><saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">' . $issuer . '</saml:Issuer>
                    </samlp:AuthnRequest>';
    $deflatedstr = gzdeflate($requestxmlstr);
    $baseencodedstr = base64_encode($deflatedstr);
    $urlencoded = urlencode($baseencodedstr);
    return $urlencoded;
  }

function generate_id() {
    return '_' .string_to_hex(generate_random_bytes(21));
}

function generate_random_bytes($length, $fallback = true) {
    return openssl_random_pseudo_bytes($length);
}

// Value conversion method for string_to_hex.
function string_to_hex($bytes) {
    $ret = '';
    for ($i = 0; $i < strlen($bytes); $i++) {
        $ret .= sprintf('%02x', ord($bytes[$i]));
    }
    return $ret;
}

function generate_timestamp($instant = null) {
    if ($instant === null) {
        $instant = time();
    }
    return gmdate('Y-m-d\TH:i:s\Z', $instant);
}

function auth_mo_saml_authenticate_user_login($accountmatcher, $userssaml, $rolesfromidp, $samlcreate=false, $samlupdate=false) {
    global $CFG, $DB;
    $authsenabled = get_enabled_auth_plugins();
    $password = get_random_password();
    $created = false;
    // It is show user already created means false or new user means true after creating new user record.
    // Below $user array returns all posible attributes which can be update for user.
    // If user already exists then $user->id will non-zero number.
    // User- auth return way of user creation.(manual or any pluginname).
    if(isset($userssaml[$accountmatcher])) {
        $user = get_complete_user_data($accountmatcher, $userssaml[$accountmatcher]);
    }
    else{
        $user = false;
    }
    if(isset($userssaml['email'])) {
        $userwithsameemail = get_complete_user_data('email', $userssaml['email']);
    }
    else{
        $userwithsameemail = false;
    }
    $allowaccountssameemail = $CFG->allowaccountssameemail;
    $authpreventaccountcreation = $CFG->authpreventaccountcreation;

    if(!$user && $authpreventaccountcreation){
        $errormsg = "Please contact your administrator. You are not allowed to access the Site.";
        print_error($errormsg);
        return false;
    }
    
    if($userwithsameemail && $userwithsameemail->auth == 'manual' && !$allowaccountssameemail)
    {
        $errormsg = "Already A User is present with same Email Address.";
        print_error($errormsg);
        return false;
    }

    if ( ($user)) {
        if ($user->auth == 'manual') {
            $samlupdate = 'false';
        }
        $auth = empty($user->auth) ? 'manual' : $user->auth;
        // If here no authentication plugin enabled then then it will show an error.
        if ($auth == 'nologin' or !is_enabled_auth($auth)) {
            $errormsg = '[client '.getremoteaddr().'] '.$CFG->wwwroot.'  --->  DISABLED_LOGIN: '.$userssaml[$accountmatcher];
            print_error($errormsg);
            return false;
        }
    } else {
        // If account matcher queryconditions detected 1 get_field of user and id return true means user already logedin.
        if(isset($userssaml[$accountmatcher])){
            $queryconditions[$accountmatcher] = $userssaml[$accountmatcher];
        }
        else{
            $queryconditions[$accountmatcher] = NULL;
        }
        $queryconditions['deleted'] = 1;
        if ($DB->get_field('user', 'id', $queryconditions)) {
            $errormsg = '[client '.$_SERVER['REMOTE_ADDR'].'] '.  $CFG->wwwroot.'  --->  ALREADY LOGEDIN:
            '.$userssaml[$accountmatcher];
            print_error($errormsg);
            return false;
        }

        $auths = $authsenabled;
        $user = new stdClass();
        $user->id = 0;
    }
    // Selecting our mo_saml plugin for updating user data.
    $auth = 'mo_saml';
    $authplugin = get_auth_plugin($auth);
    if ( isset($userssaml[$accountmatcher]) && !$authplugin->user_login($userssaml[$accountmatcher], $password)) {
        return;
    }
    if (!$user->id) {
        // For non existing user we create account here and make $created true.
        if ($samlcreate) {
            if(isset($userssaml[$accountmatcher])){
                $user = create_user_record($userssaml[$accountmatcher], $password, $auth);
            }
            else {
                $errormsg = "The username cannot be blank. Please check the Attribute Mapping.";
                print_error($errormsg);
                return false;
            }
            assign_roles($user, $rolesfromidp);
            // Synchronizing the role of user here.
            $created = true;

            if(array_key_exists('custom_attribute_values', $userssaml))
            {
                $custom_attribute_values = $userssaml['custom_attribute_values'];
                foreach ($custom_attribute_values as $attribute=>$attribute_value) 
                {
                    if (isset($attribute_value)) 
                    {
                        $custom_field_record = $DB->get_record('user_info_field', array('shortname' => $attribute));
                        if ($DB->record_exists('user_info_data', array('userid' => $user->id, 'fieldid' => $custom_field_record->id))) 
                        {
                            $record = new stdClass();
                            $record->id = $DB->get_field('user_info_data', 'id', array('userid' => $user->id, 'fieldid' => $custom_field_record->id));
                            $record->userid = $user->id;
                            $record->fieldid = $custom_field_record->id;
                            $record->data = $attribute_value;
                            $result = $DB->update_record('user_info_data', $record);
                        } 
                        else 
                        {
                            $record = new stdClass();
                            $record->userid = $user->id;
                            $record->fieldid = $custom_field_record->id;
                            $record->data = $attribute_value;
                            $result = $DB->insert_record('user_info_data', $record);
                        }
                    }
                }
            }

            // For new user created is true.
        }
    }
    // If user is created then we check its auth type, if user auth is not intialized then we created default mo_saml type.
    // We only update mo_saml auth type user .
    // For already created user no need to sync_roles of the user.
    // For help 'https://docs.moodle.org/dev/Data_manipulation_API'.

    if ($user->id && !$created) {
        if (empty($user->auth)) {
            $queryconditions['id'] = $user->id;
            $DB->set_field('user', 'auth', $auth, $queryconditions);
            $user->auth = $auth;
        }
        if ($samlupdate && $user->auth == 'mo_saml') {

            // Updating the attributes data coming into SAML response. If $samlupdate is true. only for idp user.
            if (empty($user->firstaccess)) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'firstaccess', $user->timemodified, $queryconditions);
                $user->firstaccess = $user->timemodified;
            }
            if (!empty($userssaml['username']) && $user->username != $userssaml['username']) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'username', $userssaml['username'], $queryconditions);
                $user->username = $userssaml['username'];
            }
            if (!empty($userssaml['email'])  && $user->email != $userssaml['email']) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'email', $userssaml['email'], $queryconditions);
                $user->email = $userssaml['email'];
            }
            if (!empty($userssaml['firstname']) && $user->firstname != $userssaml['firstname']) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'firstname', $userssaml['firstname'], $queryconditions);
                $user->firstname = $userssaml['firstname'];
            }
            if (!empty($userssaml['lastname']) && $user->lastname != $userssaml['lastname']) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'lastname', $userssaml['lastname'], $queryconditions);
                $user->lastname = $userssaml['lastname'];
            }
            if (!empty($userssaml['institution']) && $user->institution != $userssaml['institution']) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'institution', $userssaml['institution'], $queryconditions);
                $user->institution = $userssaml['institution'];
            }
            if (!empty($userssaml['department']) && $user->department != $userssaml['department']) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'department', $userssaml['department'], $queryconditions);
                $user->department = $userssaml['department'];
            }
            if (!empty($userssaml['phonenumber']) && $user->phone1 != $userssaml['phonenumber']) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'phone1', $userssaml['phonenumber'], $queryconditions);
                $user->phone1 = $userssaml['phonenumber'];
            }
            if (!empty($userssaml['address']) && $user->address != $userssaml['address']) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'address', $userssaml['address'], $queryconditions);
                $user->address = $userssaml['address'];
            }
            if (!empty($userssaml['idnumber']) && $user->idnumber != $userssaml['idnumber']) {
                $queryconditions['id'] = $user->id;
                $DB->set_field('user', 'idnumber', $userssaml['idnumber'], $queryconditions);
                $user->idnumber = $userssaml['idnumber'];
            }

            if(array_key_exists('custom_attribute_values', $userssaml))
            {
                $custom_attribute_values = $userssaml['custom_attribute_values'];

                foreach ($custom_attribute_values as $attribute=>$attribute_value) {
                    $custom_field_record = $DB->get_record('user_info_field', array('shortname'=>$attribute));
                    if (isset($attribute_value)) {
                        if ($DB->record_exists('user_info_data', array('userid' => $user->id, 'fieldid' => $custom_field_record->id))) {
                            $record = new stdClass();
                            $record->id = $DB->get_field('user_info_data', 'id', array('userid' => $user->id, 'fieldid' => $custom_field_record->id));
                            $record->userid = $user->id;
                            $record->fieldid = $custom_field_record->id;
                            $record->data = $attribute_value;
                            $result = $DB->update_record('user_info_data', $record);
                        } else {
                            $record = new stdClass();
                            $record->userid = $user->id;
                            $record->fieldid = $custom_field_record->id;
                            $record->data = $attribute_value;
                            $result = $DB->insert_record('user_info_data', $record);
                        }
                    }
                }

            }

            $syscontext = context_system::instance();
            $user_roles = get_user_roles($syscontext, $user->id, false);
            foreach ( $user_roles as $role){
                $roleid = $role->roleid;
                role_unassign($roleid, $user->id, $role->contextid, '');
            }

            assign_roles($user, $rolesfromidp);

            // If you want to Update role of already exiting user. Need to Uncomment below line;
            // Authplugin sync_roles user .
        }
    }

    foreach ($authsenabled as $authe) {
        $authes = get_auth_plugin($authe);
        $authes->user_authenticated_hook($user, $userssaml[$accountmatcher], $password);
    }
    if (!$user->id && !$samlcreate) {
        print_error("New coming User ". ' "'. $userssaml[$accountmatcher] . '" '
        . "not exists in moodle and auto-create is disabled");
        return false;
    }
    return $user;
}

function assign_roles( $user, $rolesfromidp){
    global $CFG, $DB;

    $defaultrole = 'user';
    $pluginconfig = get_config('auth_mo_saml');
    if (isset($pluginconfig->defaultrolemap) && !empty($pluginconfig->defaultrolemap)) {
        $defaultrole = $pluginconfig->defaultrolemap;
    }

    if ('siteadmin' == $defaultrole) {
        $siteadmins = explode(',', $CFG->siteadmins);
        if (!in_array($user->id, $siteadmins)) {
            $siteadmins[] = $user->id;
            $newadmins = implode(',', $siteadmins);
            set_config('siteadmins', $newadmins);
        }
    }

    $idp_groups_mapping = json_decode($pluginconfig->idp_reverse_role_mapping,true);
    $moodle_roles = get_all_roles();   
        
    $roles = explode(';',$rolesfromidp);
    $mappedroles = array();
       
    $checkrole = false;

    foreach($roles as $role){
        if(!empty($idp_groups_mapping[$role])){
            $mappedroles = $idp_groups_mapping[$role];
            foreach( $mappedroles as $shortname){
                $syscontext = context_system::instance();
                $assignedrole = $DB->get_record('role', array('shortname' => $shortname), '*', MUST_EXIST);
                role_assign($assignedrole->id, $user->id, $syscontext);
                $checkrole = true;
            }
        }
    }

	if($checkrole == false && $defaultrole != 'user'){
		$syscontext = context_system::instance();
		$assignedrole = $DB->get_record('role', array('shortname' => $defaultrole), '*', MUST_EXIST);
		role_assign($assignedrole->id, $user->id, $syscontext);
    }
}

function mo_saml_show_test_result($firstnamee, $lastnamee, $useremail, $groupnamee, $attrs) {
    ob_end_clean();
    echo '<div style="font-family:Calibri;padding:0 3%;">';
    if (!empty($useremail)) {
        echo '<div style="color: #3c763d;
                background-color: #dff0d8;
                padding:2%;
                margin-bottom:20px;
                text-align:center;
                border:1px solid #AEDB9A;
                font-size:18pt;">TEST SUCCESSFUL</div>
                <div style="display:block;
                text-align:center;
                margin-bottom:4%;"><img style="width:15%;"src="'. 'images/green_check.png"></div>';
    } else {
        echo '<div style="color: #a94442;
                background-color: #f2dede;
                padding: 15px;
                margin-bottom: 20px;
                text-align:center;
                border:1px solid #E6B3B2;
                font-size:18pt;">TEST FAILED</div>
                <div style="color: #a94442;
                font-size:14pt;
                margin-bottom:20px;">WARNING: Some Attributes Did Not Match.</div>
                <div style="display:block;
                text-align:center;
                margin-bottom:4%;"><img style="width:15%;"src="'. 'images/wrong.png"></div>';
    }
        echo '<span style="font-size:14pt;">
                <b>Hello</b>, '.$useremail.'</span><br/>
                <p style="font-weight:bold;
                font-size:14pt;margin-left:1%;">ATTRIBUTES RECEIVED:</p>
                <table style="border-collapse:collapse;
                border-spacing:0;
                display:table;width:100%;
                font-size:14pt;
                background-color:#EDEDED;">
                <tr style="text-align:center;"><td style="font-weight:bold;
                border:2px solid #949090;
                padding:2%;">ATTRIBUTE NAME</td><td style="font-weight:bold;
                padding:2%;border:2px solid #949090; word-wrap:break-word;">ATTRIBUTE VALUE</td></tr>';
    if (!empty($attrs)) {
        foreach ($attrs as $key => $value) {
            echo "<tr><td style='font-weight:bold;
                        border:2px solid #949090;
                        padding:2%;'>" .$key . "</td><td style='padding:2%;
                        border:2px solid #949090;
                        word-wrap:break-word;'>" .implode("<hr/>", $value). "</td></tr>";
        }
    } else {
        echo "No Attributes Received.";
    }
    echo '</table></div>';
    echo '<div style="margin:3%;
            display:block;
            text-align:center;"><input style="padding:1%;
            width:100px;
            background: #0091CD none repeat scroll 0% 0%;
            cursor: pointer;font-size:15px;
            border-width: 1px;
            border-style: solid;
            border-radius: 3px;
            white-space: nowrap;
            box-sizing: border-box;
            border-color: #0073AA;
            box-shadow: 0px 1px 0px rgba(120, 200, 230, 0.6) inset;
            color: #FFF;"type="button" value="Done" onClick="self.close();"></div>';
    exit;
}

function mo_saml_checkmapping($attrs, $relaystate, $sessionindex) {
    try {
        $config = get_config('auth_mo_saml');

        if (!isset($config->accountmatcher)) {
            $config->accountmatcher = '';
        }
        if (!isset($config->usernamemap)) {
            $config->usernamemap = '';
        }
        if (!isset($config->emailmap)) {
            $config->emailmap = '';
        }
        if (!isset($config->defaultrolemap)) {
            $config->defaultrolemap = '';
        }

        $emailattribute = $config->emailmap;
        $usernameattribute = $config->usernamemap;

        $groupnamee = $config->defaultrolemap;
        $checkifmatchby = $config->accountmatcher;
        $useremail = '';
        $username = '';
        // Attribute mapping.
        // Check if Match or Create user is by username or email.
        if (!empty($attrs)) {
            if (!empty($firstnamee) && array_key_exists($firstnamee, $attrs)) {
                $firstnamee = $attrs[$firstnamee][0];
            } else {
                $firstnamee = '';
            }

            if (!empty($lastnamee) && array_key_exists($lastnamee, $attrs)) {
                $lastnamee = $attrs[$lastnamee][0];
            } else {
                $lastnamee = '';
            }

            if (!empty($usernameattribute) && array_key_exists($usernameattribute, $attrs)) {
                $username = $attrs[$usernameattribute][0];
            } else {
                $username = $attrs['NameID'][0];
            }
            if (!empty($emailattribute) && array_key_exists($emailattribute, $attrs)) {
                $useremail = $attrs[$emailattribute][0];
            } else {
                $useremail = $attrs['NameID'][0];
            }
            if (!empty($groupnamee) && array_key_exists($groupnamee, $attrs)) {
                $groupnamee = $attrs[$groupnamee];
            } else {
                $groupnamee = array();
            }

            if (empty($checkifmatchby)) {
                $checkifmatchby = 'email';
            }
            mo_saml_show_test_result($firstnamee, $lastnamee, $useremail, $groupnamee, $attrs);
            // It will change with version.
        }
    } catch (Exception $e) {
        echo sprintf('An error occurred while processing the SAML Response.');
        exit;
    }
}

function mo_saml_create_logout_request($nameId, $sessionIndex, $logout_url, $logout_binding_type, $referer_url){
    global $CFG;
    $config = get_config('auth_mo_saml');

    $sp_base_url = $CFG->wwwroot;

    $sp_entity_id = ! empty( get_config( 'auth_mo_saml', 'spentityid' ) ) ? get_config( 'auth_mo_saml', 'spentityid' ) : $CFG->wwwroot;

    $destination = $logout_url;
    $sendRelayState = $referer_url;

    $samlRequest = utilities::createLogoutRequest($nameId, $sp_entity_id, $destination, $sessionIndex,  $logout_binding_type);
    $redirect = $logout_url;
    if (strpos($logout_url,'?') !== false) {
        $redirect .= '&';
    } else {
        $redirect .= '?';
    }
    
    if($config->saml_request_signed != 'on'){
        
        $redirect .= 'SAMLRequest=' . $samlRequest;
        require_logout();  
        set_moodle_cookie('nobody');
        header('Location: '.$redirect);
        exit();
    }

    $samlRequest = "SAMLRequest=" . $samlRequest . "&RelayState=" . urlencode($sendRelayState) . '&SigAlg='. urlencode(XMLSecurityKey::RSA_SHA256);
    $param =array( 'type' => 'private');
    $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $param);
    $private_key = get_config('auth_mo_saml', 'private_key');
    $key->loadKey($private_key);
    
    $objXmlSecDSig = new XMLSecurityDSig();
    $signature = $key->signData($samlRequest);
    $signature = base64_encode($signature);
    $redirect .= $samlRequest . '&Signature=' . urlencode($signature);
    require_logout();  
    set_moodle_cookie('nobody');
    header('Location: '.$redirect);
    exit();
}

function createLogoutResponseAndRedirect( $logout_url, $logout_binding_type ) {
    global $CFG;
    $sp_base_url = $CFG->wwwroot;
    $logout_request = $_SESSION['mo_saml_logout_request'];
    $relay_state = $_SESSION['mo_saml_logout_relay_state'];
    unset($_SESSION['mo_saml_logout_request']);
    unset($_SESSION['mo_saml_logout_relay_state']);
    $document = new DOMDocument();
    $document->loadXML($logout_request);
    $logout_request = $document->firstChild;


    if( $logout_request->localName == 'LogoutRequest' ) {
        $logoutRequest = new SAML2SPLogoutRequest( $logout_request );
        $sp_entity_id = $CFG->wwwroot;

        $destination = $logout_url;
        $logoutResponse = utilities::createLogoutResponse($logoutRequest->getId(), $sp_entity_id, $destination, $logout_binding_type);
        if(empty($logout_binding_type) || $logout_binding_type == 'HttpRedirect') {
            $redirect = $logout_url;
            if (strpos($logout_url,'?') !== false) {
                $redirect .= '&';
            } else {
                $redirect .= '?';
            }
                $redirect .= 'SAMLResponse=' . $logoutResponse . '&RelayState=' . urlencode($relay_state);
                require_logout();  
                set_moodle_cookie('nobody');
                header('Location: '.$redirect);
                exit();
        } else {
                $base64EncodedXML = base64_encode($logoutResponse);
                require_logout();  
                set_moodle_cookie('nobody');
                utilities::postSAMLResponse($logout_url, $base64EncodedXML, $relay_state);
                exit();
        }

    }
}

function get_random_password() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphalength = strlen($alphabet) - 1;
    for ($i = 0; $i < 7; $i++) {
        $n = rand(0, $alphalength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

