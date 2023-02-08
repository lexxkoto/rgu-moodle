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
 * Library of functions and constants for module RGU Tabbed Content.
 *
 * @package    mod
 * @subpackage tabbedcontent
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/filelib.php');

define('TABBEDCONTENT_MAX_NAME_LENGTH', 50);
define('TABBEDCONTENT_HORIZONTAL', 'horizontal');
define('TABBEDCONTENT_VERTICAL', 'vertical');
define('TABBEDCONTENT_DECREASE', 0);
define('TABBEDCONTENT_INCREASE', 1);

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool True if quiz supports feature
 */
function tabbedcontent_supports($feature) {

    switch($feature) {
        case FEATURE_IDNUMBER:                return FALSE;
        case FEATURE_GROUPS:                  return FALSE;
        case FEATURE_GROUPINGS:               return FALSE;
        case FEATURE_GROUPMEMBERSONLY:        return TRUE;
        case FEATURE_MOD_INTRO:               return TRUE;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return FALSE;
        case FEATURE_GRADE_HAS_GRADE:         return FALSE;
        case FEATURE_GRADE_OUTCOMES:          return FALSE;
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_BACKUP_MOODLE2:          return TRUE;
        case FEATURE_NO_VIEW_LINK:            return TRUE;
        case FEATURE_SHOW_DESCRIPTION:        return TRUE;
        default: return NULL;
    }
}

/**
 * Add new tabbedcontent instance
 *
 * @param object $instance mform submitted values
 * @return mixed ID of new instance or false
 */
function tabbedcontent_add_instance($tabbedcontent) {
    global $DB;

    if (!isset($tabbedcontent->showname)) {
        $tabbedcontent->showname = 0;
    }
    $tabbedcontent->timemodified = time();

    if ($instance = $DB->insert_record('tabbedcontent', $tabbedcontent)) {

        $context = context_module::instance($tabbedcontent->coursemodule);

        if (isset($tabbedcontent->tabtitle1) && $tabbedcontent->tabtitle1) {
            $tabbedcontent1 = new stdClass();
            $tabbedcontent1->instance = $instance;
            $tabbedcontent1->tabtitle = $tabbedcontent->tabtitle1;
            $tabbedcontent1->tabcontent = $tabbedcontent->tabcontent1['text'];
            $tabbedcontent1->tabposition = 0;
            $tabid = $DB->insert_record('tabbedcontent_content', $tabbedcontent1);
            $draftid_editor = file_get_submitted_draft_itemid('tabcontent1');
            $tabbedcontent1->id = $tabid;
            $tabbedcontent1->tabcontent = file_save_draft_area_files(
                $draftid_editor,
                $context->id,
                'mod_tabbedcontent',
                'content',
                $tabid,
                array(),
                $tabbedcontent->tabcontent1['text']
            );
            $tabbedcontent1->tabcontentformat = $tabbedcontent->tabcontent1['format'];
            $DB->update_record('tabbedcontent_content', $tabbedcontent1);
        }

        if (isset($tabbedcontent->tabtitle2) && $tabbedcontent->tabtitle2) {
            $draftid_editor = file_get_submitted_draft_itemid('tabcontent2');
            $tabbedcontent2 = new stdClass();
            $tabbedcontent2->instance = $instance;
            $tabbedcontent2->tabtitle = $tabbedcontent->tabtitle2;
            $tabbedcontent2->tabcontent = $tabbedcontent->tabcontent2['text'];
            $tabbedcontent2->tabposition = 1;
            $tabid = $DB->insert_record('tabbedcontent_content', $tabbedcontent2);
            $draftid_editor = file_get_submitted_draft_itemid('tabcontent2');
            $tabbedcontent2->id = $tabid;
            $tabbedcontent2->tabcontent = file_save_draft_area_files(
                $draftid_editor,
                $context->id,
                'mod_tabbedcontent',
                'content',
                $tabid,
                array(),
                $tabbedcontent->tabcontent2['text']
            );
            $tabbedcontent2->tabcontentformat = $tabbedcontent->tabcontent2['format'];
            $DB->update_record('tabbedcontent_content', $tabbedcontent2);
        }

        return $instance;
    }
}

/**
 * Update an existing tabbedcontent instance
 *
 * @param object $instance mform submitted values
 * @return mixed ID of new instance or false
 */
function tabbedcontent_update_instance($instance, $mform) {
    global $DB;

    $cmid = $instance->coursemodule;
    $context = context_module::instance($cmid);

    $draftid_editor = file_get_submitted_draft_itemid('tabcontent1');
    $panel1 = new stdClass();
    $panel1->id = $instance->tab1id;
    $panel1->instance = $instance->instance;
    $panel1->tabtitle = $instance->tabtitle1;
    $panel1->tabcontent = file_save_draft_area_files(
        $draftid_editor,
        $context->id,
        'mod_tabbedcontent',
        'content',
        $instance->tab1id,
        array(),
        $instance->tabcontent1['text']
    );
    $panel1->tabposition = 0;
    $panel1->tabcontentformat = $instance->tabcontent1['format'];
    $DB->update_record('tabbedcontent_content', $panel1);

    $draftid_editor = file_get_submitted_draft_itemid('tabcontent2');
    $panel2 = new stdClass();
    $panel2->id = $instance->tab2id;
    $panel2->instance = $instance->instance;
    $panel2->tabtitle = $instance->tabtitle2;
    $panel2->tabcontent = file_save_draft_area_files(
        $draftid_editor,
        $context->id,
        'mod_tabbedcontent',
        'content',
        $instance->tab2id,
        array(),
        $instance->tabcontent2['text']
    );
    $panel2->tabposition = 1;
    $panel2->tabcontentformat = $instance->tabcontent2['format'];
    $DB->update_record('tabbedcontent_content', $panel2);

    if (!isset($instance->showname)) {
        $instance->showname = 0;
    }
    if (!isset($instance->hideintro)) {
        $instance->hideintro = 0;
    }
    $instance->timemodified = time();
    $instance->id = $instance->instance;

    return $DB->update_record('tabbedcontent', $instance);
}

/**
 * Delete a tabbedcontentinstance
 *
 * @param ID $instance ID of instance to delete
 * @return boolean success
 * @todo force reload after delete to update html
 */
function tabbedcontent_delete_instance($instance) {
    global $DB;

    $cm = get_coursemodule_from_instance('tabbedcontent', $instance);
    $context = context_module::instance($cm->id);
    if ($DB->delete_records('tabbedcontent_content', array('instance' => $instance))) {
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_tabbedcontent', 'intro', $instance);
        $fs->delete_area_files($context->id, 'mod_tabbedcontent', 'content', $instance);

        return $DB->delete_records('tabbedcontent', array('id' => $instance));
    }

    return false;
}

/**
 * Define content to print after title but before controls on
 * course view section
 *
 * @param object $cm the instance to display
 */
function tabbedcontent_cm_info_view($cm) {
    global $DB, $PAGE;

    $instance = $DB->get_record('tabbedcontent', array('id' => $cm->instance));
    $contents = $DB->get_records('tabbedcontent_content', array('instance' => $instance->id), 'tabposition');
    $renderer = $PAGE->get_renderer('mod_tabbedcontent');

    $html = $renderer->display_tabbed_content($instance, $contents, $cm,$instance->type);
    $cm->set_content($html);
}

/**
 * Serves the tabbedcontent files.
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - just send the file
 */
function mod_tabbedcontent_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    if ($filearea !== 'content' && $filearea !== 'intro') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_tabbedcontent/$filearea/$relativepath";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }

    // finally send the file
    send_stored_file($file, 86400, 0, $forcedownload);
}

/**
 * Returns select menu array of possible tab positions by passed course module ID
 *
 * @param int $cmid the current course module ID for the tabbed content instance
 * @param int $tabid the current tabbed content ID or zero if new
 * @return array
 */
function mod_tabbedcontent_position_options($cmid, $tabid) {
    global $DB;

    $maxposition = $DB->get_field_select(
            'tabbedcontent_content',
            'MAX(tabposition)',
            'instance = (SELECT instance FROM {course_modules} WHERE id = ?)',
            array($cmid)
        );

    $options = array();
    for ($i = 1; $i <= $maxposition + 1; $i++) {
        $options[$i] = $i;
    }
    if (!$tabid) {
        $options[$i] = $i;
    }

    return $options;
}

/**
 * Reorders tab position values by passed tabbed content ID,
 * destination position value and original position (if available).
 *
 * @param int $tabid the current tabbed content ID
 * @param int $dest destination position value
 * @param optional int $orig original position value
 */
function mod_tabbedcontent_reorder_tabs($tabid, $dest, $orig=null) {
    global $DB;

    // Create recordset of tabs affected by reorder
    $sql = "SELECT id, tabposition
              FROM {tabbedcontent_content}
             WHERE instance = (
                SELECT instance
                  FROM {tabbedcontent_content}
                 WHERE id = ?
                ) ";
    if (is_null($orig)) {
        $sql .= "AND tabposition >= ? ";
    } else if ($orig > $dest) {
        $sql .= "AND tabposition < ?
                 AND tabposition >= ? ";
    } else if ($orig < $dest) {
        $sql .= "AND tabposition > ?
                 AND tabposition <= ? ";
    }
    $sql .= 'ORDER BY tabposition';
    $params = is_null($orig) ? array($tabid, $dest) : array($tabid, $orig, $dest);

    if ($tabs = $DB->get_records_sql($sql, $params)) {
        foreach ($tabs as $tab) {
            if (is_null($orig) || $orig > $dest) {
                $tab->tabposition++;
            } else if ($orig < $dest) {
                $tab->tabposition--;
            }

            // set new position value on affected tab
            $DB->update_record('tabbedcontent_content', $tab);
        }
    }

    // set new position value on originally moved tab
    $DB->set_field('tabbedcontent_content', 'tabposition', $dest, array('id' => $tabid));
}

/**
 * Checks whether editing mode enabled
 *
 * @return boolean
 */
function mod_tabbedcontent_isediting() {
    global $PAGE;

    return ($PAGE->user_allowed_editing() && $PAGE->user_is_editing()) ? TRUE : FALSE;
}

/**
 * Switch position values based on passed tabbed content ID
 *
 * @param int $tabid the current tabbed content ID
 * @param bool $increase increase or decrease tab position value
 * @return int position value of active tab
 */
function mod_tabbedcontent_switch($tabid, $increase) {
    global $DB;

    if ($orig = $DB->get_record('tabbedcontent_content', array('id' => $tabid), 'id, instance, tabposition')) {
        $position = $increase ? $orig->tabposition+1 : $orig->tabposition-1;
        if ($dest = $DB->get_record('tabbedcontent_content', array('instance' => $orig->instance, 'tabposition' => $position), 'id')) {
            $DB->set_field('tabbedcontent_content', 'tabposition', $position, array('id' => $orig->id));
            $DB->set_field('tabbedcontent_content', 'tabposition', $orig->tabposition, array('id' => $dest->id));
        }
        mod_tabbedcontent_safety_check($orig->instance);

        return $position;
    }
}

/**
 * Ensure tab position values are sequential from zero to prevent problems caused by multiple simultaneous users,
 * multiple browser sessions holding different position values, etc.
 *
 * @param $instance module instance
 */ 
function mod_tabbedcontent_safety_check($instance) {
    global $DB;

    if ($records = $DB->get_records('tabbedcontent_content', array('instance' => $instance), 'tabposition', 'id')) {
        $tabs = array();
        foreach ($records as $record) {
            $tabs[] = $record->id;
        }
        if ($total = $DB->count_records('tabbedcontent_content', array('instance' => $instance))) {
            for ($i = 0; $i < $total; $i++) {
                $DB->set_field('tabbedcontent_content', 'tabposition', $i, array('id' => $tabs[$i]));
            }
        }
    }
}

function correct_null_tab_positions($instance){
	global $DB;
	if(empty($instance)||!is_numeric($instance)){
		return false;
	}
	
	if ($records = $DB->get_records('tabbedcontent_content', array('instance' => $instance), 'tabposition', 'id')) {
		$containsnull = false;
		foreach($records as $record){
			if(!@is_numeric($record->tabposition)){
				$containsnull = true;
			}
		}
		if(!$containsnull){
			return true;
		}
		$order = 0;
		$update = new stdClass; 
		foreach($records as $record){
			$update->id = $record->id;
			$update->tabposition = $order; 
			$DB->update_record('tabbedcontent_content',$update);
			$order++;
		}
		return false;
	}
	return false;	
}
