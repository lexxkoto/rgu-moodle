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
 * @package    local_inspector
 * @copyright  2023 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require('manifest.php');

$CFG->debug = 0;
$CFG->debugdisplay = 0;

$givenToken = required_param('token', PARAM_TEXT);
$realToken = get_config('local_inspector', 'security_key');

$response = Array();

if($givenToken != $realToken) {
    $response['error'] = 'invalid-token';
    echo json_encode($response);
    die();
}

$prefix = get_config('local_inspector', 'prefix');

foreach ($queries as $code=>$query) {
    try {
        $result = $DB->get_records_sql($query);
        $answer = array_pop($result);
        $response[$prefix.'-'.$code] = intval($answer->ttl);
    } catch (Exception $e) {
        $response[$prefix.'-'.$code] = 0;
    }
}

$result = $DB->get_records_sql('select classname, count(*) ttl from {task_adhoc} where classname in ('.str_replace("\\", "\\\\", implode(', ', array_keys($cronTasks))).') group by classname');


$taskResults = Array();
foreach($result as $task) {
    $taskResults[str_replace('\\', '_', $task->classname)] = $task->ttl;
}

foreach($cronTasks as $key=>$value) {
    $cleanKey = str_replace('"', '', str_replace('\\', '_', $key));
    if(isset($taskResults[$cleanKey])) {
        $response[$prefix.'-'.$value] = intval($taskResults[$cleanKey]);
    } else {
        $response[$prefix.'-'.$value] = 0;
    }
}

$result = $DB->get_records_sql('select eventname, count(*) ttl from {logstore_standard_log} where eventname in ('.str_replace("\\", "\\\\", implode(', ', array_keys($logEvents))).') and timecreated >= '.strtotime('-5 minutes', $roundedTimestamp).' and timecreated < '.$roundedTimestamp.' group by eventname');

$logResults = Array();
foreach($result as $act) {
    $logResults[str_replace('\\', '_', $act->eventname)] = $act->ttl;
}

foreach($logEvents as $key=>$value) {
    $cleanKey = str_replace('"', '', str_replace('\\', '_', $key));
    if(isset($logResults[$cleanKey])) {
        $response[$prefix.'-'.$value] = intval($logResults[$cleanKey]);
    } else {
        $response[$prefix.'-'.$value] = 0;
    }
}

echo json_encode($response, JSON_PRETTY_PRINT);