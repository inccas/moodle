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

namespace tiny_stash;

use block_stash\item;
use block_stash\trade;
use context;
use context_course;
use core\persistent;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_menuitems;
use editor_tiny\plugin_with_configuration;
use filter_shortcodes\local\registry\plugin_registry;
use moodle_url;
use stdClass;

/**
 * Tiny stash plugin for Moodle.
 *
 * @package    tiny_stash
 * @copyright  2023 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugininfo extends plugin implements
    plugin_with_buttons,
    plugin_with_menuitems,
    plugin_with_configuration {

    private static ?array $itemdata = null;

    private static ?array $tradedata = null;

    private static ?array $dropdata = null;

    private static ?array $shortcodes = null;

    public static function is_enabled(context $context, array $options, array $fpoptions, ?\editor_tiny\editor $editor = null): bool {
        // Users must have permission to embed content.
        // Check that stash is enabled on this course.
        $courseid = self::get_courseid_from_context($context);
        if (!$courseid) {
            return false;
        }
        $manager = \block_stash\manager::get($courseid);
        return $manager->is_enabled();
    }

    public static function get_available_buttons(): array {
        return [
            'tiny_stash/stash',
        ];
    }

    public static function get_available_menuitems(): array {
        return [
            'tiny_stash/stash',
        ];
    }

    private static function get_courseid_from_context(context $context): int {
        while ($context->contextlevel != CONTEXT_COURSE) {
            if ($context->contextlevel == CONTEXT_SYSTEM) {
                break;
            }
            $context = $context->get_parent_context();
        }

        if ($context->contextlevel == CONTEXT_SYSTEM) {
            return 0;
        } else {
            return $context->instanceid;
        }
    }


    public static function get_plugin_configuration_for_context(context $context, array $options, array $fpoptions,
            ?\editor_tiny\editor $editor = null): array {
        global $USER, $PAGE;

        $courseid = self::get_courseid_from_context($context);

        // navigation may be the key.
        $navbar = $PAGE->navbar->get_items();
        $suggestedlocation = '';
        while (count($navbar) >= 0) {
            $navitem = array_pop($navbar);
            if ($navitem->key != 'modedit') {
                $suggestedlocation = $navitem->text;
                break;
            }
        }

        if ($courseid == 0) {
            $permissions = false;
            $courseid = 0;
            self::$itemdata = [];
            self::$tradedata = [];
            self::$dropdata = [];
        } else {
            $manager = \block_stash\manager::get($courseid);
            $permissions = $manager->can_manage($USER->id);
            if (!$permissions) {
                return ['canmanage' => false];
            }
            $courseid = $manager->get_courseid();

            // Shoutout to crmpicco for isset guidance.
            if (!isset(self::$itemdata)) {
                $addimage = fn(string $idfield) => fn(stdClass $item): array => (array)$item + [
                    'imageurl' => moodle_url::make_pluginfile_url(
                        context_course::instance($courseid)->id,
                        'block_stash',
                        'item',
                        $item->{$idfield},
                        '/',
                        'image'
                    )->out(false)
                ];

                // Persistent doesn't return an array indexed by ID, so we need to do it here.
                $itemdata = array_map(fn(item $i): array => $addimage('id')($i->to_record()), $manager->get_items());
                self::$itemdata = array_combine(array_column($itemdata, 'id'), $itemdata);
                self::$dropdata = array_map($addimage('itemid'), $manager->get_drops_for_items_and_trade()['items']);

                // Moderately massage the data from `$manager->get_all_trade_data``.
                // Two things need to be considered:
                //
                // 1. The `formatTradeInformation` function from the tiny_stash/ui module
                //    requires an array of objects, but the manager returns an array where,
                //    indices are IDs as strings (thanks to the Moodle DML). This results
                //    in TinyMCE receiving an object instead of an array. The first array_values
                //    solves this.
                //
                // 2. "additems" and "lossitems" must also be arrays of objects. The manager behaves
                //    the same as in 1. in this case and they arrays of associative arrays, indexed
                //    by string IDs. We create a new associative array with "additems" and "lossitems"
                //    set to the same values as the original data, but passed through `array_values`
                //    to get sequential, integer indices. Then filling in the remaining information
                //    from the original associative array.
                //
                // Note that it's fine for individual elements to be associative arrays as they will
                // be converted to objects in JS land.
                //
                // This massaging could also take place in JS land (in options.js), but it feels more
                // sensible here as the tiny_stash/options module shouldn't really be aware of the Moodle DML.
                //
                // This could also be addressed in block_stash directly by modifying the `get_all_trade_data`
                // function; but that may be risky as other parts of the code may rely on the indices
                // corresponding to record IDs.
                self::$tradedata = array_values(array_map(
                    fn(array $trade): object => (object)([
                        "additems" => array_values($trade['additems']),
                        "lossitems" => array_values($trade['lossitems'])
                    ] + $trade),
                    $manager->get_all_trade_data()
                ));

                self::$shortcodes = array_column(
                    array_filter(
                        iterator_to_array((new plugin_registry)->get_definitions(), false),
                        fn(stdClass $shortcode): bool => $shortcode->component === 'block_stash'
                    ),
                    'shortcode'
                );
            }
        }

        return [
            'canmanage' => $permissions,
            'courseid' => $courseid,
            'storeinrepo' => true,
            'suggestedlocation' => $suggestedlocation,
            'itemdata' => self::$itemdata,
            'tradedata' => self::$tradedata,
            'dropdata' => self::$dropdata,
            'shortcodes' => self::$shortcodes
        ];
    }
}
