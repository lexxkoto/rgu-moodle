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

require_login();

$courseID = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id'=>$courseID));

$force = false;
if(isset($_GET['force'])) {
    $force = true;
}

require_login();

$context = context_course::instance($course->id, MUST_EXIST);
require_capability('enrol/sits:manage', $context);

include('raw-styles.css');

echo '<pre>';
echo 'Detecting Rules'.PHP_EOL;
echo $course->shortname.PHP_EOL;
$plugin = enrol_get_plugin('sits');
$instance = $plugin->check_instance($courseID);
$plugin->sniffCourseRules($course->id, $instance, $force, true);
echo '</pre>';

include('back-to-course.php');
