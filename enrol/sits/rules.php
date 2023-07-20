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

$courseID = required_param('id', PARAM_INT);
$course = $DB->get_record('course', array('id'=>$courseID));

$instances = $DB->get_records('enrol', array('courseid'=>$courseID, 'enrol'=>'sits'));



require_login();

$context = context_course::instance($course->id, MUST_EXIST);

require_capability('enrol/sits:manage', $context);

$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_url('/enrol/sits/rules.php', array('id'=>$course->id));

$PAGE->set_title(get_string('managerules', 'enrol_sits'));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('enrolmentoptions','enrol'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managerules', 'enrol_sits'));

$courserenderer = $PAGE->get_renderer('core', 'course');

$output = $PAGE->get_renderer('enrol_sits');

$plugin = enrol_get_plugin('sits');

echo '
    <ul class="nav nav-pills mt-4 mb-4">
        <li class="nav-item"><a class="nav-link active" href="rules.php?id='.$courseID.'"><i class="fa fa-wrench"></i>&ensp;Manage Rules</a></li>
        <li class="nav-item"><a class="nav-link" href="log.php?id='.$courseID.'"><i class="fa fa-list"></i>&ensp;View Logs</a></li>
        <li class="nav-item"><a class="nav-link" href="detect.php?id='.$courseID.'"><i class="fa fa-bolt"></i>&ensp;Detect Rules</a></li>
        <li class="nav-item"><a class="nav-link" href="force.php?id='.$courseID.'"><i class="fa fa-refresh"></i>&ensp;Sync Users Now</a></li>
    </ul>';

foreach($instances as $instance) {
    if(!empty($instance->name)) {
        echo '<h3 class="mt-4">Rules for '.$instance->name.'</h3>';
    } else {
        echo '<h3 class="mt-4">Rules for SITS Sync</h3>';
    }
    $instanceID = $instance->id;
    $codes = $plugin->getCodesForInstance($instanceID);
    echo $output->print_codes($codes);
    echo $output->print_enrol_buttons($instanceID);
}

echo $OUTPUT->footer();
