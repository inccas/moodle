<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    local_gdpr_deleteuserdata
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Dorel Manolescu <manolescu.dorel@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// We defined the web service functions to install.
$functions = array(
        'local_gdpr_deleteuserdata_single' => array(         // Web service function name.
                'classname' => 'local_gdpr_deleteuserdata_external',  // Class containing the external function.
                'methodname' => 'single',          // External function name.
                'classpath' => 'local/gdpr_deleteuserdata/externallib.php',  // File containing the class/external function.
                'description' => 'GDPR user data deletion.',    // Human readable description of the web service function.
                'type' => 'write',                  // Database rights of the web service function (read, write).
                'capabilities' => 'moodle/user:delete'
        ),
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
        'GDPR delete userdata' => array(                                                // The name of the web service.
                'functions' => array('local_gdpr_deleteuserdata_single'),
            // Web service functions of this service.
                'requiredcapability' => '',                // If set, the web service user need this capability to access.
            // Any function of this service. For example: 'some/capability:specified'.
                'restrictedusers' => 0,   // If enabled, the Moodle administrator must link some user to this service.
            // Into the administration.
                'enabled' => 1,           // If enabled, the service can be reachable on a default installation.
        )
);

