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

$string['pluginname'] = 'Lernlandkarte';
$string['shortcode_lernlandkarte'] = 'Create an clickable image that is zoomed and centered to certain position.';
$string['shortcode_lernlandkarte_help'] = '
The following attributes may be used:

- `x` (optional) x position (percent of width, default = 50)
- `y` (optional) y position (percent of height, default = 50)
- `zoom` (optional) zoom factor (default = 100)

Example:

[lernlandkarte][/lernlandkarte]
[lernlandkarte x=30 y=65 zoom=200]/static/image/mycustom.jpg[/lernlandkarte]
';
