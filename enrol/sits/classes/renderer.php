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
        
        $plugin = enrol_get_plugin('sits');
        $courses = $plugin->getAllCourses();
        $modules = $plugin->getAllModules();
        
        $html = '';
        
        if(count($codes) == 0) {
            $html = '<div class="message-grey"><span><strong>You haven\'t added any enrolment rules yet.</strong><br />Use the options below to add an enrolment rule to this course.</span></div>';
        } else {
            foreach($codes as $code) {
                echo '<div class="enrolment-rule enrol-'.$code->type.' mb-2"><a class="btn btn-link pull-right" title="Delete Rule" href="delete-rule.php?instance='.$code->instanceid.'&rule='.$code->id.'&token='.md5('WheresMyFruitMachineGone'.$code->instanceid.$code->id).'"><i class="fa fa-trash"></i></a><span>';
                switch($code->type) {
                    case 'all-students':
                        echo '<h4>All Students</h4>';
                        echo '<p>There are no options for this type of rule.</p>';
                        break;
                    case 'all-staff':
                        echo '<h4>All Staff</h4>';
                        echo '<p>There are no options for this type of rule.</p>';
                        break;
                    case 'dept-staff':
                        echo '<h4>All Staff in a Department</h4>';
                        echo '<p>Department: <strong>'.$code->code.'</strong></p>';
                        break;
                    case 'school':
                        echo '<h4>All Students by School</h4>';
                        echo '<p>School: <strong>';
                        if(isset(enrol_sits_plugin::$schools[$code->code])) {
                            echo enrol_sits_plugin::$schools[$code->code];
                        } else {
                            echo 'Unknown School';
                        }
                        echo '</strong></p>';
                        if(!empty($code->levels)) {
                            echo '<p>Levels: <strong>'.str_replace(':', ', ', $code->levels).'</strong></p>';
                        }
                        if(!empty($code->blocks)) {
                            echo '<p>Blocks: <strong>'.str_replace(':', ', ', $code->blocks).'</strong></p>';
                        }
                        break;
                    case 'course':
                        case 'school':
                        echo '<h4>Students by Course</h4>';
                        echo '<p>Course: <strong>';
                        echo $code->code.' - ';
                        if(isset($courses[$code->code])) {
                            echo $plugin->fixModuleName($courses[$code->code]);
                        } else {
                            echo 'Course Code Not Recognised';
                        }
                        echo '</strong></p>';
                        if(!empty($code->levels)) {
                            echo '<p>Levels: <strong>'.str_replace(':', ', ', $code->levels).'</strong></p>';
                        }
                        if(!empty($code->blocks)) {
                            echo '<p>Blocks: <strong>'.str_replace(':', ', ', $code->blocks).'</strong></p>';
                        }
                }
                echo '</span></div>';
            }
        }

        return $html;
    }
    
    public function print_log_entries($entries) {
        echo '<table class="table"><thead><tr><th>Time</th><th>Information</th></tr></thead><tbody>';
        foreach($entries as $entry) {
            if($entry->level != 'd') {
                echo '<tr>';
                echo '<td>'.date('D j M H:i', $entry->timeadded).'</td>';
                echo '<td><i class="fa fa-';
                switch ($entry->level) {
                    default:
                        echo 'info-circle';
                        break;
                }
                echo '"></i> '.$entry->details.'</td>';
                echo '</tr>';
            }
        }
        echo '</tbody></table>';
    }
    
    public function print_enrol_buttons($instance) {
        $buttons = Array(
            [
                'title' => 'Add Students',
                'buttons' => [
                    [
                        'text' => 'By Module',
                        'rule' => 'module',
                        'icon' => 'cubes',
                        'type' => 'secondary'
                    ],
                    [
                        'text' => 'By Course',
                        'rule' => 'course',
                        'icon' => 'mortar-board',
                        'type' => 'secondary'
                    ],
                    [
                        'text' => 'By School',
                        'rule' => 'school',
                        'icon' => 'institution',
                        'type' => 'secondary'
                    ],
                    [
                        'text' => 'Add All Students',
                        'rule' => 'all-students',
                        'icon' => 'plus',
                        'type' => 'warning'
                    ],
                ],
            ],
            [
                'title' => 'Add Staff',
                'buttons' => [
                    [
                        'text' => 'By Department',
                        'rule' => 'dept-staff',
                        'icon' => 'group',
                        'type' => 'secondary'
                    ],
                    [
                        'text' => 'Add All Staff',
                        'rule' => 'all-staff',
                        'icon' => 'plus',
                        'type' => 'warning'
                    ],
                ]
            ],
        );
    
        echo '<div class="enrol-buttons mt-4">';
        foreach($buttons as $group) {
            echo '<div class="row mt-4"><div class="col-md-3"><span class="btn">'.$group['title'].':</span></div><div class="col-md-9 btn-toolbar">';
            foreach ($group['buttons'] as $button) {
                echo '<a class="btn btn-'.$button['type'].' mr-2" href="add-rule.php?instance='.$instance.'&rule='.$button['rule'].'">';
                echo '<i class="fa fa-'.$button['icon'].'"></i> '.$button['text'];
                echo '</a>';
            }
            echo '</div></div>';
        }
        echo '</div>';
    }
    
    function print_staff_depts($name='dept', $selected='') {
        global $DB;
        
        $depts = $DB->get_records_sql('SELECT DISTINCT department FROM {user} WHERE institution="staff" AND deleted=0 ORDER BY department');
        
        echo '<select class="form-control" name="'.$name.'" id="'.$name.'">';
        foreach ($depts as $dept) {
            echo '<option value="'.$dept->department.'"';
            if($dept->department == $selected) {
                echo ' selected="selected"';
            }
            echo'>'.$dept->department.'</option>';
        }
        echo '</select>';
    }
    
    function print_schools($name='dept', $selected='') {
        global $DB;
        
        $depts =enrol_sits_plugin::$schools;
        
        echo '<select class="form-control" name="'.$name.'" id="'.$name.'">';
        foreach($depts as $code=>$name) {
            echo '<option value="'.$code.'"';
                if($code == $selected) {
                    echo ' selected="selected"';
                }
                echo'>'.$name.'</option>';
            }
        echo '</select>';
    }
    
    function print_levels($selected=Array()) {
        global $DB;
        
        $depts = Array(
            '1'  => 'Undergraduate Year 1',
            '2'  => 'Undergraduate Year 2',
            '3'  => 'Undergraduate Year 3',
            '4'  => 'Undergraduate Year 4',
            '5'  => 'Undergraduate Year 5',
            'PG' => 'Postgraduate (All Years)'
        );
        
        foreach($depts as $code=>$name) {
            echo '<div class="form-check mb-1">';
            echo '<input class="form-check-input" type="checkbox" id="level_'.$code.'" name="level_'.$code.'"';
            if(empty($selected) || isset($selected[$code])) {
                echo ' checked="checked"';
            }
            echo' /><label class="form-check-label" for="level_'.$code.'">'.$name.'</label></div>';
        }
    }
}
