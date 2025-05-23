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


namespace theme_boost_union_vorsprung\helper;

/**
 * Vorsprung theme.
 *
 * @package     theme_vorsprung
 * @copyright   2023 Jörg Hagemann
 */

/**
 * Trait theme_vorsprung_core_h5p_renderer.
 *
 * See: https://tracker.moodle.org/browse/MDL-69087 and
 *      https://github.com/sarjona/h5pmods-moodle-plugin.
 *
 * @package     theme_vorsprung
 * @copyright   2023 Jörg Hagemann
 */
trait h5p_helper {
    /**
     * Add styles when an H5P is displayed.
     *
     * @param array $styles Styles that will be applied.
     * @param array $libraries Libraries that will be shown.
     * @param string $embedtype How the H5P is displayed.
     */
    protected function hvp_helper_alter_styles(&$styles, $libraries, $embedtype) {
        global $CFG;
        $styles[] = (object)array(
            'path' => $CFG->httpswwwroot . '/theme/boost_union_vorsprung/scss/h5p.css',
            'version' => '?ver=0.0.1',
        );
    }
}
