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
 * @package    enrol_sits
 * @copyright  2023 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_sits\task;

defined('MOODLE_INTERNAL') || die;

class sync_course extends \core\task\adhoc_task {
    
    public function get_name() {
        get_string('task_synccourse', 'enrol_sits');
    }

    public function execute() {
        global $DB;

        // Get enrolment plugin
        $plugin = enrol_get_plugin('sits');

        // Get custom data (and courseid)
        $data = $this->get_custom_data();
        $courseid = $data->courseid;
        $plugin->check_instance($courseid);
        if ($course = $DB->get_record('course', ['id' => $courseid])) {
            $plugin->addToLog(-1, $courseid, 'd', 'SITS sync triggered by '.$data->reason.'.');
            $plugin->syncCourse($courseid);
        }
    }
}
