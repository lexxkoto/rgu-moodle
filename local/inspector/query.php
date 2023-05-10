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
        if(isset($result) && isset($result[0]) && isset($result[0]->ttl)) {
            $response[$prefix.'-'.$code] = intval($result[0]->ttl);
        }
    } catch(exception $e) {
        // Do nothing
    }
}

echo json_encode($response);
