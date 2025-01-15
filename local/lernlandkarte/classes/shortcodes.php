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

namespace local_lernlandkarte;
defined('MOODLE_INTERNAL') || die();

class shortcodes {

    public static function lernlandkarte($shortcode, $args, $content, $env, $next): string {
        global $OUTPUT, $PAGE, $CFG;
        if ($shortcode === 'lernlandkarte') {

            $id = sha1(random_int(1, 100000));
            $imagesrc = empty($content) ? $CFG->lernlandkarte['imagesrc'] : $content;
            $initialX = self::get_arg($args, 'x', 0, 100);
            $initialY = self::get_arg($args, 'y', 0, 100);
            $initialZoom = self::get_arg($args, 'zoom', 100, 2000);

//            $PAGE->requires->js('/local/lernlandkarte/amd/build/external/wheelzoom.min.js');
/*           $PAGE->requires->js_call_amd('local_lernlandkarte/lernlandkarte', 'init', [[
                'id' => $id,
                'x' => $initialX * 0.01,
                'y' => $initialY * 0.01,
                'zoom' => $initialZoom * 0.01,
            ]]);

            return $OUTPUT->render_from_template('local_lernlandkarte/lernlandkarte', [
                'id' => $id,
                'imagesrc' => $imagesrc,
            ]);
*/
        }

        return '';
    }

    private static function get_arg($args, $key, $min, $max) {
        global $CFG;
        if (isset($args[$key])) {
            $value = floatval($args[$key]);
            if ($value !== false && is_float($value)) {
              if($value < $min) return $min;
              if($value > $max) return $max;
              return $value;
            }
        }
        return $CFG->lernlandkarte[$key];
    }
}
