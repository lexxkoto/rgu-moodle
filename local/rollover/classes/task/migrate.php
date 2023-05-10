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

class migrate extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('migrate', 'local_rollover');
    }

    public function execute() {
        global $DB;

        $old = get_config('local_rollover', 'old_string');
        $new = get_config('local_rollover', 'new_string');
        $limit = get_config('local_rollover', 'cron_num_courses');

        $courses = $DB->get_records_sql(
            'select * from {course} where fullname like "%:newplaceholder%" and id not in (select newcourse from {local_rollover_processed})',
            ['newplaceholder' => $new],
            0,
            $limit
        );

        foreach ($courses as $newCourse) {
            // Can we find an old course?
            
            $oldCourseName = str_replace($new, $old, $newCourse->fullname);
            
            $oldCourse = $DB->get_record('course', array('fullname'=>$oldCourseName), 'u.id id, ', IGNORE_MISSING);
            
            if($oldCourse !== null) {
                $oldCourseID = $oldCourse->id;
                $newCourseID = $newCourse->id;
                
                $oldCourseContext = context_course::instance($oldCourseID);
                $newCourseContext = context_course::instance($newCourseID);
                
                // Make sure there's a manual enrolment instance
                
                $plugin = enrol_get_plugin('manual');
                $enrolID = $plugin->add_default_instance($newCourseID);
                
                // Move things over
                
                // User enrolments
                
                // Anyone with this capability is rolled over to the new course
                $users = get_enrolled_users($oldCourseContext, 'local/rollover:include', 0, '*');
                
                $allowedRoles = array_flip(get_roles_with_cap_in_context($oldCourseContext, 'local/rollover:include'));
                
                foreach($users as $user) {
                    // Get the roles from the old course
                    $roles = get_user_roles($oldCourseContext, $user->id, false);
                    foreach($roles as $role) {
                    if(array_key_exists($role->roleid, $allowedRoles)) {
                        $plugin->enrol_user($enrolID, $user->id, $role->roleid);
                    }
                }
                
                // Save a record that we've done this course
                
                $record = new stdClass();
                $record->oldcourse = $oldCourseID;
                $record->newcourse = $newCourseID;
                $record->timemodified = time();
                
                $DB->insert_record('local_rollover_processed', $record);
            }
            
            //mtrace('Updating user '.$thisuser->id.' - Student ID '.$thisuser->idnumber);
            \local_rgu_core_services_observer::update_user($thisuser->id);
        }
    }

}
