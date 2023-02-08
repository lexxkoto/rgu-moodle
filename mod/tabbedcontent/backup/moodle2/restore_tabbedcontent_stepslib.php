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

/**
 * Define all the restore steps that will be used by the restore_url_activity_task
 */

/**
 * Structure step to restore one tabbedcontent activity
 */

class restore_tabbedcontent_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('tabbedcontent', '/activity/tabbedcontent');
        $paths[] = new restore_path_element('tabcontents', '/activity/tabbedcontent/tabcontents/tabcontent');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_tabbedcontent($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the tabbedcontent record
        $newitemid = $DB->insert_record('tabbedcontent', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_tabcontents($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->instance = $this->get_new_parentid('tabbedcontent');

        $newitemid = $DB->insert_record('tabbedcontent_content', $data);
        $this->set_mapping('tabbedcontent_content', $oldid, $newitemid, true);
    }

    protected function after_execute() {

        // Add tabbedcontent related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_tabbedcontent', 'intro', null);
        $this->add_related_files('mod_tabbedcontent', 'content', 'tabbedcontent_content');
    }

}
