<?php

class block_rgu_courses extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_rgu_courses');
    }
    
    public function outputCourseList($courses, $title='') {
        $text = '';
        if(!empty($title)) {
            $text .= '<h3>'.$title.'</h3>';
        }
        $text .= '<ul class="rgu-course-list">';
        foreach($courses as $course) {
            $text .= '<li';
            if(!$course->visible) {
                $text .= ' class="hidden"';
            }
            $text .= '>';
            
            $link = new moodle_url('/course/view.php', array('id'=>$course->id));
            $text .= '<a href="'.$link->out().'">';
            
            if(!$course->visible) {
                $text .= '<i class="fa fa-ban"></i>';
            } else {
                if($course->starred) {
                    $text .= '<i class="fa fa-heart"></i>';
                } else {
                    $text .= '<i class="fa fa-mortar-board"></i>';
                }
            }
            
            $text .= $course->fullname;
            
            $text .= '</a></li>';
        }
        $text .= '</ul>';
        return $text;
    }
    
    public function get_content() {
        global $OUTPUT, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '';
        
        $userCourses = enrol_get_all_users_courses($USER->id, false, null, 'fullname');
        
        $usercontext = context_user::instance($USER->id);
        $userservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        $starredCourses = $userservice->find_favourites_by_type('core_course', 'courses', 0, 100);
        
        // Build usable array of starred courses
        $favouriteCourses = Array();
        foreach($starredCourses as $starred) {
            $favouriteCourses[$starred->itemid] = $starred->itemid;
        }
        
        $courseArray = Array(
            'starred' => Array(),
            'module' => Array(),
            'course' => Array(),
            'school' => Array(),
            'other' => Array(),
        );
        
        // Build the course array
        
        foreach($userCourses as $course) {
            $thisCourse = new stdClass();
            $thisCourse->id = $course->id;
            $thisCourse->fullname = $course->fullname;
            $thisCourse->visible = ($course->visible == 1);
            $thisCourse->starred = isset($favouriteCourses[$thisCourse->id]);
            $thisCourse->year = '0';
            
            $matches = Array();
            preg_match('/[^a-zA-Z](20[0-9][0-9])\//', $course->shortname, $matches);
            
            if(count($matches) > 1) {
                $year = $matches[1];
                $thisCourse->year = $year;
            }
            
            $thisCourse->type = 'other';
            
            if(
                (strpos(strtolower($course->shortname), 'module study area') !== false) ||
                (strpos(strtolower($course->shortname), 'module') !== false)
            ) {
                $thisCourse->type = 'module';
            }
            
            if(strpos(strtolower($course->shortname), 'course study area') !== false) {
                $thisCourse->type = 'course';
            }
            
            if(strpos(strtolower($course->shortname), 'school study area') !== false) {
                $thisCourse->type = 'school';
            }
            
            if($thisCourse->starred) {
                $courseArray['starred'][0][] = $thisCourse;    
            }
            $courseArray[$thisCourse->type][$thisCourse->year][] = $thisCourse;
        }
        
        $this->content->text = '';
        
        $this->content->text .= '<ul class="nav nav-pills nav-fill mb-4" role="tablist">';
        $this->content->text .= '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#courses_tab_starred" role="tab" aria-controls="starred courses" aria-selected="true"><i class="fa fa-heart"></i>Favourites</a></li>';
        $this->content->text .= '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#courses_tab_module" role="tab" aria-controls="modules" aria-selected="false"><i class="fa fa-cubes"></i>Modules</a></li>';
        $this->content->text .= '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#courses_tab_course" role="tab" aria-controls="courses" aria-selected="false"><i class="fa fa-mortar-board"></i>Courses</a></li>';
        $this->content->text .= '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#courses_tab_school" role="tab" aria-controls="school" aria-selected="false"><i class="fa fa-institution"></i>School Pages</a></li>';
        $this->content->text .= '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#courses_tab_other" role="tab" aria-controls="other" aria-selected="false"><i class="fa fa-info-circle"></i>Information</a></li>';
        $this->content->text .= '</ul>';
        
        $this->content->text .= '<div class="tab-content">';
        foreach($courseArray as $type=>$data) {
            $this->content->text .= '<div class="tab-pane fade show ';
            if ($type == 'starred') {
                $this->content->text .= ' show active';
            }
            $this->content->text .= '" id="courses_tab_'.$type.'">';
            if(count($data) === 0) {
                $this->content->text .= '<ul class="rgu-course-list"><li><i class="fa fa-frown-o"></i>You don\'t have any Moodle pages of this type.</li></ul>';
            } else {
                if(count($data) === 1) {
                    // Only one academic year. Don't title.
                    
                    $thisYear = array_pop($data);
                    
                    $this->content->text .= $this->outputCourseList($thisYear);
                } else {
                    foreach($data as $year=>$content) {
                        if($year !== 0) {
                            $this->content->text .= $this->outputCourseList($content, 'Academic Year'.$year);
                        } else {
                            $this->content->text .= $this->outputCourseList($content);
                        }
                    }
                }
            }
            $this->content->text .= '</div>';
        }
        $this->content->text .= '</div>';

        return $this->content;
    }
    
    public function applicable_formats() {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => false,
            'mod' => false,
            'my' => true,
        ];
    }
}

?>