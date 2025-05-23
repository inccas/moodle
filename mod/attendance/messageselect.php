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
 * Copied from Moodle 3.3 messageselect.php - allows sending messages to multiple users.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod_attendance
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/message/lib.php');
$id = required_param('id', PARAM_INT);
$messagebody = optional_param_array('messagebody', '', PARAM_CLEANHTML);

$send = optional_param('send', '', PARAM_BOOL);
$preview = optional_param('preview', '', PARAM_BOOL);
$edit = optional_param('edit', '', PARAM_BOOL);
$returnto = optional_param('returnto', '', PARAM_LOCALURL);
$format = optional_param('format', FORMAT_MOODLE, PARAM_INT);
$deluser = optional_param('deluser', 0, PARAM_INT);
$url = new moodle_url('/user/messageselect.php', ['id' => $id]);

if ($send !== '') {
    $url->param('send', $send);
}
if ($preview !== '') {
    $url->param('preview', $preview);
}
if ($edit !== '') {
    $url->param('edit', $edit);
}
if ($returnto !== '') {
    $url->param('returnto', $returnto);
}
if ($format !== FORMAT_MOODLE) {
    $url->param('format', $format);
}
if ($deluser !== 0) {
    $url->param('deluser', $deluser);
}
if (!empty($messagebody['text'])) {
    $messagebody = $messagebody['text'];
}
$PAGE->set_url($url);
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

require_login($course);
$coursecontext = context_course::instance($id);   // Course context.
$systemcontext = context_system::instance();   // SYSTEM context.
require_capability('moodle/course:bulkmessaging', $coursecontext);
if (empty($SESSION->emailto)) {
    $SESSION->emailto = [];
}
if (!array_key_exists($id, $SESSION->emailto)) {
    $SESSION->emailto[$id] = [];
}
if ($deluser) {
    if (array_key_exists($id, $SESSION->emailto) && array_key_exists($deluser, $SESSION->emailto[$id])) {
        unset($SESSION->emailto[$id][$deluser]);
    }
}
if (empty($SESSION->emailselect[$id]) || $messagebody) {
    $SESSION->emailselect[$id] = ['messagebody' => $messagebody];
}
$messagebody = $SESSION->emailselect[$id]['messagebody'];
$count = 0;
if ($data = data_submitted()) {
    require_sesskey();
    $userfieldsapi = \core_user\fields::for_name();
    $namefields = $userfieldsapi->get_sql('', false, '', '', false)->selects;

    foreach ($data as $k => $v) {
        if (preg_match('/^(user|teacher)(\d+)$/', $k, $m)) {
            if (!array_key_exists($m[2], $SESSION->emailto[$id])) {
                if ($user = $DB->get_record_select('user', "id = ?", [$m[2]], 'id, '.
                    $namefields . ', idnumber, email, mailformat, lastaccess, lang, '.
                    'maildisplay, auth, suspended, deleted, emailstop, username')) {
                    $SESSION->emailto[$id][$m[2]] = $user;
                    $count++;
                }
            }
        }
    }
}
if ($course->id == SITEID) {
    $strtitle = get_string('sitemessage');
    $PAGE->set_pagelayout('admin');
} else {
    $strtitle = get_string('coursemessage', 'mod_attendance');
    $PAGE->set_pagelayout('incourse');
}
$link = null;
if (has_capability('moodle/course:viewparticipants', $coursecontext) ||
    has_capability('moodle/site:viewparticipants', $systemcontext)) {
    $link = new moodle_url("/user/index.php", ['id' => $course->id]);
}
$PAGE->navbar->add(get_string('participants'), $link);
$PAGE->navbar->add($strtitle);
$PAGE->set_title($strtitle);
$PAGE->set_heading($strtitle);
echo $OUTPUT->header();

if ($count) {
    if ($count == 1) {
        $heading = get_string('addedrecip', 'mod_attendance', $count);
    } else {
        $heading = get_string('addedrecips', 'mod_attendance', $count);
    }
    echo $OUTPUT->heading($heading);
}
if (!empty($messagebody) && !$edit && !$deluser && ($preview || $send)) {
    require_sesskey();
    if (count($SESSION->emailto[$id])) {
        if (!empty($preview)) {
            echo '<form method="post" action="messageselect.php" style="margin: 0 20px;">
<input type="hidden" name="returnto" value="'.s($returnto).'" />
<input type="hidden" name="id" value="'.$id.'" />
<input type="hidden" name="format" value="'.$format.'" />
<input type="hidden" name="sesskey" value="' . sesskey() . '" />
';
            echo "<h3>".get_string('previewhtml', 'mod_attendance')."</h3>";
            echo "<div class=\"messagepreview\">\n".format_text($messagebody, $format)."\n</div>\n";
            echo '<p align="center"><input type="submit" name="send" value="'.get_string('sendmessage', 'message').'" />'."\n";
            echo '<input type="submit" name="edit" value="'.get_string('update').'" /></p>';
            echo "\n</form>";
        } else if (!empty($send)) {
            $fails = [];
            foreach ($SESSION->emailto[$id] as $user) {
                if (!message_post_message($USER, $user, $messagebody, $format)) {
                    $user->fullname = fullname($user);
                    $fails[] = get_string('messagedselecteduserfailed', 'moodle', $user);
                };
            }
            if (empty($fails)) {
                echo $OUTPUT->heading(get_string('messagedselectedusers'));
                unset($SESSION->emailto[$id]);
                unset($SESSION->emailselect[$id]);
            } else {
                echo $OUTPUT->heading(get_string('messagedselectedcountusersfailed', 'moodle', count($fails)));
                echo '<ul>';
                foreach ($fails as $f) {
                    echo '<li>', $f, '</li>';
                }
                echo '</ul>';
            }
            echo '<p align="center"><a href="index.php?id='.$id.'">'.get_string('backtoparticipants', 'mod_attendance').'</a></p>';
        }
        echo $OUTPUT->footer();
        exit;
    } else {
        echo $OUTPUT->notification(get_string('nousersyet'));
    }
}
echo '<p align="center"><a href="'.$returnto.'">'.get_string("keepsearching", 'mod_attendance').'</a>'.
    ((count($SESSION->emailto[$id])) ? ', '.get_string('usemessageform', 'mod_attendance') : '').'</p>';
if ((!empty($send) || !empty($preview) || !empty($edit)) && (empty($messagebody))) {
    echo $OUTPUT->notification(get_string('allfieldsrequired'));
}
if (count($SESSION->emailto[$id])) {
    require_sesskey();
    require("message.html");
}
$PAGE->requires->yui_module('moodle-core-formchangechecker',
    'M.core_formchangechecker.init',
    [[
        'formid' => 'theform',
    ], ]
);
$PAGE->requires->string_for_js('changesmadereallygoaway', 'moodle');
echo $OUTPUT->footer();
