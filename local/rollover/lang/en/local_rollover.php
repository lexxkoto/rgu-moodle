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
 * GUID Enrolment sync
 *
 * @package    local_rgu_core_services
 * @copyright  2021 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Annual Rollover';
$string['migrate'] = 'Migrate from Old Courses';
$string['configtitle'] = 'Annual Rollover';
$string['page_matching'] = 'Course Matching Rules';
$string['new_string'] = 'Code for current year';
$string['new_string_desc'] = 'The rollover plugin will find courses that have this code in the title, and will try and import data from older course into them. This is the code for the current/upcoming year.';
$string['old_string'] = 'Code for previous year';
$string['old_string_desc'] = 'The rollover plugin will use this to match a previous course to a new course. It replaces the new string with this one, and if it finds a match, it will copy the details from this course to the new one.';
$string['cron_num_courses'] = 'Number of courses to update each run';
$string['cron_num_courses_desc'] = 'Every time the scheduled task runs, it will update this many courses. If course data isn\'t being migrated fast enough, increase this number.';
$string['include'] = 'Included in course rollover';
