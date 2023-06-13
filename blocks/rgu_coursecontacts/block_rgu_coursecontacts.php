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
                $this->content->text .= '<div class="mb-2">'.$OUTPUT->user_picture($userDetails, Array('includefullname' => true)).'</div>';
            }
            
            $context = context_course::instance($COURSE->id);
            if(has_capability('moodle/course:manageactivities', $context)) {
                if($iAmShown) {
                    $hideLink = new moodle_url('/blocks/rgu_coursecontacts/hide.php', Array('id'=>$COURSE->id));
                    $this->content->text .= '<a class="btn btn-outline-danger btn-block mt-4" href="'.$hideLink->out().'"><i class="fa fa-minus-circle mr-1"></i> Remove My Details</a>';
                } else {
                    $showLink = new moodle_url('/blocks/rgu_coursecontacts/show.php', Array('id'=>$COURSE->id));
                    $this->content->text .= '<a class="btn btn-outline-success btn-block mt-4" href="'.$showLink->out().'"><i class="fa fa-plus-circle mr-1"></i> Show My Details</a>';
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