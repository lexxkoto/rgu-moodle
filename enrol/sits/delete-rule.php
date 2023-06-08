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

function ifPosted($name, $required=false) {
    if(isset($_GET[$name]) && !empty($_GET[$name])) {
        return $_GET[$name];
    } else {
        if($required) {
            die('Missing data: '.$name);
        }
        return '';
    }
}

$instanceID = ifPosted('instance', true);
$rule = ifPosted('rule', true);
$token = ifPosted('token', true);

if($token != md5('WheresMyFruitMachineGone'.$instanceID.$rule)) {
    die('Invalid token');
}

$ruleDetails = $DB->get_record('enrol_sits_code', array('id'=>$rule));
$instance = $DB->get_record('enrol', array('id'=>$instanceID, 'enrol'=>'sits'));
$course = $DB->get_record('course', array('id'=>$instance->courseid));
require_login();
$context = context_course::instance($course->id, MUST_EXIST);

require_capability('enrol/sits:manage', $context);

// Check extra permissions here

switch($ruleDetails->code) {
    case 'all-students':
        require_capability('enrol/sits:bulk', $context);
        break;
}
$DB->delete_records('enrol_sits_code', array('id'=>$rule));

header('Location: rules.php?id='.$course->id);
