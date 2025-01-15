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
 * Filter "prismjs"
 *
 * @package    filter_prismjs
 * @copyright  2017 Sébastien Mehr, University of French Polynesia <sebastien.mehr@upf.pf>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace filter_prismjs;

defined('MOODLE_INTERNAL') || die();

class text_filter extends \moodle_text_filter {

    public function filter($text, array $options = array()) {
        global $PAGE;

        $filteredText = $text;
        $result = self::replace_prismjs_tag($text, $filteredText);
        if ($result) {
            // Finally add prism.js script and eventhandler to the page.
            // $PAGE->requires->js('/filter/prismjs/amd/build/external/prism.min.js');
            $PAGE->requires->js_call_amd('filter_prismjs/filter', 'init');
        }
        return $filteredText;
    }

    public static function hasPrismjsTag(&$text) {
        if(
            is_string($text) &&
            !empty($text) &&
            mb_strpos($text, "<prismjs language=") !== false
        ) {
            return true;
        }
        return false;
    }
    public static function replace_prismjs_tag($text, &$filteredText) {
        global $CFG, $PAGE, $languages;

        if(!self::hasPrismjsTag($text)) {
            return false;
        }

        require_once($CFG->dirroot . '/filter/prismjs/languages.php');

        // Getting all languages code in a string separated with |.
        $regextags = implode("|", array_keys($languages));
        $regexcolor = '/(<prismjs language="(' . $regextags . ')" line-?numbers="(1|0|y|n|yes|no)">)[\s\S]+?<\/prismjs>/';

        if (preg_match_all($regexcolor, $text, $matches, PREG_SET_ORDER)) {

            $languageDefault = get_config('filter_prismjs', 'language');
            $languageDefault = $languageDefault === false ? 'python' : $languageDefault;
            $filteredText = '';

            for ($i = 0; $i < count($matches); $i++) {

                if ($i > 0) {
                    $text = $filteredText;
                }

                $opentag = $matches[$i][1];
                $languageMatched = $matches[$i][2];
                $lineNumbers = $matches[$i][3];

                $classes = 'language-';
                if (array_key_exists($languageMatched, $languages)) {
                    $classes .= $languageMatched;
                } else {
                    $classes .= $languageDefault;
                }

                if(in_array(strtolower($lineNumbers), ['1', 'y', 'yes'])){
                    $classes .= " line-numbers";
                }

                $openreplace = '<pre class="'. $classes . '"><code>';
                $closereplace = '</code></pre>';

                $filteredText = str_replace(
                    [$opentag."\n", $opentag."\r\n", $opentag, '</prismjs>'],
                    [$openreplace, $openreplace, $openreplace, $closereplace],
                    $text
                );
            }
            return true;
        }
        return false;
    }
}



/**
 * https://docs.moodle.org/dev/Filter_enable/disable_by_context#Getting_filter_configuration
 *
 * Get the list of active filters, in the order that they should be used
 * for a particular context.
 *
 * @param object $context a context
 * @return array an array where the keys are the filter names and the values are any local
 *      configuration for that filter, as an array of name => value pairs
 *      from the filter_config table. In a lot of cases, this will be an
 *      empty array.
 */
function get_active_filters($contextid) {
    global $DB;

    $sql = "SELECT fc.id, active.FILTER, fc.name, fc.VALUE
            FROM (SELECT f.FILTER
              FROM {filter_active} f
              JOIN {context} ctx ON f.contextid = ctx.id
              WHERE ctx.id IN ($contextid) AND f.FILTER LIKE 'prismjs'
              GROUP BY FILTER
              HAVING MAX(f.active * ctx.depth) > -MIN(f.active * ctx.depth)
              ORDER BY MAX(f.sortorder)) active
            LEFT JOIN {filter_config} fc ON fc.FILTER = active.FILTER AND fc.contextid = $contextid";

    $courseconfig = array();

    if ($results = $DB->get_records_sql($sql, null)) {
        foreach ($results as $res) {
            if ($res->name == "language") {
                $courseconfig['language'] = $res->value;
            }
        }
    }

    return $courseconfig;
}
