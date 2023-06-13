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
 * GUID Enrolment sync
 *
 * @package    local_backupcleaner
 * @copyright  2021 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_rgu_coursecontacts\task;

defined('MOODLE_INTERNAL') || die;

class cleanup extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('cleanup', 'block_rgu_coursecontacts');
    }

    public function execute() {
        global $DB;

        $DB->delete_records_select(
            'block_rgucc_users', 'courseid not in (select id from {course})'
        );
        
        $DB->delete_records_select(
            'block_rgucc_users', 'userid not in (select id from {user} where deleted=0)'
        );
    }

}
