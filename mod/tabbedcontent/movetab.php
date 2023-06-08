<?php 
include(__DIR__.'/../../config.php');
include('lib.php');

 require_login();

//if(!has_capability


$direction = optional_param('direction',null,PARAM_ALPHA);
$tabtomove = optional_param('tab',null,PARAM_INT);
$instanceid  = optional_param('instance',null,PARAM_INT);

if(!empty($direction)&&!empty($tabtomove)&&!empty($instanceid)){

	$instance = $DB->get_record('tabbedcontent', array('id' => $instanceid));
	$tabs = $DB->get_records('tabbedcontent_content',array('instance'=>$instanceid), 'tabposition','id,tabposition');

 
if(has_capability('mod/tabbedcontent:addinstance',context_course::instance($instance->course))){ 
		$neworder = array();
		$moveincrement = ($direction == 'left' || $direction == 'up') ? -15 : 15;
		$count = 20;    	
		foreach($tabs as $tab){
			if($tab->id==$tabtomove){
				$neworder[$count + $moveincrement] = $tab->id;
			}else{
				$neworder[$count] = $tab->id;
			}
			$count = $count + 10;
			
		}
		ksort($neworder);
     
	
		$newposition = 0;

		foreach($neworder as $tabid){
			if($tabs[$tabid]->tabposition!==$newposition){
				$update = new stdClass;
				$update->id = $tabid;
				$update->tabposition = $newposition;
				$DB->update_record('tabbedcontent_content',$update);
			 
			}
			$newposition++; 
		} 
	
		$url = new moodle_url('/course/view.php',array('id'=>$instance->course));
		$url .= '#tabbedcontent_'.$instance->id;
		header('location: '.$url); 
	} 
	

} 



 
