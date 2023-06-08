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
 * @package    block
 * @subpackage rgu_course_overview
 * @copyright  2015 Robert Gordon University <http://rgu.ac.uk>
 * @copyright  2015 Catalyst IT Ltd <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 */

defined('MOODLE_INTERNAL') || die;

class block_rgu_course_overview_renderer extends plugin_renderer_base {

    /**
     * Construct html for rgu_course_overview block
     *
     * @param array $courses list of courses in sorted order
     * @return string
     */
	public function get_courses() {
		global $CFG;
		
		
		
		if(!isset($this->rgucourses)){
			
			$testdata = optional_param('testdata', null, PARAM_TEXT);
			if(false && !empty($testdata)){
				$sitecontext = context_system::instance();
				if(has_capability('moodle/site:config', $sitecontext)) {
					include($CFG->dirroot.'/blocks/rgu_course_overview/dummycourses.php');
					$this->rgucourses = rgu_sort_courses($dummycourses);
					return true;
				}
			}
			
			$this->originalcourses = enrol_get_my_courses(); 
			$this->rgucourses = rgu_sort_courses($this->originalcourses);
		}
	}
	
	
    public function rgu_course_overview($courses, $previouscourses, $pagesize, $blockinstanceid) {
        global $CFG,$USER;
        $this->get_courses();
        
        $this->get_recent_study_areas();
        
       // $html = $this->rgubanner();
        /*
        if(!empty($USER->preenrol)){
        	$html  .= html_writer::start_div('preenrol',array('class'=>'preenrollink'));
        	$preenrollinkimg  = html_writer::tag('img','', array('alt'=>'Link to course welcome area', 'src'=>$CFG->wwwroot.'/blocks/rgu_course_overview/pix/induction_area_link.png'));      
        	$html  .= html_writer::tag('a',$preenrollinkimg,array('href'=>$CFG->wwwroot.'/course/view.php?id='.$USER->preenrol));
        	$html  .= html_writer::end_div();
        }
	*/

        // Start Tabs
        $html = html_writer::start_tag('ul', array('class'=>'nav nav-tabs mb-3'));

        // Current Study Areas Link
        $outertabs1link = html_writer::link('#rguco_currentstudyareas', get_string('currentstudyareas', 'block_rgu_course_overview'), array('class'=>'nav-link active', 'data-toggle'=>'tab'));
        $html .= html_writer::tag('li', $outertabs1link, array('class'=>'nav-item'));

        // Search My Study Areas Link
        $outertabs2link = html_writer::link('#rguco_previousstudyareas', get_string('previousstudyareas', 'block_rgu_course_overview'), array('class'=>'nav-link', 'data-toggle'=>'tab'));
        $html .= html_writer::tag('li', $outertabs2link, array('class'=>'nav-item'));

        // Search Link
        $outertabs3link = html_writer::link('#rguco_search', get_string('search'), array('class'=>'nav-link', 'data-toggle'=>'tab'));
        $html .= html_writer::tag('li', $outertabs3link, array('class'=>'nav-item'));
	//$html .= $this->rgu_search_form(); 
        // End Tabs
        $html .= html_writer::end_tag('ul');
        
        $html  .= html_writer::start_div('tab-content', array('id' => 'rgu-courses-outer-panel'));
        
        $html  .= html_writer::start_div('tab-pane', array('id' => 'rguco_currentstudyareas', 'class'=>'tab-pane active'));

        // Current Study Areas
        $html .= html_writer::start_tag('ul', array('class' => 'nav nav-pills mb-3'));

        // Module Study Areas Inner Tab
        $modulestudyareastab = html_writer::link('#rguco_modulestudyareas',
        		get_string('tab_modulestudyareas', 'block_rgu_course_overview'),
        		array('class' => 'nav-link active', 'data-toggle'=>'tab'));
        $html .= html_writer::tag('li', $modulestudyareastab, array('class'=>'nav-item'));
        
        
       

        
        
        if(!empty($USER->profile['role'])&&trim(strtolower($USER->profile['role']))!=='student'&&$this->rgucourses['modulestudyareas']['future']['count']>0){
        	// Module Study Areas Inner Tab
        	$futurestudyareastab = html_writer::link('#rguco_futuremodulestudyareas',
        			get_string('futuremodulestudyareas', 'block_rgu_course_overview'),
        			array('class' => 'nav-link', 'data-toggle'=>'tab'));
        	$html .= html_writer::tag('li', $futurestudyareastab, array('class'=>'nav-item'));
        }
        

        // General Study Areas Inner Tab
        $generalstudyareatab = html_writer::link('#rguco_coursestudyarea',
        		get_string('tab_coursestudyareas', 'block_rgu_course_overview'),
        		array('class' => 'nav-link', 'data-toggle'=>'tab'));
        $html .= html_writer::tag('li', $generalstudyareatab, array('class'=>'nav-item'));

        // General Study Areas Inner Tab
        $generalstudyareatab = html_writer::link('#rguco_schoolstudyarea',
        		get_string('tab_schoolstudyareas', 'block_rgu_course_overview'),
        		array('class' => 'nav-link', 'data-toggle'=>'tab'));
        $html .= html_writer::tag('li', $generalstudyareatab, array('class'=>'nav-item'));

        // General Study Areas Inner Tab
        $generalstudyareatab = html_writer::link('#rguco_generalstudyarea',
        		get_string('tab_generalstudyareas', 'block_rgu_course_overview'),
        		array('class' => 'nav-link', 'data-toggle'=>'tab'));
        $html .= html_writer::tag('li', $generalstudyareatab, array('class'=>'nav-item'));
        
        // Teamwork Areas Inner Tab
        //$teamworkareas = html_writer::link('#teamworkareas',
        //        get_string('teamworkareas', 'block_rgu_course_overview'),
        //        array('class' => 'rgumymoodle_tab'));
        //$html .= html_writer::tag('li', $teamworkareas);

        // All My Study Areas Inner Tab
        
        if(!empty($this->recent_study_areas)){
        	$recentstudyareastab = html_writer::link('#rguco_recentstudyareas',
        			get_string('tab_recentstudyareas', 'block_rgu_course_overview'),
        			array('class' => 'nav-link', 'data-toggle'=>'tab'));
        	$html .= html_writer::tag('li', $recentstudyareastab,array('class'=>'nav-item'));
        	$moduleactive = '';
        }
        
         $allmystudyareas = html_writer::link('#rguco_allmystudyareas',
                get_string('tab_allmystudyareas', 'block_rgu_course_overview'),
                array('class' => 'nav-link', 'data-toggle'=>'tab'));
        $html .= html_writer::tag('li', $allmystudyareas, array('class'=>'nav-item'));

        $html .= html_writer::end_tag('ul');
        
        $html  .= html_writer::start_div('tab-content', array('id' => 'rgu-courses-inner-panel-current'));
        
        // Module Study Areas Content
        $html .= html_writer::div($this->listcourses('modulestudyareas', $pagesize, $blockinstanceid), '', array('id' => 'rguco_modulestudyareas', 'class'=>'tab-pane active'));

        
          
        
         if(!empty($USER->profile['role'])&&trim(strtolower($USER->profile['role']))!=='student'&&$this->rgucourses['modulestudyareas']['future']['count']>0){
        	$html .= html_writer::div($this->listcourses('futuremodulestudyareas', $pagesize, $blockinstanceid), '', array('id' => 'rguco_futuremodulestudyareas', 'class'=>'tab-pane'));
        	 
        }

        
        
        
        
        
        // General Study Areas Content
        $html .= html_writer::div($this->listcourses('coursestudyarea', $pagesize, $blockinstanceid), '', array('id' => 'rguco_coursestudyarea', 'class'=>'tab-pane'));
        
        // General Study Areas Content
        $html .= html_writer::div($this->listcourses('schoolstudyarea', $pagesize, $blockinstanceid), '', array('id' => 'rguco_schoolstudyarea', 'class'=>'tab-pane'));
        
        // General Study Areas Content
        $html .= html_writer::div($this->listcourses('generalstudyarea', $pagesize, $blockinstanceid), '', array('id' => 'rguco_generalstudyarea', 'class'=>'tab-pane'));
        
        // Team Work Areas Content
       // $html .= html_writer::div($this->listcourses('teamworkareas', $pagesize, $blockinstanceid), '', array('id' => 'teamworkareas'));
// Recent Study Areas Content
        
        
        if(!empty($this->recent_study_areas)){
        	if(!empty($CFG->block_rgu_course_overview_recent_limit)&&is_numeric($CFG->block_rgu_course_overview_recent_limit)){
        		$recentpagesize = $CFG->block_rgu_course_overview_recent_limit;
        	}else{
        		$recentpagesize = $pagesize;
        	}
        	 
        	 
        	$html .= html_writer::div($this->listcourses('recentstudyareas', $recentpagesize, $blockinstanceid), '', array('id' => 'rguco_recentstudyareas','class'=>'tab-pane'));
        }
      
        // All My Study Areas Content
        $html .= html_writer::div($this->listcourses('allmystudyareas', $pagesize, $blockinstanceid), '', array('id' => 'rguco_allmystudyareas', 'class'=>'tab-pane'));

        $html .= html_writer::end_div(); //

        $html .= html_writer::end_div(); 

        // verticalTabs
        $html .= html_writer::start_div('rgumymoodle_innertabs', array('id' => 'rguco_previousstudyareas', 'class'=>'tab-pane'));
        $prevmenu = html_writer::start_tag('ul', array('class' => 'nav nav-pills mb-3'));
        $prevyears = false;  
         
        $firstTab = true;
        for($year=2031;$year>2007;$year--){  
            if(isset($this->rgucourses['modulestudyareas'][$year])){
				$prevyears = true;
                $linktext = new stdClass();
                $linktext->year = $year;
                $linktext->nextyear = $year + 1;
                $linkclass = 'nav-link';
                if($firstTab) {
                    $linkclass .= ' active';
                    $firstTab = false;
                }
               // if ($year == $years[0]) {
                //  
               // }
                $link = html_writer::link(
                        "#rguco_modules{$year}",
                        get_string('modulesyearnextyear', 'block_rgu_course_overview', $linktext),
                        array('class' => $linkclass, 'data-toggle'=>'tab')
                    );
                $prevmenu .= html_writer::tag('li', $link, array('class'=>'nav-item'));
            }
        }
        $prevmenu .= html_writer::end_tag('ul'); // .resp-tabs-list ver_1

        
        if($prevyears){
        	$html .= $prevmenu;
        }else{
        	$html .= html_writer::div(get_string('nostudyareasavailable', 'block_rgu_course_overview'), array('id' => 'rgu_course_overview-noresults'));
        }
        
        
        // Previous Study Areas Sub Content Areas
        $html .= html_writer::start_div('tab-content');
        $firstTab = true;
       for($year=2031;$year>2007;$year--){  
            if(isset($this->rgucourses['modulestudyareas'][$year])){
                $class = 'tab-pane';
                if($firstTab) {
                    $class .= ' active';
                    $firstTab = false;
                }
            	$html .= html_writer::div($this->listcourses($year, $pagesize, 'rgumodules'.$year), '', array('id' => "rguco_modules{$year}",1,'previous', 'class'=>$class));
            }
        }
        
        $html .= html_writer::end_div(); // .rgumymoodle_innertabs

        $html .= html_writer::end_div();  // End Previous Study Areas Sub Content Areas

        // Search Content Area
        $html .= html_writer::start_div('rguco_search', array('id'=>'rguco_search', 'class'=>'tab-pane'));

        // Search Form
        
        $html .= html_writer::start_div('input-group mb-3');
        $html .= '<input type="text" class="form-control" placeholder="Search" /><div class="input-group-append"><button class="btn btn-primary" type="button"><i class="fa fa-search"></i><span class="sr-only">Search</span></div>';
        $html .= html_writer::end_div();

        $html .= html_writer::div('', '', array('id' => 'rgugo_search_results'));

        $html .= html_writer::end_div(); // .rgumymoodle_outertabs
        $html .= html_writer::end_div(); // #rgumymoodle_outerbox
        
        $html = $this->rgubanner().$html;
        
        return $html;
    }

    /**
     * Return panel HTML
     *
     * @param string $panel Panel Id
     * @param int $page Paginator value
     * @return string
     */
	public function listcourses($panel, $pagesize, $blockinstanceid, $page=1,$status='current'){
		global $DB, $CFG; 
		
		//print_r($this->rgucourses);die();
		$courses = array();
		switch($panel){
			case "recentstudyareas":
				if(!empty($this->recent_study_areas)){
					$count = count($this->recent_study_areas);
					$recentlimit = 10;
					if(is_numeric($CFG->block_rgu_course_overview_recent_limit)&&$CFG->block_rgu_course_overview_recent_limit>1){
						$recentlimit = $CFG->block_rgu_course_overview_recent_limit;
					}
					
					if($count>$recentlimit){
						$this->recent_study_areas = array_slice($this->recent_study_areas,0,$recentlimit);
						$count = $recentlimit;
					}

					
					$courses = array('key'=>'recentstudyareas','count'=>$count,'paneldata'=>$this->recent_study_areas);	
					
					
				}
				break;
			
			
			case "modulestudyareas":
				$courses = $this->rgucourses['modulestudyareas']['current'];
				
				break;
			case "futuremodulestudyareas":
				$courses = $this->rgucourses['modulestudyareas']['future'];
				
				
				break;
			
			case "coursestudyarea":
				$courses = $this->rgucourses['coursestudyareas'];
				break;

			case "schoolstudyarea":
				$courses = $this->rgucourses['schoolstudyareas'];
				break;
			
			case "generalstudyarea":
				$courses = $this->rgucourses['generalstudyareas'];
				break;
								
			
			case "teamworkareas":
						$courses = $this->rgucourses['teamworkareas'];
					
						break;
			
			case "allmystudyareas":
						$courses = $this->rgucourses['all'];
					
						break;
						
			default: 
				if(isset($this->rgucourses['modulestudyareas'][$panel])){
					$courses = $this->rgucourses['modulestudyareas'][$panel];
					$panel = 'modules'.$panel;
				}
		}

        //$query = block_rgu_course_overview_get_query($panel);
        //$limitfrom = $pagesize * ($page-1);
        //$limitnum = $pagesize;

        //$totalrecords = $DB->get_records_sql($query->sql, $query->params);
        //$totalcount = count($totalrecords);
		$totalcount = $courses['count']; 
		$tables = '';
		$class = 'frontpages rgucoursepanel';
		$start = 1;
		
		if($courses['count']==0){
			return html_writer::div(get_string('nostudyareasavailable', 'block_rgu_course_overview'), array('id' => 'rgu_course_overview-noresults'));
		}
		
		
		foreach($courses as $key=>$paneldata){
			if(!is_array($paneldata)){
				continue;
			}
			$panel = html_writer::start_div('tab-pane', array('id' => 'rgu_previous_study_areas'));
	
	        //$paneldata = $DB->get_records_sql($query->sql, $query->params, $limitfrom, $limitnum);
	        
	        
	        if ($paneldata) {
    	        $panel .= html_writer::start_tag('ul', array('class'=>'course-list'));
	            foreach ($paneldata as $panelrow) {
    	            
    	            $icon = 'fa fa-graduation-cap';

	            	/*if(!empty($panelrow->heading)){
    	            	if($isInUL) {
        	            	$panel .= html_writer::end_tag('ul');
        	            	$isInUL = false;
    	            	}
    	            	$extraclass = 'rgucourselist_heading'.(stripos($panelrow->heading,'(continued)')) ? ' rgu_courseoverview_pagination_furniture' : '';
    	            	$panel .= html_writer::tag('h3', $panelrow->heading, array('class' => $extraclass));	            		 
	            		//$table->data[] = array(html_writer::div(,'rgucourselist_heading')); 
	            	}
	            	if(!empty($panelrow->subheading)){
    	            	if($isInUL) {
        	            	$panel .= html_writer::end_tag('ul');
        	            	$isInUL = false;
    	            	}
    	            	$extraclass = 'rgucourselist_subheading'.(stripos($panelrow->subheading,'(continued)')) ? ' rgu_courseoverview_pagination_furniture' : '';
    	            	$panel .= html_writer::tag('h4', $panelrow->subheading, array('class' => $extraclass));	            		 
	            		//$table->data[] = array(html_writer::div($panelrow->subheading,''));
	            	}
	            	*/
	            	$attributes = array();
	            	if(empty($panelrow->visible)){
	            		$attributes['class'] = 'dimmed'; 
	            		$icon = 'fa fa-ban';
	            	}
	            		            	
	                $linkurl = new moodle_url($CFG->wwwroot . '/course/view.php',array('id' => $panelrow->id));
	                $panel .= html_writer::tag('li', html_writer::link($linkurl, '<div class="media"><span class="media-left"><i class="'.$icon.'"></i></span><span class="media-body">'.$panelrow->fullname.'</span></div>',$attributes));
	                 
	            }
	            $panel .= html_writer::end_tag('ul');
	        }
	        $panel .= html_writer::end_div();
		}
        return $panel;
	}

    /**
     * Return previous course panel HTML
     *
     * @param string $year Year value
     * @param int $page Paginator value
     * @return string
     */
    public function listpreviouscourses($year, $pagesize, $blockinstanceid, $page=1) {
		global $DB, $CFG;

        $totalrecords = $DB->get_records_sql($query->sql, $query->params);
        $totalcount = count($totalrecords);
        
        $panel = html_writer::start_div('rgumymoodle_innertabs', array('id' => 'rgu_course_overview-previous-' . $year));

        $paneldata = $DB->get_records_sql($query->sql, $query->params, $limitfrom, $limitnum);
        if ($paneldata) {
            foreach ($paneldata as $panelrow) {

            	$attributes = array();
            	if(empty($panelrow->visible)){
            		$attributes['class'] = 'dimmed';
            	}
            	
                $linkurl = new moodle_url($CFG->wwwroot . '/course/view.php',array('id' => $panelrow->id));
                $panel .= html_writer::link($linkurl, $panelrow->fullname,$attributes);
            }
        }

        return $panel;
    }

    /**
     * Return Search panel HTML
     *
     * @param string $searchtext Search text
     * @param int $page Paginator value
     * @return string
     */
    public function listsearchresults($searchtext, $pagesize, $blockinstanceid, $page=1) {
        global $DB, $CFG;
        
        if(!is_numeric($pagesize)||$pagesize==0){
        	$pagesize = 15;
        }
        
        
        $query = block_rgu_course_overview_get_search_query($searchtext);
        $limitfrom = $pagesize * ($page-1);
        $limitnum = $pagesize;

        $totalrecords = $DB->get_records_sql($query->sql, $query->params);
        $totalcount = count($totalrecords);

        $table = new html_table();
        $table->attributes = array('class' => 'generaltable');
        $table->id = 'rgu_course_overview-searchresults-' . str_replace(' ', '', $searchtext);

        $paneldata = $DB->get_records_sql($query->sql, $query->params, $limitfrom, $limitnum);
        if ($paneldata) {
            foreach ($paneldata as $panelrow) {

            	$attributes = array();
            	if(empty($panelrow->visible)){
            		$attributes['class'] = 'dimmed';
            	}
            	
                $linkurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $panelrow->id));
                $rowlink = html_writer::link($linkurl, $panelrow->fullname,$attributes);
                $table->data[] = array(html_writer::div($rowlink)); 
                            }
        }

        return $this->block_search_course_overview_pagination($page, $totalcount, 'search', $blockinstanceid, $pagesize).html_writer::table($table);
    }

    /**
     * Returns panel paginator HTML
     *
     * @param int $page Current pagination value
     * @param int $totalcount Total panel data records
     * @return string
     */
    public function block_course_overview_pagination($page, $totalcount, $type=NULL, $blockinstanceid, $pagesize) {

        $html = '';
        $pages = ceil($totalcount / $pagesize);
        if ($pages > 1) {
            for ($i = 1; $i <= $pages; $i++) {
                $html .= ($i != $page) ? html_writer::link('javascript:;', $i, array('class' => 'rgu_course_overview_page_flip')) :
                        html_writer::link('javascript:;', $i, array('class' => 'rgu_course_overview_page_flip rgu_course_overview_paginator_selected'));
            }
            $html .= html_writer::link('javascript:;', 'All', array('class' =>'rgu_courseoverview_showall'));
        }

        //$paginationstats = new stdClass();
        //$paginationstats->firstrecord = (($page-1) * $pagesize) + 1;
        //$paginationstats->lastrecord = $paginationstats->firstrecord + ($pagesize-1);
        //if ($paginationstats->lastrecord > $totalcount) {
        //    $paginationstats->lastrecord = $totalcount;
        //}
        //$paginationstats->totalrecords = $totalcount;
        //if ($totalcount == 0) {
        //    $html .= get_string('nocurrentmodulestudyareasavailable', 'block_rgu_course_overview');
         //else {
          //  $html .= html_writer::span(get_string('displayingx-ycoursesofz',
          //          'block_rgu_course_overview', $paginationstats), 'rgu_course_overview_paginator_stats');
       // }
        

        return html_writer::div($html, "rgu_course_overview_pagination_{$type}",
                array('id' => "block_rgu_course_overview_{$blockinstanceid}"));
    }
    
    public function block_search_course_overview_pagination($page, $totalcount, $type=NULL, $blockinstanceid, $pagesize) {
    
    	$html = '';
    	
    	$all = false;
    	if($pagesize==10000){
    		$pagesize = block_rgu_course_overview_get_pagesize();
			$all = true; 
    	}
    	
    	
    	$pages = ceil($totalcount / $pagesize);
    	if ($pages > 1) {
    		for ($i = 1; $i <= $pages; $i++) {
    			$html .= ($i != $page || $all ) ? html_writer::link('javascript:;', $i, array('class' => 'rgu_course_overview_paginator')) :
    			html_writer::span($i, 'rgu_course_overview_paginator');
    		}
    		
    		$html .= (!$all) ? html_writer::link('javascript:;', 'All', array('class' => 'rgu_course_overview_paginator')) :
    			html_writer::span('All', 'rgu_course_overview_paginator');
    		
    		
    		$html .= ' ';
    		
    		html_writer::span($i, 'rgu_course_overview_paginator');
    		
    		
    	}
    	
 
    
    	$paginationstats = new stdClass();
    	$paginationstats->firstrecord = (($page-1) * $pagesize) + 1;
    	$paginationstats->lastrecord = $paginationstats->firstrecord + ($pagesize-1);
    	if ($paginationstats->lastrecord > $totalcount) {
    		$paginationstats->lastrecord = $totalcount;
    	}
    	$paginationstats->totalrecords = $totalcount;
    	if ($totalcount == 0) {
    		$html .= get_string('nocurrentmodulestudyareasavailable', 'block_rgu_course_overview');
    	} else {
    		if($all){
    			$html .= html_writer::span(get_string('displayallstudyareas','block_rgu_course_overview'), 'rgu_course_overview_paginator_stats');
    		}else{
    		
    			$html .= html_writer::span(get_string('displayingx-ycoursesofz',
    				'block_rgu_course_overview', $paginationstats), 'rgu_course_overview_paginator_stats');
    		}
    	}
    
    	return html_writer::div($html, "rgu_course_overview_pagination_{$type}",
    	array('id' => "block_rgu_course_overview_{$blockinstanceid}"));
    }
    
     function get_recent_study_areas(){
     	global $USER,$CFG,$DB;
     	if(!empty($CFG->block_rgu_course_overview_hide_recent)){
     		return false;
     	}
     	$min = 5;
    	$limit = 10; 
    	
    	if(empty($USER->id)){
    		return false;
    	}
    	
    	
    	if(!empty($CFG->block_rgu_course_overview_recent_min_courses)&&is_numeric($CFG->block_rgu_course_overview_recent_min_courses)&&$CFG->block_rgu_course_overview_recent_min_courses>0){
    		$min = round($CFG->block_rgu_course_overview_recent_min_courses);
    	}
    	if(!empty($CFG->block_rgu_course_overview_recent_limit)&&is_numeric($CFG->block_rgu_course_overview_recent_limit)&&$CFG->block_rgu_course_overview_recent_limit>0){
    		$limit = round($CFG->block_rgu_course_overview_recent_limit);
    	}
    	if($limit<$min){
    		// misconfigured because the min number of courses can't be more than the max 
    		return false;
    	}
    	
     	//print_r($this->rgucourses['unsorted']);
     	
     	$latest = $DB->get_record('rgu_recent_courses',array('userid'=>$USER->id)); 
     	
     	if(empty($latest)){
     		return false;
     	}
     	
     	
     	
     	$coursearray =  explode(',',$latest->courses);
     	
     	
     	
     	
     	
     	
     	if(count($coursearray)<$min){
     		return false;
     	}
     	
     	
     	
     	
     	$skipaccesscheck = false;
     	$sitecontext = context_system::instance();
		if(has_capability('moodle/site:config', $sitecontext)){
			$skipaccesscheck = 1;
		}else{
			$cataccess = $DB->get_record_sql("SELECT COUNT({role_assignments}.id) AS catcount
					FROM {role_assignments}
					INNER JOIN {context} ON {role_assignments}.contextid = {context}.id
					WHERE {context}.contextlevel = 40 AND {role_assignments}.userid = {$USER->id}");
			if(!empty($cataccess->catcount)){
				$skipaccesscheck = true;
			}
		}



	if($skipaccesscheck) {
			$courses = $DB->get_records_sql("SELECT * FROM {course} where id in ({$latest->courses})");
		}else{
			$courses = array();
			
			foreach( $coursearray as $thiscourse){
				if(isset($this->originalcourses[$thiscourse])){
					$courses[$thiscourse] = $this->originalcourses[$thiscourse];
				}
			}
		}
		
		
		
		
		
		
		if(count($courses)<$min){
			return false;
		}
	
		
		$orderedcourses = array();
		// order array
		foreach(array_reverse($coursearray,true) as $id){
			if(!empty($courses[$id])){
				$orderedcourses[] = $courses[$id];
			}
		}
		
		
		
		
		$this->recent_study_areas = $orderedcourses;
		 
		
		return true;
		
		
     	
     }

	public function rgu_search_form() {

		$searchtextfield = html_writer::empty_tag(
                'input',
                array(
                    'type' => 'text',
		'size'=>'20',
                    'id' => 'rgumymoodle_studyareasearchbox',
                    'name' => 'rgumymoodle_studyareasearch',
                    'value' => get_string('searchmystudyareas', 'block_rgu_course_overview')
                )
            );
 
        $searchimagefield = html_writer::img(
                $CFG->wwwroot . '/blocks/rgu_course_overview/pix/search-icon.png',
                get_string('submitform', 'block_rgu_course_overview'),
                array(
                    'class' => 'rgumymoodle_rgumymoodle_searchicon',
                    'id' => "rgumymoodle_rgumymoodle_searchicon_{$blockinstanceid}"
                )
            );

	    return $searchtextfield.$searchimagefield; 
	}    


 
     
     function rgubanner(){
     		global $CFG;
     		
     		$config = get_config('block_rgu_course_overview'); 	
     		
     		if(empty($config->showbanners)){
     			return false; 
     		}
     		
     		
     		if($this->get_banners()){
     			$this->match_banners(); 
     		} 
     		
     		if(empty($this->matched_banners)){
     			return false;
     		}
     		$html = '';
     		foreach($this->matched_banners as $banner){
     			$id = (is_numeric($banner->courseid)&&$banner->courseid>0) ? $banner->courseid : $banner->firstcourse;
     			
     			switch($banner->renderer){
					case "standard":
						$html  .= html_writer::start_div('rgubanner',array('class'=>'rgubanner rgubanner_'.$banner->standard));
						$html  .= html_writer::tag('a',html_writer::empty_tag('img',array('src'=>$CFG->wwwroot.'/blocks/rgu_course_overview/pix/arrow50x50.png','width'=>'25')).html_writer::tag('span',$banner->bannertext),array('href'=>$CFG->wwwroot.'/course/view.php?id='.$id));
						$html  .= html_writer::end_div();
						break;
					case "custom":
						
						$html = str_replace('#courseid#',$id,$banner->bannertext);
						$html = html_writer::tag('div',$html);
						break;
				
     			}
     			
     			
     			
	        		
     		}
        	return $html;
     }
    
     function get_banners(){
     	global $DB;
     	
     	$this->banners = $DB->get_records('rgu_banner',array('status'=>1),'sortorder');
     	
     	if(empty($this->banners)){
     		return false;
     	}
     	return true;
     }

     
     function match_banners(){
     	if(empty($this->originalcourses)){
     		return false;
     	}
     	if(empty($this->banners)){
     		return false;
     	}
     	
     	$this->matched_banners = array();
     	foreach($this->originalcourses as $course){
     		foreach($this->banners as $banner){
     			if(!empty($banner->courseid)){
     				if($course->id===$banner->courseid){
     					$this->matched_banners[$banner->id] = $banner; 
     				}
     			}else{
     				if(!empty($banner->regex)){
     					preg_match('/'.$banner->regex.'/',$course->shortname,$matches);
     					if(!empty($matches)){
     						$this->matched_banners[$banner->id] = $banner;
     						
     						if(empty($this->matched_banners[$banner->id]->firstcourse)){
     							$this->matched_banners[$banner->id]->firstcourse = $course->id;
     						}
     					}
     				}
     			}
     		}
     	}
     	return true;
     }

     
}
     
     
class banner_editor {
     function render_rgu_banner_form(){
     	global $DB;
     
     	$records = $DB->get_records('rgu_banner',null,'sortorder');
     
     
     	$table = new html_table();
     	$table->attributes['class'] = 'admintable generaltable';
     	$table->id = 'filterssetting';
     	$table->head = array('Banner text','Course ID','Regex','Renderer','Sort Order','Status','');
     	$table->data  = array();
     	$row = 0;
     	foreach ($records as $record) {
     		// id[hidden], trigger (courseid/regex), renderer, sortorder, status
     		$row++;
     		$table->data[] = $this->set_row($row,$record->id,$record->bannertext,$record->courseid,$record->regex,$record->renderer,$record->sortorder);
     	}
     	$table->data[] = $this->set_row();
     
     	$html = html_writer::start_tag('form',array('name'=>'rgu_banner_settings','action'=>'bannereditor.php','method'=>'post'));
     	$html .= html_writer::table($table);
     	$html .= html_writer::empty_tag('input',array('type'=>'hidden','name'=>'rows','value'=>$row));
     	$html .= html_writer::empty_tag('input',array('type'=>'submit','name'=>'submit','value'=>'Update'));
     	$html .= html_writer::end_tag('form');
     
     
     	return $html;
     }
     

     function set_row($row='',$id=null,$bannertext='', $courseid='',$regex='',$renderer='',$sort=100,$status=1) {
     	$tablerows = array();
     	$tablerow = '';
     	if($courseid==0){
     		$courseid='';
     	}
     
     
     	if(isset($id)){
     		$tablerow = html_writer::empty_tag('input',array('type'=>'hidden','name'=>'id'.$row,'value'=>$id));
     	}
     	$tablerow .= html_writer::tag('textarea',$bannertext,array('name'=>'bannertext'.$row,'cols'=>'30','rows'=>'1'));
     	$tablerow .= html_writer::tag('a','<i class="fa fa-arrows-alt" aria-hidden="true"></i>',array('onClick'=>'toggleTextarea();return false;'));
     	
     	
     	$tablerows[] = $tablerow;
     	$tablerows[] = html_writer::empty_tag('input',array('type'=>'text','name'=>'courseid'.$row,'value'=>$courseid,'size'=>'5'));
     	$tablerows[] = html_writer::empty_tag('input',array('type'=>'text','name'=>'regex'.$row,'value'=>$regex,'size'=>'15'));
     	$tablerows[] = $this->rguselect('renderer'.$row,array('standard'=>'Standard','custom'=>'Custom'),$renderer);
     	$tablerows[] = html_writer::empty_tag('input',array('type'=>'text','name'=>'sortorder'.$row,'value'=>$sort,'size'=>'5'));
     	$tablerows[] = $this->rguselect('status'.$row,array('1'=>'Active','0'=>'Inactive'),$status);
     
     	$tablerow = '';
     	if(isset($id)){
     		$tablerow = html_writer::empty_tag('input',array('type'=>'button','name'=>'delete'.$row,'value'=>'Delete','onClick'=>'window.location=\'?delete='.$id.'\''));
     	}
     	$tablerows[] = $tablerow;
     
     
     	return $tablerows;
     }
     
     
     function rguselect($name,$options,$value){
     	$html = html_writer::start_tag('select',array('name'=>$name));
     
     	foreach($options as $optionkey=>$optiondisplayvalue){
     		$optionvalue = array('value'=>$optionkey);
     		if($optionkey===$value){
     			$optionvalue['selected'] = 'selected';
     		}
     
     		$html .= html_writer::tag('option',$optiondisplayvalue,$optionvalue);
     	}
     
     
     	$html .= html_writer::end_tag('select');
     	return $html;
     }
     function update_banners($rows){
     	global $DB;
     	if(!is_numeric($rows)||$rows<1){
     		return false;
     	}
     
     	for($row = 1; $row <= $rows; $row++){
     		$newrow = new stdClass;
     		$newrow->id = optional_param('id'.$row, 0, PARAM_INT);
     		$newrow->bannertext = optional_param('bannertext'.$row, '', PARAM_RAW);
     		$newrow->courseid = optional_param('courseid'.$row,NULL,PARAM_INT);
     		$newrow->regex = optional_param('regex'.$row, '',PARAM_RAW);
     		$newrow->renderer = optional_param('renderer'.$row, '',PARAM_RAW);
     		$newrow->sortorder = optional_param('sortorder'.$row,100, PARAM_INT);
     		$newrow->status = optional_param('status'.$row, 0, PARAM_INT);
     			
     		$oldrow = $DB->get_record('rgu_banner',array('id'=>$newrow->id));
     			
     			
     			
     			
     		if($newrow!==$oldrow){
     			$DB->update_record('rgu_banner',$newrow);
     		}
     			
     	}
     
     }
     
     function add_banner(){
     	global $DB;
     	$newrow = new stdClass;
     	$newrow->bannertext = optional_param('bannertext','',PARAM_RAW);
     	$newrow->courseid = optional_param('courseid',NULL, PARAM_INT);
     	$newrow->regex = optional_param('regex', '',PARAM_RAW);
     	$newrow->renderer = optional_param('renderer', '',PARAM_RAW);
     	$newrow->sortorder = optional_param('sortorder',100, PARAM_INT);
     	$newrow->status = optional_param('status', 0, PARAM_INT);
     
     	if(empty($newrow->bannertext)||(empty($newrow->courseid)&&empty($newrow->regex))){
     		return false;
     	}
     	$DB->insert_record('rgu_banner',$newrow);
     }
     
     
     function delete_banner($delete){
     	global $DB;
     	if(!is_numeric($delete)){
     		return false;
     	}
     	$DB->delete_records('rgu_banner',array('id'=>$delete));
     }
      
     
     
     function render_link_to_settings(){

     	$linkurl = new moodle_url($CFG->wwwroot . '/admin/settings.php',array('section'=>'blocksettingrgu_course_overview'));
     	$link = html_writer::link($linkurl,new lang_string('showbanners_returntosettings', 'block_rgu_course_overview'));
     	 
     	return html_writer::tag('div',$link);
     	
     }

     function render_description(){
     
     	return html_writer::tag('div',new lang_string('showbanners_desc', 'block_rgu_course_overview'));
     
     }
      
     
     
}
