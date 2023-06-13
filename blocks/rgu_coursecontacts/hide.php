<?php
    
    require_once('../../config.php');
    
    $course = required_param('id', PARAM_RAW);
    $context = context_course::instance($course);
    
    require_login();
    require_capability('moodle/course:manageactivities', $context);
        
    $DB->delete_records('block_rgucc_users', Array('courseid'=>$course,'userid'=>$USER->id));
    
    header('Location: '.$_SERVER['HTTP_REFERER']);
    
?>