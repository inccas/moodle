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
 * Theme Boost Union VORsprung - Sprachpaket
 *
 * @package theme_boost_union_vorsprung
 * @copyright 2025 Danou Nauck <danou@nauck.eu>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 oder höher
 */

defined('MOODLE_INTERNAL') || die();

// Codechecker soll einige Sniffs für diese Datei ignorieren, da sie perfekt sortiert ist, nur nicht alphabetisch.
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment
// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder

// Allgemein.
$string['pluginname'] = 'Boost Union VORsprung';
$string['choosereadme'] = 'Dieses Plugin ist nur eine Standardvorlage, die man zum Entwickeln von Boost Union-Unterthemen verwenden kann.';
$string['configtitle'] = 'Boost Union VORsprung';
$string['settingsoverview_buc_desc'] = 'Mit Boost Union VORsprung können Sie Boost Union an Ihre eigenen lokalen Bedürfnisse anpassen.';

// Einstellungen: Registerkarte Allgemeine Einstellungen.
// ... Abschnitt: Vererbung.
$string['inheritanceheading'] = 'Vererbung';
$string['inheritanceinherit'] = 'Erben';
$string['inheritanceduplicate'] = 'Duplizieren';
$string['inheritanceoptionsexplanation'] = 'In den meisten Fällen ist die Vererbung vollkommen in Ordnung. Es kann jedoch vorkommen, dass fehlerhafter Code in Boost Union integriert wird, der eine einfache SCSS-Vererbung für bestimmte Boost Union-Funktionen verhindert. Wenn Sie auf Probleme mit Boost Union-Funktionen stoßen, die auch in Boost Union VORsprung nicht zu funktionieren scheinen, versuchen Sie, diese Einstellung auf \'Duplizieren\' zu ändern und, wenn das Problem dadurch gelöst wird, melden Sie das Problem auf Github (Informationen zum Melden eines Problems finden Sie in der Datei README.md).';
// ... ... Einstellung: Einstellung für Vererbung vor SCSS.
$string['prescssinheritancesetting'] = 'Vererbung vor SCSS';
$string['prescssinheritancesetting_desc'] = 'Mit dieser Einstellung steuern Sie, ob der Code vor SCSS von Boost Union vererbt oder dupliziert werden soll.';
// ... ... Einstellung: Zusätzliche SCSS-Vererbungseinstellung.
$string['extrascssinheritancesetting'] = 'Zusätzliche SCSS-Vererbung';
$string['extrascssinheritancesetting_desc'] = 'Mit dieser Einstellung steuern Sie, ob der zusätzliche SCSS-Code von Boost Union übernommen oder dupliziert werden soll.';

/****************************************************************
 * ERWEITERUNGSPUNKT:
 * Fügen Sie hier Ihre Sprachzeichenfolgen für Ihre Einstellungen hinzu.
 *****************************************************************/

// Datenschutz-API.
$string['privacy:metadata'] = 'Das Boost Union VORsprung-Design speichert keine persönlichen Daten über Benutzer.';
$string['poweredbyinccas'] = 'Unterstützt von <a href="https://www.inccas.de" target="_blank">INCCAS</a>';

// Settings for the left side menu / Drawer left
$string['generalmenusettings'] = 'Menü Einstellungen';
$string['generalmenusettings_heading'] = 'In diesem Abschnitt können Sie das Erscheinungsbild der linken Seitenleiste nach Ihren Wünschen konfigurieren und angeben, ob und welche Unterabschnitte angezeigt werden sollen.';
$string['generalmenusettings_showfull'] = 'Seitenleisten-Akkordeon';
$string['generalmenusettings_showfull_desc'] = 'Möchten Sie die linke Seitenleiste mit vielen weiteren Optionen anzeigen, die im Akkordeonstil geöffnet werden können?';