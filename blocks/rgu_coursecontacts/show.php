<?php
    
    require_once('../../config.php');
    
    $course = required_param('id', PARAM_RAW);
    $context = context_course::instance($course);
    
    require_login();
    require_capability('moodle/course:manageactivities', $context);
    
    $record = new stdClass();
    $record->courseid = $course;
    $record->userid = $USER->id;
    
    $DB->insert_record('block_rgucc_users', $record);
    
    header('Location: '.$_SERVER['HTTP_REFERER']);
    
?>