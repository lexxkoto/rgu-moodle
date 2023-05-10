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
 * @package   enrol_sits
 * @copyright 2023 Alex Walker
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_enrol_sits_upgrade($oldversion) {
    global $DB, $CFG;

    $dbman = $DB->get_manager();

    if ($oldversion < 2023041800) {
        $table = new xmldb_table('enrol_sits_users');
        $field = new xmldb_field('studentno', XMLDB_TYPE_CHAR, '7', XMLDB_UNSIGNED, null, null, null, 'instanceid');

        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        }

        upgrade_plugin_savepoint(true, 2023041800, 'enrol', 'sits');
    }
    return true;
}
