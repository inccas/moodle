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


defined('MOODLE_INTERNAL') || die();

global $CFG;

class setting_role_mapping extends admin_setting_heading {

    public function __construct($name, $visiblename, $description) {
        parent::__construct($name, $visiblename, $description);
        self::role_mapping_values();
    }

    public function role_mapping_values(){
        $moodle_roles = get_all_roles();
        $pluginconfig = get_config('auth_mo_saml');

        $sysconxroles = json_decode($pluginconfig->sysconxroles,true);

        $idp_mapping = array();

        foreach ( $moodle_roles as $moodle_role ) {
            if(isset($sysconxroles[$moodle_role->id])){
                $shortname = $moodle_role->shortname;
                $val = $shortname.'map';
                if( isset($pluginconfig->$val) && !empty($pluginconfig->$val) ){
                    $idp_groups = explode(";",$pluginconfig->$val);
                    foreach($idp_groups as $idp_group){
                        if(!empty($idp_group)){
                            if (!isset($idp_mapping[$idp_group])) {
                                $idp_mapping[$idp_group] = array();
                            }

                            $idp_mapping[$idp_group][] = $shortname; 
                        }
                    }
                }
            }
        }
        set_config('idp_reverse_role_mapping',json_encode($idp_mapping), 'auth_mo_saml');
    }

}
