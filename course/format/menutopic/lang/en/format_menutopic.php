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
 * Strings for component 'format_menutopic', language 'en'
 *
 * @since 2.3
 * @package format_menutopic
 * @copyright 2012 David Herney Bernal - cirano
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['aboutresource'] = 'About the resource';
$string['aboutsection'] = 'About the topic';
$string['actionadd_sheet_daughter_sheetedit'] = 'Add as daughter sheet';
$string['actionadd_sheet_sister_sheetedit'] = 'Add as sister sheet';
$string['actiondelete_sheet_sheetedit'] = 'Delete';
$string['actiondeleteconfirm_sheet_sheetedit'] = 'If you delete the sheet will delete all child sheets. Are you really sure want to continue?';
$string['actiondown_sheet_sheetedit'] = 'Move down';
$string['actionleft_sheet_sheetedit'] = 'Move left';
$string['actionright_sheet_sheetedit'] = 'Move right';
$string['actions_sheet_sheetedit'] = 'Actions on the sheet';
$string['actionsave_sheet_sheetedit'] = 'Change sheet data';
$string['actionup_sheet_sheetedit'] = 'Move up';
$string['config_editmenu'] = 'Configurate';
$string['config_editmenu_title'] = 'Menu configuration';
$string['config_template_topic_title'] = 'Configurate -Description of the section as a template-';
$string['coursedisplay'] = 'Visualization mode of section 0';
$string['coursedisplay_help'] = 'This define as display the section 0: as a menu element or as section before the menu bar.';
$string['coursedisplay_multi'] = 'Before the menu';
$string['coursedisplay_single'] = 'As a menu element';
$string['csscode'] = 'CSS code';
$string['cssdefault'] = 'Include default CSS styles';
$string['cssdefault_help'] = 'Define if CSS styles are included by default to the menu. Disable this option can be useful to include customised styles by the option <b>"(CSS) styles template"</b>';
$string['csstemplate'] = 'About: CSS styles';
$string['csstemplate_editmenu'] = 'Styles (CSS)';
$string['csstemplate_editmenu_title'] = 'CSS styles';
$string['csstemplate_help'] = 'Allows to include customized CSS styles which you can define a customized graphic appearance for the menu
<p>A simple exercise of using the styles template will be:</p>
<div style=" white-space:nowrap; font-size: 12px; border: 1px solid #666; padding: 5px; background-color: #CCC">
#format_menutopic_menu { margin-bottom: 10px; }
</div>
<p>With the previous code, the menu is separated 10px from the bottom, according to the position defined for the menu.</p>
<p><strong>Note:</strong>
<ul>
    <li>The identifier (id) of the layer (div) that the menu contains is <strong>format_menutopic_menu</strong>. This data can be useful to manipulate the menu styles without to affect other components of the page.</li>
    <li>It is possible that to make changes in the styles, they cannot visualize immediately in the course. If so, it must refresh the page. In many browsers, you can do it pressing the key combination Ctrl+F5.</li>
</ul></p>';
$string['currentsection'] = 'This topic';
$string['defaultsectionsnavigation'] = 'Default value to sections navigation';
$string['defaultsectionsnavigation_help'] = 'Default value used in courses to define the "Uses sections navigation" feature. This can be overwrite for each course.';
$string['displaynavigation'] = 'Display navigation';
$string['displaynavigation_help'] = 'Indicates whether to display navigation between sections and the position where the show.';
$string['displaynousedmod'] = 'Show resources not included in template';
$string['displaynousedmod_help'] = 'About: Show resources not included in template';
$string['editmenu'] = 'Edit menu';
$string['enableanchorposition'] = 'Enable anchor position';
$string['enableanchorposition_help'] = 'Use an anchor to navigate to the top of menu when click in a menu option.';
$string['end_editmenu'] = 'End Edit Menu';
$string['error_jsontree'] = 'Error in data structure returned as tree composition';
$string['globalstyle'] = 'Global style';
$string['globalstyle_help'] = 'This define the style of the menu.';
$string['hidden_message'] = 'The section <em>{$a}</em> is not currently available.';
$string['hiddenmenubar'] = 'The menu are set to be hidden. They will not be seen when not in edit mode.';
$string['hidefromothers'] = 'Hide topic';
$string['htmlcode'] = 'HTML';
$string['htmltemplate_editmenu'] = 'HTML template';
$string['htmltemplate_editmenu_title'] = 'HTML';
$string['icons_templatetopic'] = 'Show icons in resources names';
$string['icons_templatetopic_help'] = 'About: Show icons in resources names';
$string['jscode'] = 'Code';
$string['jsdefault'] = 'Include default JavaScript';
$string['jsdefault_help'] = 'Define if the Javascript functions that generate the menu are included. Disables the default javascript can be useful if you want to give another appearance to the menu using Javascript code that can be
included in the <b>"Javascript template"</b>.';
$string['jstemplate'] = 'JavaScript code';
$string['jstemplate_editmenu'] = 'Javascript template';
$string['jstemplate_editmenu_title'] = 'JavaScript code';
$string['jstemplate_help'] = 'Allows to define the JavaScript code that will work over the menu or the page. It can be useful to define additional behaviors for the menu
or even a menu structure different from the default.
<p><b>Notes:</b>
<ul>
    <li>The name <b>format_menutopic_menu</b> corresponds to the div identifier that contains the menu in HTML created as nest lists, usually with the tags HTML: ul y li.</li>
    <li>It is possible that to make changes in the JavaScript, they cannot visualize immediately in the course. If so, it must refresh the page. In many browsers, you can do it pressing the key combination Ctrl+F5.</li>
</ul></p>';
$string['linkinparent'] = 'Make links in submenu fields root';
$string['linkinparent_help'] = '<b>It only works on the basic menu style because Bootstrap doesn\'t support this functionality.</b>
<p>Define the behavior of the menu options that act as roots or fathers of a submenu.</p>
<p>If it is stablished in <b>Yes</b>, the menu item acts as a link to click on it and open the URL
that is defined in the <b>"Menu tree"</b>. If it is stablished in <b>Not</b>, the menu item deploys the son links to click
on it</p>';
$string['menuposition'] = 'Menu position';
$string['menuposition_help'] = '<p>Define the position where the menu will appear in the course. The possible options are:
<ul>
    <li><b>Do not show:</b> menu is not generated</li>
    <li><b>Left:</b> menu is generated vertically in the column of the left blocks, if exist.</li>
    <li><b>Middle:</b> menu is generated horizontally as a bar in the middle part of the course, over the section</li>
    <li><b>Right:</b> menu is generated vertically in the column of the right blocks, if exist.</li>
</ul></p>';
$string['menuposition_hide'] = 'Do not show';
$string['menuposition_left'] = 'Left';
$string['menuposition_middle'] = 'Middle';
$string['menuposition_right'] = 'Right';
$string['name_sheet_sheetedit'] = 'Sheet name';
$string['navbartitle'] = 'Sections';
$string['navigationposition_both'] = 'At top and bottom section';
$string['navigationposition_bottom'] = 'Only at the bottom';
$string['navigationposition_nothing'] = 'Not use';
$string['navigationposition_site'] = 'Use the default site value';
$string['navigationposition_slide'] = 'Like slides';
$string['navigationposition_support'] = 'Only if theme not support the "uses course index" feature';
$string['navigationposition_top'] = 'Only at the top';
$string['next_topic'] = 'Next';
$string['nodesnavigation'] = 'Navigation nodes';
$string['nodesnavigation_help'] = '<p>Section numbers, separated by commas. <b>Example:</b> 1,2,8,10,3</p>. If empty, default navigation is used.
<p>The section numbers cannot be repeated because they will show navigation from the first match found.</p>';
$string['notsaved'] = 'Information could not be saved';
$string['page-course-view-topics'] = 'Any course main page in menutopic format';
$string['page-course-view-topics-x'] = 'Any course page in menutopic format';
$string['plugin_description'] = 'Course sections are displayed as menu.';
$string['pluginname'] = 'Menutopic format';
$string['previous_topic'] = 'Previous';
$string['privacy:metadata'] = 'The Menutopic format plugin does not store any personal data.';
$string['savecorrect'] = 'Information was succesfully saved';
$string['sectionname'] = 'Topic';
$string['separator_navigation'] = ' - ';
$string['showfromothers'] = 'Show topic';
$string['shownavbarbrand'] = 'Show navbar brand';
$string['shownavbarbrand_help'] = 'Show the brand text of the navbar in Bootstrap styles.';
$string['style_basic'] = 'Basic';
$string['style_boots'] = 'Bootstrap';
$string['style_bootsdark'] = 'Dark Bootstrap';
$string['target_sheet_sheetedit'] = 'Link target';
$string['targetblank_sheet_sheetedit'] = 'New window';
$string['targetself_sheet_sheetedit'] = 'Same window';
$string['template_namemenutopic'] = 'Topic {$a}';
$string['templatetopic'] = 'Activate Description of the section as a template';
$string['templatetopic_help'] = 'About: Activate Title of the topic as a template';
$string['title_panel_sheetedit'] = 'Edit tree sheet';
$string['togglemenu'] = 'Toggle menu';
$string['topic_sheet_sheetedit'] = 'Target section';
$string['tree_editmenu'] = 'Menu tree';
$string['tree_editmenu_title'] = 'Configurate subject tree';
$string['tree_struct'] = 'Tree structure';
$string['tree_struct_help'] = '<p>The basis of the menu is a tree structure where each branch or tree sheet can be associated to a URL. The URL can be external or directly linked to a course section. When you sign the first time to set the section tree, the platform suggests a lineal structure, without branches, with a quantity of sheets equal to the number of course sections.</p>
<p>Among the options that you can do on the sheet are:</p>
<ul>
    <li>
        <strong>Edit a sheet (&#9997;):</strong> updates the select values to the properties of the selected sheet. The properties that can be modified are:
            <ul>
                <li><strong>Sheet name:</strong> the tag that appears for this sheet in the menu.</li>
                <li><strong>Target section:</strong> If the sheet is used for a course section, this option indicates what section will be the selected. If a section is selected, an external URL could not be selected to which direct the link of the option in the menu.</li>
                <li><strong>URL:</strong> indicates a URL to which will do reference the menu option. It is only can be especified if a target section was not selected.</li>
                <li><strong>Link target:</strong> Indicates if you want to open the link, the section or the external URL, in a new window or in the same window. If an option is not selected, the link will open in the same window.</li>
            </ul>
    </li>
    <li><strong>Move a sheet to left (&larr;):</strong> Converts to the sheet in sister of the sheet that contains it (parent sheet). It is only available if the sheet is daughter of another sheet, never if it is in the main branch.</li>
    <li><strong>Move a sheet to right (&rarr;):</strong> Converts to the sheet in daughter of the previous sheet. It is not available for the first sheet of the main branch.</li>
    <li><strong>Up a sheet (&uarr;):</strong> Changes the order of a sheet putting it before its brother inmediately previous. It is not available for the first sheet of a branch.</li>
    <li><strong>Down a sheet (&darr;):</strong> Changes the order of a sheet putting it after its brother inmediately next. It is not available for the last sheet of a branch.</li>
    <li><strong>Delete a sheet (&#10008;):</strong> Deletes the selected sheet and all sheets that it contains.</li>
    <li><strong>Add a sheet (&#10010;):</strong> Create a new sheet and add it as its daughter.</li>
</ul>
<p>The changes realized in the menu are stored only to select the option <strong>&quot;Save changes&quot;</strong> at the bottom of the page.</p>';
$string['url_sheet_sheetedit'] = 'URL';
$string['usescourseindex'] = 'Uses course index';
$string['usescourseindex_help'] = 'Use the <em>course index</em> bar to navigate through the sections and resources';
$string['usessectionsnavigation'] = 'Uses sections navigation';
$string['usessectionsnavigation_help'] = 'Show buttons for navigate to next or previous section.';
