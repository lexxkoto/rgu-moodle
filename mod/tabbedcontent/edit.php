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
 * RGU Tabbed Content module.
 *
 * @package    mod
 * @subpackage tabbedcontent
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->dirroot . '/mod/tabbedcontent/edit_form.php');
require_once('lib.php');

$id = required_param('cmid', PARAM_INT); // course module id
$tabid = optional_param('tab', 0, PARAM_INT); // tabbedcontent_content id

if (!$cm = get_coursemodule_from_id('', $id, 0, true, MUST_EXIST)) {
    print_error('invalidcoursemodule');
}
if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemisconf');
}
if (!$tabbedcontentarea = $DB->get_record('tabbedcontent', array('id' => $cm->instance))) {
    print_error('invalidcoursemodule');
}

correct_null_tab_positions($cm->instance);

require_login($course->id, false, $cm);
$context = context_module::instance($cm->id);

$url = new moodle_url('/mod/tabbedcontent/edit.php', array('cmid' => $id, 'tab' => $tabid));
$PAGE->set_url($url);

if ($tabid) {
    $tab = $DB->get_record('tabbedcontent_content', array('id' => $tabid));
    $title = get_string('edittab', 'tabbedcontent', format_string($tab->tabtitle));
} else {
    $title = get_string('addnewtab', 'tabbedcontent');
}
$PAGE->set_title($title);
$PAGE->set_context($context);
$PAGE->set_pagelayout('incourse');
$PAGE->set_pagetype('admin');
$PAGE->set_course($course);

$sectionreturn = '#section-'.$cm->sectionnum; // Return to section of course module.

$tabposition = (isset($tab->tabposition)) ? $tab->tabposition : NULL;
$mform = new tabbedcontent_form(
        $url,
        array(
            'title' => $title,
            'cmid' => $cm->id,
            'tab' => $tabid,
            'tabposition' => $tabposition
        )
    );
if ($mform->is_cancelled()) {
    // Return to course
    redirect($CFG->wwwroot.'/course/view.php?id='.$course->id.$sectionreturn);
    exit;
}

// edit or add tab content after form submission
if ($data = $mform->get_data()) {

    $submittedtabposition = (int)$data->tabposition-1;

    $newtab = new stdClass();
    $newtab->tabtitle         = $data->tabtitle;
    $newtab->tabcontent       = $data->tabcontent['text'];
    $newtab->tabcontentformat = $data->tabcontent['format'];

    if ($tabid) {
        $newtab->id = $tabid;
        $DB->update_record('tabbedcontent_content', $newtab);
    } else {
        $tabs = $DB->get_records('tabbedcontent_content', array('instance' => $tabbedcontentarea->id));
        $newtab->tabposition = count($tabs);
        $newtab->instance = $tabbedcontentarea->id;
        $newtab->id = $DB->insert_record('tabbedcontent_content', $newtab);
    }

    // check tab positions and reorder if necessary
    $tabposition = $DB->get_field('tabbedcontent_content', 'tabposition', array('id' => $newtab->id));
    if ($tabposition != $submittedtabposition) {
        mod_tabbedcontent_reorder_tabs($newtab->id, $submittedtabposition, $tabposition);
    }

    $draftid_editor = file_get_submitted_draft_itemid('tabcontent');
    $newtab->tabcontent = file_save_draft_area_files(
        $draftid_editor,
        $context->id,
        'mod_tabbedcontent',
        'content',
        $newtab->id,
        array(),
        $data->tabcontent['text']
    );
    $DB->set_field('tabbedcontent_content', 'tabcontent', $newtab->tabcontent, array('id' => $newtab->id));

    rebuild_course_cache($course->id);

    // Return to course
    redirect($CFG->wwwroot.'/course/view.php?id='.$course->id.$sectionreturn);
    exit;
}

echo $OUTPUT->header();

if (isset($tab) && $tab) {

    $draftid_editor = file_get_submitted_draft_itemid('tabcontent');
    $tabdata = new stdClass;
    $tabdata->tabtitle = $tab->tabtitle;
    $tabdata->tabposition = $tab->tabposition + 1;
    $tabdata->tabcontent['text'] = file_prepare_draft_area(
        $draftid_editor,
        $context->id,
        'mod_tabbedcontent',
        'content',
        $tabid,
        array(),
        $tab->tabcontent
    );
    $tabdata->tabcontent['format'] = $tab->tabcontentformat;
    $tabdata->tabcontent['itemid'] = $draftid_editor;
    $mform->set_data($tabdata);
}
$mform->display();

echo $OUTPUT->footer();
