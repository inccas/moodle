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
 * @copyright 2020  miniOrange
 * @category  document
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later, see license.txt
 */

 function xmldb_auth_mo_saml_upgrade($oldversion) {
    
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 20230912110) {
       
        $currentconfig = (array)get_config('auth_mo_saml');
        $oldconfig = $DB->get_records('config_plugins', ['plugin' => 'auth/mo_saml']);

        // Convert old config items to new.
        foreach ($oldconfig as $item) {
            $DB->delete_records('config_plugins', array('id' => $item->id));
            if($item->name == 'saml_request_signed'){
                if($item->value=='on'){
                    set_config($item->name, 'on', 'auth_mo_saml');
                }
                else{
                    set_config($item->name, 'off', 'auth_mo_saml');
                }
            }
            else if($item->name == 'enableloginredirect'){
                set_config($item->name, 'Yes', 'auth_mo_saml');
            }
            else{
                set_config($item->name, $item->value, 'auth_mo_saml');
            }    
        }

        // Overwrite with any config that was created in the new format.
        foreach ($currentconfig as $key => $value) {
            set_config($key, $value, 'auth_mo_saml');
        }

        $config = get_config('auth_mo_saml');

        if(!isset($config->spentityid))
            set_config('spentityid',$CFG->wwwroot,'auth_mo_saml');
        if(!isset($config->saml_request_signed))
            set_config('saml_request_signed','off','auth_mo_saml');
        if(!isset($config->idpconfigoption))
            set_config('idpconfigoption','Manual Configuration','auth_mo_saml');
        if(!isset($config->idpmetadata))
            set_config('idpmetadata','','auth_mo_saml');
        set_config('accountmatcher','username','auth_mo_saml');

        if(!isset($config->logouturl))
            set_config('logouturl','','auth_mo_saml');

        if(!isset($config->nameidformat))
            set_config('nameidformat','urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress','auth_mo_saml');

        if(!isset($config->usernamemap))
            set_config('usernamemap','','auth_mo_saml');

        if(!isset($config->emailmap))
            set_config('emailmap','','auth_mo_saml');
        
        if(!isset($config->firstnamemap))
            set_config('firstnamemap','','auth_mo_saml');

        if(!isset($config->lastnamemap))
            set_config('lastnamemap','','auth_mo_saml');

        if(!isset($config->institutionmap))
            set_config('institutionmap','','auth_mo_saml');

        if(!isset($config->deptmap))
            set_config('deptmap','','auth_mo_saml');

        if(!isset($config->phonemap))
            set_config('phonemap','','auth_mo_saml');

        if(!isset($config->addressmap))
            set_config('addressmap','','auth_mo_saml');

        if(!isset($config->idnumbermap))
            set_config('idnumbermap','','auth_mo_saml');

        if(!isset($config->defaultrolemap))
            set_config('defaultrolemap','user','auth_mo_saml');

        if(!isset($config->enableloginredirect))
            set_config('enableloginredirect','No','auth_mo_saml');

        

        $identity_providers = array();

        $identity_providers[0] = array();
        if(isset($config->idp_name))
            $identity_providers[0]['idp_name'] = $config->idp_name;
        if(isset($config->loginurl))
            $identity_providers[0]['saml_loginurl'] = $config->loginurl;
        if(isset($config->samlissuer))
            $identity_providers[0]['Idp_entityid'] = $config->samlissuer;
        if(isset($config->samlxcertificate))
            $identity_providers[0]['x509_certificate'] = $config->samlxcertificate;
        if(isset($config->samlxcertificate))
            $identity_providers[0]['logout_url'] = $config->logouturl;

        set_config('identity_providers',json_encode($identity_providers), 'auth_mo_saml');

        upgrade_plugin_savepoint(true, 20230912110, 'auth', 'mo_saml');
    }

    if ($oldversion < 2024101072) {
        if( ! isset( $config->public_certificate ) ) {
            $public_cert_location  = $CFG->dirroot."/auth/mo_saml/resources/sp-certificate.crt";
            $public_cert_from_file = file_get_contents( $public_cert_location );

            set_config('public_certificate', $public_cert_from_file, 'auth_mo_saml');
        }

        if( ! isset( $config->private_key ) ) {
            $private_key_location  = $CFG->dirroot."/auth/mo_saml/resources/sp-key.key";
            $private_key_from_file = file_get_contents( $private_key_location );

            set_config( 'private_key', $private_key_from_file, 'auth_mo_saml' );
        }

        upgrade_plugin_savepoint(true, 2024101072, 'auth', 'mo_saml');
    }
    
    return true;
 }