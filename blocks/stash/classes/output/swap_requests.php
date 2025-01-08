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
 * Swap requests renderable
 *
 * @package    block_stash
 * @copyright  2023 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;
use moodle_url;

use block_stash\manager;
use block_stash\swap_handler;

/**
 * Trade renderable class.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class swap_requests implements renderable, templatable {

    /** @var manager The manager. */
    protected $manager;

    /** @var tradeitems items related to this trade */
    protected $swaphandler;

    protected $userid;

    /**
     * Constructor.
     *
     * @param manager $manager The manager.
     * @param swap_handler $swaphander A class for handling swaps.
     */
    public function __construct(manager $manager, swap_handler $swaphandler, int $userid) {
        $this->manager = $manager;
        $this->swaphandler = $swaphandler;
        $this->userid = $userid;

    }

    public function get_status_string($status) {
        $statusstrings = [
            \block_stash\swap::BLOCK_STASH_SWAP_VIEWED => '',
            \block_stash\swap::BLOCK_STASH_SWAP_COMPLETED => get_string('completed', 'block_stash'),
        ];
        return $statusstrings[$status];
    }


    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = [];

        $swaprequests = $this->swaphandler->get_users_swap_requests($this->userid);

        $offers = array_map(function($offer) use ($output) {

            $action = new \confirm_action(get_string('removeswapdetails', 'block_stash'));
            $params = [
                'courseid' => $this->manager->get_courseid(),
                'id' => $offer->id,
                'sesskey' => sesskey()
            ];
            $url = new moodle_url('/blocks/stash/swaprequests.php', $params);
            $actionlink = new \action_link($url, '', $action, null,
                new \pix_icon('t/delete', get_string('deleteswap', 'block_stash')));
            $offer->delete = $actionlink->export_for_template($output);

            if (!empty($offer->status)) {
                $offer->status = $this->get_status_string($offer->status);
            } else {
                $offer->status = get_string('new', 'block_stash');
            }
            return $offer;
        }, $swaprequests['offers']);

        $data['offers'] = $offers;
        $data['requests'] = $swaprequests['requests'];

        if (!empty($data['requests'])) {
            $data['haverequests'] = true;
        }
        if (!empty($data['offers'])) {
            $data['haveoffers'] = true;
        }

        if (empty($data['requests']) && empty($data['offers'])) {
            $data['zero'] = true;
        }
        $data['courseid'] = $this->manager->get_courseid();

        return $data;
    }

}
