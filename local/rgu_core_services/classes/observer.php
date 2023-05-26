<?php
    
    require_once($CFG->dirroot."/user/profile/lib.php");
    require_once($CFG->dirroot."/lib/gdlib.php");
    
    class local_rgu_core_services_observer {
        
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
        
        public static function user_created(\core\event\user_created $event) {
            
           $eventdata = $event->get_data();

            if (empty($eventdata['objectid'])) {
                return false;
            }
            
            local_rgu_core_services_observer::update_user($eventdata['objectid']);
        }
        
        public static function user_updated(\core\event\user_updated $event) {
            
           $eventdata = $event->get_data();

            if (empty($eventdata['objectid'])) {
                return false;
            }
            
            local_rgu_core_services_observer::update_user($eventdata['objectid']);
        }
        
        public static function user_loggedin(\core\event\user_loggedin $event) {
            
           $eventdata = $event->get_data();

            if (empty($eventdata['userid'])) {
                return false;
            }
            
            local_rgu_core_services_observer::update_user($eventdata['userid']);
        }
        
        public static function user_loggedinas(\core\event\user_loggedinas $event) {
            
           $eventdata = $event->get_data();

            if (empty($eventdata['relateduserid'])) {
                return false;
            }
            
            local_rgu_core_services_observer::update_user($eventdata['relateduserid']);
        }
        
        public static function course_created(\core\event\course_created $event) {
            
           $courseid = $event->courseid;

            if (empty($courseid)) {
                return false;
            }
            
            local_rgu_core_services_observer::update_course($courseid);
        }
        
        public static function course_restored(\core\event\course_restored $event) {
            
           $courseid = $event->courseid;

            if (empty($courseid)) {
                return false;
            }
            
            local_rgu_core_services_observer::update_course($courseid);
        }
        
        public static function course_updated(\core\event\course_updated $event) {
            
           $courseid = $event->courseid;

            if (empty($courseid)) {
                return false;
            }
            
            local_rgu_core_services_observer::update_course($courseid);
        }
        
        public static function isEnabled($setting) {
            $setting = get_config('local_rgu_core_services', $setting);
            
            return(isset($setting) && (!empty($setting)) && ($setting == 'enabled'));
        }
        
        public static function sendQueryToSITS($query) {
            
            $host = get_config('local_rgu_core_services', 'sits_db_host');
            $username = get_config('local_rgu_core_services', 'sits_db_username');
            $password = get_config('local_rgu_core_services', 'sits_db_password');
            $database = get_config('local_rgu_core_services', 'sits_db_database');
            
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
        
        public static function update_user($userid) {
            global $DB, $CFG;
            
            $currentYear = get_config('local_rgu_core_services', 'sits_current_year');
            
            $shouldAppendNumber = local_rgu_core_services_observer::isEnabled('number_in_lastname_enabled');
            $shouldSyncSits = local_rgu_core_services_observer::isEnabled('sits_db_enabled');
            $shouldSyncAvatars = local_rgu_core_services_observer::isEnabled('avatar_enabled');
            
            $update = false;
            
            $user = $DB->get_record('user', ['id' => $userid]);
            
            if(empty($user->idnumber)) {
                return false;
            }
            
            if(!preg_match('/^[0-9]{7}$/', $user->idnumber)) {
                //mtrace('Doesn\'t look like a real student number: '.$user->idnumber);
                return false;
            }
            
            $profileFields = profile_user_record($userid);
            $profileFields->id = $userid;
            
            switch($user->institution) {
                case 'Student':
                    
                    // If they don't have the student ID in the lastname field,
                    // add it.
                    if($shouldAppendNumber) {
                        if(strpos($user->lastname, '(') === false) {
                            if(!empty($user->idnumber)) {
                                $user->lastname = $user->lastname.' ('.$user->idnumber.')';
                                $update = true;
                                //mtrace('Student number added to last name.');
                            } else {
                                //mtrace('This student doesn\'t have a student number.');
                            }
                        }
                    } else {
                        //mtrace('Appending student numbers is switched off.');
                    }
                    
                    // Custom profile fields
                    
                    if($shouldSyncSits) {

                        $sql = 'select SCE_SCJC,SCE_STUC,CRS_NAME,SCE_CRSC,SCE_DPTC,SCE_STAC,SCE_BLOK '.
                        'from INTUIT.SRS_SCE '.
 		                'inner join INTUIT.SRS_CRS '.
 		                'ON INTUIT.SRS_SCE.SCE_CRSC = INTUIT.SRS_CRS.CRS_CODE '.
		                'where SCE_AYRC = '.$currentYear.
		                ' and SCE_STAC <> "T" '.
		                'and SCE_STUC = '.$user->idnumber.
		                ' order by SCE_SCJC';
		                
		                //mtrace($sql);
		                
		                $result = local_rgu_core_services_observer::sendQueryToSITS($sql);
		                
		                // We want to deal with the last record
		                
		                if(count($result) !== 0) {
		                
    		                $records = array_slice($result, -1);
    		                $record = $records[0];
		                
    		                $profileFields->profile_field_rgu_coursecode = $record->SCE_CRSC;
    		                $profileFields->profile_field_rgu_coursename = $record->CRS_NAME;
    		                
    		                if(isset(local_rgu_core_services_observer::$schools[$record->SCE_DPTC])) {
    		                    $profileFields->profile_field_rgu_school = local_rgu_core_services_observer::$schools[$record->SCE_DPTC];
    		                } else {
    		                    $profileFields->profile_field_rgu_school = 'Unknown Department: ('.$record->SCE_DPTC.')';
    		                }
    		                
    		                $courseType = substr($record->SCE_CRSC, 0, 1);
    		                
    		                switch($courseType) {
    		                    case 'P':
    		                    case 'R':
    		                        $profileFields->profile_field_rgu_stage = 'Postgraduate';
    		                        break;
    		                    default:
    		                        $profileFields->profile_field_rgu_stage = 'Undergraduate Level '.$courseType;
    		                }
                        } else {
                            //mtrace('No student record from SITS.');
                        }
		                
		                $sql = 'select distinct SCJ_STUC, SCJ_PRSC '.
					    'from INTUIT.SRS_SCJ '.
					    'inner join INTUIT.SRS_SCE '.
					    'on SRS_SCE.SCE_SCJC = SRS_SCJ.SCJ_CODE '.
					    'WHERE SCE_AYRC = '.$currentYear.
					    ' AND SCJ_STUC="'.$user->idnumber.'" '.
					    'AND SCJ_PRSC IS NOT NULL';
					    
					    $result = local_rgu_core_services_observer::sendQueryToSITS($sql);
					    
					    if(count($result) > 0) {
					    
					        $tutors = Array();
					        
					        foreach($result as $tutor) {
					            if(!isset($tutors[$tutor->SCJ_PRSC])) {
					                $tutors[$tutor->SCJ_PRSC] = $DB->get_record(
					                    'user',
					                    array('idnumber'=>$tutor->SCJ_PRSC),
					                    'id,username,firstname,lastname,email');
					            }
					        }
					        
					        $tutorText = '<ul>';
					        
					        foreach($tutors as $tutor) {
					            $tutorText .= '<li>'.html_writer::tag('a', $tutor->firstname.' '.$tutor->lastname, array('href'=>new moodle_url('/user/profile.php', array('id'=>$tutor->id)))).'</li>';
					        }
					        
					        $tutorText .= '</ul>';
					        
					        $profileFields->profile_field_rgu_tutors = $tutorText;
					    } else {
                            //mtrace('No tutor record from SITS.');
                        }
		                
		                $profileFields->profile_field_rgu_lastupdate = time();
		                
		                profile_save_data($profileFields);
                    } else {
                        //mtrace('Syncing with SITS is switched off.');
                    }
                    
                    // Update the user's profile picture
                    
                    if($shouldSyncAvatars) {
                        if(isset($profileFields->profile_field_rgu_nophoto) && $profileFields->profile_field_rgu_nophoto == 0) {
                            if($user->picture == 0) {
                                $ftpserver = get_config('local_rgu_core_services', 'avatar_host');
                                $ftpport = get_config('local_rgu_core_services', 'avatar_port');
                                $ftpuser = get_config('local_rgu_core_services', 'avatar_username');
                                $ftppass = get_config('local_rgu_core_services', 'avatar_password');
                                $ftpfolder = get_config('local_rgu_core_services', 'avatar_folder');                    
                                
                                try{
                                    $ftp = ftp_connect($ftpserver, $ftpport, 15);
                                    ftp_login($ftp, $ftpuser, $ftppass);
                                    ftp_pasv($ftp, true);
                                    //ftp_chdir($ftp, $ftpfolder);
                                    ftp_get($ftp, $CFG->tempdir.'/profile_pic_'.$user->idnumber.'.jpg', $ftpfolder.'/'.$user->idnumber.'.jpg', FTP_BINARY);
                                    ftp_close($ftp);
                                    $iconid = process_new_icon(context_user::instance($userid), 'user', 'icon', 0, $CFG->tempdir.'/profile_pic_'.$user->idnumber.'.jpg');
                                    $DB->set_field('user', 'picture', $iconid, array('id'=>$userid));
                                    unlink($CFG->tempdir.'/profile_pic_'.$user->idnumber.'.jpg');
                                } catch (Exception $e) {
                                    error_log('Problem updating profile photo for user ID '.$userid);
                                }
                            }
                        } else {
                            $userContext = context_user::instance($userid);
                             $files = $DB->get_records_sql(
                                'SELECT * FROM {files} WHERE component="user" AND filearea="icon" AND contextid=:contextid',
                                ['contextid' => $userContext->id],
                                0,
                            );

                            $fs = get_file_storage();

                            foreach ($files as $thisfile) {

                                $file = $fs->get_file(
                                    $thisfile->contextid, $thisfile->component, $thisfile->filearea,
                                    $thisfile->itemid, $thisfile->filepath, $thisfile->filename
                                );

                                if ($file) {
                                    $file->delete();
                                }
                            }
                            $DB->set_field('user', 'picture', '0', array('id'=>$userid));
                        }
                    } else {
                        //mtrace('Syncing photos is switched off.');
                    }
                    break;
                default:
            }
            if($update) {
                $DB->update_record('user', $user);
            }
            
        }
        
        public static function update_course($courseid) {
            global $DB, $CFG;
            
            $update = false;
            
            mtrace('Updating course '.$courseid);
            
            $course = $DB->get_record('course', Array('id'=>$courseid));
            
            if(empty($course->idnumber) && ($course->startdate < time() && $course->enddate > time())) {
                $month = date('n');
                $thisYear = date('Y');
                $nextYear = date('y');
                // Allow some leeway for course creation before the real rollover
                if($month < 7) {
                    $thisYear--;
                } else {
                    $nextYear++;
                }
                $yearCode = $thisYear.'-'.$nextYear;
                
                $matches = Array();
                preg_match('/\]\s{1,3}[A-Z]{2}[0-9|M][0-9]{3}[A-Za-z]{0,1}/', $course->shortname, $matches);
                if(!empty($matches)) {
                    mtrace('Match in '.$course->shortname.': '.substr($matches[0], -6));
                    $course->idnumber = substr($matches[0], -6).'_'.$yearCode;
                    $update = true;
                } else {
                    mtrace('No match in '.$course->shortname);
                }
            }
            
            if(empty($course->sortorder)) {
                $course->sortorder = 60;
                
                $matches = Array();
                
                $matches = preg_match('/School Study Area/', $course->shortname, $matches);
                if(!empty($result)) {
                    $course->sortorder = 50;
                }
                
                $matches = preg_match('/Course Study Area/', $course->shortname, $matches);
                if(!empty($result)) {
                    $course->sortorder = 40;
                }
                
                preg_match('/Module Study Area 20[0|1|2|3][0-9]\//', $course->shortname, $moduleCheck);
                
                if(!empty($moduleCheck)) {
                    preg_match('/20[0|1|2|3][0-9]/', $moduleCheck[0], $matches);
                    $course->sortorder = 2050 - (int)$matches[0];
                }
                
                $update = true;
            }
            
            if($update) {
                $DB->update_record('course', $course);
            }
        }
        
    }
