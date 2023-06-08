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
 * Define all the backup steps that will be used by the backup_tabbedcontent_activity_task
 */

/**
 * Define the complete tabbedcontent structure for backup, with file and id annotations
 */

class backup_tabbedcontent_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // Define each element separated
        $tabbedcontent = new backup_nested_element('tabbedcontent', array('id'), array(
            'name', 'type', 'intro', 'introformat', 'showname', 'timemodified'));

        $tabcontents = new backup_nested_element('tabcontents');

        $tabcontent = new backup_nested_element('tabcontent', array('id'), array(
            'instance', 'tabtitle', 'tabcontent', 'tabcontentformat'));

        // Build the tree
        $tabbedcontent->add_child($tabcontents);
        $tabcontents->add_child($tabcontent);

        // Define sources
        $tabbedcontent->set_source_table('tabbedcontent', array('id' => backup::VAR_ACTIVITYID));

        $tabcontent->set_source_sql('
            SELECT *
            FROM {tabbedcontent_content}
            WHERE instance = ?
            ORDER BY id',
            array(backup::VAR_PARENTID));

        // Define id annotations
        // (none)

        // Define file annotations
        $tabbedcontent->annotate_files('mod_tabbedcontent', 'intro', null); // This file area hasn't itemid
        $tabcontent->annotate_files('mod_tabbedcontent', 'content', 'id');

        // Return the root element (tabbedcontent), wrapped into standard activity structure
        return $this->prepare_activity_structure($tabbedcontent);
    }
}
