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
 * Version information
 *
 * @package    local_gdpr_deleteuserdata
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Manolescu Dorel <manolescu.dorel@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$plugin->version = 2018041344;
$plugin->requires = 2018051700;
$plugin->component = 'local_gdpr_deleteuserdata';
$plugin->cron = 1;
$plugin->release = '1.0';
$plugin->maturity = MATURITY_ALPHA;
