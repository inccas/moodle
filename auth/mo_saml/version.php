<?php
// This file is part of miniOrange moodle plugin - http://moodle.org/
//
// This Plugin is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This Program is distributed in the hope that it will be useful,
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

defined('MOODLE_INTERNAL') || die();

$plugin->requires = 2016052300;   // Requires Moodle 3.1 or later.
$plugin->release = '7.0.0';
$plugin->component = 'auth_mo_saml';
$plugin->version = 20230912110;    // YYYYMMDDXX.
$plugin->cron = 0;     // Time in sec.
$plugin->maturity = MATURITY_STABLE;

$plugin->dependencies = [
    'mod_forum' => ANY_VERSION
];