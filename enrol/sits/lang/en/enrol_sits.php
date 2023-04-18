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
 * Strings for component 'enrol_database', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   enrol_sits
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'RGU SITS Sync';
$string['pluginname_desc'] = 'Lets you add an entire class of students from SITS and keep them in sync. This replaces the old AutoEnrol system.';
$string['configtitle'] = 'RGU SITS Sync';
$string['backgroundrefresh'] = 'RGU SITS Background Refresh';

$string['page_sits_db'] = 'SITS Database';
$string['sits_db_enabled'] = 'Enable SITS Sync';
$string['sits_db_enabled_desc'] = 'If this is disabled, students will not be synced with SITS.';
$string['sits_db_host'] = 'Database host';
$string['sits_db_host_desc'] = 'The hostname or IP that will be used to connect to the SITS database.';
$string['sits_db_database'] = 'Database name';
$string['sits_db_database_desc'] = 'The name of the database to read SITS data from.';
$string['sits_db_username'] = 'Username';
$string['sits_db_username_desc'] = 'The username that will be used to connect to the SITS database.';
$string['sits_db_password'] = 'Password';
$string['sits_db_password_desc'] = 'The password that will be used to connect to the SITS database.';

$string['page_parameters'] = 'SITS Sync Database Parameters';
$string['sits_current_year'] = 'Current SITS Year';
$string['sits_current_year_desc'] = 'The current year to use when querying SITS. This will be updated when the rollover happens each summer.';
$string['sits_allowed_codes'] = 'Allowed Status Codes';
$string['sits_allowed_codes_desc'] = 'For enrolment rules that talk to SITS, this is the list of status codes that are allowed to be enrolled.';

$string['option_on'] = 'Enabled';
$string['option_off'] = 'Disabled';

$string['section_settings'] = 'Settings';
$string['section_codes'] = 'Enrolment Rules';

$string['customname'] = 'Custom Name';
$string['customname_desc'] = 'If you have a complex set of rules, you can add multiple copies of the SITS enrolment service to your course. This lets you give each one a name, so you can tell them apart.';
$string['instance_enabled'] = 'Sync Users';
$string['instance_enabled_desc'] = 'If you set this to "No", your course will not be synced with SITS. New users will not be added, and old users will not be removed. This is useful for old archived courses where you want users to stay as they are.';
$string['roleid'] = 'Role to give to users';
$string['roleid_desc'] = 'If you\'re adding students to a course, this should always be "Student". You can use the SITS enrolment service to add staff members to a course, which is why this option exists.';
$string['expireaction'] = 'When a user is no longer in SITS:';
$string['expireaction_desc'] = '<ul><li><strong>Keep the users active in the course - </strong>old users will stay in the course and will still be able to access it. If you want to remove a user from the course, you\'ll need to do it manually from the Participants page.</li><li><strong>Hide the users, but keep all their work</strong> - the users will be suspended. You won\'t see them listed anywhere and they won\'t be able to access the course, but all their old work will be saved. If you add them back into the course later, all their old work will re-appear. This is the default, and it is the safest option to choose.</li><li><strong>Delete the users and their work - </strong>the users will be suspended right away (see above). After 24 hours they will be permanently deleted, unless they are added back onto the course.</li></ul>';

$string['managerules'] = 'Manage Enrolment Rules';
$string['page_cron'] = 'Scheduled Task';
$string['cron_num_courses'] = 'Number of courses to update';
$string['cron_num_courses_desc'] = 'When the scheduled task runs, it will update courses that haven\'t been updated recently. This controls how many courses you want to update every time the task runs. Setting it to a large number will put stress on the SITS DB mirror.';
$string['cron_inactive_time'] = 'Consider courses inactive after';
$string['cron_inactive_time_desc'] = 'The scheduled task updates courses that haven\'t been updated recently. Courses are considered inactive if a SITS sync hasn\'t run for this period of time.';
$string['cron_inactive_1_day'] = '1 day';
$string['cron_inactive_1_week'] = '1 week';
$string['cron_inactive_2_weeks'] = '2 weeks';
$string['cron_inactive_4_weeks'] = '4 weeks';
$string['cron_inactive_8_weeks'] = '8 weeks';
$string['cron_inactive_12_weeks'] = '12 weeks';
$string['cron_inactive_24_weeks'] = '24 weeks';
$string['cron_inactive_48_weeks'] = '48 weeks';
$string['trigger_inactive_time'] = 'Triggered syncs won\'t repeat within';
$string['trigger_inactive_time_desc'] = 'When a sync is triggered by a user action instead of a scheduled task, it won\'t run if it has run within the time set here.';
$string['trigger_5_minutes'] = '5 minutes';
$string['trigger_15_minutes'] = '15 minutes';
$string['trigger_30_minutes'] = '30 minutes';
$string['trigger_1_hour'] = '1 hour';
$string['trigger_2_hours'] = '2 hours';
$string['trigger_4_hours'] = '4 hours';
$string['trigger_6_hours'] = '6 hours';
$string['trigger_12_hours'] = '12 hours';
$string['trigger_1_day'] = '1 day';

$string['addrule'] = 'Add Rule';
$string['viewlog'] = 'View Log';

$string['task_synccourse'] = 'Sync Course';
$string['task_deleteusers'] = 'Delete Users';
$string['navlink'] = 'SITS Sync';