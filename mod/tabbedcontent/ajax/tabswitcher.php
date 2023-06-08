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
 * @package    mod
 * @subpackage tabbedcontent
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->dirroot . '/mod/tabbedcontent/lib.php');

$tabinstance = required_param('tabinstance', PARAM_TEXT);
$tabid = required_param('tabid', PARAM_INT);
$direction = required_param('tabdirection', PARAM_INT);

require_login();

$context = context_system::instance();
$PAGE->set_context($context);

$renderer = $PAGE->get_renderer('mod_tabbedcontent');

$contents = mod_tabbedcontent_switch($tabid, $direction);

$instance = $DB->get_record('tabbedcontent', array('id' => $tabinstance));
$contents = $DB->get_records('tabbedcontent_content', array('instance' => $tabinstance), 'tabposition');
$cm = $DB->get_record('course_modules', array('course' => $instance->course, 'instance' => $instance->id));

$response = new stdClass();
$response->html = $renderer->render_outertabs($instance, $contents, $cm,$instance->type, $tabid);

echo json_encode($response);
