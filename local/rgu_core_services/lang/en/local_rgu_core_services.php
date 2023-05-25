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

$string['pluginname'] = 'RGU Core Services';
$string['configtitle'] = 'RGU Core Services';
$string['page_general'] = 'General Features';
$string['number_in_lastname_enabled'] = 'Add student number to surnames';
$string['number_in_lastname_enabled_desc'] = 'If enabled, student numbers will be added in brackets to the end of students\' surnames.';
$string['page_sits_db'] = 'SITS Database';
$string['sits_db_enabled'] = 'Enable SITS Sync';
$string['sits_db_enabled_desc'] = 'Whether or not RGU Core Services will try to contact SITS.';
$string['sits_db_host'] = 'Database host';
$string['sits_db_host_desc'] = 'The hostname or IP that will be used to connect to the SITS database.';
$string['sits_db_database'] = 'Database name';
$string['sits_db_database_desc'] = 'The name of the database to read SITS data from.';
$string['sits_db_username'] = 'Username';
$string['sits_db_username_desc'] = 'The username that will be used to connect to the SITS database.';
$string['sits_db_password'] = 'Password';
$string['sits_db_password_desc'] = 'The password that will be used to connect to the SITS database.';
$string['sits_current_year'] = 'Current SITS Year';
$string['sits_current_year_desc'] = 'The current year to use when querying SITS. This will be updated when the rollover happens each summer.';
$string['page_avatar'] = 'Profile Picture Syncing';
$string['avatar_enabled'] = 'Enable Profile Picture Sync';
$string['avatar_enabled_desc'] = 'Whether or not RGU Core Services will try to contact the profile picture server.';
$string['avatar_host'] = 'FTP Server Hostname';
$string['avatar_host_desc'] = 'The FTP server to pull profile pictures from.';
$string['avatar_port'] = 'FTP Server Port';
$string['avatar_port_desc'] = 'The port used to connect to the FTP server.';
$string['avatar_username'] = 'FTP Username';
$string['avatar_username_desc'] = 'The username used to connect to the FTP server.';
$string['avatar_password'] = 'FTP Server Password';
$string['avatar_password_desc'] = 'The password used to connect to the FTP server.';
$string['avatar_folder'] = 'FTP Server Path';
$string['avatar_folder_desc'] = 'The folder on the FTP server to pull photos from. No trailing slash!';

$string['option_on'] = 'Enabled';
$string['option_off'] = 'Disabled';

$string['page_cron'] = 'Scheduled Task';
$string['cron_num_users'] = 'Number of users to update';
$string['cron_num_users_desc'] = 'When the scheduled task runs, it will update users who haven\'t logged in recently. This controls how many users you want to update every time the task runs. Setting it to a large number will put stress on the SITS DB mirror.';
$string['cron_inactive_time'] = 'Consider users inactive after';
$string['cron_inactive_time_desc'] = 'The scheduled task updates users who haven\'t logged in recently. Users are considered inactive and will be updated if they haven\'t logged in for this period of time.';
$string['cron_inactive_1_day'] = '1 day';
$string['cron_inactive_1_week'] = '1 week';
$string['cron_inactive_2_weeks'] = '2 weeks';
$string['cron_inactive_4_weeks'] = '4 weeks';
$string['cron_inactive_8_weeks'] = '8 weeks';
$string['cron_inactive_12_weeks'] = '12 weeks';
$string['cron_inactive_24_weeks'] = '24 weeks';
$string['cron_inactive_48_weeks'] = '48 weeks';
$string['cron_num_users'] = 'Number of courses to update';
$string['cron_num_users_desc'] = 'When the scheduled task runs, it will update courses that haven\'t been updated recently. This controls how many courses you want to update every time the task runs. This updates a few settings, including the library course ID number and the sort order.';

$string['updateusers'] = 'Update inactive users';
$string['updateusers'] = 'Update courses';

$string['page_course'] = 'Course Settings';
$string['library_idnumber_enabled'] = 'Save library codes to new courses';
$string['library_idnumber_enabled_desc'] = 'If enabled, this will find current courses with nothing in the idnumber field, and put the library course code in there.';

