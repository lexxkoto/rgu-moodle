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
 * Capabilities for database enrolment plugin.
 *
 * @package    enrol
 * @subpackage gudatabase
 * @copyright  2012 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$observers = array(

    [
        'eventname' => '\core\event\course_reset_ended',
        'callback' => 'enrol_sits_observer::course_reset_ended',
    ],
    [
        'eventname' => '\core\event\course_updated',
        'callback' => 'enrol_sits_observer::course_updated',
    ],
    [
        'eventname' => '\core\event\course_created',
        'callback' => 'enrol_sits_observer::course_created',
    ],
    [
        'eventname' => '\core\event\course_viewed',
        'callback' => 'enrol_sits_observer::course_viewed',
    ],
    [
        'eventname' => '\core\event\enrol_instance_updated',
        'callback' => 'enrol_sits_observer::enrol_instance_updated',
    ],

);
