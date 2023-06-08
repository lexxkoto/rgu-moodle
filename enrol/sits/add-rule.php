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

$instanceID = required_param('instance', PARAM_INT);
$rule = required_param('rule', PARAM_TEXT);

$instance = $DB->get_record('enrol', array('id'=>$instanceID, 'enrol'=>'sits'));

$course = $DB->get_record('course', array('id'=>$instance->courseid));

require_login();

$context = context_course::instance($course->id, MUST_EXIST);

require_capability('enrol/sits:manage', $context);

$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->add_body_class('limitedwidth');
$PAGE->set_url('/enrol/sits/add-rule.php', array('instance'=>$instanceID, 'rule'=>$rule));

$PAGE->set_title(get_string('addrule', 'enrol_sits'));
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add(get_string('enrolmentoptions','enrol'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('addrule', 'enrol_sits'));

$PAGE->requires->js_call_amd('enrol_sits/showfilters', 'init', array());

$courserenderer = $PAGE->get_renderer('core', 'course');

$output = $PAGE->get_renderer('enrol_sits');

echo '<form method="post" action="add-rule-process.php">';
echo '<input type="hidden" name="instance" value="'.$instanceID.'" />';
echo '<input type="hidden" name="type" value="'.$rule.'" />';
switch($rule) {
        case 'module':
        echo '<input type="hidden" name="token" value="'.md5('TheHandThatFeeds'.$instanceID.$rule).'" />';
        echo '<h3 class="mt-4 mb-4">Add Students by Module</h3>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Module Code:</div><div class="col-sm-9">';
        echo '<input type="text" class="form-control mb-2" name="code" id="code" />';
        echo '<p class="text-dimmed">You can type multiple module codes with a comma between them.</p>';
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Academic Year:</div><div class="col-sm-9">';
        echo '<input type="text" class="form-control mb-2" name="year" id="year" />';
        echo '<p class="text-dimmed">This should be four digits (e.g. 2023).</p>';
        echo '</div></div>';
        echo '<div class="row mb-4" id="ruleFilterEnabler"><div class="col-sm-3 col-form-label">Filter Students:</div><div class="col-sm-9">';
        echo '<a class="btn btn-secondary mb-2" href="#" id="showRuleFilterBox"><i class="fa fa-filter"></i> Show Filter Options</a> ';
        echo '<p class="text-dimmed">If you want to filter students based on their mode of attendance, start month, block or any other option, use the button above. Otherwise, all students who match the module code above will be added.</p>';
        echo '</div></div>';
        echo '<div id="ruleFilterBox" class="hide">';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Modes of Attendance:</div><div class="col-sm-9">';
        $output->print_modes();
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Academic Levels:</div><div class="col-sm-9">';
        $output->print_levels();
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Filter By Course Code:</div><div class="col-sm-9">';
        echo '<input type="text" class="form-control mb-2" name="course" id="course" />';
        echo '<p class="text-dimmed">If you leave this blank, any student who matches the module codes above will be added. If you type a course code here, only students who are taking the module as part of this course will be added. You can type multiple course codes with a comma between them.</p>';
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Occurrence:</div><div class="col-sm-9">';
        echo '<input type="text" class="form-control mb-2" name="occurrence" id="occurrence" />';
        echo '<p class="text-dimmed">You can type multiple occurrences with a comma between them. If you leave this blank, students from every occurence will be enrolled.</p>';
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Start Month:</div><div class="col-sm-9">';
        $output->print_months();
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Period:</div><div class="col-sm-9">';
        $output->print_periods();
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Blocks:</div><div class="col-sm-9">';
        echo '<input type="text" class="form-control mb-2" name="blocks" id="blocks" />';
        echo '<p class="text-dimmed">You can type multiple block codes with commas between them. If you type something here, only students in those blocks will be enrolled. If you leave this blank, everyone will be enrolled.</p>';
        echo '</div></div>';
        echo '</div>';
        echo '<div class="row mb-4"><div class="col-sm-3"></div><div class="col-sm-9"><button type="submit" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add Rule</button></div></div>';
        break;
    case 'course':
        echo '<input type="hidden" name="token" value="'.md5('TheHandThatFeeds'.$instanceID.$rule).'" />';
        echo '<h3 class="mt-4 mb-4">Add Students by Course</h3>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Course Code:</div><div class="col-sm-9">';
        echo '<input type="text" class="form-control mb-2" name="code" id="code" />';
        echo '<p class="text-dimmed">You can type multiple course codes with a comma between them.</p>';
        echo '</div></div>';
        echo '<div class="row mb-4" id="ruleFilterEnabler"><div class="col-sm-3 col-form-label">Filter Students:</div><div class="col-sm-9">';
        echo '<a class="btn btn-secondary mb-2" href="#" id="showRuleFilterBox"><i class="fa fa-filter"></i> Show Filter Options</a> ';
        echo '<p class="text-dimmed">If you want to filter students based on their mode of attendance, start month, block or any other option, use the button above. Otherwise, all students who match the module code above will be added.</p>';
        echo '</div></div>';
        echo '<div id="ruleFilterBox" class="hide">';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Academic Levels:</div><div class="col-sm-9">';
        $output->print_levels();
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Blocks:</div><div class="col-sm-9">';
        echo '<input type="text" class="form-control mb-2" name="blocks" id="blocks" />';
        echo '<p class="text-dimmed">You can type multiple block codes with commas between them. If you type something here, only students in those blocks will be enrolled. If you leave this blank, everyone will be enrolled.</p>';
        echo '</div></div>';
        echo '</div>';
        echo '<div class="row mb-4"><div class="col-sm-3"></div><div class="col-sm-9"><button type="submit" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add Rule</button></div></div>';
        break;
    case 'school':
        echo '<input type="hidden" name="token" value="'.md5('TheHandThatFeeds'.$instanceID.$rule).'" />';
        echo '<h3 class="mt-4 mb-4">Add All Students in a School</h3>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">School:</div><div class="col-sm-9">';
        $output->print_schools();
        echo '</div></div>';
        echo '<div class="row mb-4" id="ruleFilterEnabler"><div class="col-sm-3 col-form-label">Filter Students:</div><div class="col-sm-9">';
        echo '<a class="btn btn-secondary mb-2" href="#" id="showRuleFilterBox"><i class="fa fa-filter"></i> Show Filter Options</a> ';
        echo '<p class="text-dimmed">If you want to filter students based on their mode of attendance, start month, block or any other option, use the button above. Otherwise, all students who match the module code above will be added.</p>';
        echo '</div></div>';
        echo '<div id="ruleFilterBox" class="hide">';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Academic Levels:</div><div class="col-sm-9">';
        $output->print_levels();
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Blocks:</div><div class="col-sm-9">';
        echo '<input type="text" class="form-control mb-2" name="blocks" id="blocks" />';
        echo '<p class="text-dimmed">You can type multiple block codes with commas between them. If you type something here, only students in those blocks will be enrolled. If you leave this blank, everyone will be enrolled.</p>';
        echo '</div></div>';
        echo '</div>';
        echo '<div class="row mb-4"><div class="col-sm-3"></div><div class="col-sm-9"><button type="submit" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add Rule</button></div></div>';
        break;
    case 'all-students':
        require_capability('enrol/sits:bulk', $context);
        echo '<input type="hidden" name="token" value="'.md5('TheHandThatFeeds'.$instanceID.$rule).'" />';
        echo '<h3 class="mt-4 mb-4">Add All Students to Course</h3>';
        echo '<div class="row mb-4"><div class="col-sm-3"></div><div class="col-sm-9"><p><strong>Don\'t use this option unless you really need to.</strong> There are a lot of students at the University, and adding them all to one Moodle course can make the course very slow and unpredictable.</p>';
        echo '<p>Consider putting the information on the main University website or intranet instead.</p></div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3"></div><div class="col-sm-9"><button type="submit" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add Rule</button></div></div>';
        break;
    case 'all-staff':
        require_capability('enrol/sits:bulk', $context);
        echo '<input type="hidden" name="token" value="'.md5('TheHandThatFeeds'.$instanceID.$rule).'" />';
        echo '<h3 class="mt-4 mb-4">Add All Staff to Course</h3>';
        echo '<div class="row mb-4"><div class="col-sm-3"></div><div class="col-sm-9"><p><strong>Don\'t use this option unless you really need to.</strong> There are a lot of staff at the University, and adding them all to one Moodle course can make the course very slow and unpredictable.</p>';
        echo '<p>Consider putting the information on the main University website or intranet instead.</p></div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3"></div><div class="col-sm-9"><button type="submit" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add Rule</button></div></div>';
        break;
    case 'dept-staff':
        echo '<input type="hidden" name="token" value="'.md5('TheHandThatFeeds'.$instanceID.$rule).'" />';
        echo '<h3 class="mt-4 mb-4">Add All Staff in a Department</h3>';
        echo '<div class="row mb-4"><div class="col-sm-3 col-form-label">Department:</div><div class="col-sm-9">';
        $output->print_staff_depts();
        echo '</div></div>';
        echo '<div class="row mb-4"><div class="col-sm-3"></div><div class="col-sm-9"><button type="submit" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add Rule</button></div></div>';
        break;
}

echo '</form>';

echo $OUTPUT->footer();
