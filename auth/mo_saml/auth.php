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

 defined('MOODLE_INTERNAL') || die();
 global $CFG;

 require_once($CFG->libdir.'/authlib.php');
 require_once('functions.php');

 /**
 * This class contains authentication plugin method
 *
 * @package    mo_saml
 * @category   authentication
 * @copyright  2020 miniOrange
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_mo_saml extends auth_plugin_base {

    public function __construct() {
        $this->authtype = 'mo_saml';
        $this->config = get_config('auth_mo_saml');
    }


    public function user_login($username, $password) {
        global $SESSION;
        if (isset($SESSION->mo_saml_attributes)) {
            return true;
        }
        return false;
    }

    public function get_userinfo($username = null) {
        global $SESSION,$DB;
        $samlattributes = $SESSION->mo_saml_attributes;
        
        // Reading saml attributes from session varible assigned before.
        $nameid = $SESSION->mo_saml_nameID; // $SESSION->mo_saml_nameID has been set to NameID returned of user
        $mapping = $this->get_attributes();
        // Plugin attributes mapped values coming from get_attributes method of this class.
        if (empty($samlattributes)) {
            $username = $nameid;
            $email = $username;
        } else {
            // If saml is not empty.
            $usernamemapping = $mapping['username'];
            $mailmapping = $mapping['email'];
            if (!empty($usernamemapping) && isset($samlattributes[$usernamemapping]) && !empty($samlattributes[$usernamemapping][0])) {
                $username = $samlattributes[$usernamemapping][0];
            }
            if (!empty($mailmapping) && isset($samlattributes[$mailmapping]) && !empty($samlattributes[$mailmapping][0])) {
                $email = $samlattributes[$mailmapping][0];
            }
        }
        $user = array();
        // This array contain and return the value of attributes which are mapped.
        if (!empty($username)) {
            $user['username'] = $username;
        }
        if (!empty($email)) {
            $user['email'] = $email;
        }
        $firstnamemapping = $mapping['firstname'];
        // Plugin mapped variable firstname.
        $lastnamemapping = $mapping['lastname'];
        // Plugin mapped variable lastname.
        if (!empty($firstnamemapping) && isset($samlattributes[$firstnamemapping]) && !empty($samlattributes[$firstnamemapping][0])) {
            $user['firstname'] = $samlattributes[$firstnamemapping][0];
            // Assigning the value in user array by attribute value coming from saml response.
        }
        if (!empty($lastnamemapping) && isset($samlattributes[$lastnamemapping]) && !empty($samlattributes[$lastnamemapping][0])) {
            $user['lastname'] = $samlattributes[$lastnamemapping][0];
        }

        $institutionmapping = $mapping['institution'];
        $deptmapping = $mapping['department'];
        if (!empty($institutionmapping) && isset($samlattributes[$institutionmapping]) && !empty($samlattributes[$institutionmapping][0])) {
            $user['institution'] = $samlattributes[$institutionmapping][0];
        }
        if (!empty($deptmapping) && isset($samlattributes[$deptmapping]) && !empty($samlattributes[$deptmapping][0])) {
            $user['department'] = $samlattributes[$deptmapping][0];
        }

        $phonemapping = $mapping['phonenumber'];
        $addressmapping = $mapping['address'];
        $idnumbermapping = $mapping['idnumber'];
        if (!empty($phonemapping) && isset($samlattributes[$phonemapping]) && !empty($samlattributes[$phonemapping][0])) {
            $user['phonenumber'] = $samlattributes[$phonemapping][0];
        }

        if (!empty($addressmapping) && isset($samlattributes[$addressmapping]) && !empty($samlattributes[$addressmapping][0])) {
            $user['address'] = $samlattributes[$addressmapping][0];
        }

        if (!empty($idnumbermapping) && isset($samlattributes[$idnumbermapping]) && !empty($samlattributes[$idnumbermapping][0])) {
            $user['idnumber'] = $samlattributes[$idnumbermapping][0];
        }

        $accountmatcher = "username";

        if (empty($accountmatcher)) {
            // Saml account matcher define which attribute is responsible for account creation.
            $accountmatcher = 'username';
            // Saml matcher is email if not selected.
        }
        if (($accountmatcher == 'username' && empty($user['username']) ||
            ($accountmatcher == 'email' && empty($user['email'])))) {
            $user = false;
        }

        $custom_attributes = $DB->get_records( 'user_info_field' );
        $custom_attribute_values = array();
		foreach ( $custom_attributes as $attribute ) {
            $shortname = $attribute->shortname;
            $shortname_value = get_config('auth_mo_saml',$shortname.'map');
            if( empty( $samlattributes[$shortname_value] ) ) {
                continue;
            }
            $custom_attribute_values[$shortname] = $samlattributes[$shortname_value][0];
        }
        $user["custom_attribute_values"] = $custom_attribute_values;
        return $user;
    }

    public function get_attributes() 
    {

        if(isset($this->config->firstnamemap))
        {
            $firstName = $this->config->firstnamemap;
        }
        else
        {
            $firstName = '';
        }
        if(isset($this->config->lastnamemap))
        {
            $lastName = $this->config->lastnamemap;
        }
        else
        {
            $lastName = '';
        }
        if(isset($this->config->usernamemap))
        {
            $username = $this->config->usernamemap;
        }
        else
        {
            $username = '';
        }
        if(isset($this->config->emailmap))
        {
            $email = $this->config->emailmap;
        }
        else
        {
            $email = '';
        }
        if(isset($this->config->institutionmap))
        {
            $institution = $this->config->institutionmap;
        }
        else
        {
            $institution = '';
        }
        if(isset($this->config->deptmap))
        {
            $dept = $this->config->deptmap;
        }
        else
        {
            $dept = '';
        }
        if(isset($this->config->phonemap))
        {
            $phonenumber = $this->config->phonemap;
        }
        else
        {
            $phonenumber = '';
        }
        if(isset($this->config->addressmap))
        {
            $address = $this->config->addressmap;
        }
        else
        {
            $address = '';
        }
        if(isset($this->config->idnumbermap))
        {
            $idnumber = $this->config->idnumbermap;
        }
        else
        {
            $idnumber = '';
        }

        $attributes = array (
            "username" =>$username,
            "email" => $email,
            "firstname" => $firstName,
            "lastname" => $lastName,
            "institution" => $institution,
            "department" => $dept,
            "phonenumber" => $phonenumber,
            "address" => $address,
            "idnumber" => $idnumber
        );
        return $attributes;
    }

     // Hook for overriding behaviour of login page.

    public function loginpage_hook() 
    {
        global $CFG;
        $config = get_config('auth_mo_saml');
        $CFG->nolastloggedin = true;
        if ($config->enableloginredirect == "Yes")
        {
            if (!isset($_GET['saml_sso']) && (empty($_POST['username']) && empty($_POST['password']))) 
            {
                $initssourl = $CFG->wwwroot.'/auth/mo_saml/index.php';
                redirect($initssourl);
            }
        } 
        else 
        {
            if(isset($config->identityname))
            {
                ?>
                <script src='../auth/mo_saml/includes/js/jquery.min.js'></script>
                <script>$(document).ready(function(){
                    $('<a class = "btn btn-primary btn-block m-t-1" style="margin-left:auto;" href="<?php echo $CFG->wwwroot.'/auth/mo_saml/index.php';
                    ?>">Login with <?php echo($this->config->identityname); ?> </a>').insertAfter('#loginbtn')
                });</script>
                <?php
            }
        }
    }



    //  public function loginpage_idp_list($wantsurl) {
    //     global $CFG;

    //     $idplist = [];

    //     $config = get_config('auth_mo_saml');

    //     $idpurl = $CFG->wwwroot.'/auth/mo_saml/index.php';
    //     $idpname = $config->identityname;

    //     if ($config->enableloginredirect == "Yes") {
    //         if (!isset($_GET['saml_sso']) && (empty($_POST['username']) && empty($_POST['password']))) 
    //         {
    //             $initssourl = $CFG->wwwroot.'/auth/mo_saml/index.php';
    //             redirect($initssourl);
    //         }
    //     }
    //     else{
    //         if(!empty($idpname)){

    //             $idpiconurl = null;
            
    //             $idpicon = new pix_icon('i/user', 'Login');

    //             $idplist[] = [
    //                 'url'  => $idpurl,
    //                 'icon' => $idpicon,
    //                 'iconurl' => $idpiconurl,
    //                 'name' => $idpname,
    //             ];

    //         }
    //     }

    //     return $idplist;
    // }

    public function logoutpage_hook() {
        // $logouturl = $CFG->wwwroot.'/login/index.php?saml_sso=false';
        // require_logout();
        // set_moodle_cookie('nobody');
        // redirect($logouturl);
        
        global $CFG, $USER, $SESSION;
        $config = get_config('auth_mo_saml');
        if (isset($SESSION->isSAMLSessionControlled) && $SESSION->isSAMLSessionControlled) {
            $this->mo_saml_logout();
        }
        
    }

    public function mo_saml_logout() {
        global $CFG, $USER, $SESSION;
        $config = get_config('auth_mo_saml');

        if (!isset($config->logouturl)) {
            $config->logouturl = '';
        }

        $logout_url =  $config->logouturl;
        $logout_binding_type = 'HttpRedirect';            
        $actuallink = $CFG->wwwroot;

        if( !empty($logout_url) )  {
            
            // if(isset($_SESSION['mo_saml_logout_request'])) {
            //     self::createLogoutResponseAndRedirect($logout_url, $logout_binding_type);
            //     exit();
            // } else

            if ( $SESSION->isSAMLSessionControlled) {
                
                $nameId = $SESSION->mo_saml_nameID;
                $sessionIndex = $SESSION->mo_saml_sessionIndex;

                if(!empty($nameId)) {
                    mo_saml_create_logout_request($nameId, $sessionIndex, $logout_url, $logout_binding_type, $actuallink);
                    // unset($_SESSION['mo_saml']);
                }
            }
        }
        return $actuallink;
    }

    public function test_settings() {
        global $CFG;
        $config = get_config('auth_mo_saml');
        ?>
        <table style="width: 690px; height: 70px;">
        <tr > 
            <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Identity Providers</th>
            <th style="border: 1px solid #dddddd; text-align: left; padding: 8px;">Test Configuration</th>
        </tr>
        <tr>
            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;">
                <?php  if ($config->identityname) { echo $config->identityname;} ?> 
            </td>
            <td style="border: 1px solid #dddddd; text-align: left; padding: 8px;" >
                <input type="button" class="button button-primary button-large" name="test"
                title="You can only test your Configuration after saving your Service Provider Settings."
                onclick="show_test_window();"  value="Test configuration"/>
            </td>
        </tr>
        <script>
            function show_test_window() {
            var myWindow = window.open("<?php echo $CFG->wwwroot."/auth/mo_saml/index.php".'/?option=testConfig'; ?>",
            "TEST SAML IDP", "scrollbars=1, width=800, height=600");
            }
        </script>
    <?php
    }

}