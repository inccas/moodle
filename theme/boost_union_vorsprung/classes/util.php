<?php

/**
 * Vorsprung theme - Utilities class.
 *
 * @package    theme_boost_union_vorsprung
 * @copyright  2025 Danou Nauck <danou@nauck.eu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost_union_vorsprung;
defined('MOODLE_INTERNAL') || die();

global $CFG;

use pix_icon;
use stdClass;
use moodle_url;

/**
 * Utility class for the local_lai_connector plugin.
 */
class util
{


    /** Check if the menu should be displayed at all
     * @param
     * @return mixed
     * @throws \vorsprung_exception
     */
    public static function show_sidedrawer_extramenu() {
        global $CFG;

        $returnboolean = ((isset($CFG->theme_boost_union_vorsprung_sidedraweraccordeon) &&
                    $CFG->theme_boost_union_vorsprung_sidedraweraccordeon == 1));
        return $returnboolean;
    }

    /** Returns an array with all trackable activities which we can use in the settings.php
     *
     */
    public static function get_sidedrawer_menuelements($withDefaults = false)
    {
        global $DB;

// The 23 standard moodle ModuleID -> ModuleName
        /**
         * 1 -> assign
         * 2 -> bigbluebuttonbn
         * 3 -> book
         * 4 -> chat
         * 5 -> choice
         * 6 -> data
         * 7 -> feedback
         * 8 -> folder
         * 9 -> forum
         * 10 -> glossary
         * 11 -> h5pactivity
         * 12 -> imscp
         * 13 -> label
         * 14 -> lesson
         * 15 -> lti
         * 16 -> page
         * 17 -> quiz
         * 18 -> resource
         * 19 -> scorm
         * 20 -> survey
         * 21 -> url
         * 22 -> wiki
         * 23 -> workshop
         * */

        $returnArray = array();
        $allMenuTypes = \theme_boost_union_vorsprung\definitions::SIDEDRAWER_ACTIVITY_TYPES;

        foreach ($allMenuTypes as $menuType => $thisAvailableMenuType) {
            if ($withDefaults == true) {
                if (isset($thisAvailableMenuType['default']) && $thisAvailableMenuType['default'] == 1) {
                    $returnArray[$menuType] = get_string('modulename', $thisAvailableMenuType['mod_name']);
                }
            } else {
                $returnArray[$menuType] = get_string('modulename', $thisAvailableMenuType['mod_name']);
            }
        }

        // Now eventually we need to add also the extra activities, that are not standard
        if ($withDefaults == false) {

            // Only go in there if we do NOT get the available defaults, to save a DB Query
            $allModules = $DB->get_records('modules');
            foreach ($allModules as $thisModule) {
                if (!array_key_exists(strtoupper($thisModule->name), $returnArray)) {

                    // Map the correct activity name to the array-key
                    $returnArray[strtoupper($thisModule->name)] = get_string('modulename', 'mod_' . $thisModule->name);
                }
            }
        }

        sort($returnArray);

        return $returnArray;
    }
}