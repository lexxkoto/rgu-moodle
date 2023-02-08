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
// GNU General ublic License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Delete an RGU tabbedcontent module
 *
 * @package    mod
 * @subpackage tabbedcontent
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir.'/filelib.php');

$id      = required_param('cmid', PARAM_INT); // course module id
$delete  = required_param('id', PARAM_INT); // If we're deleting a tab
$confirm = optional_param('confirm', false, PARAM_BOOL);

if (!$cm = get_coursemodule_from_id('tabbedcontent', $id)) {
    print_error('invalidcoursemodule');
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}
if (!$content = $DB->get_record('tabbedcontent_content', array('id' => $delete))) {
    print_error('invalidcontentid', 'tabbed');
}

require_login($course->id, false, $cm);
$context = context_module::instance($cm->id);

$url = new moodle_url('/mod/tabbedcontent/delete.php', array('cmid' => $id, 'id' => $delete, 'confirm' => $confirm));
$PAGE->set_url($url);
$PAGE->set_title(get_string('deletecontent', 'tabbedcontent'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_pagetype('admin');
$PAGE->set_course($course);

if ($confirm && confirm_sesskey()) {

    $tabinstance = $DB->get_field('tabbedcontent_content', 'instance', array('id' => $delete));
    if ($DB->delete_records('tabbedcontent_content', array('id' => $delete))) {
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_tabbedcontent', 'content', $delete);
    }

    // resort tab positions
    if ($tabs = $DB->get_records('tabbedcontent_content', array('instance' => $tabinstance), 'tabposition', 'tabposition, id AS tabid')) {
        sort($tabs);
        for ($i = 0; $i < count($tabs); $i++) {
            $DB->set_field('tabbedcontent_content', 'tabposition', $i, array('id' => $tabs[$i]->tabid));
        }
    }
    redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
    exit;
} else {
    echo $OUTPUT->header();
    $cancelurl  = new moodle_url('/course/view.php', array('id' => $course->id));
    $confirmurl = new moodle_url($PAGE->url, array('confirm' => true, 'sesskey' => sesskey()));
    echo $OUTPUT->confirm(get_string('confirmdeletecontent', 'tabbedcontent'), $confirmurl, $cancelurl);
    echo $OUTPUT->footer();
}
