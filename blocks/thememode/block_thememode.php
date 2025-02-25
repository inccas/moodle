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
 * Theme mode toggle block.
 *
 * @package    block_thememode
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_thememode extends block_base {
    
    /**
     * Initialize the block
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_thememode');
    }

    /**
     * Get the content of the block
     */
    public function get_content() {
        global $PAGE, $USER, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Load JS and CSS
        $PAGE->requires->js_call_amd('block_thememode/toggle', 'init');
        $PAGE->requires->css('/blocks/thememode/styles.css');

        // Get user preference
        $darkmode = get_user_preferences('thememode_darkmode', 0, $USER->id);
        $checked = $darkmode ? 'checked' : '';

        // Create the toggle switch HTML
        $this->content->text = '
            <div class="theme-mode-toggle-container">
                <div class="theme-mode-toggle">
                    <label class="switch">
                        <input type="checkbox" id="thememode-toggle" ' . $checked . '>
                        <span class="slider round"></span>
                    </label>
                    <span class="mode-text">' . ($darkmode ? get_string('lightmode', 'block_thememode') : get_string('darkmode', 'block_thememode')) . '</span>
                </div>
            </div>';

        return $this->content;
    }

    /**
     * Allow the block to have a configuration page
     */
    public function has_config() {
        return true;
    }

    /**
     * Locations where block can be displayed
     */
    public function applicable_formats() {
        return array('all' => true);
    }
}
