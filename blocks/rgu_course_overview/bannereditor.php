<?php

	require_once('../../config.php');
	require_once('renderer.php');

	if(!has_capability('moodle/site:config', context_system::instance())){
		print_error('Sorry, you do not have access to this page');
	}
	
	
    $title = "Banner Editor";
    $PAGE->set_pagelayout('admin');
	$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
    $PAGE->set_url(new moodle_url('/blocls/rgu_course_overview/bannereditor.php'));
    $PAGE->requires->js('/blocks/rgu_course_overview/js/bannereditor.js');
    $PAGE->set_title($title);
    
    $PAGE->set_button('');
   
    $editor = new banner_editor;
    $rows = optional_param('rows', 0, PARAM_INT);
    $delete = optional_param('delete', NULL, PARAM_INT);
    
    if(!empty($delete)){
    	$editor->delete_banner($delete);
    }
    
    $editor->update_banners($rows);
    $editor->add_banner();
    
    
    
    
    
	echo $OUTPUT->header();
	echo $OUTPUT->heading($title);
	
	echo $editor->render_link_to_settings(); 
	
	echo $editor->render_description();
	
	echo $editor->render_rgu_banner_form();
	

    echo $OUTPUT->footer();
?>