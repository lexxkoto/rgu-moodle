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
 * UofG Database enrolment plugin.
 *
 * This plugin synchronises enrolment and roles with external database table.
 *
 * @package    enrol
 * @subpackage gudatabase
 * @copyright  2012 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class enrol_sits_observer {

    /**
     * Triggered when a course is reset
     */
    public static function course_reset_ended(\core\event\course_reset_ended $event) {
        global $DB;

        $courseid = $event->courseid;
        $plugin = enrol_get_plugin('sits');
        
        enrol_sits_plugin::addToLog(-1, $courseid, 'i', 'SITS sync triggered by course reset');
    }
    
    public static function course_updated(\core\event\course_updated $event) {
        global $DB;
        
        $courseid = $event->courseid;
        
        $plugin = enrol_get_plugin('sits');
        $plugin->check_instance($courseid);
        $plugin->addToLog(-1, intval($courseid), 'i', 'SITS sync triggered by course settings change');
        
        $sync = new \enrol_sits\task\sync_course();
        $sync->set_custom_data(array('courseid'=>$courseid));
        \core\task\manager::queue_adhoc_task($sync);
    }
    
    public static function course_viewed(\core\event\course_viewed $event) {
        return false;
        global $DB;
        
        $courseid = $event->courseid;
        
        $plugin = enrol_get_plugin('sits');
        $plugin->check_instance($courseid);
        $plugin->addToLog(-1, intval($courseid), 'i', 'SITS sync triggered by course view');
    }
    
    public static function enrol_instance_updated(\core\event\enrol_instance_updated $event) {
        global $DB;
        
        
    }

    /**
     * Triggered when a course is created
     */
    public static function course_created(\core\event\course_created $event) {
        global $DB, $USER;

        $courseid = $event->objectid;
        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
        
        $plugin->ddToLog(-1, $courseid, 'i', 'SITS sync triggered by course creation');

	// get default course length
        $courseconfig = get_config('moodlecourse');
        
        // how many days is current end
        $currentlength = $course->enddate - time();

        // Check if this is 'close' as Moodle does some odd things with timestamp
        // calculation
        if (abs($currentlength - $courseconfig->courseduration) < 3 * 86400) {

            // Set to end of july
            $month = date('m');
            $year = date('Y');
            if ($month > 7) {
                $year++;
            }
            $future = strtotime("$year-07-31");
            $course->enddate = $future;
            $DB->update_record('course', $course);
        }

        return;
    }

}
