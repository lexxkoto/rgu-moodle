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

namespace local_rollover\task;

use context_course;

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
            'select * from {course} where fullname like CONCAT("%", ?, "%") and id not in (select newcourse from {local_rollover_processed})',
            [$new],
            0,
            $limit
        );
        
        if(empty($new) || empty($old) || empty($limit)) {
            mtrace('Looks like the plugin is not configured. Quitting.');
            return false;
        }
        
        mtrace('Considering '.count($courses).' courses for rollover...');

        foreach ($courses as $newCourse) {
        
            mtrace('Considering course '.$newCourse->id.' - '.$newCourse->fullname);
        
            // Can we find an old course?
            
            $oldCourseName = str_replace($new, $old, $newCourse->fullname);
            
            $oldCourse = $DB->get_record('course', array('fullname'=>$oldCourseName), '*', IGNORE_MISSING);
            
            if($oldCourse !== false && $oldCourse->id != $newCourse->id) {
            
                mtrace('Found match in course '.$oldCourse->id.' - '.$oldCourse->fullname);
            
                $oldCourseID = $oldCourse->id;
                $newCourseID = $newCourse->id;
                
                $oldCourseContext = context_course::instance($oldCourseID);
                $newCourseContext = context_course::instance($newCourseID);
                
                // Make sure there's a manual enrolment instance
                
                $plugin = enrol_get_plugin('manual');
                $plugin->add_default_instance($newCourse);
                $thisEnrolment = $DB->get_record('enrol', Array('courseid'=>$newCourseID, 'enrol'=>'manual'));
                
                // Move things over
                
                // User enrolments
                
                // Anyone with this capability is rolled over to the new course
                $users = get_enrolled_users($oldCourseContext, 'local/rollover:include', 0, '*');
                
                $allowedRoles = get_roles_with_cap_in_context($oldCourseContext, 'local/rollover:include');
                
                foreach($users as $user) {
                    mtrace('Migrating user '.$user->id.' - '.$user->firstname.' '.$user->lastname);
                    // Get the roles from the old course
                    $roles = get_user_roles($oldCourseContext, $user->id, false);
                    foreach($roles as $role) {
                        if(array_key_exists($role->roleid, $allowedRoles[0])) {
                            mtrace('Giving them role '.$role->roleid);
                            $plugin->enrol_user($thisEnrolment, $user->id, $role->roleid);
                        } else {
                            mtrace('Role '.$role->roleid.' not in the approved list');
                        }
                    }
                }
                
                // SITS Sync Rules
                $plugin = enrol_get_plugin('sits');
                
                if($plugin->is_configured()) {
                     $instances = $plugin->getEnrolInstancesForCourse($oldCourseID);
                     
                     if(!empty($instances)) {
                        
                        $firstRun = true;
                        
                        foreach($instances as $oldInstance) {
                            if($firstRun) {
                                // Every course comes with a default instance. If this
                                // is the first instance we're touching, add the rules
                                // to it. Otherwise, make a new instance.
                                $newInstance = $plugin->check_instance($newCourseID);
                                $firstRun = false;
                            } else {
                                // Horrible misuse of this function
                                $newInstance = $plugin->add_first_instance($newCourse);
                            }
                            
                            $rules = $plugin->getCodesForInstance($oldInstance->id);
                            
                            foreach($rules as $rule) {
                                unset($rule->id);
                                $rule->instanceid = $newInstance;
                                $rule->timeadded = time();
                                
                                // If this is a module and there's a year attached,
                                // add one to the year.
                                
                                if($rule->type == 'module' && !empty($rule->year)) {
                                    $rule->year++;
                                }
                                
                                $DB->insert_record('enrol_sits_code', $rule);
                                
                            }
                        }
                        
                     }
                }
                
                // Save a record that we've done this course
                
                $record = new \stdClass();
                $record->oldcourse = $oldCourseID;
                $record->newcourse = $newCourseID;
                $record->timemodified = time();
                
                $DB->insert_record('local_rollover_processed', $record);
            } else {
                mtrace('No match found');
            }
        }
    }

}
