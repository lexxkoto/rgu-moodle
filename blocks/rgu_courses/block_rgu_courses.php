<?php

class block_rgu_courses extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_rgu_courses');
    }
    
    public function outputCourseList($courses, $type, $year, $title='') {
        $text = '';
        
        $isInPast = ($year < $this->timeToAcademicYear(time()));
        
        if($year != 9999) {
            if($isInPast) {
                $text .= '<h6 class="mt-3 mb-3 yearTitle"><a data-toggle="collapse" href="#coursepanel-'.$type.'-'.$year.'" role="button" aria-expanded="false" aria-controls="coursepanel-'.$type.'-'.$year.'"><i class="fa fa-caret-right"></i>'.$title.'</a></h6>';
                $text .= '<div id="coursepanel-'.$type.'-'.$year.'" class="collapse">';
            } else {
                $text .= '<h6 class="mt-3 mb-3 yearTitle"><a data-toggle="collapse" href="#coursepanel-'.$type.'-'.$year.'" role="button" aria-expanded="true" aria-controls="coursepanel-'.$type.'-'.$year.'"><i class="fa fa-caret-down"></i>'.$title.'</a></h6>';
                $text .= '<div id="coursepanel-'.$type.'-'.$year.'" class="collapse show">';
            }
        } else {
            $text .= '<div>';
        }
        $text .= '<ul class="rgu-course-list">';
        foreach($courses as $course) {
            $text .= '<li';
            if(!$course->visible) {
                $text .= ' class="dim"';
            }
            $text .= '>';
            
            $link = new moodle_url('/course/view.php', array('id'=>$course->id));
            $text .= '<div class="media"><span class="media-left mr-2"><a href="'.$link->out().'">';
            
            if(!$course->visible) {
                $text .= '<i class="fa fa-ban"></i>';
            } else {
                $text .= '<i class="fa fa-mortar-board"></i>';
            }
            
            $text .= '</a></span><span class="media-body"><a href="'.$link->out().'">'.$course->fullname;
            
            $text .= '</a></span><span class="media-right ml-2">';
            if($course->starred) {
                $starLink = new moodle_url('/blocks/rgu_courses/unstar.php', array('id'=>$course->id));
                $text .= '<a href="'.$starLink->out().'" class="star-starred" title="Course favourited. Click to unfavourite."><i class="fa fa-heart"></i></a>';
            } else {
                $starLink = new moodle_url('/blocks/rgu_courses/star.php', array('id'=>$course->id));
                $text .= '<a href="'.$starLink->out().'" class="star-unstarred" title="Course not favourited. Click to favourite."><i class="fa fa-heart-o"></i></a>';
            }
            $text .= '</div></a></li>';
        }
        $text .= '</ul></div>';
        return $text;
    }
    
    public function get_content() {
        global $OUTPUT, $USER;
        
        $this->page->requires->js_call_amd('block_rgu_courses/modulefilter', 'init');

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '';
        
        $userCourses = enrol_get_all_users_courses($USER->id, false, array('enddate'), 'fullname');
        
        $usercontext = context_user::instance($USER->id);
        $userservice = \core_favourites\service_factory::get_service_for_user_context($usercontext);
        $starredCourses = $userservice->find_favourites_by_type('core_course', 'courses', 0, 100);
        
        // Build usable array of starred courses
        $favouriteCourses = Array();
        foreach($starredCourses as $starred) {
            $favouriteCourses[$starred->itemid] = $starred->itemid;
        }
        
        $courseArray = Array(
            'module' => Array(),
            'course' => Array(),
            'school' => Array(),
            'other' => Array(),
            'starred' => Array(),
            'all' => Array()
        );
        
        // Build the course array
        
        $currentYear = $this->timeToAcademicYear(time());
        
        foreach($userCourses as $course) {
            $thisCourse = new stdClass();
            $thisCourse->id = $course->id;
            $thisCourse->fullname = $course->fullname;
            $thisCourse->visible = ($course->visible == 1);
            $thisCourse->starred = isset($favouriteCourses[$thisCourse->id]);
            $thisCourse->startdate = $course->startdate;
            $thisCourse->enddate = $course->enddate;
            $thisCourse->year = 9999;
            
            $matches = Array();
            preg_match('/[^a-zA-Z](20[0-9][0-9])\//', $course->shortname, $matches);
            
            if(count($matches) > 1) {
                $year = $matches[1];
                $thisCourse->year = intval($year);
            }
            
            // Code detection failed - look at the set dates instead
            
            if($thisCourse->year == 9999) {
                if($thisCourse->enddate != 0) {
                    $thisCourse->year = intval($this->timeToAcademicYear($thisCourse->enddate));
                }
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
            
            $courseArray['all'][9999][] = $thisCourse;
        }
        
        $this->content->text = '';
        
        $this->content->text .= '<ul class="nav nav-pills nav-fill mb-4" role="tablist">';
        $this->content->text .= '<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#courses_tab_module" role="tab" aria-controls="modules" aria-selected="true"><i class="fa fa-cubes"></i>Modules</a></li>';
        $this->content->text .= '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#courses_tab_course" role="tab" aria-controls="courses" aria-selected="false"><i class="fa fa-mortar-board"></i>Courses</a></li>';
        $this->content->text .= '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#courses_tab_school" role="tab" aria-controls="school" aria-selected="false"><i class="fa fa-institution"></i>School</a></li>';
        $this->content->text .= '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#courses_tab_other" role="tab" aria-controls="other" aria-selected="false"><i class="fa fa-puzzle-piece"></i>Other</a></li>';
        $this->content->text .= '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#courses_tab_starred" role="tab" aria-controls="starred" aria-selected="false"><i class="fa fa-heart"></i>Favourites</a></li>';
        $this->content->text .= '<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#courses_tab_all" role="tab" aria-controls="other" aria-selected="false"><i class="fa fa-search"></i>Search</a></li>';
        $this->content->text .= '</ul>';
        
        $this->content->text .= '<div class="tab-content">';
        foreach($courseArray as $type=>$data) {
            $this->content->text .= '<div class="tab-pane fade show ';
            if ($type == 'module') {
                $this->content->text .= ' show active';
            }
            $this->content->text .= '" id="courses_tab_'.$type.'">';
            
            if($type == 'all') {
                $this->content->text .= '<div class="input-group mb-3">';
                $this->content->text .= '<input type="text" class="form-control" placeholder="Search" id="rgu_course_search" />';
                $this->content->text .= '<div class="input-group-append"><button class="btn btn-primary" type="button"><i class="fa fa-search"></i><span class="sr-only">Search</span></button></div>';
                $this->content->text .= '</div>';
            }
            
            if(count($data) === 0) {
                $this->content->text .= '<ul class="rgu-course-list"><li><div class="media"><span class="media-left mr-2"><i class="fa fa-frown-o"></i></span><span class="media-body">You don\'t have any Moodle pages of this type.</span></div></li></ul>';
            } else {
                if(count($data) === 1) {
                    // Only one academic year. Don't title.
                    
                    $thisYear = array_pop($data);
                    
                    $this->content->text .= $this->outputCourseList($thisYear, $type, 9999, '');
                } else {
                    krsort($data);
                    foreach($data as $year=>$content) {
                        if($year !== 9999) {
                            $this->content->text .= $this->outputCourseList($content, $type, $year, 'Academic Year '.$year.'-'.(substr($year, 2)+1));
                        } else {
                            $this->content->text .= $this->outputCourseList($content, $type, $year, '');
                        }
                    }
                }
            }
            $this->content->text .= '</div>';
        }
        $this->content->text .= '</div>';

        return $this->content;
    }
    
    public function timeToAcademicYear($timestamp) {
        if (date('n', $timestamp) < 7) {
            return date('Y', $timestamp)-1; 
        }
        return date('Y', $timestamp);
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
