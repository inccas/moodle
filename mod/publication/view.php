<?php
// This file is part of mod_publication for Moodle - http://moodle.org/
//
// It is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// It is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays a single mod_publication instance
 *
 * @package       mod_publication
 * @author        Philipp Hager
 * @author        Andreas Windbichler
 * @copyright     2014 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/publication/locallib.php');
require_once($CFG->dirroot . '/mod/publication/mod_publication_files_form.php');
require_once($CFG->dirroot . '/mod/publication/mod_publication_allfiles_form.php');

$id = required_param('id', PARAM_INT); // Course Module ID.
$allfilespage = optional_param('allfilespage', 0, PARAM_BOOL);

$url = new moodle_url('/mod/publication/view.php', ['id' => $id, 'allfilespage' => $allfilespage]);
$cm = get_coursemodule_from_id('publication', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_login($course, true, $cm);
$PAGE->set_url($url);

$context = context_module::instance($cm->id);

require_capability('mod/publication:view', $context);

if ($allfilespage) {
    require_capability('mod/publication:approve', $context);
}

$publication = new publication($cm, $course, $context);

$publication->set_allfilespage($allfilespage);

$event = \mod_publication\event\course_module_viewed::create([
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
]);
$event->add_record_snapshot('course', $PAGE->course);
$event->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$pagetitle = strip_tags($course->shortname . ': ' . format_string($publication->get_instance()->name));
$action = optional_param('action', 'view', PARAM_ALPHA);
$savevisibility = optional_param('savevisibility', false, PARAM_RAW);

$download = optional_param('download', 0, PARAM_INT);
if ($download > 0) {
    $publication->download_file($download);
}

if ($savevisibility) {
    require_capability('mod/publication:approve', $context);
    require_sesskey();

    $files = optional_param_array('files', [], PARAM_INT);
    $params = [];

    $params['pubid'] = $publication->get_instance()->id;
    $publication->update_files_teacherapproval($files);
    publication::send_all_pending_notifications();
    redirect($url);

} else if ($action == 'zip') {
    $publication->download_zip(true);
} else if ($action == 'zipusers') {
    $users = optional_param_array('selecteduser', false, PARAM_INT);
    if (!$users) {
        // No users selected.
        header('Location: view.php?id=' . $id);
        die();
    }
    $users = array_keys($users);
    $publication->download_zip($users);

} else if ($action == 'import') {
    require_capability('mod/publication:approve', $context);
    require_sesskey();

    if (!isset($_POST['confirm'])) {
        $message = get_string('updatefileswarning', 'publication');

        echo $OUTPUT->header();
        echo $OUTPUT->heading(format_string($publication->get_instance()->name), 1);
        echo $OUTPUT->confirm($message, 'view.php?id=' . $id . '&action=import&confirm=1&sesskey=' . sesskey(), 'view.php?id=' . $id);
        echo $OUTPUT->footer();
        exit;
    }

    $publication->importfiles();
    publication::send_all_pending_notifications();
} else if ($action == 'grantextension') {
    require_capability('mod/publication:grantextension', $context);
    require_sesskey();

    $users = optional_param_array('selecteduser', [], PARAM_INT);
    $users = array_keys($users);

    if (count($users) > 0) {
        $url = new moodle_url('/mod/publication/grantextension.php', ['id' => $cm->id]);
        foreach ($users as $idx => $u) {
            $url->param('userids[' . $idx . ']', $u);
        }

        redirect($url);
        die();
    }
} else if ($action == 'approveusers' || $action == 'rejectusers' || $action == 'resetstudentapproval') {
    require_capability('mod/publication:approve', $context);
    require_sesskey();

    $userorgroupids = optional_param_array('selecteduser', [], PARAM_INT);
    $userorgroupids = array_keys($userorgroupids);
    if (count($userorgroupids) > 0) {
        $publication->update_users_or_groups_teacherapproval($userorgroupids, $action);
        publication::send_all_pending_notifications();
        redirect($url);
    }
}

$submissionid = $USER->id;

$filesform = new mod_publication_files_form(null,
    ['publication' => $publication, 'sid' => $submissionid, 'filearea' => 'attachment']);

if ($data = $filesform->get_data()) {
    $datasubmitted = $filesform->get_submitted_data();

    if (isset($datasubmitted->gotoupload)) {
        redirect(new moodle_url('/mod/publication/upload.php',
            ['id' => $publication->get_instance()->id, 'cmid' => $cm->id]));
    }
    if ($publication->is_approval_open()) {
        $studentapproval = optional_param_array('studentapproval', [], PARAM_INT);

        $conditions = [];
        $conditions['publication'] = $publication->get_instance()->id;
        $conditions['userid'] = $USER->id;

        $pubfileids = $DB->get_records_menu('publication_file', ['publication' => $publication->get_instance()->id],
            'id ASC', 'fileid, id');

        // Update records.
        foreach ($studentapproval as $idx => $approval) {
            $conditions['fileid'] = $idx;

            if ($approval != 1 && $approval != 2) {
                continue;
            }
            $dataforlog = new stdClass();
            $dataforlog->approval = $approval == 1 ? 'approved' : 'rejected';
            $stats = null;

            if ($publication->get_mode() == PUBLICATION_MODE_ASSIGN_TEAMSUBMISSION) {
                /* We have to deal with group approval! The method sets group approval for the specified user
                 * and returns current cumulated group approval (and it also sets it in publication_file table)! */
                $stats = $publication->set_group_approval($approval, $pubfileids[$idx], $USER->id);
            } else {
                $DB->set_field('publication_file', 'studentapproval', $approval, $conditions);
            }
            if (is_array($stats)) {
                $dataforlog->approval = '(Students ' . $stats['approving'] . ' out of ' . $stats['needed'] . ') ' . $dataforlog->approval;
            }
            $dataforlog->publication = $conditions['publication'];
            $dataforlog->userid = $USER->id;
            $dataforlog->reluser = $USER->id;
            $dataforlog->fileid = $idx;

            \mod_publication\event\publication_approval_changed::approval_changed($cm, $dataforlog)->trigger();
            if ($publication->get_instance()->notifystatuschange != 0) {
                $pubfile = $DB->get_record('publication_file', ['id' => $pubfileids[$idx]]);
                $newstatus = $approval == 2 ? 'not' : ''; // Used for string identifier..
                publication::send_notification_statuschange($cm, $USER, $newstatus, $pubfile, $cm->id, $publication);
            }
        }
        publication::send_all_pending_notifications();
        redirect($url);
    }
}

$filesform = new mod_publication_files_form(null,
    ['publication' => $publication, 'sid' => $submissionid, 'filearea' => 'attachment']);

// Print the page header.
$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);
if (!$allfilespage) {
    $PAGE->add_body_class('limitedwidth');
} else {
    $PAGE->add_body_class('allfilespage');
}
echo $OUTPUT->header();

$allfilesform = $publication->display_allfilesform();

$publicationinstance = $publication->get_instance();
$publicationmode = $publication->get_mode();
$templatecontext = new stdClass;
$templatecontext->obtainstudentapprovaltitle = get_string('obtainstudentapproval', 'publication');
$templatecontext->obtainteacherapproval = $publicationinstance->obtainteacherapproval == 1 ?
    get_string('obtainteacherapproval_yes', 'publication') : get_string('obtainteacherapproval_no', 'publication');
if ($publicationmode == PUBLICATION_MODE_FILEUPLOAD) {
    $templatecontext->mode = get_string('modeupload', 'publication');
    $templatecontext->obtainstudentapproval = $publicationinstance->obtainstudentapproval == 1 ?
        get_string('obtainstudentapproval_yes', 'publication') : get_string('obtainstudentapproval_no', 'publication');
} else {
    $templatecontext->mode = get_string('modeimport', 'publication');
    if ($publicationmode == PUBLICATION_MODE_ASSIGN_TEAMSUBMISSION) {
        $templatecontext->obtainstudentapprovaltitle = get_string('obtaingroupapproval', 'publication');
        if ($publicationinstance->obtainstudentapproval == 0) {
            $templatecontext->obtainstudentapproval = get_string('obtainstudentapproval_no', 'publication');
        } else {
            $templatecontext->obtainstudentapproval = $publicationinstance->groupapproval == PUBLICATION_APPROVAL_ALL ?
                get_string('obtaingroupapproval_all', 'publication') : get_string('obtaingroupapproval_single', 'publication');
        }
    } else {
        $templatecontext->obtainstudentapproval = $publicationinstance->obtainstudentapproval == 1 ?
            get_string('obtainstudentapproval_yes', 'publication') : get_string('obtainstudentapproval_no', 'publication');
    }
}


if ($publicationinstance->duedate > 0) {
    $timeremainingdiff = $publicationinstance->duedate - time();
    if ($timeremainingdiff > 0) {
        $templatecontext->timeremaining = format_time($publicationinstance->duedate - time());
    } else {
        $templatecontext->timeremaining = get_string('overdue', 'publication');
    }
}
$templatecontext->isteacher = false;
if (has_capability('mod/publication:approve', $context)) {
    $templatecontext->isteacher = true;
    $templatecontext->studentcount = count($publication->get_users([], true));
    $allfilestable = $publication->get_allfilestable(PUBLICATION_FILTER_ALLFILES, true);
    $templatecontext->allfilescount = $allfilestable->get_count();
    $templatecontext->allfiles_url = (new moodle_url('/mod/publication/view.php',
        ['id' => $cm->id, 'filter' => PUBLICATION_FILTER_ALLFILES, 'allfilespage' => 1]))->out(false);
    $templatecontext->allfiles_empty = $templatecontext->allfilescount == 0;
    $templatecontext->assign = $publication->get_importlink();
    if ($publicationinstance->obtainteacherapproval == 1) {
        $templatecontext->viewall_approvalneeded_url = (new moodle_url('/mod/publication/view.php',
            ['id' => $cm->id, 'filter' => PUBLICATION_FILTER_APPROVALREQUIRED, 'allfilespage' => 1]))->out(false);
        $templatecontext->showapprovalrequired = true;
        $notapprovedtable = $publication->get_allfilestable(PUBLICATION_FILTER_APPROVALREQUIRED, true);
        $templatecontext->approvalrequiredcount = $notapprovedtable->get_count();
    }
}

$mode = $publication->get_mode();
$templatecontext->myfilestitle = $mode == PUBLICATION_MODE_ASSIGN_TEAMSUBMISSION ? get_string('mygroupfiles', 'publication') : get_string('myfiles', 'publication');
$filestable = $publication->get_filestable();
$filestable->init();
$templatecontext->myfiles = $filestable->data;
$templatecontext->hasmyfiles = count($templatecontext->myfiles) > 0;
$templatecontext->myfilesform = $filesform->render();
if (!$allfilespage) {
    echo $OUTPUT->render_from_template('mod_publication/overview', $templatecontext);
}


echo $allfilesform;

echo $OUTPUT->footer();
