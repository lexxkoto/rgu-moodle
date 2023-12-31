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

// This file keeps track of upgrades to
// the feedback module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class
//
// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

/**
 * @package    mod
 * @subpackage tabbedcontent
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
function xmldb_tabbedcontent_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2013021400) {
        $table = new xmldb_table('tabbedcontent');
        $field = new xmldb_field('hidename', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, false, '0', null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'showname');
        }
        upgrade_mod_savepoint(true, 2013021400, 'tabbedcontent');
    }

    if ($oldversion < 2013082900) {
        $table = new xmldb_table('tabbedcontent_content');
        $field = new xmldb_field('tabposition', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, null, false, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Populate new field with sequential position values per instance
        $sql = 'SELECT instance
                FROM {tabbedcontent_content}
                GROUP BY instance
                ORDER BY instance';
        if ($instances = $DB->get_records_sql($sql)) {
            foreach ($instances as $instance) {
                if ($ids = $DB->get_records('tabbedcontent_content', array('instance' => $instance->instance), 'id')) {
                    sort($ids);
                    for ($i = 0; $i < count($ids); $i++) {
                        $DB->set_field('tabbedcontent_content', 'tabposition', $i, array('id' => $ids[$i]->id));
                    }
                }
            }
        }
        upgrade_mod_savepoint(true, 2013082900, 'tabbedcontent');
    }

    return true;
}
