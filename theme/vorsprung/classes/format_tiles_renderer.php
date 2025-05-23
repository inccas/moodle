<?php

/**
 * Vorsprung theme.
 *
 * @package     theme_vorsprung
 * @copyright   2023 Jörg Hagemann
 */
defined('MOODLE_INTERNAL') || die;

global $CFG;

$tilesRenderer = $CFG->dirroot . "/course/format/tiles/format_tiles_renderer.php";
if (file_exists($tilesRenderer)) {
    require_once($tilesRenderer);
    class theme_vorsprung_format_tiles_renderer extends format_tiles_renderer {
        public function format_summary_text($section) {
            $context = context_course::instance($section->course);
            $summarytext = file_rewrite_pluginfile_urls($section->summary, 'pluginfile.php',
                $context->id, 'course', 'section', $section->id);

            $options = new stdClass();
            $options->noclean = true;
            $options->overflowdiv = false; // Allow overflow
            return format_text($summarytext, $section->summaryformat, $options);
        }

        public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
            $templateable = new \format_tiles\output\course_output($course, false, $displaysection, $this->courserenderer);
            $data = $templateable->export_for_template($this);
            $this->format_tile_title($data);
            echo $this->render_from_template('format_tiles/single_section_page', $data);
        }

        public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {
            $templateable = new \format_tiles\output\course_output($course, false, null, $this->courserenderer);
            $data = $templateable->export_for_template($this);
            $this->format_tile_title($data);
            echo $this->render_from_template('format_tiles/multi_section_page', $data);
        }
        private function format_tile_title(&$data){
            if(!empty($data['tiles'])) {
                foreach ($data['tiles'] as $key => &$tile) {
                    $tile['title'] = preg_replace('/&(nbsp);/i', ' ', $tile['title']);
                    $tile['title'] = preg_replace('/^LE\s*(\d+)\s*/', 'LE&nbsp;$1<br/>', $tile['title']);
                }
            }
        }
    }
}
