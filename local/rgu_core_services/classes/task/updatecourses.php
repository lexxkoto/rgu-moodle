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

class updatecourses extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('updatecourses', 'local_rgu_core_services');
    }

    public function execute() {
        global $DB;

        $limit = get_config('local_rgu_core_services', 'cron_num_courses');

        $now = time();

        $courses = $DB->get_records_sql(
            'select id from {course} where (startime<'.$now.' and endtime>'.$now.')'.
            'and (idnumber='' or sortorder='')',
            0,
            $limit
        );

        foreach ($courses as $course) {
            //mtrace('Updating user '.$thisuser->id.' - Student ID '.$thisuser->idnumber);
            \local_rgu_core_services_observer::update_course($course->id);
        }
    }

}
