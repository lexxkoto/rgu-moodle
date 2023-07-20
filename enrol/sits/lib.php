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
        $instance = $this->add_instance($course, $fields);
        $this->sniffCourseRules($course->id, $instance);
        return $instance;
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
        $instances = enrol_get_instances($courseid, false);

        // Search for this one.
        $found = false;
        foreach ($instances as $instance) {
            if ($instance->enrol == 'sits') {
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
    
    public static $schools = Array(
		'RGU'=>'RGU Professional', 
		'BUS'=>'Aberdeen Business School',
		'CIM'=>'School of Creative and Cultural Business',
		'LAW'=>'The Law School',
		'HES'=>'Centre for Enhancement of Learning & Teaching',
		'CEN'=>'Centre for Student Access',
		'DAA'=>'Gray\'s School of Art',
		'HSS'=>'School of Applied Social Studies',
		'DCD'=>'School of Computing', 
		'DEN'=>'School of Engineering',
		'HHS'=>'School of Health Sciences',
		'HPL'=>'School of Pharmacy and Life Sciences',
		'HNU'=>'School of Nursing, Midwifery and Paramedic Practice',
		'DSC'=>'The Scott Sutherland School of Architecture and the Built Environment'
    );
    
    public static function addToLog($instance, $course, $level, $message, $debug=false) {
        global $DB;
        
        $entry = new stdClass;
        $entry->instanceid = $instance;
        $entry->courseid = $course;
        $entry->level = $level;
        $entry->details = $message;
        $entry->timeadded = time();
        
        //mtrace($message);
        $DB->insert_record('enrol_sits_log', $entry);
        
        if($debug) {
            echo $message.PHP_EOL;
        }
        
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
        
        
        $instances = $DB->get_records('enrol', array('enrol'=>'sits', 'courseid'=>$courseid));
        
        return $instances;
    }
    
    function getAllModules() {
        $mods = $this->sendQueryToSITS('SELECT mod_code,mod_name FROM INS_MOD WHERE mod_iuse = "Y" ORDER BY mod_name');
        
        $modules = Array();
        
        foreach($mods as $mod) {
            $modules[$mod->mod_code] = $mod->mod_name;
        }
        
        return $modules;
    }
    
    function checkModCode($code) {
        $mods = $this->sendQueryToSITS('SELECT mod_code,mod_name FROM INS_MOD WHERE mod_code="'.$code.'" AND mod_iuse = "Y"');
        if(count($mods) === 1) {
            return $mods;
        } else {
            return false;
        }
    }
    
    function getAllCourses() {
        $crss = $this->sendQueryToSITS('SELECT crs_code,crs_name FROM srs_crs ORDER BY crs_name');
        
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
        if($name != strtoupper($name)) {
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
		        $newWords[] = mb_convert_case($word, MB_CASE_TITLE);
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
    
    function syncCourse($courseid, $force=false, $debug=false) {
        global $DB;
        
        if($force) {
            $this->addToLog(-1, $courseid, 'i', 'Force enabled. Ignoring safeguards and syncing anyway.', $debug);
        } else {
            if(get_config('enrol_sits', 'sits_db_enabled') !== 'enabled') {
                $this->addToLog(-1, $courseid, 'i', 'Not syncing. SITS Sync is switched off system-wide.', $debug);
                return false;
            }
            
            $limit = get_config('enrol_sits', 'trigger_inactive_time');
            $courseDetails = $DB->get_record('course', array('id'=>$courseid));
            
            if($courseDetails->startdate != 0 && $courseDetails->startdate > time()) {
                $this->addToLog(-1, $courseid, 'i', 'Not syncing. Course start date is in the future.', $debug);
                return false;
            }
            if($courseDetails->enddate != 0 && $courseDetails->enddate < time()) {
                $this->addToLog(-1, $courseid, 'i', 'Not syncing. Course end date is in the past.', $debug);
                return false;
            }
            if($courseDetails->visible != 1) {
                $this->addToLog(-1, $courseid, 'i', 'Not syncing. Course is hidden from students.', $debug);
                return false;
            }
        }
        
        $instances = $this->getEnrolInstancesForCourse($courseid);
        $this->addToLog(-1, $courseid, 'i', 'Found '.count($instances).$this->s(count($instances), ' copy ', ' copies ').'of SITS Sync on this course.', $debug);
        foreach($instances as $instance) {
            if($instance->status != 0 && !$force) {
                $this->addToLog($instance->id, $courseid, 'i', 'Not syncing. Sync users is set to off ('.$instance->status.').', $debug);
                continue;
            }
            if($instance->customint8 > (time()-$limit) && !$force) {
                $this->addToLog($instance->id, $courseid, 'i', 'Not syncing. Sync was run recently.', $debug);
                continue;
            }
        
            $this->addToLog($instance->id, $courseid, 'i', 'Syncing with SITS...', $debug);
            $this->syncEnrolInstance($instance, $debug);
            $this->updateInstanceTime($instance);
        }
    }
    
    function updateInstanceTime($instance) {
        global $DB;
    
        $instance->customint8 = time();
        $DB->update_record('enrol', $instance);
    }
    
    function syncEnrolInstance($instance, $debug=false) {
        global $DB;
        
        $this->addToLog($instance->id, $instance->courseid, 'i', 'Syncing this copy of SITS Sync', $debug);
        
        $codes = $this->getCodesForInstance($instance->id);
        
        $usersWhoBelong = Array();
        $usersRemoved = Array();
        
        // Now we add the users who belong
        
        foreach($codes as $code) {
            $usersAdded = Array();
            switch($code->type) {
                case 'all-students':
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Adding all students to course.', $debug);
                    $students = $DB->get_records('user', array('institution'=>'student', 'deleted'=>'0'));
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Got a list of '.number_format(count($students)).' user'.$this->s(count($students)), $debug);
                    foreach($students as $student) {
                        $usersWhoBelong[$student->id] = $student->id;
                        if($this->createEnrolmentRecord($instance, $student->id, $instance->roleid)) {
                            $this->addToLog($instance->id, $instance->courseid, 'a', 'Added '.$student->firstname.' '.$student->lastname.' - User ID '.$student->idnumber, $debug);
                            $usersAdded[$student->id] = $student->id;
                        }
                    }
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Added '.count($usersAdded).' new user'.$this->s(count($usersAdded)).' to the course.', $debug);
                    break;
                case 'all-staff':
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Adding all staff to course.', $debug);
                    $staff = $DB->get_records('user', array('institution'=>'staff', 'deleted'=>'0'));
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Got a list of '.number_format(count($staff)).' user'.$this->s(count($staff)).'.', $debug);
                    foreach($staff as $user) {
                        $usersWhoBelong[$user->id] = $user->id;
                        if($this->createEnrolmentRecord($instance, $user->id, $instance->roleid)) {
                            $this->addToLog($instance->id, $instance->courseid, 'a', 'Added '.$user->firstname.' '.$user->lastname.' to the course.', $debug);
                            $usersAdded[$user->id] = $user->id;
                        }
                    }
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Added '.count($usersAdded).' new user'.$this->s(count($usersAdded)).' to the course.', $debug);
                    break;
                case 'dept-staff':
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Adding all staff in a department to course.', $debug);
                    if(isset(enrol_sits_plugin::$schools[$code->code])) {
                        $dept = enrol_sits_plugin::$schools[$code->code];
                        $staff = $DB->get_records('user', array('institution'=>'staff', 'deleted'=>'0', 'department'=>$dept));
                        $this->addToLog($instance->id, $instance->courseid, 'i', 'Got a list of '.number_format(count($staff)).' user'.$this->s(count($staff).' in '.$dept).'.', $debug);
                        foreach($staff as $user) {
                            $usersWhoBelong[$user->id] = $user->id;
                            if($this->createEnrolmentRecord($instance, $user->id, $instance->roleid)) {
                                $this->addToLog($instance->id, $instance->courseid, 'a', 'Added '.$user->firstname.' '.$user->lastname.' to the course.', $debug);
                                $usersAdded[$user->id] = $user->id;
                            }
                        }
                        $this->addToLog($instance->id, $instance->courseid, 'i', 'Added '.count($usersAdded).' new user'.$this->s(count($usersAdded)).' to the course.', $debug);
                    } else {
                        $this->addToLog($instance->id, $instance->courseid, 'e', 'The department "'.$code->code.'" doesn\'t seem to exist.', $debug);
                    }
                    break;
                case 'school':
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Adding all students in '.enrol_sits_plugin::$schools[$code->code]);
                    $currentYear = get_config('enrol_sits', 'sits_current_year');
                    $allowedCodes = get_config('enrol_sits', 'allowed_codes');
                    $sql = 'SELECT sce_stuc, sce_blok FROM INTUIT.srs_sce WHERE sce_dptc LIKE "'.$code->code.'%" AND sce_ayrc = '.$currentYear.' AND sce_stac IN ('.$allowedCodes.')';
                    if(!empty($code->levels)) {
                        $levels = explode(':', $code->levels);
                        $levelsSQL = Array();
                        foreach($levels as $level) {
                            if($level == 'PG') {
                                $levelsSQL[] = '(sce_crsc LIKE "P%")';
                            } else {
                                $levelsSQL[] = '(sce_blok LIKE "'.$level.'%" AND sce_crsc LIKE "U%")';
                            }
                        }
                    }
                    
                    if(!empty($levelsSQL)) {
                        $sql .= ' AND ('.implode(' OR ', $levelsSQL).')';
                    }
                    
                    if(!empty($code->blocks)) {
                        $blocks = explode(':', $code->blocks);
                        $blocksSQL = Array();
                        foreach($blocks as $block) {
                            $blocksSQL[] = '(sce_blok LIKE "'.$block.'")';
                        }
                    }
                    
                    if(!empty($blocksSQL)) {
                        $sql .= 'AND ('.implode(' OR ', $blocksSQL).')';
                    }
                    
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Got a list of '.count($users).' user'.$this->s(count($users)).' from SITS.', $debug);
                    
                    $users = $this->sendQueryToSITS($sql);
                                        
                    foreach($users as $user) {
                        $realUser = $DB->get_record('user', array('idnumber'=>$user->sce_stuc, 'deleted'=>0));
                        if(isset($realUser->id)) {
                            $usersWhoBelong[$realUser->id] = $realUser->id;
                            if($this->createEnrolmentRecord($instance, $realUser->id, $instance->roleid)) {
                                $this->addToLog($instance->id, $instance->courseid, 'a', 'Added '.$realUser->firstname.' '.$realUser->lastname.' to the course.', $debug);
                                $usersAdded[$realUser->id] = $realUser->id;
                            }
                        } else {
                            $this->addToLog($instance->id, $instance->courseid, 'a', 'Student ID '.$user->sce_stuc.' does not exist in Moodle.', $debug);
                        }
                    }
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Added '.count($usersAdded).' new user'.$this->s(count($usersAdded)).' to the course.', $debug);
                    break;
                case 'course':
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Adding all students in course code '.$code->code, $debug);
                    $currentYear = get_config('enrol_sits', 'sits_current_year');
                    $allowedCodes = get_config('enrol_sits', 'allowed_codes');
                    $sql = 'SELECT sce_stuc, sce_blok FROM INTUIT.srs_sce WHERE sce_ayrc = '.$currentYear.' AND sce_stac IN ('.$allowedCodes.')';
                    if(strpos($code->code, '*') === false) {
                        $sql .= ' AND sce_crsc="'.$code->code.'"';
                    } else {
                        $sql .= ' AND sce_crsc LIKE "'.str_replace('*', '%', $code->code).'"';
                    }
                    if(!empty($code->levels)) {
                        $levels = explode(':', $code->levels);
                        $levelsSQL = Array();
                        foreach($levels as $level) {
                            if($level == 'PG') {
                                $levelsSQL[] = '(sce_crsc LIKE "P%")';
                            } else {
                                $levelsSQL[] = '(sce_blok LIKE "'.$level.'%" AND sce_crsc LIKE "U%")';
                            }
                        }
                        if(!empty($levelsSQL)) {
                            $sql .= ' AND ('.implode(' OR ', $levelsSQL).')';
                        }
                    }
                    
                    if(!empty($code->blocks)) {
                        $blocks = explode(':', $code->blocks);
                        $blocksSQL = Array();
                        foreach($blocks as $block) {
                            $blocksSQL[] = '(sce_blok LIKE "'.$block.'")';
                        }
                        if(!empty($blocksSQL)) {
                            $sql .= 'AND ('.implode(' OR ', $blocksSQL).')';
                        }
                    }
                    
                    if(!empty($code->period)) {
                        $periods = explode(':', $code->period);
                        $periodSQL = Array();
                        foreach($periods as $period) {
                            if($period=='YE') {
                                $periodSQL[] = '(cam_smo.psl_code="YEAR")';
                            } else {
                                $periodSQL[] = '(cam_smo.psl_code="SEM'.$period.'")';
                            }
                        }
                        if(!empty($periodSQL)) {
                            $sql .= ' AND ('.implode(' OR ', $periodSQL).')';
                        }
                    }
                    
                    $users = $this->sendQueryToSITS($sql);
                    
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Got a list of '.count($users).' user'.$this->s(count($users)).' from SITS.', $debug);
                                        
                    foreach($users as $user) {
                        $realUser = $DB->get_record('user', array('idnumber'=>$user->sce_stuc, 'deleted'=>0));
                        if(isset($realUser->id)) {
                            $usersWhoBelong[$realUser->id] = $realUser->id;
                            if($this->createEnrolmentRecord($instance, $realUser->id, $instance->roleid)) {
                                $this->addToLog($instance->id, $instance->courseid, 'a', 'Added '.$realUser->firstname.' '.$realUser->lastname.' to the course.', $debug);
                                $usersAdded[$realUser->id] = $realUser->id;
                            }
                        } else {
                            $this->addToLog($instance->id, $instance->courseid, 'a', 'Student ID '.$user->sce_stuc.' does not exist in Moodle.', $debug);
                        }
                    }
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Added '.count($usersAdded).' new user'.$this->s(count($usersAdded)).' to the course.', $debug);
                    break;
                case 'module':
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Adding all students in module '.$code->code.'.', $debug);
                    if(!empty($code->year)) {
                        $year = $code->year;
                        $this->addToLog($instance->id, $instance->courseid, 'i', 'Year specified: '.$year.'.', $debug);
                    } else {
                        if(date('n') < 8) {
                            $year = date('Y')-1;
                        } else {
                            $year = date('Y');
                        }
                        $this->addToLog($instance->id, $instance->courseid, 'i', 'Year not specified, using '.$year.'.', $debug);
                    }
                    
                    $currentYear = get_config('enrol_sits', 'sits_current_year');
                    $allowedCodes = get_config('enrol_sits', 'allowed_codes');
                    $sql = 'select INTUIT.srs_sce.sce_stuc,
INTUIT.srs_sce.sce_crsc,
INTUIT.srs_sce.sce_blok,
INTUIT.srs_sce.sce_occl,
INTUIT.srs_sce.sce_stad,
INTUIT.cam_smo.mav_occur,
INTUIT.cam_smo.psl_code
from INTUIT.cam_smo
inner join INTUIT.srs_sce ON INTUIT.cam_smo.spr_code = INTUIT.srs_sce.sce_scjc
inner join INTUIT.ins_stu on INTUIT.srs_sce.sce_stuc = INTUIT.ins_stu.stu_code
inner join INTUIT.cam_mav
on INTUIT.cam_smo.mod_code = INTUIT.cam_mav.mod_code
AND INTUIT.cam_smo.mav_occur = INTUIT.cam_mav.mav_occur
AND INTUIT.cam_smo.ayr_code = INTUIT.cam_mav.ayr_code
AND INTUIT.cam_smo.psl_code = INTUIT.cam_mav.psl_code
where INTUIT.cam_mav.mod_code = "'.$code->code.'"
and INTUIT.srs_sce.sce_ayrc = "'.$year.'"
and INTUIT.cam_smo.ayr_code = "'.$year.'"
AND
INTUIT.srs_sce.sce_stac IN ('.$allowedCodes.')
and INTUIT.cam_mav.mav_begp = "Y"';
                    
                    if(!empty($code->levels)) {
                        $levels = explode(':', $code->levels);
                        $levelsSQL = Array();
                        foreach($levels as $level) {
                            if($level == 'PG') {
                                $levelsSQL[] = '(sce_crsc LIKE "P%")';
                            } else {
                                $levelsSQL[] = '(sce_blok LIKE "'.$level.'%" AND sce_crsc LIKE "U%")';
                            }
                        }
                        if(!empty($levelsSQL)) {
                            $sql .= ' AND ('.implode(' OR ', $levelsSQL).')';
                        }
                    }
                    
                    if(!empty($code->blocks)) {
                        $blocks = explode(':', $code->blocks);
                        $blocksSQL = Array();
                        foreach($blocks as $block) {
                            // Support for putting levels in as block codes (old system)
                            if($block == 'PG') {
                                $blocksSQL[] = '(sce_crsc LIKE "P%")';
                                continue;
                            }
                            if(in_array($block, [1, 2, 3, 4, 5])) {
                                $blocksSQL[] = '(sce_blok LIKE "'.$level.'%" AND sce_crsc LIKE "U%")';
                                continue;
                            }
                            $blocksSQL[] = '(sce_blok LIKE "'.$block.'")';
                        }
                        if(!empty($blocksSQL)) {
                            $sql .= ' AND ('.implode(' OR ', $blocksSQL).')';
                        }
                    }
                    
                    if(!empty($code->start)) {
                        $starts = explode(':', $code->start);
                        $startSQL = Array();
                        foreach($starts as $start) {
                            $startSQL[] = '(sce_occl="'.$start.'")';
                        }
                        if(!empty($startSQL)) {
                            $sql .= ' AND ('.implode(' OR ', $startSQL).')';
                        }
                    }
                    
                    if(!empty($code->occurrence)) {
                        $occs = explode(':', $code->occurrence);
                        $occSQL = Array();
                        foreach($occs as $occ) {
                            $occSQL[] = '(cam_mav.mav_occur="'.$occ.'")';
                        }
                        if(!empty($occSQL)) {
                            $sql .= ' AND ('.implode(' OR ', $occSQL).')';
                        }
                    }
                    
                    if(!empty($code->period)) {
                        $periods = explode(':', $code->period);
                        $periodSQL = Array();
                        foreach($periods as $period) {
                            if($period=='YE') {
                                $periodSQL[] = '(cam_smo.psl_code="YEAR")';
                            } else {
                                $periodSQL[] = '(cam_smo.psl_code="SEM'.$period.'")';
                            }
                        }
                        if(!empty($periodSQL)) {
                            $sql .= ' AND ('.implode(' OR ', $periodSQL).')';
                        }
                    }
                    
                    $allModes = Array(
                        'FT' =>Array(
                            'FT'
                        ),
                        'PT' => Array(
                            'PT',
                            'PE',
                            'PY',
                            'PP',
                        ),
                        'OD' => Array(
                            'PD',
                            'FD'
                        )
                    );
                    
                    if(!empty($code->modes)) {
                        $modes = explode(':', $code->modes);
                        $modeSQL = Array();
                        foreach($modes as $mode) {
                            if(isset($allModes[$mode])) {
                                foreach($allModes[$mode] as $possibility) {
                                    $modeSQL[] = '(sce_crsc LIKE "___'.$possibility.'%")';
                                }
                            }
                        }
                        if(!empty($modeSQL)) {
                            $sql .= ' AND ('.implode(' OR ', $modeSQL).')';
                        }
                    }

                    $users = $this->sendQueryToSITS($sql);
                    
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Got a list of '.count($users).' user'.$this->s(count($users)).' from SITS.', $debug);
                                        
                    foreach($users as $user) {
                        $realUser = $DB->get_record('user', array('idnumber'=>$user->sce_stuc, 'deleted'=>0));
                        if(isset($realUser->id)) {
                            $usersWhoBelong[$realUser->id] = $realUser->id;
                            if($this->createEnrolmentRecord($instance, $realUser->id, $instance->roleid)) {
                                $this->addToLog($instance->id, $instance->courseid, 'a', 'Added '.$realUser->firstname.' '.$realUser->lastname.' to the course.', $debug);
                                $usersAdded[$realUser->id] = $realUser->id;
                            }
                        } else {
                            $this->addToLog($instance->id, $instance->courseid, 'a', 'Student ID '.$user->sce_stuc.' does not exist in Moodle.', $debug);
                        }
                    }
                    $this->addToLog($instance->id, $instance->courseid, 'i', 'Added '.count($usersAdded).' new user'.$this->s(count($usersAdded)).' to the course.', $debug);
                    break;
                
                default:
                    addToLog($instance->id, $instance->courseid, 'e', 'Unknown enrolment rule: '.$code->type, $debug);
                    break;
            }
        }
        
        $this->addToLog($instance->id, $instance->courseid, 'i', 'Finished adding students to the course.', $debug);
        $this->addToLog($instance->id, $instance->courseid, 'i', 'There are '.count($usersWhoBelong).' user'.$this->s(count($usersWhoBelong)).' who meet a SITS Sync rule.', $debug);
        
        
        // Get the users who don't belong and decide what to do with them
        
        if(count($usersWhoBelong)) {
            $deletions = $DB->get_records_sql('SELECT * FROM {enrol_sits_users} WHERE instanceid='.$instance->id.' AND frozen=0 AND userid NOT IN ('.implode(',', $usersWhoBelong).')');
        } else {
            // Nobody belongs. Remove everyone? Do nothing for safety?
            $deletions = $DB->get_records_sql('SELECT * FROM {enrol_sits_users} WHERE instanceid='.$instance->id);
            $this->addToLog($instance->id, $instance->courseid, 'd', 'This enrolment rule has selected nobody. Rollover? Nasty Bug? Filter that doesn\'t exist?', $debug);
        }
        
        $usersToDelete = Array();
        
        foreach($deletions as $deletion) {
            $usersToDelete[$deletion->userid] = $deletion->userid;
        }
        
        if(count($usersToDelete)) {
            $this->addToLog($instance->id, $instance->courseid, 'a', 'Found '.count($usersToDelete).' expired user'.$this->s(count($usersToDelete)).'.', $debug);
        } else {
            $this->addToLog($instance->id, $instance->courseid, 'a', 'Didn\'t find any expired users.', $debug);
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
                    $this->addToLog($instance->id, $instance->courseid, 'r', $userDetails->firstname.' '.$userDetails->lastname.' has expired. Doing nothing.', $debug);
                    break;
                case 1:
                    $this->addToLog($instance->id, $instance->courseid, 'r', $userDetails->firstname.' '.$userDetails->lastname.' has expired. Suspending them.', $debug);
                    //$record = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$victim));
                    //$record->status = ENROL_USER_SUSPENDED;
                    //$DB->update_record('user_enrolments', $record);
                    $this->update_user_enrol($instance, $userDetails->id, ENROL_USER_SUSPENDED);
                    break;
                case 2:
                    $this->addToLog($instance->id, $instance->courseid, 'r', $userDetails->firstname.' '.$userDetails->lastname.' has expired. Removing them from the course.', $debug);
                    // We suspend them now, so they're hidden. We don't delete
                    // them now for safety - we schedule an ad-hoc task in the
                    // future to delete them if they're still frozen.
                    //$record = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$victim));
                    //$record->status = ENROL_USER_SUSPENDED;
                    //$DB->update_record('user_enrolments', $record);
                    $this->update_user_enrol($instance, $userDetails->id, ENROL_USER_SUSPENDED);
                    break;
            }              
        }
        $this->addToLog($instance->id, $instance->courseid, 'i', 'Finished syncing this copy of SITS Sync.', $debug);
    }
    
    function createEnrolmentRecord($instance, $userid, $role) {
        global $DB;
                
        // This function returns true if we create a record or unsuspend a
        // suspended user.
        $touched = false;
        
        $exists = $DB->get_record('enrol_sits_users', array('instanceid'=>$instance->id, 'userid'=>$userid));
        
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
            $record->instanceid = $instance->id;
            $record->userid = $userid;
            $record->studentno = 0;
            $record->timeupdated = time();
            $record->frozen = '0';
            $DB->insert_record('enrol_sits_users', $record);
            $touched = true;
        }
        
        $exists = $DB->get_record('user_enrolments', array('enrolid'=>$instance->id, 'userid'=>$userid));
        if($exists && $exists->status != ENROL_USER_ACTIVE) {
            // Users was enrolled before and needs to be re-added. Unsuspend.
            /*$record = $exists;
            $record->status = ENROL_USER_ACTIVE;
            $record->timemodified = time();
            $DB->update_record('user_enrolments', $record);*/
            $this->update_user_enrol($instance, $userid, ENROL_USER_ACTIVE);
            $touched = true;
        }
        if(!$exists) {
            // User was never added. This is a new enrolment.
            /*$record = new stdClass;
            $record->status = ENROL_USER_ACTIVE;
            $record->enrolid = $instanceid;
            $record->userid = $userid;
            $record->modifierid = 2;
            $record->timecreated = time();
            $record->timemodified = time();
            $DB->insert_record('user_enrolments', $record);*/
            $this->enrol_user($instance, $userid, $role);
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
    
    public function sniffCourseRules($courseid, $instance, $force = false, $debug = false) {
        global $DB;
        
        $plugin = enrol_get_plugin('sits');
        
        $course = $DB->get_record('course', array('id'=>$courseid));
        
        $record = new stdClass();
        $record->instanceid = $instance;
        
        $existingRules = $DB->get_records('enrol_sits_code', array('instanceid'=>$instance));
                
        if(count($existingRules) > 0) {
            if($debug) {
                 echo 'This course already has SITS Sync rules.'.PHP_EOL;
                if(!$force) {
                    echo 'Not detecting rules. There are already rules and I don\'t want to make a mess.'.PHP_EOL;
                    return false;
                } else {
                    echo 'Force detection enabled. Detecting rules anyway.'.PHP_EOL;
                }
            }
        }
        
        if((strpos(strtolower($course->shortname), 'module study area') !== false) || (strpos(strtolower($course->shortname), 'module') !== false)) {
            if($debug) {
                echo 'This looks like a module study area.'.PHP_EOL;
            }            
            
            $record->type = 'module';
            
            // Get the year
            $matches = Array();
            preg_match('/[^a-zA-Z](20[0-9][0-9])\//', $course->shortname, $matches);
            
            if(count($matches) > 1) {
                $year = $matches[1];
                if($debug) {
                    echo 'This module belongs to the academic year '.$year.'.'.PHP_EOL;
                } 
                $record->year = $year;
            } else {
                if($debug) {
                    echo 'This module has no academic year. Will automatically roll over.'.PHP_EOL;
                } 
            }
            
            
            // Get the module code
            
            $matches = Array();
            preg_match('/](.*)-/U', $course->shortname, $matches);
            
            if(count($matches) < 2) {
                if($debug) {
                    echo 'This module doesn\'t appear to have a module code.'.PHP_EOL;
                } 
                return false;
            }
            
            $code = trim($matches[1]);
            if($debug) {
                echo 'This module has the code '.$code.'.'.PHP_EOL;
            } 
            
            $record->code = $code;
            
            
            $parts = explode('-', $course->shortname);
            $filters = array_slice($parts, 1);
            foreach($filters as $filter) {
                $value = trim($filter);
                
                // Is this a block filter?
                
                $match = preg_match('/Block [0-9]+/i', $value);
                if($match) {
                    $matches = Array();
                    preg_match('/Block ([0-9, ]+)/i', $value, $matches);
                    if(count($matches) > 1) {
                        $blocks = Array();
                        $dirtyBlocks = explode(',', $blocks);
                        foreach($dirtyBlocks as $block) {
                            $blocks[] = trim(strtoupper($block));
                        }
                        $record->blocks = implode(':', $blocks);
                        if($debug) {
                            echo 'Added a filter for blocks: '.$record->blocks.'.'.PHP_EOL;
                        } 
                    }
                    continue;
                }
                
                // Semester filter
                
                $match = preg_match('/Sem(ester){0,1}[ ]{0,1}([1-3])/i', $value);
                if($match) {
                    $matches = Array();
                    preg_match('/Sem(?:ester){0,1}(?:[ ]{0,1})([1-3])/i', $value, $matches);
                    if(count($matches) > 1) {
                        $semesters = Array();
                        $foundSems = array_slice($matches, 1);
                        foreach($foundSems as $sem) {
                            $semesters[] = $sem;
                        }
                        $record->period = implode(':',$semesters);
                        if($debug) {
                            echo 'Added a filter for period: '.$record->period.'.'.PHP_EOL;
                        } 
                    }
                }
                  
                // Is this an occurrence?
                
                $match = preg_match('/Occurrence ([a-zA-Z0-9, ]+)/i', $value);
                if($match) {
                    $matches = Array();
                    preg_match('/Block ([0-9, ]+)/i', $value, $matches);
                    if(count($matches) > 1) {
                        $cleanMatches = array_slice($matches, 1);
                        $occs = Array();
                        foreach($cleanMatches as $cleanMatch) {
                            //out('Occurrence match: '.$cleanMatch);
                            $occs[] = trim(strtoupper($cleanMatch));
                            $record->occurrence = implode(':', $occs);
                            if($debug) {
                                echo 'Added a filter for occurrence: '.$record->occurrence.'.'.PHP_EOL;
                            } 
                        }
                    }
                    continue;
                }
                
                // Does this look like months of the year?
                
                $match = preg_match('/ja(?:nuary){0,1}|fe(?:bruary){0,1}|ma(?:rch){0,1}|ap(?:ril){0,1}|m(?:a){0,1}y|ju(?:ne){0,1}|j(?:u){0,1}l(?:y){0,1}|au(?:gust){0,1}|se(?:ptember){0,1}|oc(?!cur)(?:tober){0,1}|no(?:vember){0,1}|de(?:cember)/i', $value);
                if($match) {
                    $months = Array();
                    
                    $searches = Array(
                        'ja' => '/ja(?:nuary){0,1/i',
                        'fe' => '/fe(?:bruary){0,1}/i',
                        'ma' => '/ma(?:rch){0,1}/i',
                        'ap' => '/ap(?:ril){0,1}/i',
                        'my' => '/m(?:a){0,1}y/i',
                        'ju' => '/ju(?:ne){0,1}/i',
                        'jl' => '/j(?:u){0,1}l(?:y){0,1}/i',
                        'au' => '/au(?:gust){0,1}/i',
                        'se' => '/se(?:ptember){0,1}/i',
                        'oc' => '/oc(?!cur)(?:tober){0,1}/i',
                        'no' => '/no(?:vember){0,1}/i',
                        'de' => '/de(?:cember){0,1}/i'
                    );
                    
                    foreach($searches as $code=>$pattern) {
                        $matches = Array();
                        preg_match($pattern, $value, $matches);
                        if(count($matches) > 1) {
                            //out('Matched a month: '.$code);
                            $months[] = $code;
                        }
                    }
                    
                    if(count($months) !== 0) {
                        $record->start = strtoupper(implode(':', $months));
                        if($debug) {
                            echo 'Added a filter for start month: '.$record->start.'.'.PHP_EOL;
                        } 
                    }
                    continue;
                }
                
                // Does this look like one or more course codes?
            
                $match = preg_match('/([A-Z]{9,10})/i', $value);
                
                if($match) {
                    $courseCodes = Array();
                    preg_match_all('/([A-Z]{9,10})/i', $value, $matches);
                    if(count($matches) > 1) {
                        $foundCodes = $matches[0];
                        foreach($foundCodes as $foundCode) {
                            $courseCodes[] = trim(strtoupper($foundCode));
                        }
                        $record->course = implode(':', $courseCodes);
                        if($debug) {
                            echo 'Added a filter for course code: '.$record->course.'.'.PHP_EOL;
                        } 
                    }
                    continue;
                }
                
                // is this a mode of delivery filter?
                
                // Let's hope that no occurrences or blocks have 'OD' in them.
                // Maybe check this one last and use a 'break'?
                
                $match = preg_match('/(F(ull){0,1}[ -]{0,1}T)|(P(art){0,1}[ -]{0,1}T)|(O(nline){0,1}[ -]{0,1}D(istance){0,1}[ -]{0,1}L{0,1})/i', $value);
                if($match) {
                    $modes = Array();
                    
                    $searches = Array(
                        'ft' => '/(F(?:ull){0,1}[ -]{0,1}T)/i',
                        'pt' => '/(P(?:art){0,1}[ -]{0,1}T)/i',
                        'od' => '/(O(?:nline){0,1}[ -]{0,1}D(?:istance){0,1}[ -]{0,1}L{0,1})/i'
                    );
                    
                    foreach($searches as $code=>$pattern) {
                        $matches = Array();
                        preg_match($pattern, $value, $matches);
                        if(count($matches) > 1) {
                            //out('Matched a mode of attendance: '.$code);
                            $modes[] = $code;
                        }
                    }
                    
                    if(count($modes) !== 0) {
                        $record->modes = strtoupper(implode(':', $modes));
                        if($debug) {
                            echo 'Added a filter for mode of delivery: '.$record->modes.'.'.PHP_EOL;
                        } 
                    }
                    continue;  
                }
            }
            $DB->insert_record('enrol_sits_code', $record);
        }
        
        if(strpos(strtolower($course->shortname), 'course study area') !== false) {
            
            if($debug) {
                echo 'This looks like a course study area.'.PHP_EOL;
            } 
            
            $record->type = 'course';
            
            $parts = explode('-', $course->shortname);
            
            if(count($parts) < 2) {
                out('No course name found');
                if($debug) {
                    echo 'This course has no course codes.'.PHP_EOL;
                }
                return false;
            }
            
            $courseCodes = explode(',', $parts[1]);
            
            $parts = explode('-', $course->shortname);
            $courseCodes = explode(',', $parts[1]);
            
            $filters = array_slice($parts, 1);
            foreach($filters as $filter) {
                $value = trim($filter);
                
                // Is this a block filter?
                
                $match = preg_match('/Block [0-9]+/i', $value);
                if($match) {
                    $matches = Array();
                    preg_match('/Block ([0-9, ]+)/i', $value, $matches);
                    if(count($matches) > 1) {
                        //out('Block match: '.$matches[1]);
                        $blocks = Array();
                        $dirtyBlocks = explode(',', $blocks);
                        foreach($dirtyBlocks as $block) {
                            $blocks[] = trim($block);
                        }
                        $record->blocks = implode(':', $blocks);
                        if($debug) {
                            echo 'Added filter for blocks: '.$record->blocks.'.'.PHP_EOL;
                        }
                    }
                    continue;
                }
                
                // Does this look like months of the year?
                
                $match = preg_match('/ja(?:nuary){0,1}|fe(?:bruary){0,1}|ma(?:rch){0,1}|ap(?:ril){0,1}|m(?:a){0,1}y|ju(?:ne){0,1}|j(?:u){0,1}l(?:y){0,1}|au(?:gust){0,1}|se(?:ptember){0,1}|oc(?!cur)(?:tober){0,1}|no(?:vember){0,1}|de(?:cember)/i', $course->shortname);
                if($match) {
                    $months = Array();
                    
                    $searches = Array(
                        'ja' => '/ja(?:nuary){0,1/i',
                        'fe' => '/fe(?:bruary){0,1}/i',
                        'ma' => '/ma(?:rch){0,1}/i',
                        'ap' => '/ap(?:ril){0,1}/i',
                        'my' => '/m(?:a){0,1}y/i',
                        'ju' => '/ju(?:ne){0,1}/i',
                        'jl' => '/j(?:u){0,1}l(?:y){0,1}/i',
                        'au' => '/au(?:gust){0,1}/i',
                        'se' => '/se(?:ptember){0,1}/i',
                        'oc' => '/oc(?!cur)(?:tober){0,1}/i',
                        'no' => '/no(?:vember){0,1}/i',
                        'de' => '/de(?:cember){0,1}/i'
                    );
                    
                    foreach($searches as $code=>$pattern) {
                        $matches = Array();
                        preg_match($pattern, $value, $matches);
                        if(count($matches) > 1) {
                            //out('Matched an occurrence month: '.$code);
                            $months[] = $code;
                        }
                    }
                    
                    if(count($months) !== 0) {
                        $record->occurrence = strtoupper(implode(':', $months));
                    }
                    if($debug) {
                        echo 'Added filter for start month: '.$record->occurrence.'.'.PHP_EOL;
                    }
                    continue;
                }
                
                // is this a mode of delivery filter?
                
                // Let's hope that no occurrences or blocks have 'OD' in them.
                // Maybe check this one last and use a 'break'?
                
                $match = preg_match('/(F(ull){0,1}[ -]{0,1}T)|(P(art){0,1}[ -]{0,1}T)|(O(nline){0,1}[ -]{0,1}D(istance){0,1}[ -]{0,1}L{0,1})/i', $value);
                if($match) {
                    $modes = Array();
                    
                    $searches = Array(
                        'ft' => '/(F(?:ull){0,1}[ -]{0,1}T)/i',
                        'pt' => '/(P(?:art){0,1}[ -]{0,1}T)/i',
                        'od' => '/(O(?:nline){0,1}[ -]{0,1}D(?:istance){0,1}[ -]{0,1}L{0,1})/i'
                    );
                    
                    foreach($searches as $code=>$pattern) {
                        $matches = Array();
                        preg_match($pattern, $value, $matches);
                        if(count($matches) > 1) {
                            $modes[] = $code;
                        }
                    }
                    
                    if(count($modes) !== 0) {
                        $record->modes = strtoupper(implode(':', $modes));
                    }
                    if($debug) {
                        echo 'Added filter for modes of delivery: '.$record->modes.'.'.PHP_EOL;
                    }
                    continue;  
                }
            }
            
            foreach($courseCodes as $code) {
                if(preg_match('/[A-Z]{7,12}/', $code) || preg_match('/[A-Z]{3,9}\*([A-Z\*]){0-6}/', $code)) {
                    if($debug) {
                        echo 'Matched course code: '.trim($code).'.'.PHP_EOL;
                    } 
                    $record->code = trim($code);
                    $DB->insert_record('enrol_sits_code', $record);
                } else {
                    if($debug) {
                        echo 'Doesn\'t look like a real course code: '.trim($code).'.'.PHP_EOL;
                    }
                }
            }
        }
        
        if(strpos(strtolower($course->shortname), 'school study area') !== false) {
            if($debug) {
                echo 'This looks like a school study area.'.PHP_EOL;
            }
            
            $record->type = 'school';
            
            $parts = explode('-', $course->shortname);
            
            if(count($parts) < 2) {
                if($debug) {
                    echo 'Couldn\'t find a course code.'.PHP_EOL;
                }
                return false;
            } else {
                $courseCodes = explode(',', $parts[1]);
                
                foreach($courseCodes as $code) {
                    if(preg_match('/[a-zA-Z]{5,10}/', trim($code))) {
                        //out('Matched course code '.trim($code));
                        $record = new stdClass();
                        $record->type = 'course';
                        $record->code = trim($code);
                    } else {
                        if($debug) {
                            echo 'Doesn\'t looke like a real course code: '.$record->code.'.'.PHP_EOL;
                        }
                    }
                }
            }
            $DB->insert_record('enrol_sits_code', $record);
        }
    }
}
