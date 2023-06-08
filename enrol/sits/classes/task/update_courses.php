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
 * @package    enrol_sits
 * @copyright  2023 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_sits\task;

defined('MOODLE_INTERNAL') || die;

class update_courses extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('backgroundrefresh', 'enrol_sits');
    }

    public function execute() {
        global $DB;

        $days = get_config('enrol_sits', 'cron_inactive_time');
        $limit = get_config('enrol_sits', 'cron_num_courses');

        $courses = $DB->get_records_sql(
            'select distinct courseid from {enrol} where enrol="sits" and (customint8 < :inactivetime or customint8 is null)',
            ['inactivetime' => (time() - ($days))],
            0,
            $limit
        );

        foreach ($courses as $course) {
            $plugin = enrol_get_plugin('sits');
            $plugin->check_instance($course->courseid);            
            $sync = new \enrol_sits\task\sync_course();
            $sync->set_custom_data(array('courseid'=>$course->courseid, 'reason'=>'scheduled task'));
            \core\task\manager::queue_adhoc_task($sync);
        }
        
        $expirytime = time() - (86400*30);
        $usersToDelete = $DB->get_records_sql('SELECT * FROM {enrol_sits_users} WHERE frozen=1 AND timeupdated<'.$expirytime.' AND instanceid IN (SELECT id FROM {enrol} WHERE enrol="sits" AND status=0 AND customint2=2)');
        
        foreach($usersToDelete as $victim) {
            $instance = $DB->get_record('enrol', array('id'=>$victim->instanceid));
            $user = $DB->get_record('user', array('id'=>$victim->userid));
            $plugin->addToLog($instance->id, $instance->courseid, 'r', 'Deleting '.$user->firstname.' '.$user->lastname.' from the course.');
            
            $DB->delete_records('enrol_sits_users', array('id'=>$victim->id));
            //$DB->delete_records('user_enrolments', array('enrolid'=>$victim->instanceid,$userid=>$victim->userid));
            $plugin->unenrol_user($instance->id, $user->id);
        }
        
        $DB->delete_records_select('enrol_sits_log', 'timeadded < '.$expirytime);
    }

}
