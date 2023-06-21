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
 * Filter main class for the filter_pluginname plugin.
 *
 * @package   filter_rgu
 * @copyright 2023 Alex Walker a.walker43@rgu.ac.uk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
class filter_rgu extends moodle_text_filter {
	 
	function filter($text, array $options = []) {
		
		if (!is_string($text) or empty($text)) {
	        return $text;
	    }
		
		$pattern = '/\{RGU::([^}]*)\}/';
		$newtext =  preg_replace_callback($pattern, fn($m)=>filter_rgu::rewrite($m), $text);
		 
		if(is_null($newtext)){
			return $text;
		}	
		return $newtext;
	}
	
	function check_audience($audience) {
    	global $USER;
    	
    	//var_dump($USER);
    	
    	switch($audience) {
        	case 'all':
        	    return true;
        	    break;
            case 'staff':
                return $USER->institution == 'Staff';
                break;
            case 'student':
                return $USER->institution == 'Student';
                break;
            case 'student-grays':
                return ($USER->institution == 'Student' && $USER->profile['rgu_school'] == 'Gray\'s School of Art');
                break;
            case 'student-notgrays':
                return ($USER->institution == 'Student' && $USER->profile['rgu_school'] != 'Gray\'s School of Art');
                break;
    	}
	}
	
	function rewrite($array) {
		global $DB, $USER;
		
		$filterText = $array[1];
		$parts = explode(',', $filterText);
		
		$filterType = $parts[0];
		
		$filterOptions = Array();
		if(count($parts) > 1) {
    		foreach(array_slice($parts, 1) as $thisOption) {
        		$option = explode('=', $thisOption);
        		$filterOptions[$option[0]] = $option[1];
    		}
        }
		
		$text = '';
		
		switch($filterType) {
			case 'content_item':
				$filterItem = str_replace('key=', '', $parts[1]);
				$placeholder = $DB->get_record('filter_rgu_content', array('contentkey'=>$filterItem));
				
				if(!filter_rgu::check_audience($placeholder->audience)) {
    				return '';
				}
				
				if(!empty($placeholder)) {
					return $placeholder->text;
				}
				
				return '';
				break;
            case 'welcome_area':
                $userCourses = enrol_get_all_users_courses($USER->id, false, array(), 'fullname');
                $welcomeArea = false;
                
                // I know. But doing it this way doesn't hit the database
                
                foreach($userCourses as $course) {
                    if(preg_match('/\] PRE/', $course->shortname)) {
                        $welcomeArea = $course->id;
                    }
                }
                
                if($welcomeArea) {
                    $welcomeLink = new moodle_url('/course/view.php', array('id'=>$welcomeArea));
                    $text .= '<a class="btn';
                    if(!empty($filterOptions['class'])) {
                        $text .= ' '.$filterOptions['class'];
                    } else {
                        $text .= ' btn-primary';
                    }
                    $text .= '" href="'.$welcomeLink->out().'"><i class="fa fa-arrow-circle-right"></i>&ensp;Course Induction for New Students</a>';
                    return $text;
                }
                $text .= '<a class="btn btn-primary';
                if(!empty($filterOptions['class'])) {
                    $text .= ' '.$filterOptions['class'];
                }
                $text .= '" href="#"><i class="fa fa-ban"></i>&ensp;Course Induction for New Students</a>';
                return $text;
                
                break;
			default:
				return 'Unknown Filter Type: '.$filterType;
				break;
		}
		
		return $text;
	}
	 
 }