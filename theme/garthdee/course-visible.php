<?php

    include(__DIR__.'/../../config.php');

    require_login(); 

    $id = required_param('id',PARAM_INT);
    $coursecontext = context_course::instance($id);
    require_capability('moodle/course:update', $coursecontext);

    $record = new stdClass;
    $record->id = $id;
    $record->visible = 1;

    $DB->update_record('course',$record);

    $url = new moodle_url('/course/view.php',array('id'=>$id));

    header('location: '.$url);
