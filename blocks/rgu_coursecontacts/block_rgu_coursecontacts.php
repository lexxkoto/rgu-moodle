<?php
    
class block_rgu_coursecontacts extends block_base {
    
    public function init() {
        $this->title = get_string('pluginname', 'block_rgu_coursecontacts');
    }
    
    public function get_content() {
        global $OUTPUT, $USER, $COURSE, $DB;

        if ($this->content !== null) {
            return $this->content;
        }
        
        $roles = Array(
            '10' => 'Module Coordinator',
            '20' => 'Tutor',
            '30' => 'Course Admin',
            '40' => 'eLearning Support'
        );

        $this->content = new stdClass();
        $this->content->footer = '';
        
        $this->content->text = '';
        
        if(isset($USER) && isset($COURSE) && isset($COURSE->id) && $COURSE->id > 1) {
            $users = $DB->get_records('block_rgucc_users', array('courseid'=>$COURSE->id));
            
            $iAmShown = false;
            
            foreach($users as $user) {
                if($user->userid == $USER->id) {
                    $iAmShown = true;
                }
                $userDetails = $DB->get_record('user', Array('id'=>$user->userid));
                $profileLink = new moodle_url('/user/view.php', array('id'=>$user->userid,'course'=>$COURSE->id));
                $this->content->text .= '<div class="media course-contact-user mb-3"><span class="media-left mr-2">'.$OUTPUT->user_picture($userDetails, Array('includefullname' => false, 'size' => '48', 'alttext'=>'Picture of '.$userDetails->firstname.' '.$userDetails->lastname)).'</span><span class="media-body"><a href="'.$profileLink->out().'"><span class="name">'.$userDetails->firstname.' '.$userDetails->lastname.'</span><span class="role">'.$roles[$user->role].'</span></a></span></div>';
            }
            
            $context = context_course::instance($COURSE->id);
            if(has_capability('moodle/course:manageactivities', $context)) {
                if($iAmShown) {
                    $hideLink = new moodle_url('/blocks/rgu_coursecontacts/hide.php', Array('id'=>$COURSE->id));
                    $this->content->text .= '<a class="btn btn-outline-danger btn-block mt-4" href="'.$hideLink->out().'"><i class="fa fa-minus-circle mr-1"></i> Remove My Details</a>';
                } else {
                    $showLink = new moodle_url('/blocks/rgu_coursecontacts/show.php', Array('id'=>$COURSE->id));
                    $this->content->text .= '<div class="dropdown"><button class="btn btn-outline-success btn-block mt-4 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"><i class="fa fa-plus-circle mr-1"></i> Show My Details</button><div class="dropdown-menu">';
                    
                    foreach($roles as $id=>$name) {
                        $showLink = new moodle_url('/blocks/rgu_coursecontacts/show.php', Array('id'=>$COURSE->id,'role'=>$id));
                        $this->content->text .= '<a class="dropdown-item" href="'.$showLink->out().'">Show me as '.$name.'</a>';
                    }
                    
                    $this->content->text .= '</div></div>';
                }
            }
        }
        return $this->content;
    }
    
    public function applicable_formats() {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => true,
            'mod' => false,
            'my' => false,
        ];
    }
    
}