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
 * This page shows all course enrolment options for current user.
 *
 * @package    core_enrol
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$CFG->debug = 0;
$CFG->debugdisplay = 0;

$force = false;
if(isset($_GET['force'])) {
    $force = true;
}

require_login();

$courseID = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id'=>$courseID));

require_login();

$context = context_course::instance($course->id, MUST_EXIST);
require_capability('enrol/sits:manage', $context);

include('raw-styles.css');

echo '<pre>';
$plugin = enrol_get_plugin('sits');
$usersToDelete = $DB->get_records_sql('SELECT * FROM {enrol_sits_users} WHERE frozen=1 AND instanceid IN (SELECT id FROM {enrol} WHERE enrol="sits" AND status=0 AND courseid='.$courseID.')');

foreach($usersToDelete as $victim) {
    $instance = $DB->get_record('enrol', array('id'=>$victim->instanceid));
    $user = $DB->get_record('user', array('id'=>$victim->userid));
    $plugin->addToLog($instance->id, $instance->courseid, 'r', 'Deleting '.$user->firstname.' '.$user->lastname.' from the course.', true);
    
    $DB->delete_records('enrol_sits_users', array('id'=>$victim->id));
    //$DB->delete_records('user_enrolments', array('enrolid'=>$victim->instanceid,$userid=>$victim->userid));
    $plugin->unenrol_user($instance, $user->id);
}
echo '</pre>';

include('back-to-course.php');