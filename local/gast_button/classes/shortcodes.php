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

namespace local_gast_button;
defined('MOODLE_INTERNAL') || die();
require_once('/auth/cas/CAS/vendor/autoload.php');
use Hashids\Hashids;

class shortcodes {
    public static function gast_button($shortcode, $args, $content, $env, $next):string {

        global $USER;
        global $PAGE;
        global $CFG;

        if ($shortcode === 'gastbutton') {
            $activeEnv = $CFG->gast_button['activeenv'] ?? 'test';
            $clientId = $CFG->gast_button['clientId'];

            $productId = self::get_arg($args, 'productid') ?? $CFG->gast_button['productId'];
            $productId = intval($productId);

            $targetUrl = $CFG->gast_button['env'][$activeEnv]['url'];
            $targetUrl = strip_tags($targetUrl);

            $align = self::get_arg($args, 'align') ?? '';

            $salt = $CFG->gast_button['salt'];

            // Pad result to length 5.
            $hashids = new HashIds($salt, 5);
            // Obfuscate userId.
            $userId = $hashids->encode($USER->id); // Test Taker Id from FernUni Hagen

            $form = new gast_button_form($CFG->wwwroot . '/local/gast_button/form_handler.php', $content, $align);
            $form->set_data([
                'clientId' => $clientId,
                'productId' => $productId,
                'userId' => $userId,
                'returnUrl' => $PAGE->url,
                'targetUrl' => $targetUrl
            ]);

            return $form->render();
        }

        return '';
    }

    private static function get_arg($args, $key) {
        if (isset($args[$key])) {
            return $args[$key];
        }
        return null;
    }
}
