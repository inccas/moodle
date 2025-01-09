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
 * FormillaLiveChat block definition
 *
 * @package    block_formillalivechat
 * @copyright  formilla.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

/**
 * FormillaLiveChat block class
 *
 * @copyright formilla.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_formillalivechat extends block_base {

    /**
     * Sets the block title
     *
     * @return none
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_formillalivechat');
    }

    /**
     * Defines where the block can be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array('all' => true);
    }

    /**
     * Controls global configurability of block
     *
     * @return bool
     */
    public function instance_allow_config() {
        return false;
    }

    /**
     * Controls global configurability of block
     *
     * @return bool
     */
    public function has_config() {
        return false;
    }

    /**
     * Constrols if a block header is shown based on instance configuration
     *
     * @return bool
     */
    public function hide_header() {
        return isset($this->config->show_header) && $this->config->show_header == 0;
    }

    /**
     * Controls the block title based on instance configuration
     *
     * @return bool
     */
    public function specialization() {
        $this->title = "Formilla Live Chat";
    }

    /**
     * Creates the blocks main content
     *
     * @return string
     */
    public function get_content() {
        if (isset($this->content)) {
            return $this->content;
        }

        // Settings variables based on config.
        $formillalivechatid = "";

        if (isset($this->config->formillalivechatid)) {
            $formillalivechatid = trim($this->config->formillalivechatid);
        }

        $this->content = new stdClass;
        $this->content->text = "";

        $chatbutton = "<div class=\"formilla_chat\">";
        $chatbutton .= "<script type=\"text/javascript\">";
        $chatbutton .= " (function () { ";
        $chatbutton .= "   var head = document.getElementsByTagName(\"head\").item(0); ";
        $chatbutton .= "   var script = document.createElement('script'); ";
        $chatbutton .= "   var src = (document.location.protocol == \"https:\" ? ";
        $chatbutton .= "   'https://www.formilla.com/scripts/feedback.js' : 'http://www.formilla.com/scripts/feedback.js');";
        $chatbutton .= "   script.setAttribute(\"type\", \"text/javascript\"); script.setAttribute(\"src\", src);";
        $chatbutton .= "   script.setAttribute(\"async\", true); ";
        $chatbutton .= "   var complete = false; ";

        $chatbutton .= "   script.onload = script.onreadystatechange = function () { ";
        $chatbutton .= "     if (!complete && (!this.readyState || this.readyState == 'loaded'";
        $chatbutton .= "       || this.readyState == 'complete')) { ";
        $chatbutton .= "       complete = true; ";
        $chatbutton .= "       Formilla.guid = '" . $formillalivechatid . "';";
        $chatbutton .= "       Formilla.loadFormillaChatButton(); ";
        $chatbutton .= "         }";
        $chatbutton .= "   }; ";

        $chatbutton .= "   head.appendChild(script); ";
        $chatbutton .= " })(); ";
        $chatbutton .= " </script> ";
        $chatbutton .= "</div>";

        $this->content->text = $chatbutton;

        $this->content->footer = '';
        return $this->content;
    }
}
