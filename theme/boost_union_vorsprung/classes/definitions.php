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

/** This is a definition file which stores all the constants used in the theme_boost_union_vorsprung
 *
 * @package    theme_boost_union_vorsprung
 * @copyright  2025 Danou Nauck <danou@nauck.eu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_boost_union_vorsprung;

defined('MOODLE_INTERNAL') || die;

class definitions
{

    // Options for the different Menu elements being displayed in the side drawer
    // usage:
    // $allMenuTypes = \theme_boost_union_vorsprung\definitions::SIDEDRAWER_ACTIVITY_TYPES;
    const SIDEDRAWER_ACTIVITY_TYPES = array(
        'ASSIGN'  => array('mod_id' => 1, 'mod_name' => 'mod_assign', 'default' => true, 'disabled' => true ),
        'BIGBLUEBUTTONBN'  => array('mod_id' => 2, 'mod_name' => 'mod_bigbluebuttonbn', 'default' => true, 'disabled' => true ),
        'BOOK'  => array('mod_id' => 3, 'mod_name' => 'mod_book', 'default' => true, 'disabled' => true ),
        'CHAT'  => array('mod_id' => 4, 'mod_name' => 'mod_chat', 'default' => true, 'disabled' => true ),
        'CHOICE'  => array('mod_id' => 5, 'mod_name' => 'mod_choice', 'default' => true, 'disabled' => true ),
        'DATA'  => array('mod_id' => 6, 'mod_name' => 'mod_data', 'default' => true, 'disabled' => true ),
        'FEEDBACK'  => array('mod_id' => 7, 'mod_name' => 'mod_feedback', 'default' => true, 'disabled' => true ),
        'FOLDER'  => array('mod_id' => 8, 'mod_name' => 'mod_folder', 'default' => true, 'disabled' => true ),
        'FORUM'  => array('mod_id' => 9, 'mod_name' => 'mod_forum', 'default' => true, 'disabled' => true ),
        'GLOSSARY'  => array('mod_id' => 10, 'mod_name' => 'mod_glossary', 'default' => true, 'disabled' => true ),
        'H5PACTIVITY'  => array('mod_id' => 11, 'mod_name' => 'mod_h5pactivity', 'default' => true, 'disabled' => true ),
        'IMSCP'  => array('mod_id' => 12, 'mod_name' => 'mod_imscp', 'default' => true, 'disabled' => true ),
        'LABEL'  => array('mod_id' => 13, 'mod_name' => 'mod_label', 'default' => false, 'disabled' => false ),
        'LESSON'  => array('mod_id' => 14, 'mod_name' => 'mod_lesson', 'default' => true, 'disabled' => true ),
        'LTI'  => array('mod_id' => 15, 'mod_name' => 'mod_lti', 'default' => true, 'disabled' => true ),
        'PAGE'  => array('mod_id' => 16, 'mod_name' => 'mod_page', 'default' => true, 'disabled' => false ),
        'QUIZ'  => array('mod_id' => 17, 'mod_name' => 'mod_quiz', 'default' => true, 'disabled' => true ),
        'RESOURCE'  => array('mod_id' => 18, 'mod_name' => 'mod_resource', 'default' => true, 'disabled' => false ),
        'SCORM'  => array('mod_id' => 19, 'mod_name' => 'mod_scorm', 'default' => true, 'disabled' => true ),
        'SURVEY'  => array('mod_id' => 20, 'mod_name' => 'mod_survey', 'default' => true, 'disabled' => true ),
        'URL'  => array('mod_id' => 21, 'mod_name' => 'mod_url', 'default' => true, 'disabled' => false ),
        'WIKI'  => array('mod_id' => 22, 'mod_name' => 'mod_wiki', 'default' => true, 'disabled' => true ),
        'WORKSHOP'  => array('mod_id' => 23, 'mod_name' => 'mod_workshop', 'default' => true, 'disabled' => true ),
    );


    // Constants used for finding date threshold from config
    // usage: $time_constants = \theme_boost_union_vorsprung\definitions::TIME_CONSTANTS['SECONDS_PER_WEEK']
    const TIME_CONSTANTS = array(
        'SECONDS_PER_MINUTE' => 60,
        'SECONDS_PER_HOUR'   => 3600,
        'SECONDS_PER_DAY'    => 86400,
        'SECONDS_PER_WEEK'   => 604800,
        'SECONDS_PER_MONTH'  => 2592000,
        'SECONDS_PER_YEAR'   => 31536000,
    );

}