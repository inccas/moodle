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
 * Item reset page.
 *
 * @package    block_stash
 * @copyright  2019 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);

$itemid = optional_param('itemid', null, PARAM_INT);
$quantity = optional_param('quantity', null, PARAM_INT);

require_login($courseid);

$notifications = [];
$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

if (isset($itemid)) {
    require_sesskey();
    if (isset($quantity) && $quantity > 0) {
        try {
            $manager->update_user_item_amount($itemid, $userid, $quantity);
            $notifications[] = $OUTPUT->notification(get_string('itemamountupdate', 'block_stash'),
                    \core\output\notification::NOTIFY_SUCCESS);
        } catch (\exception $e) {
            $notifications[] = $OUTPUT->notification(get_string('nomorescarceitems', 'block_stash'),
                    \core\output\notification::NOTIFY_WARNING);
        }
    } else {
        $manager->reset_user_item($userid, $itemid);
    }
}

$user = \core_user::get_user($userid);
$url = new moodle_url('/blocks/stash/item_reset.php', ['userid' => $userid, 'courseid' => $courseid]);
list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_reset($url, $manager, $user,
        get_string('advancedreset', 'block_stash'));

$renderer = $PAGE->get_renderer('block_stash');
$group = groups_get_course_group($COURSE, true);

echo $OUTPUT->header();

echo $OUTPUT->heading($title);
foreach ($notifications as $notification) {
    echo $notification;
}
echo $renderer->navigation($manager, 'report');

if (!empty($subtitle)) {
    echo $OUTPUT->heading($subtitle, 3);
}

$useritems = $manager->get_all_user_items_in_stash($userid);

$data = [];

foreach ($useritems as $item) {
    $exporter = new block_stash\external\user_item_summary_exporter([], [
        'context' => $manager->get_context(),
        'item' => $item->item,
        'useritem' => $item->useritem
    ]);
    // This should be in the exporter.
    $itemdetail = $exporter->export($renderer);
    $itemdetail->itemcontent = json_encode($itemdetail->item);
    $itemdetail->useritemcontent = json_encode($itemdetail->useritem);
    $data['userstash'][] = $itemdetail;
}
$data['courseid'] = $manager->get_courseid();
$data['userid'] = $user->id;
$data['sesskey'] = sesskey();

echo $renderer->render_from_template('block_stash/user_item_reset', $data);
$PAGE->requires->js_call_amd('block_stash/item-selector-modal', 'init');

echo $OUTPUT->footer();
