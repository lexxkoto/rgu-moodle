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
 * @package    block
 * @subpackage rgu_course_overview
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->dirroot . '/blocks/rgu_course_overview/locallib.php');

$PAGE->set_context(context_system::instance());

$searchtext = required_param('searchtext', PARAM_TEXT);
$page = required_param('page', PARAM_ALPHANUMEXT);
$blockinstance = required_param('blockinstance', PARAM_INT);

if(trim(strtolower($page))=='all'){
	$page = 1;
	$pagesize = 10000;
}else{
	$pagesize = block_rgu_course_overview_get_pagesize();
}
require_login();

$configdata = $DB->get_field('block_instances', 'configdata', array('id' => $blockinstance));
$configdata = unserialize(base64_decode($configdata));

$renderer = $PAGE->get_renderer('block_rgu_course_overview');

$response = new stdClass();
$out = $renderer->listsearchresults($searchtext, $pagesize, $blockinstance, $page);
if(is_string($out)&&stripos($out,'view.php')===false){	
	$out = '<p>'.get_string('nostudyareasfound', 'block_rgu_course_overview').'</p>';
}

$response->html = $out;
echo json_encode($response);
