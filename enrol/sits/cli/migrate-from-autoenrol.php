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
 * CLI update for manual enrolments expiration.
 *
 * Notes:
 *   - it is required to use the web server account when executing PHP CLI scripts
 *   - you need to change the "www-data" to match the apache user account
 *   - use "su" if "sudo" not available
 *
 * @package    enrol_sits
 * @copyright  2023 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once("$CFG->libdir/clilib.php");

function out($message, $type='-') {
    echo date('[H:i:s]').' ['.$type.'] '.$message.PHP_EOL;
}

out('SITS Sync Migration Script by Alex Walker', '+');

if (!enrol_is_enabled('sits')) {
    out('SITS Sync is disabled system-wide. Enable before running this script.', '!');
}

$courses = $DB->get_records('course');
$plugin = enrol_get_plugin('sits');
//$courses = $DB->get_records_sql('select * from {course}');

$numCourses = count($courses);
out('Migrating '.$numCourses.' course'.$plugin->s($numCourses), '+');

$i = 1;

foreach($courses as $course) {
    
    out($i.'/'.$numCourses.' - '.$course->fullname.' - '.$course->shortname.' - '.$course->id, '+');
    
    if($course->id > 1) {
        $instance = $plugin->check_instance($course->id);
        $plugin->sniffCourseRules($course->id, $instance);
    }
    $i++;
}

