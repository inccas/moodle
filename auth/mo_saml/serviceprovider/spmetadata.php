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
 * @package   auth_mo_saml
 * @copyright   2020  miniOrange
 * @category    document
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL v3 or later, see license.txt
 */

// @codingStandardsIgnoreStart
require_once(__DIR__ . '/../../../config.php');
// @codingStandardsIgnoreEnd
// require_once('../setup.php');
require_once('../locallib.php');

$download = optional_param('download', '', PARAM_RAW);
if ($download) {
    mo_saml_miniorange_generate_metadata(true);
}
else{
    mo_saml_miniorange_generate_metadata();
}

