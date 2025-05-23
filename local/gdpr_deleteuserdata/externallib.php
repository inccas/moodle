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
 * External Web Service
 *
 * @package   local_gdpr_deleteuserdata
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Dorel Manolescu <manolescu.dorel@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/externallib.php");

class local_gdpr_deleteuserdata_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function single_parameters() {
        return new external_function_parameters(
            array(
                'parameters' => new external_single_structure(
                    array(
                        'userid' => new external_value(PARAM_INT, 'userid'),
                    )
                 )
             )
        );
    }

    public static function single_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array(
                    'message' => new external_value(PARAM_TEXT, 'message'),
                )
            )
        );
    }

    /**
     * Register a slave site to master instance
     * @return array with message
     */
    public static function single($parameters) {
        global $USER;

        $params = self::validate_parameters(self::single_parameters(), array(
            'parameters' => $parameters,
        ));

        try {
            $user = \core_user::get_user($params['parameters']['userid']);
            if ($user) {
                if ($user->id == $USER->id) { // Self deletion attempt.
                    $raspuns[] = array('message' => get_string('nopermissions', 'local_gdpr_deleteuserdata'));
                    return $raspuns;
                }
                if ($user->id != $USER->id and is_siteadmin($user) and !is_siteadmin($USER)) {
                    $raspuns[] = array('message' => get_string('nopermissions', 'local_gdpr_deleteuserdata'));
                    return $raspuns;
                }
                // Other checks (deleted, remote or guest users).
                if ($user->deleted or is_mnet_remote_user($user) or isguestuser($user->id)) { // Should we allow for deleted user?
                    $raspuns[] = array('message' => get_string('nopermissionsoruserdeleted', 'local_gdpr_deleteuserdata'));
                    return $raspuns;
                }

                $manager = new \core_privacy\manager();
                $manager->set_observer(new \tool_dataprivacy\manager_observer());

                $approvedlist = new \core_privacy\local\request\contextlist_collection($user->id);

                $contextlists = $manager->get_contexts_for_userid($user->id);

                foreach ($contextlists as $contextlist) {
                    $approvedlist->add_contextlist(new \core_privacy\local\request\approved_contextlist(
                            $user,
                            $contextlist->get_component(),
                            $contextlist->get_contextids()
                    ));
                }
                \core\session\manager::kill_user_sessions($user->id); // Do we really need this??
                $manager->delete_data_for_user($approvedlist);
                delete_user($user);
                $raspuns[] = array('message' => get_string('success', 'local_gdpr_deleteuserdata'));
            } else {
                $raspuns[] = array('message' => get_string('invaliduser', 'local_gdpr_deleteuserdata'));
            }
        } catch (Exception $e) {
            $error = get_string('error', 'error');
            $error .= ' ' . $e->getMessage();
            $raspuns[] = array('message' => $error);
        }
        return $raspuns;
    }
}