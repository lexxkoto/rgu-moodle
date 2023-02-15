<?php

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime('@'.$datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function user_link($userid) {
    global $DB, $OUTPUT;
    
    $user = $DB->get_record('user', Array('id'=>$userid));
    echo $OUTPUT->user_picture($user, Array('includefullname' => true));
}

function course_link($courseid) {
    global $DB;
    
    $course = $DB->get_record('course', Array('id'=>$courseid));
    
    $url = new moodle_url('/course/view.php', Array('id'=>$courseid));
    
    echo '<a href="'.$url.'">'.$course->fullname.'</a>';
}

function assign_link($assignid) {
    global $DB;
    
    $assign = $DB->get_record('assign', Array('id'=>$assignid));
    
    $modid = $DB->get_record('modules', Array('name'=>'assign'));
    
    $cm = $DB->get_record('course_modules', array('instance'=>$assignid, 'module'=>$modid->id));
    
    $url = new moodle_url('/mod/assign/view.php', Array('id'=>$cm->id));
    
    echo '<a href="'.$url.'">'.$assign->name.'</a>';
}

function module_link($cmid) {
    global $DB;
    
    $module = $DB->get_record('course_modules', Array('id'=>$cmid));
    
    $moduleType = $module->module;
    
    $moduleRecord = $DB->get_record('modules', Array('id'=>$moduleType));
    
    $moduleName = $moduleRecord->name;
    
    switch($moduleName) {
        case 'assign':
            $assign = $DB->get_record('assign', Array('id'=>$module->instance));
            $url = new moodle_url('/mod/assign/view.php', Array('id'=>$cmid));
            echo '<a href="'.$url.'">'.$assign->name.'</a>';
            break;
        default:
            echo 'Unknown Module: '.$moduleName.' '.$cmid;
    }
}

?>
