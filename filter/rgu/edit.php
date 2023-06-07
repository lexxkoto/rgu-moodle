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
require('edit_form.php');

$contentItem = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();

require_login();
require_capability('filter/rgu:manage', $context);

$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->add_body_class('limitedwidth');

$PAGE->set_title(get_string('editcontent', 'filter_rgu'));
$PAGE->set_heading(get_string('editcontent', 'filter_rgu'));

$editform = new content_items_edit_form();

if($editform->is_cancelled()){
	header('location: index.php'); 
	die(); 
}

if($data = $editform->get_data()){
	$data->contentkey = trim($data->contentkey);
	
	// Check for duplicat entries
	$duplicate  = $DB->get_record_sql('SELECT COUNT(ID) ttl  FROM {filter_rgu_content} where contentkey = "'.$data->contentkey.'" and id <> '.$data->id);
	
	if($duplicate->ttl  > 0) {
    	die('Duplicate content key - '.$data->contentkey.' already exists');
	}
    	
	$record = new stdClass();
	$record->contentkey = $data->contentkey;
	$record->text = $data->text;
	$record->audience = $data->audience;
	$record->timemodified = time();

    if($data->id === 0) {
    	// Save a new record
    	$record->timecreated = time();
    	$DB->insert_record('filter_rgu_content', $record);
    } else {
        $record->id = $data->id;
        $DB->update_record('filter_rgu_content', $record);
    }
    header('location: index.php');
}else{ 
	$content = $DB->get_record('filter_rgu_content', array('id'=>$contentItem));
	if(!empty($content)){
		$editform->set_data($content); 
	}
}

if(!empty($contentItem)) {
    $PAGE->set_url('/filter/rgu/edit.php', array('id'=>$contentItem));
} else {
    $PAGE->set_url('/filter/rgu/edit.php');
}

echo $OUTPUT->header();

$editform->display();

echo $OUTPUT->footer();
