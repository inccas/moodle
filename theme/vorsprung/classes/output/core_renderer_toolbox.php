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
 * Trait for core renderer.
 *
 * @package    theme_vorsprung
 */

namespace theme_vorsprung\output;

use theme_vorsprung\helper\mustache_staticimageurl_helper;

defined('MOODLE_INTERNAL') || die;

use html_writer;
use moodle_url;

define('VSP_PIXDIR', '/theme/vorsprung/pix');

/**
 * Trait for core and core maintenance renderers.
 */
trait core_renderer_toolbox {
    protected $context;

    protected function get_mustache() {
        $mustache = parent::get_mustache();
        $mustache->addHelper('staticimageurl', array(new mustache_staticimageurl_helper(), 'staticimageurl'));
        return $mustache;
    }

    protected function initContextObj() {
        $context = new stdClass();
        $context->vsppixdir = VSP_PIXDIR;
        return $context;
    }

    protected function initContextArray(array $additionalSettings = []) {
        $context = [];
        $context['vsppixdir'] = VSP_PIXDIR;
        if (!empty($additionalSettings)) {
            $context = array_merge($context, $additionalSettings);
        }
        return $context;
    }

    public function vsp_footerlogos_html() {
        $context = $this->initContextArray();
        return $this->render_from_template('theme_vorsprung/footerlogos', $context);
    }

    /**
     * Render the navbar.
     *
     * @return string Markup.
     */
    public function navbar(): string {

        $items = $this->page->navbar->get_items();

        if (empty($items)) {
            return '';
        }

        $breadcrumbseparator = 'angle-right';

        $breadcrumbs = "";

        $start = true;
        foreach ($items as $item) {
            $item->hideicon = true;
            if($item->key == 'mycourses') {
                continue;
            }
            if ($start) {
                $liClasses="breadcrumb-item";
                if($item->hidden) {
                    $liClasses .= ' dimmed_text';
                }
                $breadcrumbs .= '<li class="'.$liClasses.'">';

                $strHome = 'Start';
                $breadcrumbHome = 'icon';
                if ($breadcrumbHome == 'icon') {
                    $breadcrumbs .= html_writer::link(new moodle_url('/'),
                        html_writer::tag('i', '', array(
                            'title' => $strHome,
                            'class' => 'fa fa-home fa-lg')
                        )
                    );
                    $breadcrumbs .= '</li>';
                } else {
                    $breadcrumbs .= html_writer::link(new moodle_url('/'), $strHome);
                    $breadcrumbs .= '</li>';
                }
                $start = false;
                continue;
            }
            $breadcrumbs .= '<span class="mx-1"><i class="fa-'.$breadcrumbseparator.' fa"></i></span><li>'.
                $this->render($item).'</li>';
        }

        $classes = 'd-none d-md-flex';
        $strBreadcrumb = 'Breadcrumb';
        return '<nav role="navigation" aria-label="'. $strBreadcrumb .'">
            <ol class="breadcrumb ' . $classes . '">'.$breadcrumbs.'</ol>
        </nav>';
    }
}
