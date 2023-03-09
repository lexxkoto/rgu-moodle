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
 * @package    enrol_gudatabase
 * @copyright  2013-2014 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class enrol_sits_renderer extends plugin_renderer_base {

    /**
     * Print tabs for edit page
     * @param int $courseid
     * @param string $selected selected tab
     */
    public function print_tabs($courseid, $instanceid, $selected) {
        $rows = array();
        $rows[] = new tabobject(
            'config',
            new moodle_url('/enrol/gudatabase/edit.php', array('courseid' => $courseid, 'id' => $instanceid, 'tab' => 'config')),
            get_string('config', 'enrol_gudatabase')
        );
        $rows[] = new tabobject(
            'codes',
            new moodle_url('/enrol/gudatabase/edit.php', array('courseid' => $courseid, 'id' => $instanceid, 'tab' => 'codes')),
            get_string('codes', 'enrol_gudatabase')
        );
        $rows[] = new tabobject(
            'groups',
            new moodle_url('/enrol/gudatabase/edit.php', array('courseid' => $courseid, 'id' => $instanceid, 'tab' => 'groups')),
            get_string('groups', 'enrol_gudatabase')
        );
        return $this->output->tabtree($rows, $selected) . '<p></p>';
    }

    /**
     * Get course description for code
     * @param int $courseid
     * @param string $code
     * @return string
     */
    public function courseinfo($courseid, $code) {
        global $DB;

        if (substr($code, -1) == '*') {
            $courseinfo = get_string('starcode', 'enrol_gudatabase');
        } else if ($codeinfo = $DB->get_record('enrol_gudatabase_codes', array('courseid' => $courseid, 'code' => $code), '*', IGNORE_MULTIPLE)) {
            $courseinfo = "{$codeinfo->subjectname} > {$codeinfo->coursename}";
        } else {
            $courseinfo = get_string('nocourseinfo', 'enrol_gudatabase');
        }

        return $courseinfo;
    }

    /**
     * Print legacy codes
     */
    public function print_codes($codes) {
        $html = '';
        
        if(count($codes) == 0) {
            $html = '<div class="message-grey"><span><strong>You haven\'t added any enrolment rules yet.</strong><br />Use the form below to add an enrolment rule to this course.</span></div>';
        } else {
            $html = '<pre>'.print_r($codes, true).'</pre>';
        }

        return $html;
    }
}
