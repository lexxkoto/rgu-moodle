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
 * @package    block
 * @subpackage rgu_course_overview
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

// Required strings
$string['pluginname'] = 'RGU course overview';

// Capability strings
$string['rgu_course_overview:addinstance'] = 'Add a new course overview block';
$string['rgu_course_overview:myaddinstance'] = 'Add a new course overview block to My home';

// Block strings
$string['allmystudyareas'] = 'All My Study Areas';
$string['nostudyareasfound'] = 'No study area found';
$string['blockinstancesettings'] = 'Block instance settings';
$string['currentstudyareas'] = 'Current Academic Year';
$string['coursestudyareas'] = 'Course Study Areas';
$string['schoolstudyareas'] = 'School  Study Areas';
$string['generalstudyareas'] = 'General Study Areas';




$string['defaultpaginationvalue'] = 'Default pagination value';
$string['defaultpaginationvaluedesc'] = 'Default number of courses to list within each paginated set';
$string['displayingx-ycoursesofz'] = '(Displaying {$a->firstrecord}-{$a->lastrecord} courses of {$a->totalrecords})';
$string['generalstudyareas'] = 'General Study Areas';
$string['hideheader'] = 'Hide header';
$string['hideheaderdesc'] = 'The the area above the RGU course overview block?';
$string['instancepaginationvalue'] = 'Instance pagination value';
$string['instancepaginationvalue_help'] = 'By default this setting takes the \'Default pagination value\' from the plugin global settings. If the value is updated here, it overrides the global setting and affects this instance of the block only.';
$string['modulestudyareas'] = 'Module Study Areas';
$string['futuremodulestudyareas'] = 'Future Module Study Areas';
$string['modulesyearnextyear'] = 'Modules {$a->year}/{$a->nextyear}';
$string['nextsessionsstudyareas'] = 'Next Session\'s Study Areas'; // ?
$string['nocurrentmodulestudyareasavailable'] = 'No current module study areas available.'; // ?
$string['nopreviousmodulestudyareasavailable'] = 'No previous module study areas available'; // ?
$string['previousstudyareas'] = 'Previous Academic Years';
$string['searchmystudyareas'] = 'Search My Study Areas';
$string['submitform'] = 'Submit Form';
$string['hide_recent'] = 'Hide recent modules';
$string['hide_recent_desc'] = 'Set to 1 to hide the recent modules tab';
$string['min_recent'] = 'Minimum number of recent modules';
$string['min_recent_desc'] = 'Hide recent modules tabs if users has less than this numbeer of courses';
$string['show_x_recent_courses'] = 'Number of recent courses';
$string['show_x_recent_courses_desc'] = 'Show this number of recent courses';
$string['recentstudyareas'] = 'Recently visited study areas';
$string['currentyearofstudy'] = 'Current year of study';
$string['currentyearofstudydesc'] = 'Current year of study';
$string['teamworkareas'] = 'Teamwork Areas';
$string['nostudyareasavailable'] = 'No study areas available'; // ?
$string['displayallstudyareas'] = '(Displaying all search results)'; 

$string['showbanners_returntosettings'] = 'Return to settings page';
$string['showbanners'] = 'Show banners';
$string['showbannerseditorlink'] = 'Configure banners';
$string['showbanners_desc'] = '	<div><strong>Use this page to edit or create new banners</strong><div>
								<div><strong>Notes</strong><div>
								<ul>
									<li>If a course id is entered, the banner will be displayed if a user is attached to that study area.</li>
									<li>If no course id is entered but a regular expression is, the banner will be displayed if a user is attached to a study area that matches the regex.</li>
									<li>"Standard" will produced a standard banner linked to a course (either the course id or the first course picked up by the regex).</li>
									<li>"Custom" allows any html as a banner</li>
									<li>In custom banners, #courseid# will be replaced by either the entered course id or the id of the first study area found by the regex.</li>
		
								</ul>';
$string['title'] = 'My Study Areas';
$string['tab_modulestudyareas'] = 'Modules';
$string['tab_coursestudyareas'] = 'Course Areas'; 
$string['tab_schoolstudyareas'] = 'School Areas'; 
$string['tab_generalstudyareas'] = 'General Areas';
$string['tab_recentstudyareas'] = 'Recently Accessed';
$string['tab_allmystudyareas'] = 'All My Study Areas'; 

$string['blocktitle'] = 'My Study Areas';