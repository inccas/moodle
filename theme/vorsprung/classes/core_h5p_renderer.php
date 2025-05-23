<?php

use theme_vorsprung\helper\h5p_helper;

/**
 * Vorsprung theme.
 *
 * @package     theme_vorsprung
 * @copyright   2023 Jörg Hagemann
 */
defined('MOODLE_INTERNAL') || die;

global $CFG;

if (file_exists($CFG->dirroot.'/h5p/classes/output/renderer.php')) {
    if (method_exists(\core_h5p\output\renderer::class, 'h5p_alter_styles')) {
        /**
         * Class theme_vorsprung_core_h5p_renderer.
         *
         * See: https://tracker.moodle.org/browse/MDL-69087 and
         *      https://github.com/sarjona/h5pmods-moodle-plugin.
         *
         * @package     theme_vorsprung
         * @copyright   2023 Jörg Hagemann
         */
        class theme_vorsprung_core_h5p_renderer extends \core_h5p\output\renderer {
            use h5p_helper;

            public function h5p_alter_styles(&$styles, $libraries, $embedtype) {
                $this->hvp_helper_alter_styles($styles, $libraries, $embedtype);
            }
        }
    }
}


$h5prenderer = $CFG->dirroot.'/mod/hvp/renderer.php';
if (file_exists($h5prenderer)) {
    require_once($h5prenderer);

    /**
     * Class theme_vorsprung_mod_hvp_renderer
     *
     * @package     theme_vorsprung
     * @copyright   2023 Jörg Hagemann
     */
    class theme_vorsprung_mod_hvp_renderer extends mod_hvp_renderer {
        use h5p_helper;

        public function hvp_alter_styles(&$styles, $libraries, $embedtype) {
            $this->hvp_helper_alter_styles($styles, $libraries, $embedtype);
        }
    }
}
