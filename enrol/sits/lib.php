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
 *
 * @package    enrol
 * @subpackage sits
 * @copyright  2023 Alex Walker
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
 
function enrol_sits_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    global $PAGE, $COURSE;

    $canview = has_capability('enrol/sits:config', context_course::instance($COURSE->id));

    if ($COURSE->id !== SITEID && $canview) {
        $url = new moodle_url('/enrol/sits/rules.php', ['id' => $COURSE->id]);
        $parentnode->add(
            get_string('navlink', 'enrol_sits'),
            $url,
        );
    }
}
    
class enrol_sits_plugin extends enrol_plugin {

    protected $trace;

    public function instance_deleteable($instance) {
        return true;
    }

    public function roles_protected() {
        return true;
    }

    public function allow_unenrol_user(stdClass $instance, stdClass $ue) {
        return true;
    }

    public function allow_manage(stdClass $instance) {
        return true;
    }

    public function allow_unenrol(stdClass $instance) {
        return true;
    }
    
    public function can_hide_show_instance($instance) {
        return false;
    }

    public function get_instance_name($instance) {
        global $DB;

        if (empty($instance->name)) {
            if (!empty($instance->roleid) and $role = $DB->get_record('role', array('id' => $instance->roleid))) {
                $role = ' (' . role_get_name($role, context_course::instance($instance->courseid, IGNORE_MISSING)) . ')';
            } else {
                $role = '';
            }
            $enrol = $this->get_name();
            return get_string('pluginname', 'enrol_'.$enrol) . $role;
        } else {
            return format_string($instance->name);
        }
    }

    public function can_add_instance($courseid) {
        $context = context_course::instance($courseid, MUST_EXIST);
        return true;
        if (!has_capability('moodle/course:enrolconfig', $context) or !has_capability('enrol/sits:config', $context)) {
            return false;
        }

        return true;
    }

    public function use_standard_editing_ui() {
        return true;
    }

    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {
        $actions = array();
        $context = $manager->get_context();
        $instance = $ue->enrolmentinstance;
        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;
        if ($this->allow_unenrol_user($instance, $ue) && has_capability('enrol/sits:unenrol', $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'),
                    $url, array('class' => 'unenrollink', 'rel' => $ue->id));
        }
        return $actions;
    }

    public function is_configured() {
        return true;
        if (!$this->get_config('sits_db_enabled') or
                !$this->get_config('sits_db_host') or
                !$this->get_config('sits_db_username') or
                !$this->get_config('sits_db_password') or
                !$this->get_config('sits_db_database')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * synchronise enrollments for particular course
     * @param object $course
     */
    public function sync_course_enrolments($course) {
        global $CFG, $DB;

        if (!$this->is_configured()) {
            return false;
        }
    }

    /**
     * split the course code into an array accounting
     * for multiple delimeters etc.
     * @param string $code (list of) course codes
     * @return array array of course codes
     */
    public function split_code($code) {

        // Split on comma or space.
        $codes = preg_split("/[\s,]+/", $code, null, PREG_SPLIT_NO_EMPTY );

        return $codes;
    }

    /**
     * Add new instance of gudatabaseenrol plugin when there isn't one already
     * using appropriate defaults.
     * @param object $course
     * @param array instance fields
     * @return int id of new instance, null if can not be created
     */
    public function add_first_instance($course, array $fields = null) {

        $fields['roleid'] = '5';
        $fields['customint1'] = 14400; // Deletion Grace Period
        $fields['customint2'] = 1; // Expire action
        $fields['customint8'] = 0; // Last update timestamp
        return $this->add_instance($course, $fields);
    }
    
    /**
     * check if course has at least one instance of this plugin
     * add if not
     * @param object $course
     * @return int instanceid
     */
    public function check_instance($courseid) {
        global $DB;
        
        $this->addToLog(-1, $courseid, 'd', 'Checking if SITS sync is already used on course.');
        $course = $DB->get_record('course', array('id'=>$courseid));
        
        // Get all instances in this course.
        $instances = enrol_get_instances($courseid, true);

        // Search for this one.
        $found = false;
        foreach ($instances as $instance) {
            if ($instance->enrol == $this->get_name()) {
                $found = true;
                $instanceid = $instance->id;
                $this->addToLog($instanceid, $courseid, 'd', 'Found SITS Sync on course.');
            }
        }

        // If we didn't find it then add it.
        if (!$found) {
            $instanceid = $this->add_first_instance($course);
            $this->addToLog($instanceid, $courseid, 'c', 'Added SITS Sync to course.');
        }

        return $instanceid;
    }

    /**
     * Check if automatic enrolment possible.
     * Do not do anything if course outside of date range
     * or not visible
     * @param object $course
     * @param object $instance (if we know it)
     * @return boolean
     */
    public function enrolment_possible($course, $instance = null) {
        return true;
    }
    
    protected function get_course($instance) {
        global $DB;

        return $DB->get_record('course', array('id' => $instance->courseid), '*', MUST_EXIST);
    }

    /**
     * Perform custom validation of the data used to edit the instance.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @param object $instance The instance loaded from the DB
     * @param context $context The context of the instance we are editing
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK.
     * @return void
     */
    public function edit_instance_validation($data, $files, $instance, $context) {
        $errors = array();

        // Valid data.
        if ($instance->id) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $this->get_config('roleid'));
        }
        $roles = array_keys($roles);
        $yesno = array(0, 1);

        // Parameters to validate.
        $rules = array(
            'customint3' => $yesno,
            'roleid' => $roles,
            'expireroleid' => [0] + $roles,
            'customint5' => $yesno,
        );

        $errors = $this->validate_param_types($data, $rules);

        return $errors;
    }
    
    private static $schools = Array(
        'MBS'=>'Aberdeen Business School',
		'RGU'=>'RGU Professional', 
		'BUS'=>'Aberdeen Business School',
		'CIM'=>'School of Creative and Cultural Business',
		'LAW'=>'The Law School',
		'HES'=>'Centre for Enhancement of Learning & Teaching',
		'CEN'=>'Centre for Student Access',
		'DAA'=>'Gray\'s School of Art',
		'HSS'=>'School of Applied Social Studies',
		'DCD'=>'School of Computing', 
		'DCO'=>'School of Computing',
		'DEN'=>'School of Engineering',
		'HHS'=>'School of Health Sciences',
		'HPL'=>'School of Pharmacy and Life Sciences',
		'HNU'=>'School of Nursing, Midwifery and Paramedic Practice',
		'HPL'=>'School of Pharmacy & Life Sciences',
		'DSC'=>'The Scott Sutherland School of Architecture and the Built Environment'
    );
    
    public static function addToLog($instance, $course, $level, $message) {
        global $DB;
        
        $entry = new stdClass;
        $entry->instanceid = $instance;
        $entry->courseid = $course;
        $entry->level = $level;
        $entry->details = $message;
        $entry->timeadded = time();
        
        mtrace($message);
        $DB->insert_record('enrol_sits_log', $entry);
        
    }
    
    public static function sendQueryToSITS($query) {
        
        $host = get_config('enrol_sits', 'sits_db_host');
        $username = get_config('enrol_sits', 'sits_db_username');
        $password = get_config('enrol_sits', 'sits_db_password');
        $database = get_config('enrol_sits', 'sits_db_database');
        
        if(
            empty($host) ||
            empty($username) ||
            empty($password) ||
            empty($database) ||
            empty($query)
        ) {
            return false;
        }
        
        $sitsDB = new mysqli($host, $username, $password, $database);
        
        $result = $sitsDB->query($query);
        
        $results = Array();
        while ($row = $result->fetch_object()) {
            $results[] = $row;
        }
        
        return $results;
    }
    
    function s($count, $one='', $many='s') {
        if($count===1) {
            return $one;
        }
        return $many;
    }
    
    function getCodesForInstance($instanceid) {
        global $DB;
        
        $codes = $DB->get_records('enrol_sits_code', array('instanceid'=>$instanceid));
        
        return $codes;
    }
    
    function getEnrolInstancesForCourse($courseid) {
        global $DB;
        
        
        $instances = $DB->get_records('enrol', array('enrol'=>'sits','status'=>'0','courseid'=>$courseid));
        
        return $instances;
    }
    
    function getAllModules() {
        $mods = sendQueryToSITS('SELECT mod_code,mod_name FROM INS_MOD WHERE mod_iuse = "Y" ORDER BY mod_name');
        
        $modules = Array();
        
        foreach($mods as $mod) {
            $modules[$mod->mod_code] = $mod->mod_name;
        }
        
        return $modules;
    }
    
    function checkModCode($code) {
        $mods = sendQueryToSITS('SELECT mod_code,mod_name FROM INS_MOD WHERE mod_code="'.$code.'" AND mod_iuse = "Y"');
        if(count($mods) === 1) {
            return $mods;
        } else {
            return false;
        }
    }
    
    function getAllCourses() {
        $crss = sendQueryToSITS('SELECT crs_code,crs_name FROM srs_crs ORDER BY crs_name');
        
        $courses = Array();
        
        foreach($crss as $crs) {
            $courses[$crs->crs_code] = $crs->crs_name;
        }
        
        return $courses;
    }
    
    function checkCourseCode($code) {
        $crss = sendQueryToSITS('SELECT crs_code,crs_name FROM src_crs WHERE crs_code="'.$code.'"');
        if(count($crss) === 1) {
            return $crss;
        } else {
            return false;
        }
    }
    
    function fixModuleName($name) {
        if($name != ucase($name)) {
            return $name;
        }
        
        $fixWords = Array(
            'OF'        => 'of',
            'A'         => 'a',
            'THE'       => 'the',
            'AND'       => 'and',
            'AN'        => 'an',
            'OR'        => 'or',
            'NOR'       => 'nor',
            'BUT'       => 'but',
            'IS'        => 'is',
            'IF'        => 'if',
            'THEN'      => 'then',
            'ELSE'      => 'else',
            'WHEN'      => 'when',
            'AT'        => 'at',
            'FROM'      => 'from',
            'BY'        => 'by',
            'ON'        => 'on',
            'OFF'       => 'off',
            'FOR'       => 'for',
            'IN'        => 'in',
            'OUT'       => 'out',
            'OVER'      => 'over',
            'TO'        => 'to',
            'INTO'      => 'into',
            'WITH'      => 'with',
            'MA'        => 'MA',
            'MSC'       => 'MSc',
            'BA'        => 'BA',
            'HE'        => 'HE',
            'PT'        => 'PT',
            '(PT)'      => '(PT)',
            'BSC'       => 'BSc',
            'BDES'      => 'BDES',
            '(HONS)'    => '(Hons)',
            'DL'        => 'DL',
            'MBA'       => 'MBA',
            'PGCERT'    => 'PGCert',
            'PGDIP'     => 'PGDip',
            'PGDIP/MSC' => 'PGDip/MSc',
        );
		
		$words = explode(' ', $name);
		$newWords = Array();
		
		foreach($words as $word) {
		    if(isset($fixWords[$word])) {
		        $newWords[] = $fixWords[$word];
		    } else {
		        $newWords[] = ucwords($word);
		    }
		}
		
		return implode(' ', $newWords);
    }
    
    function getStudentsWhoMatchCode($code) {
        
    }
    
    public static function getAllCodesForCourse($courseid) {
        global $DB;
        
        $codes = $DB->get_records_sql('SELECT * FROM {enrol_sits_code} WHERE instanceid in (SELECT id FROM {enrol} WHERE courseid='.$courseid.' AND enrol="sits")');
        
        return $codes;
    }
    
    function syncCourse($courseid, $force=false) {
        $limit = get_config('enrol_sits', 'trigger_inactive_time');
        $courseDetails = $DB->get_record('course', array('id'=>$courseid));
        
        if($courseDetails->startdate != 0 && $courseDetails->startdate > time()) {
            $this->addToLog(-1, $courseid, 'i', 'Not syncing. Course start date is in the future.');
        }
        if($courseDetails->enddate != 0 && $courseDetails->enddate < time()) {
            $this->addToLog(-1, $courseid, 'i', 'Not syncing. Course end date is in the past.');
        }
        if($courseDetails->visible != 1) {
            $this->addToLog(-1, $courseid, 'i', 'Not syncing. Course is hidden from students.');
        }
        
        $instances = $this->getEnrolInstancesForCourse($courseid);
        foreach($instances as $instance) {
            if($instance->status !== 0) {
                $this->addToLog($instance->id, $courseid, 'i', 'Not syncing. Sync users is set to off.');
                break;
            }
            if($instance->customint8 > (time()-$limit) && !$force) {
                $this->addToLog($instance->id, $courseid, 'i', 'Not syncing. Sync was run recently.');
                break;
            }
        
            $this->addToLog($instance->id, $courseid, 'i', 'Syncing with SITS...');
            $this->syncEnrolInstance($instance);
            $this->updateInstanceTime($instance);
        }
    }
    
    function updateInstanceTime($instance) {
        global $DB;
    
        $instance->customint8 = time();
        $DB->update_record('enrol', $instance);
    }
    
    function syncEnrolInstance($instance) {
        global $DB;
        
        //addToLog($instance->id, $instance->courseid, 'i', 'Syncing enrolments for this copy of the plugin');
        
        $codes = $this->getCodesForInstance($instance->id);
        
        $usersWhoBelong = Array();
        $usersRemoved = Array();
        
        // Now we add the users who belong
        
        foreach($codes as $code) {
            $usersAdded = Array();
            switch($code->type) {
                case 'module':
                
                    break;
                case 'course':
                
                    break;
                case 'school':
                
                    break;
                case 'all-students':
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Adding all students to course.');
                    $students = $DB->get_records('user', array('institution'=>'student', 'deleted'=>'0'));
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Got a list of '.number_format(count($students)).' user'.$this->s(count($students)));
                    foreach($students as $student) {
                        $usersWhoBelong[$student->id] = $student->id;
                        if($this->createEnrolmentRecord($instance->id, $student->id)) {
                            $this->addToLog($instance->id, $instance->courseid, 'a', 'Added '.$student->firstname.' '.$student->lastname.' - User ID '.$student->idnumber);
                            $usersAdded[$student->id] = $student->id;
                        }
                    }
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Added '.count($usersAdded).' new user'.$this->s(count($usersAdded)).' to the course.');
                    break;
                case 'all-staff':
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Adding all staff to course.');
                    $staff = $DB->get_records('user', array('institution'=>'staff', 'deleted'=>'0'));
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Got a list of '.number_format(count($staff)).' user'.$this->s(count($staff)).'.');
                    foreach($staff as $user) {
                        $usersWhoBelong[$user->id] = $user->id;
                        if($this->createEnrolmentRecord($instance->id, $user->id)) {
                            $this->addToLog($instance->id, $instance->courseid, 'a', 'Added '.$user->firstname.' '.$user->lastname.' to the course.');
                            $usersAdded[$user->id] = $user->id;
                        }
                    }
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Added '.count($usersAdded).' new user'.$this->s(count($usersAdded)).' to the course.');
                    break;
                case 'dept-staff':
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Adding all staff in a department to course.');
                    if(isset(enrol_sits_plugin::$schools[$code->code])) {
                        $dept = enrol_sits_plugin::$schools[$code->code];
                        $staff = $DB->get_records('user', array('institution'=>'staff', 'deleted'=>'0', 'department'=>$dept));
                        $this->addToLog($instance->id, $instance->courseid, 'i', 'Got a list of '.number_format(count($staff)).' user'.$this->s(count($staff).' in '.$dept).'.');
                        foreach($staff as $user) {
                            $usersWhoBelong[$user->id] = $user->id;
                            if(createEnrolmentRecord($instance->id, $user->id)) {
                                $this->addToLog($instance->id, $instance->courseid, 'a', 'Added '.$user->firstname.' '.$user->lastname.' to the course.');
                                $usersAdded[$user->id] = $user->id;
                            }
                        }
                        $this->addToLog($instance->id, $instance->courseid, 'i', 'Added '.count($usersAdded).' new user'.$this->s(count($usersAdded)).' to the course.');
                    } else {
                        $this->addToLog($instance->id, $instance->courseid, 'e', 'The department "'.$code->code.'" doesn\'t seem to exist.');
                    }
                    break;
            }
        }
        
        // Get the users who don't belong and decide what to do with them
        
        if(count($usersWhoBelong)) {
            $deletions = $DB->get_records_sql('SELECT * FROM {enrol_sits_users} WHERE instanceid='.$instance->id.' AND userid NOT IN ('.implode(',', $usersWhoBelong).')');
        } else {
            // Nobody belongs. Remove everyone? Do nothing for safety?
            $deletions = $DB->get_records_sql('SELECT * FROM {enrol_sits_users} WHERE instanceid='.$instance->id);
            $this->addToLog($instance->id, $instance->courseid, 'd', 'This enrolment rule has selected nobody. Rollover? Nasty Bug?');
        }
        
        $usersToDelete = Array();
        
        foreach($deletions as $deletion) {
            $usersToDelete[$deletion->userid] = $deletion->userid;
        }
        
        if(count($usersToDelete)) {
            $this->addToLog($instance->id, $instance->courseid, 'a', 'Found '.count($usersToDelete).' expired user'.$this->s(count($usersToDelete)).'.');
        } else {
            $this->addToLog($instance->id, $instance->courseid, 'a', 'Didn\'t find any expired users.');
        }
        
        foreach($usersToDelete as $victim) {
            $userDetails = $DB->get_record('user', array('id'=>$victim));
            
            // Each enrolment creates two records - one in this plugin's table
            // and one in Moodle's enrolment table. We'll freeze the plugin's
            // record so we can clean up the user later, then decide what to
            // do with the enrolment record.
            
            $record = $DB->get_record('enrol_sits_users', array('instanceid'=>$instance->id,'userid'=>$victim));
            $record->frozen = 1;
            $record->timeupdated = time();
            $DB->update_record('enrol_sits_users', $record);
            
            switch($instance->customint2) {
                case 0:
                    $this->addToLog($instance->id, $instance->courseid, 'r', $userDetails->firstname.' '.$userDetails->lastname.' has expired. Doing nothing.');
                    break;
                case 1:
                    $this->addToLog($instance->id, $instance->courseid, 'r', $userDetails->firstname.' '.$userDetails->lastname.' has expired. Suspending them.');
                    $record = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$victim));
                    $record->status = ENROL_USER_SUSPENDED;
                    $DB->update_record('user_enrolments', $record);
                    break;
                case 2:
                    $this->addToLog($instance->id, $instance->courseid, 'r', $userDetails->firstname.' '.$userDetails->lastname.' has expired. Removing them from the course.');
                    // We suspend them now, so they're hidden. We don't delete
                    // them now for safety - we schedule an ad-hoc task in the
                    // future to delete them if they're still frozen.
                    $record = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$victim));
                    $record->status = ENROL_USER_SUSPENDED;
                    $DB->update_record('user_enrolments', $record);
                    break;
            }              
        }
    }
    
    function createEnrolmentRecord($instanceid, $userid, $usernumber="") {
        global $DB;
        
        // This function returns true if we create a record or unsuspend a
        // suspended user.
        $touched = false;
        
        $exists = $DB->get_record('enrol_sits_users', array('instanceid'=>$instanceid, 'userid'=>$userid));
        
        if($exists && $exists->frozen==1) {
            // User was enrolled before and needs to be re-added. Unfreeze record.
            $record = $exists;
            $record->frozen = 0;
            $record->timeupdated = time();
            $DB->update_record('enrol_sits_users', $record);
            $touched = true;
        }
        if(!$exists) {
            // User was never added. This is a new enrolment.
            $record = new stdClass;
            $record->instanceid = $instanceid;
            $record->userid = $userid;
            $record->studentno = $usernumber;
            $record->timeupdated = time();
            $record->frozen = '0';
            $DB->insert_record('enrol_sits_users', $record);
            $touched = true;
        }
        
        $exists = $DB->get_record('user_enrolments', array('enrolid'=>$instanceid, 'userid'=>$userid));
        if($exists && $record->status != ENROL_USER_ACTIVE) {
            // Users was enrolled before and needs to be re-added. Unsuspend.
            $record = $exists;
            $record->status = ENROL_USER_ACTIVE;
            $record->timemodified = time();
            $DB->update_record('user_enrolments', $record);
            $touched = true;
        }
        if(!$exists) {
            // User was never added. This is a new enrolment.
            $record = new stdClass;
            $record->status = ENROL_USER_ACTIVE;
            $record->enrolid = $instanceid;
            $record->userid = $userid;
            $record->modifierid = 2;
            $record->timecreated = time();
            $reeord->timemodified = time();
            $DB->insert_record('user_enrolments', $record);
            $touched = true;
        }
        
        return $touched;
    }
    
        /**
     * Add elements to the edit instance form.
     *
     * @param stdClass $instance
     * @param MoodleQuickForm $mform
     * @param context $context
     * @return bool
     */
    public function edit_instance_form($instance, MoodleQuickForm $mform, $context) {
        global $PAGE;

        // Get renderer.
        $output = $PAGE->get_renderer('enrol_sits');

        $mform->addElement('text', 'name', get_string('customname', 'enrol_sits'));
        $mform->addElement('static', 'name_desc', '', get_string('customname_desc', 'enrol_sits'));
        $mform->setType('name', PARAM_TEXT);

        $options = array(ENROL_INSTANCE_ENABLED  => get_string('yes'),
                         ENROL_INSTANCE_DISABLED => get_string('no'));
        $mform->addElement('select', 'status', get_string('instance_enabled', 'enrol_sits'), $options);
        $mform->addElement('static', 'instance_enabled__desc', '', get_string('instance_enabled_desc', 'enrol_sits'));
        $mform->setDefault('status', $this->get_config('status'));

        if ($instance->id) {
            $roles = get_default_enrol_roles($context, $instance->roleid);
        } else {
            $roles = get_default_enrol_roles($context, $this->get_config('roleid'));
        }
        $mform->addElement('select', 'roleid', get_string('roleid', 'enrol_sits'), $roles);
        $mform->addElement('static', 'instance_enabled_desc', '', get_string('roleid_desc', 'enrol_sits'));
        $mform->setDefault('roleid', 5);

        $unenrolActions = Array(
            0 => 'Keep the users active in the course',
            1 => 'Hide the users, but keep all their work',
            2 => 'Delete the users and their work',
        );
        $mform->addElement('select', 'customint2', get_string('expireaction', 'enrol_sits'), $unenrolActions);
        $mform->setDefault('customint2', 1);
        $mform->addElement('static', 'expireaction_desc', '', get_string('expireaction_desc', 'enrol_sits'));

        //$mform->closeHeaderBefore('section_settings');

        // Automatic groups settings.
        
    }

    /**
     * Update instance of enrol plugin.
     * @param stdClass $instance
     * @param stdClass $data modified instance fields
     * @return boolean
     */
    public function update_instance($instance, $data) {
        global $DB;
        /*
        // Needed data.
        $course = $this->get_course($instance);
        list($codeclasses, $coursedescriptions) = $this->get_coursedescriptions($course, $instance);

        // Standard settings.
        $data->customint1 = $data->expireroleid;

        // Codes settings.
        $instance->customtext1 = strtoupper($data->customtext1);

        // Course groups.
        $instance->customint2 = isset($data->coursegroups) ? $data->coursegroups : '';

        // Group settings.
        $groups = array();
        foreach ($codeclasses as $code => $codeclass) {
            $groups[$code] = array();
            foreach ($codeclass as $class) {
                $classnospace = str_replace(' ', '_', $class);
                $selector = "{$code}_{$classnospace}";

                // If code has just been added, expected classes are not on the form.
                if (!isset($data->$selector)) {
                    continue;
                }
                $groups[$code][$class] = $data->$selector == 1;
            }
        }
        $data->customtext2 = serialize($groups);

        // Update enrolments.
        $this->course_updated(false, $course, null);
        */
        return parent::update_instance($instance, $data);
    }
    
}
