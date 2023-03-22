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
    if(isset($_POST[$name]) && !empty($_POST[$name])) {
        return $_POST[$name];
    } else {
        if($required) {
            die('Missing data: '.$name);
        }
        return '';
    }
}

$instanceID = ifPosted('instance', true);
$rule = ifPosted('type', true);
$token = ifPosted('token', true);

if($token != md5('TheHandThatFeeds'.$instanceID.$rule)) {
    die('Invalid token');
}

$instance = $DB->get_record('enrol', array('id'=>$instanceID, 'enrol'=>'sits'));
$course = $DB->get_record('course', array('id'=>$instance->courseid));
require_login();
$context = context_course::instance($course->id, MUST_EXIST);

require_capability('enrol/sits:manage', $context);

//echo '<pre>'.PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL; var_dump($_POST); echo '</pre>';

$record = new stdClass();
$record->instanceid = $instanceID;
$record->type = $rule;
$record->modes = ifPosted('modes');
$record->status = ifPosted('status');
$record->course = ifPosted('course');
$record->start = ifPosted('start');
$record->occurrence = ifPosted('occurrence');
$record->period = ifPosted('period');
$record->timeadded = time();

$levels = Array(
    'level_1'  => '1',
    'level_2'  => '2',
    'level_3'  => '3',
    'level_4'  => '4',
    'level_5'  => '5',
    'level_PG' => 'PG'
);

$usingLevels = false;
$postedLevels = Array();

foreach($levels as $level=>$code) {
    if(isset($_POST[$level])) {
        $postedLevels[] = $code;
        $usingLevels = true;
    }
}

$record->levels = '';

if($usingLevels) {
    $record->levels = implode(':', $postedLevels);
}

if(isset($_POST['blocks'])) {
    $exploded = explode(',', $_POST['blocks']);
    $cleanBlocks = array();
    foreach($exploded as $block) {
        $cleanBlocks[] = trim($block);
    }
    $record->blocks = implode(':', $cleanBlocks);
}

switch($rule) {
    case 'all-students':
        require_capability('enrol/sits:bulk', $context);
        $DB->insert_record('enrol_sits_code', $record);
        break;
    case 'dept-staff':
        $record->code = ifPosted('dept', true);
        $DB->insert_record('enrol_sits_code', $record);
        break;
    case 'school':
        $record->code = ifPosted('dept', true);
        $DB->insert_record('enrol_sits_code', $record);
        break;
    case 'course':
        $code = ifPosted('code', true);
        $codeArray = explode(',', $code);
        
        $codes = Array();
        foreach($codeArray as $cleanCode) {
            $codes[] = trim($cleanCode);
        }
        
        foreach($codes as $code) {
            $record->code = $code;
            $DB->insert_record('enrol_sits_code', $record);
        }
}



header('Location: rules.php?id='.$course->id);
