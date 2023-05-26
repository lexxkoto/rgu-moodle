<?php 
include('../../config.php');

 $t = new temp;
$rows = optional_param('rows', 0, PARAM_INT);
$delete = optional_param('delete', NULL, PARAM_INT);

if(!empty($delete)){
	$t->delete_banner($delete);
}

$t->update_banners($rows);
$t->add_banner();

echo $t->render_rgu_banner_form();

class temp {
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
		
		$html = html_writer::start_tag('form',array('name'=>'rgu_banner_settings','action'=>'temp.php','method'=>'post'));
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
		$tablerow .= html_writer::empty_tag('input',array('type'=>'text','name'=>'bannertext'.$row,'value'=>$bannertext,'size'=>'30'));
		$tablerows[] = $tablerow;
		$tablerows[] = html_writer::empty_tag('input',array('type'=>'text','name'=>'courseid'.$row,'value'=>$courseid,'size'=>'5')); 
		$tablerows[] = '/'.html_writer::empty_tag('input',array('type'=>'text','name'=>'regex'.$row,'value'=>$regex,'size'=>'15')).'/';
		$tablerows[] = $this->rguselect('renderer'.$row,array('standard'=>'Standard','custom'=>'Custom','preenrol'=>'Preenrol'),$renderer);
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
	
}