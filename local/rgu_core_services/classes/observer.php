<?php
    
    require_once($CFG->dirroot."/user/profile/lib.php");
    require_once($CFG->dirroot."/lib/gdlib.php");
    
    class local_rgu_core_services_observer {
        
        private $schools = Array(
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
        
        public static function isEnabled($setting) {
            $setting = get_config('local_rgu_core_services', $setting);
            
            return(isset($setting) && (!empty($setting)) && ($setting == 'enabled'));
        }
        
        public static function update_user($userid) {
            global $DB, $CFG;
            
            $shouldAppendNumber = local_rgu_core_services_observer::isEnabled('number_in_lastname_enabled');
            $shouldSyncSits = local_rgu_core_services_observer::isEnabled('sits_db_enabled');
            $shouldSyncAvatars = local_rgu_core_services_observer::isEnabled('avatar_enabled');
            
            $update = false;
            
            $user = $DB->get_record('user', ['id' => $userid]);
            
            switch($user->institution) {
                case 'Student':
                    
                    // If they don't have the student ID in the lastname field,
                    // add it.
                    if($shouldAppendNumber) {
                        if(strpos($user->lastname, '(') === false) {
                            if(!empty($user->idnumber)) {
                                $user->lastname = $user->lastname.' ('.$user->idnumber.')';
                                $update = true;
                            }
                        }
                    }
                    
                    // Custom profile fields
                    
                    if($shouldSyncSits) {
                        $profileFields = profile_user_record($userid);
                    }
                    
                    // Update the user's profile picture
                    
                    if($shouldSyncAvatars) {
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
                    }
                    
                    
                    break;
                default:
            }
            if($update) {
                $DB->update_record('user', $user);
            }
            
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
        
    }