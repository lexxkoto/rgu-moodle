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
 * @package    local_backupcleaner
 * @copyright  2021 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_rgu_core_services\task;

defined('MOODLE_INTERNAL') || die;

class updateusers extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('updateusers', 'local_rgu_core_services');
    }

    public function execute() {
        global $DB;

        $days = get_config('local_rgu_core_services', 'cron_inactive_time');
        $limit = get_config('local_rgu_core_services', 'cron_num_users');

        $users = $DB->get_records_sql(
            'select id, idnumber from mdl_user u where id not in (select d.userid from mdl_user_info_field f join mdl_user_info_data d on f.id = d.fieldid where f.shortname="rgu_lastupdate" and d.data > :inactivetime) and u.institution="Student" and u.deleted=0',
            ['inactivetime' => (time() - ( $days))],
            0,
            $limit
        );

        foreach ($users as $thisuser) {
            //mtrace('Updating user '.$thisuser->id.' - Student ID '.$thisuser->idnumber);
            \local_rgu_core_services_observer::update_user($thisuser->id);
        }
    }

}
