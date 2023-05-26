<?php  
defined('MOODLE_INTERNAL') || die;

function unreleasedlast($courses){
	$return = array();
	foreach($courses as $key=>$course){
		if($course->visible){
			$return[] = $course;
			unset($courses[$key]);
		}
	}
	foreach($courses as $course){
		$return[] = $course;
	}
	return $return;
}

function rgu_course_compare_module($a,$b){
	$bitsa = explode(']',$a->fullname,2);
	if(isset($bitsa[1])){
		$sorta  = trim($bitsa[1]);
	}else{
		$sorta = $a->fullname;
	}
	$bitsb = explode(']',$b->fullname,2);
	if(isset($bitsb[1])){
		$sortb  = trim($bitsb[1]);
	}else{
		$sortb = $b->fullname;
	}
//	echo($sorta);echo('<br>');echo($sortb);echo('<br><br><br><br>');
	if($sorta<$sortb){
		return -1;
	}
	if($sorta>$sortb){
		return 1;
	}
	
	return 0; 	
}

function rgu_course_compare_general($a,$b){ 	
	$afullname = strtoupper($a->fullname);
	$bfullname = strtoupper($b->fullname);
	
	if(substr($afullname,0,1)==='['&&substr($bfullname,0,1)!=='['){
		return -1;
	}
	if(substr($afullname,0,1)!='['&&substr($bfullname,0,1)==='['){
		return 1;
	}
	
	if($afullname<$bfullname){
		return -1;
	}
	if($afullname>$bfullname){
		return 1;
	}
	return 0;
}


function rgu_course_compare_course($a,$b){
	$bitsa = explode('-',$a->fullname,2);
	if(isset($bitsa[1])){
		$sorta  = trim($bitsa[1]);
	}else{
		$sorta = $a->fullname;
	}
	$bitsb = explode('-',$b->fullname,2);
	if(isset($bitsb[1])){
		$sortb  = trim($bitsb[1]);
	}else{
		$sortb = $b->fullname;
	}
	//	echo($sorta);echo('<br>');echo($sortb);echo('<br><br><br><br>');
	if($sorta<$sortb){
		return -1;
	}
	if($sorta>$sortb){
		return 1;
	}

	return 0;
}




function rgu_sort_courses($courses){
	
	global $USER ; 
	$config = get_config('block_rgu_course_overview');
	
	
	if(!empty($config->defaultpaginationvalue)&&is_numeric($config->defaultpaginationvalue)){
		$itemsperpage = $config->defaultpaginationvalue;
	} else {
		$itemsperpage = 5;
	}
	
	if(!empty($config->currentyearofstudy)){
		$currentyearofstudy = $config->currentyearofstudy;
	}
	
	
	
	
	
	$containshiddencourses = false;
	
	$rgu_sorted_courses = array("modulestudyareas"=>array('undated'=>array(),'current'=>array(),'future'=>array()),"coursestudyareas"=>array(),"schoolstudyareas"=>array(),"teamworkareas"=>array(),"generalstudyareas"=>array(),"others"=>array(),'unsorted'=>$courses);	

	
	foreach($courses as $course){
		
		// does this student uaer have a pre-enrolment area?  If more than one, just pick the first found 
		if(!isset($USER->preenrol)&&isset($USER->profile['role'])&&trim(strtolower($USER->profile['role']))!=='staff'){
			if(((stripos($course->shortname,'course')!==false)||(stripos($course->shortname,'school')!==false))&&(stripos($course->shortname,'pre')!==false)){
				$USER->preenrol	= $course->id; 
			}
		}
		
		
		// exclude from my moodle
		
		if(get_rgu_settings('excludefrommymoodle',$course->id,$courses)=='yes'){
			continue;
		}
		
		if($course->visible==0){
			$containshiddencourses = true;
		}
		
		
		
		$shortnameelements = explode(']',$course->shortname);
		if(count($shortnameelements)==2){
			if(stripos($shortnameelements[0],'module')!==false){
				preg_match('/20[0-9][0-9]/', $shortnameelements[0], $matches);
				if(!empty($matches[0])){
					if($matches[0]==$currentyearofstudy||($matches[0]==$currentyearofstudy-1&&get_rgu_settings('span',$course->id,$courses)=='yes')){
						$rgu_sorted_courses['modulestudyareas']['current'][] = $course; 
						continue;
					}
					if(intval($matches[0])>intval($currentyearofstudy)){
						if($course->visible==1){
							$rgu_sorted_courses['modulestudyareas']['current'][] = $course;
							continue;
						}
						
						
						$rgu_sorted_courses['modulestudyareas']['future'][] =  $course;
						continue;
					}
					
					if(empty($rgu_sorted_courses['modulestudyareas'][$matches[0]])){
						$rgu_sorted_courses['modulestudyareas'][$matches[0]] = array();
					}
					
					
					
					$rgu_sorted_courses['modulestudyareas'][$matches[0]][] =  $course;
					continue;
					
					
				} 
				$rgu_sorted_courses['modulestudyareas']['current'][] = $course;
				$rgu_sorted_courses['modulestudyareas']['undated'][] = $course;
				continue; 
			}
			
			
			
			
			if(stripos($shortnameelements[0],'course')!==false){
				$rgu_sorted_courses['coursestudyareas'][] = $course;	
				continue;
			}
			if(stripos($shortnameelements[0],'school')!==false){
				$rgu_sorted_courses['schoolstudyareas'][] = $course;
				continue;
			}
			
			if(stripos($shortnameelements[0],'general')!==false){
				$rgu_sorted_courses['generalstudyareas'][] = $course;
				continue;
			}
			if(stripos($shortnameelements[0],'teamwork')!==false){
				$rgu_sorted_courses['teamworkareas'][] = $course;
				continue;
			}
			
		}
		
		
		
		$rgu_sorted_courses['others'][] = $course;
	}
	
	$rgu_sorted_courses['combined'] = array();
	
	
	
	
	

	
	foreach($rgu_sorted_courses as $key=>$coursegroup){
		if($key==='modulestudyareas'){
			foreach($coursegroup as $modulekey=>$group){
				usort($group, "rgu_course_compare_module");
				$group = unreleasedlast($group);		
				$rgu_sorted_courses[$key.'_raw'][$modulekey] = $group;
				$rgu_sorted_courses[$key][$modulekey] = rgu_course_overview_paginate($group,$containshiddencourses,$itemsperpage);
				$rgu_sorted_courses[$key][$modulekey]['count'] = count($group);
			}
		}else{
			if($key==='teamworkareas'){ 
				usort($coursegroup, "rgu_course_compare_module"); // sort after ] if set in full name
			}elseif($key==='generalstudyareas'){
				usort($coursegroup, "rgu_course_compare_general");
			}else{	
				usort($coursegroup, "rgu_course_compare_course"); // sort after first - if set in fullname
			}
			$coursegroup = unreleasedlast($coursegroup);
			$rgu_sorted_courses[$key.'_raw'] = $coursegroup;
			$rgu_sorted_courses[$key] = rgu_course_overview_paginate($coursegroup,$containshiddencourses,$itemsperpage);
			$rgu_sorted_courses[$key]['count'] = count($coursegroup);
	
		}
		
	}

	$combined = array();
	//foreach(array('coursestudyareas','schoolstudyareas','generalstudyareas') as $key) {
		//foreach($rgu_sorted_courses[$key] as $course){
			//$course->type = $key;
			//$combined[] = $course;
		//}
		//unset($rgu_sorted_courses[$key]);
	//}
	$rgu_sorted_courses['combined'] = rgu_course_overview_paginate($combined,$containshiddencourses,$itemsperpage);
	$rgu_sorted_courses['combined']['count'] = count($combined);
	
	$all = array();
	
	$allkeys = array();
	$allkeys['currentmodules'] = array('modulestudyareas_raw'=>'current');	
	$allkeys['futuremodules'] = array('modulestudyareas_raw'=>'future');
	for($i=2030;$i>2006;$i--){
		if(isset($rgu_sorted_courses['modulestudyareas'][$i])){
			$allkeys['modules'.$i] = array('modulestudyareas_raw'=>$i);
		}
	}
	//$allkeys['undated'] = array('modulestudyareas_raw'=>'undated');
	$allkeys['coursestudyareas'] = 'coursestudyareas_raw';
	$allkeys['schoolstudyareas'] = 'schoolstudyareas_raw';
	$allkeys['generalstudyareas'] = 'generalstudyareas_raw';
	$allkeys['others'] = 'others_raw';
	
	foreach($allkeys as  $type=>$rgucoursekey) {
		if(is_array($rgucoursekey)&&!empty($rgu_sorted_courses[key($rgucoursekey)][$rgucoursekey[key($rgucoursekey)]])){
			$courses = $rgu_sorted_courses[key($rgucoursekey)][$rgucoursekey[key($rgucoursekey)]];
		}elseif(!@empty($rgu_sorted_courses[$rgucoursekey])){
			$courses = $rgu_sorted_courses[$rgucoursekey];
			
		}
		if(!empty($courses)){
			foreach($courses as $course){
				if(is_object($course)){
					$course->type = $type;
					$all[] = $course;
				}
			}
		}
		unset($courses);
	}
	
	$rgu_sorted_courses['all'] = rgu_course_overview_paginate($all,$containshiddencourses,$itemsperpage);
	$rgu_sorted_courses['all']['count'] = count($all);
//	print_r($rgu_sorted_courses);die();
	return $rgu_sorted_courses;
}


function rgu_get_courses($coursetype,$page=1,$year=null){
	global $rgu_sorted_courses;
	if(empty($rgu_sorted_courses)){
		return false;
	}

	
	if(empty($coursetype)||!is_string($coursetype)){
		return false;
	}
	if(!in_array($coursetype,array('modulestudyareas','coursestudyareas','schoolstudyareas','teamworkareas','undated','unsorted'))){
		return false;
	}
	if($coursetype=='modulestudyareas'){
		if(!is_numeric($year)||$year<2007&&$year>2025){
			if($year!=='current'&&$year!=='future'){
				$year = "undated";
			}
		}
	}

	if($coursetype=='modulestudyareas'){
		if(isset($rgu_sorted_courses['modulestudyareas'][$year][$page])){
			return $rgu_sorted_courses['modulestudyareas'][$year][$page];
		}
		return false;
	}
	if(isset($rgu_sorted_courses[$coursetype])){
		return $rgu_sorted_courses[$coursetype][$page];
	}
	return false;
	
}

function rgu_course_overview_paginate($courses,$containshiddencourses = false,$itemsperpage=5){
	


	
	$return = array();
		
	$page = 1;
	$itemcount = 1;
	$lastvisible = -1;
	$lastheading = '';
	$newpage = false;
	$headings = array('currentmodules'=>'Current Module Study Areas','futuremodules'=>'Future Module Study Areas','undated'=>'Undated Module Study Areas','coursestudyareas'=>'Course Study Areas','schoolstudyareas'=>'School Study Areas','generalstudyareas'=>'General Study Areas','others'=>'Others');
	
	for($year=2007;$year<2030;$year++){
		$nextyear = $year + 1;
		$headings['modules'.$year] = 'Module Study Area '.$year.'/'.$nextyear; 
	}
	
	
	$releaseheadings = array("Study Areas Not Released to Students","Released Study Areas");
	foreach($courses as $course){
		$c =  new stdClass();
		$c->id = $course->id;
		$c->shortname = $course->shortname;
		$c->fullname = $course->fullname;
		$c->visible = $course->visible; 
		if(isset($course->type)){
			$heading = 'Undefined';
			
			if(isset($headings[$course->type])){
				$heading = $headings[$course->type];
			
				if($lastheading!==$headings[$course->type]){
					$c->heading = $heading;
				}elseif($newpage){
					$c->heading = $heading.' (continued)';
				}
			}
			$lastheading = $heading;  
		}
		
		
		if($containshiddencourses){
			if($c->visible!==$lastvisible){
				$c->subheading = $releaseheadings[$c->visible];
			}elseif($newpage){
				$c->subheading = $releaseheadings[$c->visible].' (continued)';
			} 

		}
		
		$newpage = false;
		
		
		$lastvisible = $c->visible;
		$return[$page][$itemcount] = $c;
		$itemcount++;
		if($itemcount>$itemsperpage){
			$page++;
			$itemcount = 1;
			$newpage = true;
		}
		unset($c);
	}
	
	return $return;
}

function rgu_pages($type,$year=null){
	global $rgu_sorted_courses;
	if(empty($rgu_sorted_courses)){
		return false;
	}
	
	if(!is_string($type)){
		return false;
	}
	if(!is_string($year)&&!is_numeric($year)){
		return false;
	}
	
	if(!empty($year)){
		if(isset($rgu_sorted_courses[$type][$year])){ 
			return count($rgu_sorted_courses[$type][$year]);
		}
	}else{
		if(isset($rgu_sorted_courses[$type])){ 
			return count($rgu_sorted_courses[$type]);
		}	
	}
}	


function rgu_overview_printheading($heading,$level=1){
	static $lastheading = null; 
	static $lastsubheading = null;
	if($level===1){
		if($lastheading===$heading){
			return false;
		}
		$lastsubheading = null;
		$lastheading = $heading;
	}
	if($level===2){
		if($lastsubheading===$heading){
			return false;
		}
		$lastsubheading = $heading;
	}
	echo("<h{$level}>$heading</h{$level}>");
}


function  rgu_render_course_list($list){
	global $CFG;
	$return = '';
	foreach($list as $listitem){
		$attributes = array();
		if(empty($listitem->visible)){
			$attributes['class'] = 'dimmed';
		}
		$linkurl = new moodle_url($CFG->wwwroot . '/course/view.php',array('id' => $listitem->id));
		$rowlink = html_writer::link($linkurl, $listitem->fullname,$attributes);
		
		$return .= '<p>'.$rowlink.'</p>';
	}
	
	return $return;
}




	
function rgu_render_courses($courses,$type,$year){

	//echo $type; echo('<br>');
	//echo $year; echo('<br>');
	
	if(!is_string($type)){
		return false;
	}
	if(!is_string($year)&&!is_numeric(!is_string($year))){
		return false;
	}
	
	
	if($type=='modulestudyareas'){
		
		if(isset($courses[$type][$year])){ 
			$c = $courses[$type][$year];
			
		}else{
			$html = 'No module study areas found';
			return false;
		}	
	}else{
		if(isset($courses[$type])){
			$c = $courses[$type];
		}else{
			$html = 'No module study areas found';
		}	return false;
		
	}	
	
	echo(count($c));
		
	$html = '';
	$page = 1;
	$pagetotal = count($c);
	$pageclass = 'frontpage';
	
	$html .= html_writer::start_div('', array('id' => $type,'class'=>$pageclass));
	
	foreach($c as $key=>$coursepage){
		$html .= html_writer::start_div('', array('id' => $type.$page,'class'=>$pageclass));
		$pageclass = 'mybackpages'; // nod to bob
		$html .= rgu_render_course_list($coursepage);

		$html .= rgu_page_menu($page,$pagetotal);
		
		$html .= html_writer::end_div();
		$page++;
	}
	$html .= html_writer::end_div();
	//$html = '*********************************';

	
	
	// temp code !!!
	echo('<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>The HTML5 Herald</title>
  <meta name="description" content="The HTML5 Herald">
  <meta name="author" content="SitePoint">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
			
<style>

	.rgu_page_link	{	margin-left:5p;
						margin-right:5px;

	}	
			
	.mybackpages { display:none; }
</style>
			
<script>
$(function(){
	$( "a" ).click(function( event ) {
		event.preventDefault();
		  var showpanel = \'#\'+$(this).parent().parent().parent().parent().attr(\'id\')+$(event.target).text();
		  $("span").parent().parent().css({"display": "none"});
		  $(showpanel).css({"display": "block"});
	});
});
						
</script>
</head>
<body>');

echo $html;
	
	
echo('</body>
</html>');


	die();
	
	
	
	return $html;
}


function rgu_page_menu($page,$pagetotal){
	if(!is_numeric($page)||!is_numeric($pagetotal)){
		return false;
	}
	$return = '';
	$return .= html_writer::start_div('', array('id' => 'rgu_page_menu_'.$page));
	for($p=1;$p<=$pagetotal;$p++){
		 if($p==$page){
		 	$link = $page;
		 }else{
		 	$link = html_writer::tag('a',$p, array('href'=>'#','class'=>'rgu_page_link_a'));
		 }
		 $return .= html_writer::tag('span', $link, array('class'=>'rgu_page_link'));
	}
	
	
	$return .= html_writer::end_div();
	
	return $return;
	
}


